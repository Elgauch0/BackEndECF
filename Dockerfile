FROM php:8.2-fpm

# Installer les dépendances
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    librabbitmq-dev \
    libssh-dev \
    libxslt1-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Installer MongoDB extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Configurer et installer les extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    zip \
    intl \
    gd \
    pdo_mysql \
    bcmath \
    sockets \
    xsl \
    opcache

# Optimiser PHP pour la production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && sed -i 's/memory_limit = 128M/memory_limit = 256M/g' "$PHP_INI_DIR/php.ini"

# Configurer OPcache
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/symfony

# Copier les fichiers de l'application
COPY . /var/www/symfony

# Installer les dépendances
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --prefer-dist --no-dev --no-progress --no-interaction

# Changer les permissions
RUN chown -R www-data:www-data /var/www/symfony/var

# Exposer le port PHP-FPM
EXPOSE 9000

# Configurer l'entrypoint
CMD ["php-fpm"]