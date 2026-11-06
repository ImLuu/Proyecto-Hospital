# Imagen única para Dev y Test: PHP 8.2 + Apache + extensión PDO para PostgreSQL.
FROM php:8.2-apache

# Instala la extensión pdo_pgsql (necesita las librerías de PostgreSQL).
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Permite usar archivos .htaccess (igual que en un hosting tradicional).
RUN a2enmod rewrite \
    && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copia el código. En Dev se sobrescribe con un volumen (hot-reload).
COPY frontend/ /var/www/html/

EXPOSE 80
