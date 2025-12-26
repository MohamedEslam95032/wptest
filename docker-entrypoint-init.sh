#!/bin/bash
set -e

echo "▶ Starting Coonex WordPress Init Script"

# Wait for DB
echo "⏳ Waiting for database..."
until mysqladmin ping -h"$WORDPRESS_DB_HOST" --silent; do
  sleep 2
done
echo "✅ Database is reachable"

cd /var/www/html

# Install WordPress if not installed
if ! wp core is-installed --allow-root; then
  echo "▶ Installing WordPress"

  wp core install \
    --url="${WP_URL}" \
    --title="Coonex CMS" \
    --admin_user="${WP_ADMIN_USER}" \
    --admin_password="${WP_ADMIN_PASSWORD}" \
    --admin_email="${WP_ADMIN_EMAIL}" \
    --skip-email \
    --allow-root
else
  echo "ℹ WordPress already installed"
fi

# Ensure plugins directory exists
mkdir -p wp-content/plugins/coonex-jwt-sso

# Activate SSO plugin
wp plugin activate coonex-jwt-sso --allow-root || true

echo "🚀 Starting Apache"
exec "$@"
