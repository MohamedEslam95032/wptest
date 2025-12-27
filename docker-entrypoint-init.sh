#!/bin/bash
set -e

WP_PATH="/var/www/html"
WP_CONFIG="$WP_PATH/wp-config.php"

echo "▶ Starting Coonex WordPress Init Script"

# --------------------------------------------------
# 1) Wait for DB
# --------------------------------------------------
until mariadb -h"$WORDPRESS_DB_HOST" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
  echo "⏳ Waiting for database..."
  sleep 2
done
echo "✅ Database is reachable"

# --------------------------------------------------
# 2) Ensure DB exists
# --------------------------------------------------
mariadb -h"$WORDPRESS_DB_HOST" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" \
  -e "CREATE DATABASE IF NOT EXISTS \`${WORDPRESS_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# --------------------------------------------------
# 3) Copy WordPress core
# --------------------------------------------------
if [ ! -f "$WP_PATH/wp-load.php" ]; then
  cp -a /usr/src/wordpress/. "$WP_PATH/"
  chown -R www-data:www-data "$WP_PATH"
fi

# --------------------------------------------------
# 4) wp-config
# --------------------------------------------------
if [ ! -f "$WP_CONFIG" ]; then
  wp config create \
    --path="$WP_PATH" \
    --dbname="$WORDPRESS_DB_NAME" \
    --dbuser="$WORDPRESS_DB_USER" \
    --dbpass="$WORDPRESS_DB_PASSWORD" \
    --dbhost="$WORDPRESS_DB_HOST" \
    --skip-check \
    --allow-root
fi

# --------------------------------------------------
# 5) Install WP
# --------------------------------------------------
if ! wp core is-installed --allow-root --path="$WP_PATH"; then
  wp core install \
    --path="$WP_PATH" \
    --url="$WP_URL" \
    --title="Coonex CMS" \
    --admin_user="$WP_ADMIN_USER" \
    --admin_password="$WP_ADMIN_PASS" \
    --admin_email="$WP_ADMIN_EMAIL" \
    --skip-email \
    --allow-root
fi

# --------------------------------------------------
# 6) Activate uiXpress SAFELY
# --------------------------------------------------
if wp plugin is-installed xpress/uixpress.php --allow-root --path="$WP_PATH"; then
  if ! wp plugin is-active xpress/uixpress.php --allow-root --path="$WP_PATH"; then
    wp plugin activate xpress/uixpress.php --allow-root --path="$WP_PATH"
  fi
fi

# --------------------------------------------------
# 7) Permissions + Start
# --------------------------------------------------
chown -R www-data:www-data "$WP_PATH"
exec apache2-foreground
