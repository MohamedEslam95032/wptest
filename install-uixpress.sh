#!/bin/bash
set -e

echo "▶ Activating uiXpress via WP-CLI (SAFE)"

WP_PATH="/var/www/html"
PLUGIN_PATH="xpress/uixpress.php"
ADMIN_USER="${WP_ADMIN_USER:-admin}"

# ----------------------------------------
# 1) تأكد إن ووردبريس متسطب
# ----------------------------------------
if ! wp core is-installed --allow-root --path="$WP_PATH"; then
  echo "❌ WordPress not installed yet – aborting"
  exit 0
fi

# ----------------------------------------
# 2) تأكد إن البلاجن موجود
# ----------------------------------------
if [ ! -f "$WP_PATH/wp-content/plugins/$PLUGIN_PATH" ]; then
  echo "❌ uiXpress files not found – aborting"
  exit 0
fi

# ----------------------------------------
# 3) تأكد إن اليوزر موجود
# ----------------------------------------
if ! wp user get "$ADMIN_USER" --allow-root --path="$WP_PATH" >/dev/null 2>&1; then
  echo "❌ Admin user not found – aborting"
  exit 0
fi

# ----------------------------------------
# 4) لو متفعلش → فعّله (ده المهم)
# ----------------------------------------
if wp plugin is-active "$PLUGIN_PATH" --allow-root --path="$WP_PATH"; then
  echo "ℹ uiXpress already active"
else
  echo "▶ Activating uiXpress as user: $ADMIN_USER"
  wp plugin activate "$PLUGIN_PATH" \
    --allow-root \
    --path="$WP_PATH" \
    --user="$ADMIN_USER"
fi

echo "✅ uiXpress activation finished"
