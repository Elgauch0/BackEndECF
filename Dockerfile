FROM php:8.2-apache

# Installation des dépendances essentielles
RUN apt-get update && \
    apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libssl-dev \               
    libcurl4-openssl-dev \ 
    libjpeg-dev \
    libfreetype6-dev \
    netcat-openbsd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configuration et installation des extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd pdo_mysql intl zip && \
    pecl install mongodb && \
    docker-php-ext-enable mongodb && \
    a2enmod rewrite

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configuration du VirtualHost Apache pour Symfony
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/symfony/public\n\
    DirectoryIndex index.php\n\
    <Directory /var/www/symfony/public>\n\
    AllowOverride All\n\
    Order Allow,Deny\n\
    Allow from All\n\
    FallbackResource /index.php\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
    </VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Définition du ServerName pour éviter les avertissements
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Configuration du dossier de travail
WORKDIR /var/www/symfony
COPY . .

# Installation des dépendances et configuration des permissions
RUN composer install --optimize-autoloader --no-dev --no-scripts && \
    chown -R www-data:www-data var public

CMD ["apache2-foreground"]