FROM php:8.2-cli

# 1. Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    libffi-dev \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install ffi pdo_pgsql pgsql

# 2. Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 3. Копируем файлы проекта
COPY . .

# 4. Устанавливаем зависимости
# Добавляем --no-interaction, чтобы сборка не ждала ответов от тебя
RUN composer install --no-interaction --ignore-platform-reqs --no-dev

EXPOSE 8080

# 5. Запуск
CMD php -S 0.0.0.0:8080 & php index.php
