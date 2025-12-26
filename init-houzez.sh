#!/bin/bash
set -e

# ==================================================
# Config
# ==================================================
WP_PATH="/var/www/html"
THEME_SLUG="houzez"
INIT_FLAG="coonex_houzez_initialized"

# اسم الديمو الثابت (عدله لو لزم)
HOUZEZ_DEMO_SLUG="${HOUZEZ_DEMO_SLUG:-default}"

THEME_PATH="$WP_PATH/wp-content/themes/$THEME_SLUG"
DEMO_BASE_PATH="$THEME_PATH/framework/demos"
DEMO_PATH="$DEMO_BASE_PATH/$HOUZEZ_DEMO_SLUG"

echo "▶ Starting Houzez initialization"

# ==================================================
# 0) Run once only (idempotent)
# ==================================================
if wp option get "$INIT_FLAG" --allow-root --path="$WP_PATH" >/dev/null 2>&1; then
  echo "ℹ Houzez already initialized, skipping"
  exit 0
fi

# ==================================================
# 1) Validate theme exists
# ==================================================
if [ ! -d "$THEME_PATH" ]; then
  echo "❌ Houzez theme not found at: $THEME_PATH"
  exit 1
fi

# ==================================================
# 2) Activate Houzez theme
# ==================================================
echo "▶ Activating Houzez theme"
wp theme activate "$THEME_SLUG" --allow-root --path="$WP_PATH"

# ==================================================
# 3) Activate required plugins (if installed)
# ==================================================
echo "▶ Activating Houzez plugins"

REQUIRED_PLUGINS=(
  "houzez-theme-functionality"
  "elementor"
  "redux-framework"
  "contact-form-7"
)

for plugin in "${REQUIRED_PLUGINS[@]}"; do
  if wp plugin is-installed "$plugin" --allow-root --path="$WP_PATH"; then
    wp plugin activate "$plugin" --allow-root --path="$WP_PATH" || true
  else
    echo "⚠ Plugin $plugin not found, skipping"
  fi
done

# ==================================================
# 4) Validate demo directory
# ==================================================
if [ ! -d "$DEMO_PATH" ]; then
  echo "❌ Houzez demo not found:"
  echo "   Expected: $DEMO_PATH"
  exit 1
fi

echo "▶ Using Houzez demo: $HOUZEZ_DEMO_SLUG"

# ==================================================
# 5) Import demo content (XML)
# ==================================================
DEMO_XML=$(find "$DEMO_PATH" -maxdepth 1 -type f -iname "*.xml" | head -n 1)

if [ -n "$DEMO_XML" ]; then
  echo "▶ Importing demo content"
  wp import "$DEMO_XML" \
    --authors=create \
    --allow-root \
    --path="$WP_PATH"
else
  echo "⚠ No demo XML found, skipping content import"
fi

# ==================================================
# 6) Import Redux options (if available)
# ==================================================
DEMO_OPTIONS=$(find "$DEMO_PATH" -maxdepth 1 -type f -iname "*.json" | head -n 1)

if [ -n "$DEMO_OPTIONS" ] && wp plugin is-active redux-framework --allow-root --path="$WP_PATH"; then
  echo "▶ Importing Houzez theme options"
  wp redux import houzez_options "$DEMO_OPTIONS" \
    --allow-root \
    --path="$WP_PATH" || true
else
  echo "ℹ No Redux options imported"
fi

# ==================================================
# 7) Import widgets (if available)
# ==================================================
DEMO_WIDGETS=$(find "$DEMO_PATH" -maxdepth 1 -type f -iname "*.wie" | head -n 1)

if [ -n "$DEMO_WIDGETS" ] && wp plugin is-installed widget-importer-exporter --allow-root --path="$WP_PATH"; then
  echo "▶ Importing widgets"
  wp widget import "$DEMO_WIDGETS" \
    --allow-root \
    --path="$WP_PATH" || true
else
  echo "ℹ No widgets imported"
fi

# ==================================================
# 8) Set homepage automatically
# ==================================================
HOME_ID=$(wp post list \
  --post_type=page \
  --posts_per_page=1 \
  --orderby=ID \
  --order=ASC \
  --field=ID \
  --allow-root \
  --path="$WP_PATH")

if [ -n "$HOME_ID" ]; then
  echo "▶ Setting static homepage (ID: $HOME_ID)"
  wp option update show_on_front page --allow-root --path="$WP_PATH"
  wp option update page_on_front "$HOME_ID" --allow-root --path="$WP_PATH"
fi

# ==================================================
# 9) Fix permalinks
# ==================================================
wp rewrite structure '/%postname%/' --hard --allow-root --path="$WP_PATH"

# ==================================================
# 10) Elementor final sync
# ==================================================
if wp plugin is-active elementor --allow-root --path="$WP_PATH"; then
  echo "▶ Regenerating Elementor files"
  wp elementor flush_css --allow-root --path="$WP_PATH" || true
fi

# ==================================================
# 11) Mark as initialized
# ==================================================
wp option update "$INIT_FLAG" 1 --allow-root --path="$WP_PATH"

echo "✅ Houzez initialization completed successfully"
