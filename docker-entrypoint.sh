#!/bin/bash

# Si existe el archivo composer.json, instalamos las dependencias
if [ -f "/var/www/html/composer.json" ]; then
    echo "Encontrado composer.json. Instalando dependencias..."
    composer install --no-interaction --optimize-autoloader
else
    echo "No se encontró composer.json, saltando instalación."
fi

# Una vez instalado todo, ejecutamos el comando original de Apache
# "$@" ejecuta cualquier comando que le pase Docker después
exec "$@"