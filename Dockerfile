FROM php:8.4-cli

# Instalar dependencias del sistema y extensiones de PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libsqlite3-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_sqlite pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar los archivos del proyecto
COPY . .

# Instalar dependencias de PHP optimizadas para producción
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Asegurar permisos y directorios necesarios
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache database

# Script de inicio para Render
RUN chmod +x docker-entrypoint.sh

EXPOSE 10000

CMD ["./docker-entrypoint.sh"]
