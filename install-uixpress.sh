#!/bin/bash
set -e

WP_PATH="/var/www/html"
PLUGIN="xpress/uixpress.php"
FLAG_FILE="$WP_PATH/.uixpress_activated"

echo "▶ uiXpress post-boot activation check"

# Run once only
if [ -f "$FLAG_FILE" ]; then
  echo "ℹ uiXpress already activated (flag exists)"
  exit 0
fi

# Wait until WordPress is fully ready
until wp core is-installed --allow-root --path="$WP_PATH" >/dev/null 2>&1; do
  echo "⏳ Waiting for WordPress to be ready..."
  sleep 2
done

# Activate plugin
if wp plugin is-installed "$PLUGIN" --allow-root --path="$WP_PATH"; then
  if wp plugin is-active "$PLUGIN" --allow-root --path="$WP_PATH"; then
    echo "ℹ uiXpress already active"
  else
    echo "▶ Activating uiXpress via WP-CLI"
    wp plugin activate "$PLUGIN" --allow-root --path="$WP_PATH"
  fi
else
  echo "❌ uiXpress not installed"
  exit 0
fi

# Create flag
touch "$FLAG_FILE"
chown www-data:www-data "$FLAG_FILE"

echo "✅ uiXpress activated successfully"
