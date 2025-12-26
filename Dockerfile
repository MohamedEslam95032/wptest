FROM wordpress:php8.2-apache

# --------------------------------------------------
# System packages
# --------------------------------------------------
RUN apt-get update && apt-get install -y \
    curl unzip less mariadb-client \
 && rm -rf /var/lib/apt/lists/*

# --------------------------------------------------
# Install WP-CLI (IMPORTANT)
# --------------------------------------------------
RUN curl -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
 && chmod +x /usr/local/bin/wp \
 && wp --info

# --------------------------------------------------
# PHP DEFAULTS
# --------------------------------------------------
RUN { \
      echo "upload_max_filesize = 20M"; \
      echo "post_max_size = 25M"; \
      echo "memory_limit = 256M"; \
    } > /usr/local/etc/php/conf.d/coonex.ini

# --------------------------------------------------
# Copy plugins
# --------------------------------------------------
COPY assets/plugins/ /usr/src/wordpress/wp-content/plugins/
COPY assets/mu-plugins/ /usr/src/wordpress/wp-content/mu-plugins/

# --------------------------------------------------
# Entrypoint
# --------------------------------------------------
COPY docker-entrypoint-init.sh /usr/local/bin/docker-entrypoint-init.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-init.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint-init.sh"]
