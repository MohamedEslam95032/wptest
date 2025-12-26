#!/bin/bash
set -e

echo "▶ Fetching Houzez assets from DigitalOcean Spaces"

# --------------------------------------------
# Paths & Flags
# --------------------------------------------
WP_PATH="/var/www/html"
THEMES_PATH="$WP_PATH/wp-content/themes"
PLUGINS_PATH="$WP_PATH/wp-content/plugins"
MU_PLUGINS_PATH="$WP_PATH/wp-content/mu-plugins"

TMP_DIR="/tmp/coonex-assets"
TMP_THEME="$TMP_DIR/theme"
TMP_PLUGINS="$TMP_DIR/plugins"

FLAG_FILE="$WP_PATH/.houzez_assets_installed"

# --------------------------------------------
# Guard: run once only
# --------------------------------------------
if [ -f "$FLAG_FILE" ]; then
  echo "ℹ Houzez assets already installed. Skipping."
  exit 0
fi

# --------------------------------------------
# Validate ENV
# --------------------------------------------
if [ -z "$SPACES_BUCKET" ] || [ -z "$SPACES_ENDPOINT" ]; then
  echo "❌ Missing SPACES_BUCKET or SPACES_ENDPOINT"
  exit 1
fi

if [ -z "$AWS_ACCESS_KEY_ID" ] || [ -z "$AWS_SECRET_ACCESS_KEY" ]; then
  echo "❌ Missing AWS credentials (AWS_ACCESS_KEY_ID / AWS_SECRET_ACCESS_KEY)"
  exit 1
fi

# --------------------------------------------
# Prepare directories
# --------------------------------------------
mkdir -p \
  "$THEMES_PATH" \
  "$PLUGINS_PATH" \
  "$MU_PLUGINS_PATH" \
  "$TMP_THEME" \
  "$TMP_PLUGINS"

# --------------------------------------------
# 1) Houzez Theme
# --------------------------------------------
echo "▶ Downloading Houzez theme"

aws s3 cp \
  s3://$SPACES_BUCKET/assets/themes/houzez.zip \
  "$TMP_THEME/houzez.zip" \
  --endpoint-url="$SPACES_ENDPOINT" \
  --no-progress

echo "▶ Installing Houzez theme"
unzip -oq "$TMP_THEME/houzez.zip" -d "$THEMES_PATH"

# --------------------------------------------
# 2) Houzez Plugins
# --------------------------------------------
echo "▶ Downloading Houzez plugins"

aws s3 sync \
  s3://$SPACES_BUCKET/assets/plugins \
  "$TMP_PLUGINS" \
  --endpoint-url="$SPACES_ENDPOINT" \
  --exclude ".DS_Store" \
  --no-progress

echo "▶ Installing Houzez plugins"
for zip in "$TMP_PLUGINS"/*.zip; do
  [ -f "$zip" ] || continue
  echo "  → $(basename "$zip")"
  unzip -oq "$zip" -d "$PLUGINS_PATH"
done

# --------------------------------------------
# 3) MU Plugins (direct copy)
# --------------------------------------------
echo "▶ Installing MU plugins"

aws s3 sync \
  s3://$SPACES_BUCKET/assets/mu-plugins \
  "$MU_PLUGINS_PATH" \
  --endpoint-url="$SPACES_ENDPOINT" \
  --exclude ".DS_Store" \
  --no-progress

# --------------------------------------------
# Permissions
# --------------------------------------------
chown -R www-data:www-data "$WP_PATH"

# --------------------------------------------
# Finalize
# --------------------------------------------
touch "$FLAG_FILE"

echo "✅ Houzez assets installed successfully"
