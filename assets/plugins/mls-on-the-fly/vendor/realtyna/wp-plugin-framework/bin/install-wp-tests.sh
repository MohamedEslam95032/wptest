#!/usr/bin/env bash

# Load environment variables from .env file
if [ ! -f .env ]; then
    echo ".env file not found"
    exit 1
fi

export $(grep -v '^#' .env | xargs)

DB_NAME=${DB_NAME}
DB_USER=${DB_USER}
DB_PASS=${DB_PASSWORD}
DB_HOST=${DB_HOST}
WP_VERSION=${WP_VERSION}

WP_CORE_DIR=~/test-area/wordpress/
WP_TESTS_DIR=~/test-area/wordpress-tests-lib

set -ex

# Download WordPress core if it doesn't already exist
if [ ! -d $WP_CORE_DIR ]; then
    mkdir -p $WP_CORE_DIR
    wget -nv -O /tmp/wordpress.tar.gz https://wordpress.org/latest.tar.gz
    tar --strip-components=1 -zxmf /tmp/wordpress.tar.gz -C $WP_CORE_DIR
fi

# Download WordPress tests if they don't already exist
if [ ! -d $WP_TESTS_DIR ]; then
    mkdir -p $WP_TESTS_DIR
    svn co --quiet https://develop.svn.wordpress.org/trunk/tests/phpunit/includes/ $WP_TESTS_DIR/includes
    svn co --quiet https://develop.svn.wordpress.org/trunk/tests/phpunit/data/ $WP_TESTS_DIR/data
fi

# Create wp-tests-config.php if it doesn't exist
if [ ! -f $WP_TESTS_DIR/wp-tests-config.php ]; then
    if [ ! -f $WP_TESTS_DIR/wp-tests-config-sample.php ]; then
        cat <<EOL > $WP_TESTS_DIR/wp-tests-config.php
<?php

define( 'ABSPATH', dirname( __FILE__ ) . '/' );

define( 'DB_NAME', '${DB_NAME}' );
define( 'DB_USER', '${DB_USER}' );
define( 'DB_PASSWORD', '${DB_PASS}' );
define( 'DB_HOST', '${DB_HOST}' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

define( 'WP_TESTS_TABLE_PREFIX', 'wptests_' );

define( 'WP_DEBUG', true );

define( 'WP_LANG_DIR', ABSPATH . 'wp-content/languages' );

$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['HTTP_HOST'] = 'example.org';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
define( 'WP_USE_THEMES', false );
define( 'DISABLE_WP_CRON', true );
define( 'DISALLOW_FILE_MODS', true );
EOL
    else
        cp $WP_TESTS_DIR/wp-tests-config-sample.php $WP_TESTS_DIR/wp-tests-config.php
        sed -i "s/youremptytestdbnamehere/$DB_NAME/" $WP_TESTS_DIR/wp-tests-config.php
        sed -i "s/yourusernamehere/$DB_USER/" $WP_TESTS_DIR/wp-tests-config.php
        sed -i "s/yourpasswordhere/$DB_PASS/" $WP_TESTS_DIR/wp-tests-config.php
        sed -i "s|localhost|$DB_HOST|" $WP_TESTS_DIR/wp-tests-config.php
    fi
fi
