FROM php:8.2-apache

# 1. Instalar dependencias del sistema (Git, Unzip)
RUN apt-get update && apt-get install -y git unzip

# 2. Instalar extensiones de PHP (mysqli)
RUN docker-php-ext-install mysqli

# 3. Activar el módulo rewrite de Apache
RUN a2enmod rewrite

# 4. Instalar COMPOSER (La forma oficial y más limpia en Docker)
# En lugar de usar el script largo de PHP, copiamos el ejecutable de la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Establecer el directorio de trabajo
WORKDIR /var/www/html