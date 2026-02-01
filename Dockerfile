FROM php:8.2-cli-alpine

# Устанавливаем системные зависимости для MadelineProto и PostgreSQL
RUN apk add --no-cache \
    libffi-dev \
    postgresql-dev \
    libssl3 \
    openssl-dev \
    git \
    unzip \
    zlib-dev

# Устанавливаем расширения PHP: FFI для скорости и pdo_pgsql для базы данных
RUN docker-php-ext-install ffi pdo_pgsql pgsql

# Устанавливаем Composer (менеджер зависимостей PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Рабочая директория внутри контейнера
WORKDIR /app

# Копируем файлы проекта
COPY . .

# Устанавливаем зависимости из composer.json
RUN composer install --no-dev --optimize-autoloader

# Открываем порт 8080 (требование Koyeb для Health Check)
EXPOSE 8080

# Команда запуска: 
# 1. Запускаем встроенный PHP-сервер на фоне, чтобы Koyeb видел, что порт активен
# 2. Запускаем твой основной скрипт (обычно index.php или bot.php)
CMD php -S 0.0.0.0:8080 & php index.php
