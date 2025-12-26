FROM wordpress:php8.2-apache

# -----------------------------
# System packages
# -----------------------------
RUN apt-get update && apt-get install -y \
    curl unzip less mariadb-client \
 && rm -rf /var/lib/apt/lists/*

# -----------------------------
# Install WP-CLI
# -----------------------------
RUN curl -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
 && chmod +x /usr/local/bin/wp \
 && wp --info

# -----------------------------
# Apache "ServerName" (remove warning - optional)
# -----------------------------
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# -----------------------------
# PHP defaults
# -----------------------------
RUN { \
      echo "upload_max_filesize = 64M"; \
      echo "post_max_size = 64M"; \
      echo "memory_limit = 512M"; \
      echo "max_execution_time = 120"; \
    } > /usr/local/etc/php/conf.d/coonex.ini

# -----------------------------
# Copy assets (plugins + MU plugins)
# -----------------------------
COPY assets/plugins/ /usr/src/wordpress/wp-content/plugins/
COPY assets/mu-plugins/ /usr/src/wordpress/wp-content/mu-plugins/

# -----------------------------
# Entrypoint
# -----------------------------
COPY docker-entrypoint-init.sh /usr/local/bin/docker-entrypoint-init.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-init.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint-init.sh"]
