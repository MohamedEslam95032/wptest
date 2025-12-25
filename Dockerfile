FROM wordpress:php8.2-apache

# --------------------------------------------------
# System packages
# --------------------------------------------------
RUN apt-get update && apt-get install -y \
    curl unzip less mariadb-client \
 && rm -rf /var/lib/apt/lists/*

# --------------------------------------------------
# PHP defaults
# --------------------------------------------------
RUN { \
      echo "upload_max_filesize = 50M"; \
      echo "post_max_size = 60M"; \
      echo "memory_limit = 256M"; \
      echo "max_execution_time = 300"; \
      echo "max_input_time = 300"; \
    } > /usr/local/etc/php/conf.d/99-coonex-defaults.ini

# --------------------------------------------------
# WP-CLI
# --------------------------------------------------
RUN curl -sSLo /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
 && chmod +x /usr/local/bin/wp

# --------------------------------------------------
# Inject Coonex config into wp-config-sample.php
# --------------------------------------------------
RUN sed -i "/require_once ABSPATH . 'wp-settings.php';/i \
/** ==============================\\n\
 * Coonex URL & Proxy Detection\\n\
 * ============================== */\\n\
if (getenv('WP_URL')) {\\n\
    define('WP_HOME', getenv('WP_URL'));\\n\
    define('WP_SITEURL', getenv('WP_URL'));\\n\
}\\n\\n\
if (!empty(\\$_SERVER['HTTP_X_FORWARDED_PROTO'])) {\\n\
    \\$_SERVER['HTTPS'] = \\$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'on' : 'off';\\n\
}\\n" /usr/src/wordpress/wp-config-sample.php

# --------------------------------------------------
# Entrypoint
# --------------------------------------------------
COPY docker-entrypoint-init.sh /usr/local/bin/docker-entrypoint-init.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-init.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint-init.sh"]
CMD ["apache2-foreground"]
