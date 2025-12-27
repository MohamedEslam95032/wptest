#!/bin/bash
set -e

echo "▶ Starting Coonex WordPress Init Script"

WP_PATH="/var/www/html"
WP_CONFIG="$WP_PATH/wp-config.php"

# --------------------------------------------------
# 1) Wait for Database
# --------------------------------------------------
echo "⏳ Waiting for database..."

ATTEMPTS=0
MAX_ATTEMPTS=30

until mariadb \
  -h"${WORDPRESS_DB_HOST}" \
  -u"${WORDPRESS_DB_USER}" \
  -p"${WORDPRESS_DB_PASSWORD}" \
  -e "SELECT 1" >/dev/null 2>&1; do

  ATTEMPTS=$((ATTEMPTS+1))
  echo "⏳ DB not ready ($ATTEMPTS/$MAX_ATTEMPTS)"

  if [ "$ATTEMPTS" -ge "$MAX_ATTEMPTS" ]; then
    echo "❌ Database not reachable"
    exit 1
  fi

  sleep 2
done

echo "✅ Database is reachable"

# --------------------------------------------------
# 2) Ensure Database Exists
# --------------------------------------------------
echo "▶ Ensuring database exists..."

mariadb \
  -h"${WORDPRESS_DB_HOST}" \
  -u"${WORDPRESS_DB_USER}" \
  -p"${WORDPRESS_DB_PASSWORD}" \
  -e "CREATE DATABASE IF NOT EXISTS \`${WORDPRESS_DB_NAME}\`
      DEFAULT CHARACTER SET utf8mb4
      COLLATE utf8mb4_unicode_ci;"

# --------------------------------------------------
# 3) Copy WordPress Core (if not exists)
# --------------------------------------------------
if [ ! -f "$WP_PATH/wp-load.php" ]; then
  echo "▶ Copying WordPress core"
  cp -a /usr/src/wordpress/. "$WP_PATH/"
  chown -R www-data:www-data "$WP_PATH"
else
  echo "ℹ WordPress core already exists"
fi

# --------------------------------------------------
# 4) Create wp-config.php (if not exists)
# --------------------------------------------------
if [ ! -f "$WP_CONFIG" ]; then
  echo "▶ Creating wp-config.php"

  wp config create \
    --path="$WP_PATH" \
    --dbname="${WORDPRESS_DB_NAME}" \
    --dbuser="${WORDPRESS_DB_USER}" \
    --dbpass="${WORDPRESS_DB_PASSWORD}" \
    --dbhost="${WORDPRESS_DB_HOST}" \
    --skip-check \
    --allow-root
else
  echo "ℹ wp-config.php already exists"
fi

# --------------------------------------------------
# 5) Install WordPress (once only)
# --------------------------------------------------
if ! wp core is-installed --allow-root --path="$WP_PATH"; then
  echo "▶ Installing WordPress"

  wp core install \
    --path="$WP_PATH" \
    --url="${WP_URL}" \
    --title="${WP_TITLE:-Coonex}" \
    --admin_user="${WP_ADMIN_USER:-admin}" \
    --admin_password="${WP_ADMIN_PASS:-Admin@123}" \
    --admin_email="${WP_ADMIN_EMAIL:-admin@coonex.io}" \
    --skip-email \
    --allow-root
else
  echo "ℹ WordPress already installed"
fi

# --------------------------------------------------
# 6) Ensure admin user from ENV exists
# --------------------------------------------------
if [ -n "$WP_ADMIN_USER" ] && [ -n "$WP_ADMIN_PASS" ] && [ -n "$WP_ADMIN_EMAIL" ]; then
  echo "▶ Ensuring admin user from ENV exists"

  if ! wp user get "$WP_ADMIN_USER" --allow-root --path="$WP_PATH" >/dev/null 2>&1; then
    wp user create \
      "$WP_ADMIN_USER" \
      "$WP_ADMIN_EMAIL" \
      --user_pass="$WP_ADMIN_PASS" \
      --role="${WP_ADMIN_ROLE:-administrator}" \
      --allow-root \
      --path="$WP_PATH"
  else
    echo "ℹ Admin user already exists"
  fi
fi

# --------------------------------------------------
# 7) Activate uiXpress (CLEAN – like manual install)
# --------------------------------------------------
echo "▶ Checking uiXpress plugin"

if wp plugin is-installed xpress/uixpress.php --allow-root --path="$WP_PATH"; then
  if ! wp plugin is-active xpress/uixpress.php --allow-root --path="$WP_PATH"; then
    echo "▶ Activating uiXpress via WP-CLI"
    wp plugin activate xpress/uixpress.php --allow-root --path="$WP_PATH"
  else
    echo "ℹ uiXpress already active"
  fi
else
  echo "⚠ uiXpress plugin not found"
fi

# --------------------------------------------------
# 8) Permissions
# --------------------------------------------------
chown -R www-data:www-data "$WP_PATH"

# --------------------------------------------------
# 9) Start Apache
# --------------------------------------------------
echo "🚀 Starting Apache"
exec apache2-foreground
