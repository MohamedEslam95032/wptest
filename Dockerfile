FROM wordpress:php8.2-apache

# --------------------------------------------------
# System packages
# --------------------------------------------------
RUN apt-get update && apt-get install -y \
    curl unzip less mariadb-client \
 && rm -rf /var/lib/apt/lists/*

# --------------------------------------------------
# WP-CLI
# --------------------------------------------------
RUN curl -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
 && chmod +x /usr/local/bin/wp

# --------------------------------------------------
# Inject Coonex config into wp-config-sample.php
# (NO FORCE HTTPS – SAFE behind proxy)
# --------------------------------------------------
RUN sed -i "/require_once ABSPATH . 'wp-settings.php';/i \
/** ==============================\\n\
 * Coonex URL & Proxy Detection (NO FORCE HTTPS)\\n\
 * ============================== */\\n\
if (getenv('WP_URL')) {\\n\
    define('WP_HOME', getenv('WP_URL'));\\n\
    define('WP_SITEURL', getenv('WP_URL'));\\n\
}\\n\\n\
if (!empty(\$_SERVER['HTTP_X_FORWARDED_PROTO'])) {\\n\
    \$_SERVER['HTTPS'] = \$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'on' : 'off';\\n\
}\\n\
" /usr/src/wordpress/wp-config-sample.php

# --------------------------------------------------
# Copy themes / plugins (custom only)
# --------------------------------------------------
COPY assets/themes/ /usr/src/wordpress/wp-content/themes/
COPY assets/plugins/ /usr/src/wordpress/wp-content/plugins/
COPY assets/mu-plugins/ /usr/src/wordpress/wp-content/mu-plugins/

# --------------------------------------------------
# WordPress init entrypoint (CORE ONLY)
# --------------------------------------------------
COPY docker-entrypoint-init.sh /usr/local/bin/docker-entrypoint-init.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-init.sh

# --------------------------------------------------
# uiXpress installer (SEPARATE – NOT AUTO-RUN)
# --------------------------------------------------
COPY install-uixpress.sh /usr/local/bin/install-uixpress.sh
RUN chmod +x /usr/local/bin/install-uixpress.sh

# --------------------------------------------------
# Entrypoint
# --------------------------------------------------
ENTRYPOINT ["/usr/local/bin/docker-entrypoint-init.sh"]
CMD ["apache2-foreground"]
