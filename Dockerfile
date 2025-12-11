FROM php:8.2-apache

# 1. Instalar dependencias del sistema Y dos2unix (para arreglar saltos de línea)
RUN apt-get update && apt-get install -y git unzip dos2unix

# 2. INSTALAR DRIVERS DE MYSQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# 3. Activar el módulo rewrite de Apache
RUN a2enmod rewrite

# 4. Instalar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Copiar el script de arranque
COPY docker-entrypoint.sh /usr/local/bin/

# 6. ARREGLAR SALTOS DE LÍNEA Y DAR PERMISOS (¡Esta es la clave!)
RUN dos2unix /usr/local/bin/docker-entrypoint.sh && chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html

# 7. Definir el punto de entrada
ENTRYPOINT ["docker-entrypoint.sh"]

# 8. Arrancar Apache
CMD ["apache2-foreground"]