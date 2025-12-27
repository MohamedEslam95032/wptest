#!/bin/bash
set -e

echo "▶ Installing uiXpress (SAFE ROOT INSTALL)"

WP_PATH="/var/www/html"
PLUGIN_DIR="$WP_PATH/wp-content/plugins/uxpress"
PLUGIN_MAIN="uxpress/uixpress.php"

# --------------------------------------------------
# 1) Ensure WordPress is installed
# --------------------------------------------------
if ! wp core is-installed --allow-root --path="$WP_PATH"; then
  echo "❌ WordPress not installed yet – aborting uiXpress install"
  exit 0
fi

# --------------------------------------------------
# 2) Ensure plugins directory exists
# --------------------------------------------------
mkdir -p "$WP_PATH/wp-content/plugins"
chown -R www-data:www-data "$WP_PATH/wp-content/plugins"

# --------------------------------------------------
# 3) Remove ANY legacy MU plugin (CRITICAL)
# --------------------------------------------------
if [ -f "$WP_PATH/wp-content/mu-plugins/xpress-install.php" ]; then
  echo "⚠ Removing legacy MU plugin xpress-install.php"
  rm -f "$WP_PATH/wp-content/mu-plugins/xpress-install.php"
fi

# --------------------------------------------------
# 4) Verify plugin files exist
# --------------------------------------------------
if [ ! -f "$WP_PATH/wp-content/plugins/$PLUGIN_MAIN" ]; then
  echo "❌ uiXpress plugin files not found at:"
  echo "   wp-content/plugins/uxpress/uixpress.php"
  exit 1
fi

echo "✅ uiXpress files detected"

# --------------------------------------------------
# 5) Activate uiXpress (SAFE)
# --------------------------------------------------
if wp plugin is-active "$PLUGIN_MAIN" --allow-root --path="$WP_PATH"; then
  echo "ℹ uiXpress already active"
else
  echo "▶ Activating uiXpress"
  wp plugin activate "$PLUGIN_MAIN" \
    --allow-root \
    --path="$WP_PATH"
fi

# --------------------------------------------------
# 6) Fix permissions
# --------------------------------------------------
chown -R www-data:www-data "$PLUGIN_DIR"

# --------------------------------------------------
# 7) Final verification
# --------------------------------------------------
echo "✅ uiXpress activation completed"
wp plugin list --allow-root --path="$WP_PATH" | grep uxpress || true
