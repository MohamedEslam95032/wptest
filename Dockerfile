FROM wordpress:6.4-php8.2-apache

# Install required packages
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    less \
    mariadb-client \
    && rm -rf /var/lib/apt/lists/*

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Copy entrypoint
COPY docker-entrypoint.sh /usr/local/bin/coonex-entrypoint.sh
RUN chmod +x /usr/local/bin/coonex-entrypoint.sh

ENTRYPOINT ["coonex-entrypoint.sh"]
CMD ["apache2-foreground"]
