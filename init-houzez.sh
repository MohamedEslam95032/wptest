#!/bin/bash
set -e

WP_PATH="/var/www/html"
THEME="houzez"
DEMO_FLAG="coonex_houzez_initialized"

echo "▶ Initializing Houzez module"

# --------------------------------------------------
# 0) Exit if already initialized
# --------------------------------------------------
if wp option get $DEMO_FLAG --allow-root --path="$WP_PATH" >/dev/null 2>&1; then
  echo "ℹ Houzez already initialized"
  exit 0
fi

# --------------------------------------------------
# 1) Activate Houzez theme
# --------------------------------------------------
if wp theme is-installed "$THEME" --allow-root --path="$WP_PATH"; then
  echo "▶ Activating Houzez theme"
  wp theme activate "$THEME" --allow-root --path="$WP_PATH"
else
  echo "❌ Houzez theme not found"
  exit 1
fi

# --------------------------------------------------
# 2) Activate required plugins
# --------------------------------------------------
echo "▶ Activating Houzez required plugins"

REQUIRED_PLUGINS=(
  "houzez-theme-functionality"
  "elementor"
  "contact-form-7"
)

for plugin in "${REQUIRED_PLUGINS[@]}"; do
  if wp plugin is-installed "$plugin" --allow-root --path="$WP_PATH"; then
    wp plugin activate "$plugin" --allow-root --path="$WP_PATH" || true
  else
    echo "⚠ Plugin $plugin not installed, skipping"
  fi
done

# --------------------------------------------------
# 3) Import Houzez Demo Content (ONCE)
# --------------------------------------------------
echo "▶ Importing Houzez demo content"

wp import /opt/coonex/demo/houzez/content.xml \
  --authors=create \
  --allow-root \
  --path="$WP_PATH"

# --------------------------------------------------
# 4) Import Theme Options (Redux)
# --------------------------------------------------
if wp plugin is-active redux-framework --allow-root --path="$WP_PATH"; then
  wp redux import houzez_options /opt/coonex/demo/houzez/options.json \
    --allow-root \
    --path="$WP_PATH" || true
fi

# --------------------------------------------------
# 5) Import Widgets
# --------------------------------------------------
if wp plugin is-installed widget-importer-exporter --allow-root --path="$WP_PATH"; then
  wp widget import /opt/coonex/demo/houzez/widgets.wie \
    --allow-root \
    --path="$WP_PATH" || true
fi

# --------------------------------------------------
# 6) Set Homepage
# --------------------------------------------------
HOME_ID=$(wp post list --post_type=page --pagename=home --field=ID --allow-root --path="$WP_PATH")
if [ -n "$HOME_ID" ]; then
  wp option update show_on_front page --allow-root --path="$WP_PATH"
  wp option update page_on_front "$HOME_ID" --allow-root --path="$WP_PATH"
fi

# --------------------------------------------------
# 7) Permalinks & Elementor
# --------------------------------------------------
wp rewrite structure '/%postname%/' --hard --allow-root --path="$WP_PATH"

if wp plugin is-active elementor --allow-root --path="$WP_PATH"; then
  wp elementor flush_css --allow-root --path="$WP_PATH" || true
fi

# --------------------------------------------------
# 8) Mark as initialized
# --------------------------------------------------
wp option update $DEMO_FLAG 1 --allow-root --path="$WP_PATH"

echo "✅ Houzez initialization completed"
