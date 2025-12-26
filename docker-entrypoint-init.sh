#!/bin/bash
set -e

echo "▶ Starting Coonex WordPress Init Script"

# -----------------------------
# 1️⃣ Validate ENV variables
# -----------------------------
if [ -z "$DB_HOST" ] || [ -z "$DB_NAME" ] || [ -z "$DB_USER" ] || [ -z "$DB_PASSWORD" ]; then
  echo "❌ Database environment variables are missing"
  exit 1
fi

if [ -z "$WP_URL" ]; then
  echo "❌ WP_URL is not set"
  exit 1
fi

# -----------------------------
# 2️⃣ Wait for Database (REAL check)
# -----------------------------
until mysqladmin ping \
  -h"$DB_HOST" \
  -u"$DB_USER" \
  -p"$DB_PASSWORD" \
  --silent; do
  echo "⏳ Waiting for database..."
  sleep 3
done

echo "✅ Database is reachable"

# -----------------------------
# 3️⃣ Create wp-config.php if missing
# -----------------------------
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

# -----------------------------
# 4️⃣ Install WordPress (ONLY ONCE)
# -----------------------------
if ! wp core is-installed --allow-root; then
  echo "▶ Installing WordPress"

  wp core install \
    --url="$WP_URL" \
    --title="Coonex CMS" \
    --admin_user="${WP_ADMIN_USER:-admin}" \
    --admin_password="${WP_ADMIN_PASSWORD:-Admin@123}" \
    --admin_email="${WP_ADMIN_EMAIL:-admin@coonex.io}" \
    --skip-email \
    --allow-root

  echo "✅ WordPress installed"
else
  echo "ℹ WordPress already installed"
fi

# -----------------------------
# 5️⃣ Force siteurl & home (SAFE)
# -----------------------------
echo "▶ Enforcing siteurl & home"

wp option update siteurl "$WP_URL" --allow-root
wp option update home "$WP_URL" --allow-root

# -----------------------------
# 6️⃣ Permissions fix (optional but recommended)
# -----------------------------
chown -R www-data:www-data /var/www/html || true

echo "🚀 Coonex WordPress Init Completed"
