#!/bin/bash
set -e

echo "▶ Starting Coonex WordPress Init Script"

# Wait for DB
until wp db check --allow-root >/dev/null 2>&1; do
  echo "⏳ Waiting for database..."
  sleep 3
done

# Install WordPress ONLY if not installed
if ! wp core is-installed --allow-root; then
  echo "▶ Installing WordPress"
  wp core install \
    --url="$WP_URL" \
    --title="Coonex CMS" \
    --admin_user="$WP_ADMIN_USER" \
    --admin_password="$WP_ADMIN_PASSWORD" \
    --admin_email="$WP_ADMIN_EMAIL" \
    --skip-email \
    --allow-root
else
  echo "ℹ WordPress already installed"
fi

# Ensure siteurl & home (once)
wp option update siteurl "$WP_URL" --allow-root
wp option update home "$WP_URL" --allow-root

echo "✅ WordPress Init Completed"
