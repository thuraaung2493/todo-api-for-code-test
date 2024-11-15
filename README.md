# Todo List API - Deployment Guide

This guide provides instructions to deploy the Laravel Todo List API on an Nginx server.

## Prerequisites

-   A Linux server with Nginx installed.
-   PHP 8.x and MySQL installed.
-   composer installed on the server.
-   A MySQL database created for the Laravel application.

## Steps to Deploy

### SSH Login

```bash
  # ssh login
  ssh -i /path/to/private_key user@todo-list-api.com
```

### Clone project

```bash
  # Go to /var/www
  cd /var/www

  # Clone the project
  git clone <repo> todo-api
  cd todo-api
```

### Install dependencies

```bash
  composer install
```

### Enviornment setup

```bash
  cp .env.example .env
  php artisan key:generate
```

Edit the .env file with your database details and other necessary configuration:

```
APP_NAME=TodoListAPI
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY
APP_DEBUG=false
APP_URL=http://todo-list-api.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_database
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### Database migration

```bash
  php artisan migrate
```

### Setup permissions

```bash
  sudo chown -R www-data:www-data /var/www/todo-api
  sudo chmod -R 775 /var/www/todo-api/storage
  sudo chmod -R 775 /var/www/todo-api/bootstrap/cache
```

### Config Ngnix

```bash
  sudo vim /etc/nginx/sites-available/todo-api
```

Add the following configuration

```
server {
    listen 80;
    listen [::]:80;
    server_name todo-list-api.com;
    root /var/www/todo-api/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the configuration:

```bash
  sudo ln -s /etc/nginx/sites-available/todo-api /etc/nginx/sites-enabled/

  # Test and restart
  sudo nginx -t
  sudo systemctl restart nginx
```

### Setup caching

```bash
  php artisan config:cache
  php artisan route:cache
```

### Run test script

```bash
  php artisan test
```
