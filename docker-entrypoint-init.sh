#!/usr/bin/env bash
set -euo pipefail

echo "▶ Starting Coonex WordPress Init Script"

WP_PATH="/var/www/html"

# --------------------------------------------------
# Helper: wait for database
# --------------------------------------------------
wait_for_db() {
  local host="${WORDPRESS_DB_HOST:-mysql}"
  local user="${WORDPRESS_DB_USER:-root}"
  local pass="${WORDPRESS_DB_PASSWORD:-}"
  local db="${WORDPRESS_DB_NAME:-wordpress}"

  echo "⏳ Waiting for database (host=$host, db=$db)..."

  for i in {1..60}; do
    if mariadb-admin ping -h"$host" -u"$user" -p"$pass" --silent >/dev/null 2>&1; then
      echo "✅ Database is reachable"
      return 0
    fi
    sleep 1
  done

  echo "❌ Database not reachable after 60s"
  exit 1
}

# --------------------------------------------------
# Ensure WordPress core exists
# --------------------------------------------------
if [ ! -f "$WP_PATH/wp-settings.php" ]; then
  echo "▶ Copying WordPress core to $WP_PATH"
  cp -a /usr/src/wordpress/. "$WP_PATH"/
  chown -R www-data:www-data "$WP_PATH"
fi

cd "$WP_PATH"

# --------------------------------------------------
# Ensure wp-config.php exists
# --------------------------------------------------
if [ ! -f "$WP_PATH/wp-config.php" ]; then
  echo "▶ Creating wp-config.php"
  cp wp-config-sample.php wp-config.php
fi

# --------------------------------------------------
# Wait for DB before wp-cli operations
# --------------------------------------------------
wait_for_db

# --------------------------------------------------
# Detect WP_URL
# --------------------------------------------------
WP_URL="${WP_URL:-}"

if [ -z "$WP_URL" ]; then
  if [ -n "${COOLIFY_URL:-}" ]; then
    WP_URL="$COOLIFY_URL"
  elif [ -n "${COOLIFY_FQDN:-}" ]; then
    WP_URL="https://${COOLIFY_FQDN}"
  fi
fi

if [ -z "$WP_URL" ]; then
  echo "⚠️ WP_URL is empty. Site URL will not be forced."
else
  echo "✅ WP_URL=$WP_URL"
fi

# --------------------------------------------------
# Install WordPress if not installed
# --------------------------------------------------
if ! wp core is-installed --allow-root >/dev/null 2>&1; then
  echo "▶ WordPress not installed. Running wp core install..."

  ADMIN_USER="${WP_ADMIN_USER:-admin}"
  ADMIN_PASS="${WP_ADMIN_PASSWORD:-Admin@123456}"
  ADMIN_EMAIL="${WP_ADMIN_EMAIL:-admin@example.com}"
  SITE_TITLE="${WP_SITE_TITLE:-Coonex CMS}"

  if [ -z "$WP_URL" ]; then
    echo "❌ Cannot install WordPress without WP_URL"
    exit 1
  fi

  wp core install \
    --url="$WP_URL" \
    --title="$SITE_TITLE" \
    --admin_user="$ADMIN_USER" \
    --admin_password="$ADMIN_PASS" \
    --admin_email="$ADMIN_EMAIL" \
    --skip-email \
    --allow-root

  echo "✅ WordPress installed successfully"
else
  echo "ℹ WordPress already installed"
fi

# --------------------------------------------------
# Enforce home & siteurl in database
# --------------------------------------------------
if [ -n "$WP_URL" ]; then
  echo "▶ Enforcing home & siteurl in database"
  wp option update home "$WP_URL" --allow-root || true
  wp option update siteurl "$WP_URL" --allow-root || true
fi

# --------------------------------------------------
# Ensure admin user exists (idempotent)
# --------------------------------------------------
if [ -n "${WP_ADMIN_EMAIL:-}" ]; then
  ADMIN_USER="${WP_ADMIN_USER:-admin}"
  ADMIN_PASS="${WP_ADMIN_PASSWORD:-Admin@123456}"
  ADMIN_EMAIL="${WP_ADMIN_EMAIL}"

  if ! wp user get "$ADMIN_USER" --allow-root >/dev/null 2>&1; then
    echo "▶ Creating admin user: $ADMIN_USER"
    wp user create "$ADMIN_USER" "$ADMIN_EMAIL" \
      --role=administrator \
      --user_pass="$ADMIN_PASS" \
      --allow-root || true
  else
    echo "ℹ Admin user already exists"
  fi
fi

echo "🚀 Starting Apache"
exec docker-entrypoint.sh "$@"
