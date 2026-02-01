FROM php:8.2-cli-alpine

# 1. Устанавливаем ВСЕ необходимые системные пакеты
RUN apk add --no-cache \
    libffi-dev \
    postgresql-dev \
    libssl3 \
    openssl-dev \
    git \
    unzip \
    zlib-dev \
    linux-headers \
    build-base \
    autoconf

# 2. Устанавливаем и ВКЛЮЧАЕМ расширения PHP
RUN docker-php-ext-install ffi pdo_pgsql pgsql

# 3. Ставим Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 4. Сначала копируем только файлы зависимостей (ускоряет сборку)
COPY composer.json ./
# Если есть composer.lock, раскомментируй строку ниже
# COPY composer.lock ./

# Устанавливаем зависимости, игнорируя системные требования (важно для Alpine)
RUN composer install --ignore-platform-reqs --no-dev --no-scripts --no-autoloader

# 5. Копируем остальной код
COPY . .

# Финальная донастройка composer
RUN composer dump-autoload --optimize

EXPOSE 8080

# 6. Проверка наличия файла перед стартом
CMD if [ -f index.php ]; then php -S 0.0.0.0:8080 & php index.php; else echo "CRITICAL ERROR: index.php NOT FOUND"; ls -la; exit 1; fi
