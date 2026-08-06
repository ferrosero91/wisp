# ============================================================
# Dockerfile - WISP Internet System
# PHP 8.2 + Apache + Composer
# ============================================================

FROM php:8.2-apache

# Instalar dependencias del sistema y extensiones PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    libxml2-dev \
    unzip \
    curl \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        gd \
        mbstring \
        zip \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar PHP para producción
RUN { \
    echo 'display_errors=Off'; \
    echo 'log_errors=On'; \
    echo 'error_reporting=E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_NOTICE & ~E_WARNING'; \
    echo 'memory_limit=256M'; \
    echo 'upload_max_filesize=20M'; \
    echo 'post_max_size=25M'; \
    echo 'max_execution_time=60'; \
    echo 'date.timezone=America/Lima'; \
    echo 'session.cookie_httponly=1'; \
    echo 'session.use_strict_mode=1'; \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.max_accelerated_files=10000'; \
} > /usr/local/etc/php/conf.d/wisp.ini

# Configurar Apache para producción
RUN { \
    echo 'ServerName localhost'; \
    echo '<Directory /var/www/html>'; \
    echo '    Options -Indexes +FollowSymLinks'; \
    echo '    AllowOverride All'; \
    echo '    Require all granted'; \
    echo '</Directory>'; \
    echo 'ServerTokens Prod'; \
    echo 'ServerSignature Off'; \
} > /etc/apache2/conf-available/wisp.conf \
&& a2enconf wisp

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY --chown=www-data:www-data . .

# Instalar dependencias de Composer en cada subdirectorio
RUN cd Libraries/dompdf && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd Libraries/resize && composer install --no-dev --optimize-autoloader --no-interaction
RUN cd Libraries/spreadsheet && composer install --no-dev --optimize-autoloader --no-interaction

# Crear directorios necesarios y establecer permisos
RUN mkdir -p /var/www/html/Assets/uploads/users \
    && mkdir -p /var/www/html/Assets/uploads/pdf \
    && mkdir -p /var/www/html/Assets/uploads/qr \
    && mkdir -p /var/www/html/Assets/uploads/certificates \
    && mkdir -p /var/www/html/Logs \
    && mkdir -p /var/www/html/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/Assets/uploads \
    && chmod -R 775 /var/www/html/Logs \
    && chmod -R 775 /var/www/html/cache

# Puerto expuesto
EXPOSE 80

# Healthcheck
HEALTHCHECK --interval=30s --timeout=10s --start-period=15s --retries=3 \
    CMD curl -f http://localhost/ || exit 1
