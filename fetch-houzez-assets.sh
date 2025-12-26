#!/bin/bash
set -e

echo "▶ Fetching Houzez assets from DigitalOcean Spaces"

# --------------------------------------------
# Config
# --------------------------------------------
WP_PATH="/var/www/html"
THEMES_PATH="$WP_PATH/wp-content/themes"
PLUGINS_PATH="$WP_PATH/wp-content/plugins"
MU_PLUGINS_PATH="$WP_PATH/wp-content/mu-plugins"

TMP_DIR="/tmp/coonex-assets"
TMP_THEME="$TMP_DIR/theme"
TMP_PLUGINS="$TMP_DIR/plugins"

mkdir -p "$TMP_THEME" "$TMP_PLUGINS" "$MU_PLUGINS_PATH"

# --------------------------------------------
# 1) Download & install Houzez theme
# --------------------------------------------
echo "▶ Downloading Houzez theme"

aws s3 cp \
  s3://$SPACES_BUCKET/assets/themes/houzez.zip \
  "$TMP_THEME/houzez.zip" \
  --endpoint-url="$SPACES_ENDPOINT"

echo "▶ Installing Houzez theme"
unzip -oq "$TMP_THEME/houzez.zip" -d "$THEMES_PATH"

# --------------------------------------------
# 2) Download & install Houzez plugins
# --------------------------------------------
echo "▶ Downloading Houzez plugins"

aws s3 sync \
  s3://$SPACES_BUCKET/assets/plugins \
  "$TMP_PLUGINS" \
  --endpoint-url="$SPACES_ENDPOINT" \
  --exclude ".DS_Store"

echo "▶ Installing Houzez plugins"
for zip in "$TMP_PLUGINS"/*.zip; do
  echo "  → $(basename "$zip")"
  unzip -oq "$zip" -d "$PLUGINS_PATH"
done

# --------------------------------------------
# 3) Install MU plugins
# --------------------------------------------
echo "▶ Installing MU plugins"

aws s3 sync \
  s3://$SPACES_BUCKET/assets/mu-plugins \
  "$MU_PLUGINS_PATH" \
  --endpoint-url="$SPACES_ENDPOINT" \
  --exclude ".DS_Store"

# --------------------------------------------
# 4) Permissions cleanup
# --------------------------------------------
chown -R www-data:www-data "$WP_PATH"

echo "✅ Houzez assets installed successfully"
