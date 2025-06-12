# Usamos imagen oficial PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias (por ej. para mail, curl, etc)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite de Apache si usás rutas amigables
RUN a2enmod rewrite

# Copiar el código PHP al contenedor (ajustá la ruta si hace falta)
COPY . /var/www/html/

# Dar permisos adecuados (opcional, depende de la app)
RUN chown -R www-data:www-data /var/www/html/

# Exponer puerto 80
EXPOSE 80

# Comando por defecto (apache en primer plano)
CMD ["apache2-foreground"]
