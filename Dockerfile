FROM php:8.2-apache

# 1. Instalar dependencias del sistema
RUN apt-get update && apt-get install -y git unzip

# 2. INSTALAR DRIVERS DE MYSQL (Aquí está la magia)
# Instalamos 'mysqli' (clásico) y 'pdo_mysql' (moderno/PDO)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# 3. Activar el módulo rewrite de Apache
RUN a2enmod rewrite

# 4. Instalar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Copiar el script de arranque y darle permisos
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html

# 6. Definir el punto de entrada
ENTRYPOINT ["docker-entrypoint.sh"]

# 7. Arrancar Apache
CMD ["apache2-foreground"]