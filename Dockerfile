# 1️⃣ PHP + Apache
FROM php:8.2-apache

# 2️⃣ Cài extension cần thiết cho Laravel + PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    zip \
    npm \
    nodejs \
    && docker-php-ext-install pdo pdo_pgsql

# 3️⃣ Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4️⃣ Copy code vào container
WORKDIR /var/www/html
COPY . .

# 5️⃣ Set permission cho storage + bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 6️⃣ Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# 7️⃣ Build Vue nếu cùng repo
RUN npm install
RUN npm run build

# 8️⃣ Migrate database
# (Chỉ migrate khi container khởi động, không nên migrate ở build)
# RUN php artisan migrate --force

# 9️⃣ Expose port
EXPOSE 8000

# 10️⃣ Start Laravel server khi container chạy
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
