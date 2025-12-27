#!/bin/bash
set -e

echo "▶ Activating uiXpress via WP-CLI (SAFE)"

WP_PATH="/var/www/html"
PLUGIN_SLUG="xpress"
PLUGIN_FILE="xpress/uixpress.php"

# ----------------------------------------
# 1) تأكد إن ووردبريس متسطب
# ----------------------------------------
if ! wp core is-installed --allow-root --path="$WP_PATH"; then
  echo "❌ WordPress not installed yet – skipping uiXpress activation"
  exit 0
fi

# ----------------------------------------
# 2) تأكد إن البلاجن موجود
# ----------------------------------------
if [ ! -f "$WP_PATH/wp-content/plugins/$PLUGIN_FILE" ]; then
  echo "❌ uiXpress plugin files not found – skipping"
  exit 0
fi

# ----------------------------------------
# 3) لو متفعلش → فعّله
# ----------------------------------------
if wp plugin is-active "$PLUGIN_SLUG" --allow-root --path="$WP_PATH"; then
  echo "ℹ uiXpress already active"
else
  echo "▶ Activating uiXpress"
  wp plugin activate "$PLUGIN_SLUG" \
    --allow-root \
    --path="$WP_PATH"
fi

echo "✅ uiXpress activation finished"
