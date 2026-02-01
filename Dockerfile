FROM php:8.2-cli

# Устанавливаем системные пакеты
RUN apt-get update && apt-get install -y \
    libffi-dev libpq-dev git unzip libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install ffi pdo_pgsql pgsql

# Ставим Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Копируем только composer.json для быстрой установки
COPY composer.json ./

# Устанавливаем MadelineProto (это может занять пару минут)
RUN composer install --no-interaction --ignore-platform-reqs

# Копируем остальной код
COPY . .

EXPOSE 8000

# Запуск заглушки и основного файла
CMD php -S 0.0.0.0:8000 & php index.php
