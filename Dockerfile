FROM php:8.2

WORKDIR /app
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    git \
    zip \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


RUN pecl install mongodb && docker-php-ext-enable mongodb
RUN docker-php-ext-install pdo pdo_mysql


COPY . /app

RUN useradd -ms /bin/bash symfony && chown -R symfony:symfony /app

# Passer à l'utilisateur non-root
USER symfony

RUN rm -rf var/cache/*


RUN composer install --no-dev --optimize-autoloader

RUN php bin/console cache:clear --env=prod

EXPOSE 8000


CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]



