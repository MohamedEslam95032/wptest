#!/bin/bash
set -e

WP_PATH="/var/www/html"
FLAG_OPTION="coonex_houzez_initialized"

echo "▶ Houzez One-Time Initialization"

# --------------------------------------------------
# Guard: run ONCE only
# --------------------------------------------------
if wp option get "$FLAG_OPTION" --allow-root --path="$WP_PATH" >/dev/null 2>&1; then
  echo "ℹ Houzez already initialized. Skipping."
  exit 0
fi

# --------------------------------------------------
# Activate Houzez Theme
# --------------------------------------------------
echo "▶ Activating Houzez theme"
wp theme activate houzez --allow-root --path="$WP_PATH"

# --------------------------------------------------
# Activate required plugins
# --------------------------------------------------
echo "▶ Activating Houzez plugins"
wp plugin activate \
  houzez-theme-functionality \
  houzez-login-register \
  houzez-crm \
  houzez-studio \
  favethemes-insights \
  revslider \
  --allow-root --path="$WP_PATH"

# --------------------------------------------------
# Import demo content (XML)
# --------------------------------------------------
if [ -f "$WP_PATH/wp-content/themes/houzez/demo/content.xml" ]; then
  echo "▶ Importing demo content"
  wp import "$WP_PATH/wp-content/themes/houzez/demo/content.xml" \
    --authors=create \
    --allow-root --path="$WP_PATH"
fi

# --------------------------------------------------
# Import theme options
# --------------------------------------------------
if [ -f "$WP_PATH/wp-content/themes/houzez/demo/options.json" ]; then
  echo "▶ Importing Houzez options"
  wp option update houzez_options \
    "$(cat $WP_PATH/wp-content/themes/houzez/demo/options.json)" \
    --format=json \
    --allow-root --path="$WP_PATH"
fi

# --------------------------------------------------
# Import widgets
# --------------------------------------------------
if [ -f "$WP_PATH/wp-content/themes/houzez/demo/widgets.wie" ]; then
  echo "▶ Importing widgets"

  if ! wp plugin is-installed widget-importer-exporter --allow-root --path="$WP_PATH"; then
    wp plugin install widget-importer-exporter --activate --allow-root --path="$WP_PATH"
  fi

  wp widget import "$WP_PATH/wp-content/themes/houzez/demo/widgets.wie" \
    --allow-root --path="$WP_PATH"
fi

# --------------------------------------------------
# Set homepage
# --------------------------------------------------
HOME_ID=$(wp post list --post_type=page --pagename=home --field=ID --allow-root --path="$WP_PATH" | head -n1)

if [ -n "$HOME_ID" ]; then
  echo "▶ Setting homepage"
  wp option update show_on_front page --allow-root --path="$WP_PATH"
  wp option update page_on_front "$HOME_ID" --allow-root --path="$WP_PATH"
fi

# --------------------------------------------------
# Final flag (CRITICAL)
# --------------------------------------------------
wp option add "$FLAG_OPTION" 1 --allow-root --path="$WP_PATH"

echo "✅ Houzez initialization completed (ONE-TIME)"
