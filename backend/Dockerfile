FROM php:8.4-cli
RUN apt-get update && apt-get install -y git unzip libzip-dev libonig-dev libxml2-dev libsqlite3-dev && docker-php-ext-install pdo_mysql pdo_sqlite mbstring zip dom && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . .
RUN composer install --no-interaction --prefer-dist
EXPOSE 8000
CMD ["sh", "/app/docker-entrypoint.sh"]
