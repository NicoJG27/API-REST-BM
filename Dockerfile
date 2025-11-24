FROM php:8.2-apache

# 1. Instalar dependencias del sistema
RUN apt-get update && apt-get install -y git unzip

# 2. Instalar extensiones PHP
RUN docker-php-ext-install mysqli
RUN a2enmod rewrite

# 3. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Copiar el script de arranque mágico
COPY docker-entrypoint.sh /usr/local/bin/
# Darle permisos de ejecución (importante)
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html

# 5. Decirle a Docker que USE ese script al iniciar
ENTRYPOINT ["docker-entrypoint.sh"]

# 6. El comando por defecto sigue siendo arrancar Apache
CMD ["apache2-foreground"]