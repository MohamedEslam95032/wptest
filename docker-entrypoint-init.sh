#!/bin/bash
set -e

WP_PATH="/var/www/html"
WP_CONFIG="$WP_PATH/wp-config.php"

echo "▶ Starting Coonex WordPress Init Script"

# -----------------------------
# 0) Validate ENV
# -----------------------------
: "${WORDPRESS_DB_HOST:?Missing WORDPRESS_DB_HOST}"
: "${WORDPRESS_DB_NAME:?Missing WORDPRESS_DB_NAME}"
: "${WORDPRESS_DB_USER:?Missing WORDPRESS_DB_USER}"
: "${WORDPRESS_DB_PASSWORD:?Missing WORDPRESS_DB_PASSWORD}"
: "${WP_URL:?Missing WP_URL}"

cd "$WP_PATH"

echo "ℹ DB_HOST=${WORDPRESS_DB_HOST}"
echo "ℹ DB_NAME=${WORDPRESS_DB_NAME}"
echo "ℹ DB_USER=${WORDPRESS_DB_USER}"

# -----------------------------
# 1) Wait for DB
# -----------------------------
echo "⏳ Waiting for database..."
ATTEMPTS=0
MAX_ATTEMPTS=40

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

# -----------------------------
# 2) Ensure DB exists
# -----------------------------
mariadb \
  -h"${WORDPRESS_DB_HOST}" \
  -u"${WORDPRESS_DB_USER}" \
  -p"${WORDPRESS_DB_PASSWORD}" \
  -e "CREATE DATABASE IF NOT EXISTS \`${WORDPRESS_DB_NAME}\`
      DEFAULT CHARACTER SET utf8mb4
      COLLATE utf8mb4_unicode_ci;"

# -----------------------------
# 3) Ensure WP core exists
# -----------------------------
if [ ! -f "$WP_PATH/wp-load.php" ]; then
  echo "▶ Copying WordPress core"
  cp -a /usr/src/wordpress/. "$WP_PATH/"
  chown -R www-data:www-data "$WP_PATH"
else
  echo "ℹ WordPress core already exists"
fi

# -----------------------------
# 4) Create wp-config.php
# -----------------------------
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

# -----------------------------
# 5) Inject Proxy + WP_URL
# -----------------------------
if ! grep -q "Coonex URL & Proxy Detection" "$WP_CONFIG"; then
  sed -i "/require_once ABSPATH . 'wp-settings.php';/i \
/** ==============================\\n\
 * Coonex URL & Proxy Detection\\n\
 * ============================== */\\n\
if (getenv('WP_URL')) {\\n\
  define('WP_HOME', getenv('WP_URL'));\\n\
  define('WP_SITEURL', getenv('WP_URL'));\\n\
}\\n\\n\
if (!empty(\$_SERVER['HTTP_X_FORWARDED_PROTO'])) {\\n\
  \$_SERVER['HTTPS'] = \$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'on' : 'off';\\n\
}\\n\
" "$WP_CONFIG"
fi

# -----------------------------
# 6) Install WordPress
# -----------------------------
if ! wp core is-installed --allow-root --path="$WP_PATH"; then
  echo "▶ Installing WordPress"
  wp core install \
    --path="$WP_PATH" \
    --url="$WP_URL" \
    --title="${WP_TITLE:-Coonex}" \
    --admin_user="${WP_ADMIN_USER:-admin}" \
    --admin_password="${WP_ADMIN_PASS:-Admin@123}" \
    --admin_email="${WP_ADMIN_EMAIL:-admin@coonex.io}" \
    --skip-email \
    --allow-root
else
  echo "ℹ WordPress already installed"
fi

# -----------------------------
# 7) Fix siteurl/home
# -----------------------------
wp option update siteurl "$WP_URL" --allow-root --path="$WP_PATH"
wp option update home "$WP_URL" --allow-root --path="$WP_PATH"

# -----------------------------
# 8) Permissions
# -----------------------------
chown -R www-data:www-data "$WP_PATH"

# -----------------------------
# 9) Start Apache
# -----------------------------
echo "🚀 Starting Apache"
exec apache2-foreground
