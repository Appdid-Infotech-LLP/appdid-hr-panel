# ==========================================
# Stage 1: Build frontend assets
# ==========================================
FROM node:22 AS frontend

WORKDIR /var/www/html

COPY package*.json ./

RUN npm ci

COPY . .

RUN npm run build


# ==========================================
# Stage 2: Laravel + PHP-FPM + Nginx
# ==========================================
FROM php:8.4-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mysqli \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && rm -rf /var/lib/apt/lists/*


# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# Laravel application
COPY . /var/www/html


# PHP dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --ignore-platform-reqs


# Copy compiled Vite assets
COPY --from=frontend /var/www/html/public/build /var/www/html/public/build

# Ensure the Laravel storage and framework directories exist
RUN mkdir -p /var/www/html/storage/app/htmlpurifier

# Permissions
RUN chmod -R 777 storage bootstrap/cache storage/app/htmlpurifier


# Laravel cache
RUN php artisan config:cache
# RUN php artisan route:cache
RUN php artisan view:cache


# Nginx configuration
RUN cat > /etc/nginx/sites-available/default <<'EOF'
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    server_name _;

    root /var/www/html/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF


EXPOSE 80


CMD ["sh", "-c", "php artisan migrate --force --no-interaction && php-fpm -D && nginx -g 'daemon off;'"]