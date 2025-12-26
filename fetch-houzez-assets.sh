#!/bin/bash
set -e

WP_PATH="/var/www/html"
TMP="/tmp/coonex-assets"

echo "▶ Fetching Houzez assets from Spaces"

mkdir -p "$TMP"

export AWS_ACCESS_KEY_ID="$SPACES_KEY"
export AWS_SECRET_ACCESS_KEY="$SPACES_SECRET"
export AWS_DEFAULT_REGION="us-east-1"

# -------------------------------
# Download Houzez theme
# -------------------------------
if [ ! -d "$WP_PATH/wp-content/themes/houzez" ]; then
  echo "▶ Downloading Houzez theme"
  aws s3 cp \
    s3://$SPACES_BUCKET/themes/houzez.zip \
    "$TMP/houzez.zip" \
    --endpoint-url="$SPACES_ENDPOINT"

  unzip -o "$TMP/houzez.zip" -d "$WP_PATH/wp-content/themes/"
fi

# -------------------------------
# Download plugins
# -------------------------------
echo "▶ Downloading plugins"
aws s3 cp \
  s3://$SPACES_BUCKET/plugins/ \
  "$TMP/plugins/" \
  --recursive \
  --endpoint-url="$SPACES_ENDPOINT"

unzip -o "$TMP/plugins/*.zip" -d "$WP_PATH/wp-content/plugins/"

echo "✅ Assets fetched successfully"
