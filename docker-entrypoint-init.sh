#!/bin/bash
set -e

echo "▶ Starting Coonex WordPress Init Script"

# --------------------------------------------------
# 1️⃣ Map WordPress DB ENV variables
# --------------------------------------------------
DB_HOST="${WORDPRESS_DB_HOST}"
DB_NAME="${WORDPRESS_DB_NAME}"
DB_USER="${WORDPRESS_DB_USER}"
DB_PASSWORD="${WORDPRESS_DB_PASSWORD}"

# --------------------------------------------------
# 2️⃣ Validate ENV
# --------------------------------------------------
if [ -z "$DB_HOST" ] || [ -z "$DB_NAME" ] || [ -z "$DB_USER" ] || [ -z "$DB_PASSWORD" ]; then
  echo "❌ Database environment variables are missing"
  exit 1
fi

if [ -z "$WP_URL" ]; then
  echo "❌ WP_URL is not set"
  exit 1
fi

echo "ℹ Using DB_HOST=$DB_HOST"
echo "ℹ Using DB_NAME=$DB_NAME"
echo "ℹ Using DB_USER=$DB_USER"

# --------------------------------------------------
# 3️⃣ Wait for Database
# --------------------------------------------------
until mysqladmin ping \
  -h"$DB_HOST" \
  -u"$DB_USER" \
  -p"$DB_PASSWORD" \
  --silent; do
  echo "⏳ Waiting for database..."
  sleep 3
done

echo "✅ Database is reachable"

# --------------------------------------------------
# 4️⃣ Ensure WordPress core exists
# --------------------------------------------------
if [ ! -f wp-load.php ]; then
  echo "▶ Downloading WordPress core"
  wp core download --allow-root
else
  echo "ℹ WordPress core already exists"
fi

# --------------------------------------------------
# 5️⃣ Create wp-config.php (ONLY if missing)
# --------------------------------------------------
if [ ! -f wp-config.php ]; then
  echo "▶ Creating wp-config.php"

  wp config create \
    --dbname="$DB_NAME" \
    --dbuser="$DB_USER" \
    --dbpass="$DB_PASSWORD" \
    --dbhost="$DB_HOST" \
    --skip-check \
    --allow-root

  echo "✅ wp-config.php created"
else
  echo "ℹ wp-config.php already exists"
fi

# --------------------------------------------------
# 6️⃣ Install WordPress (ONLY ONCE)
# --------------------------------------------------
if ! wp core is-installed --allow-root; then
  echo "▶ Installing WordPress"

  wp core install \
    --url="$WP_URL" \
    --title="Coonex CMS" \
    --admin_user="${WP_ADMIN_USER:-admin}" \
    --admin_password="${WP_ADMIN_PASS:-Admin@123}" \
    --admin_email="${WP_ADMIN_EMAIL:-admin@coonex.io}" \
    --skip-email \
    --allow-root

  echo "✅ WordPress installed"
else
  echo "ℹ WordPress already installed"
fi

# --------------------------------------------------
# 7️⃣ Enforce siteurl & home
# --------------------------------------------------
wp option update siteurl "$WP_URL" --allow-root
wp option update home "$WP_URL" --allow-root

# --------------------------------------------------
# 8️⃣ Permissions
# --------------------------------------------------
chown -R www-data:www-data /var/www/html || true

echo "🚀 Coonex WordPress Init Completed"
