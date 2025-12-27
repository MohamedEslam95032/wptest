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
# 3) Copy WordPress Core
# --------------------------------------------------
if [ ! -f "$WP_PATH/wp-load.php" ]; then
  echo "▶ Copying WordPress core"
  cp -a /usr/src/wordpress/. "$WP_PATH/"
  chown -R www-data:www-data "$WP_PATH"
else
  echo "ℹ WordPress core already exists"
fi

# --------------------------------------------------
# 4) Create wp-config.php
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
# 5) Inject Coonex URL + Proxy Fix
# --------------------------------------------------
if ! grep -q "Coonex URL & Proxy Detection" "$WP_CONFIG"; then
  echo "▶ Injecting Coonex URL & Proxy Detection"

  sed -i "/require_once ABSPATH . 'wp-settings.php';/i \
/** ==============================\\n\
 * Coonex URL & Proxy Detection (NO FORCE HTTPS)\\n\
 * ============================== */\\n\
if (getenv('WP_URL')) {\\n\
    define('WP_HOME', getenv('WP_URL'));\\n\
    define('WP_SITEURL', getenv('WP_URL'));\\n\
}\\n\\n\
if (!empty(\$_SERVER['HTTP_X_FORWARDED_PROTO'])) {\\n\
    \$_SERVER['HTTPS'] = \$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'on' : 'off';\\n\
}\\n\
" "$WP_CONFIG"
else
  echo "ℹ Proxy config already present"
fi

# --------------------------------------------------
# 6) Install WordPress
# --------------------------------------------------
if ! wp core is-installed --allow-root --path="$WP_PATH"; then
  echo "▶ Installing WordPress"

  wp core install \
    --
