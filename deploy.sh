#!/bin/bash

# Laravel Deployment Script for Amazon Lightsail
# This script automates the deployment process

set -e

echo "🚀 Starting Laravel deployment to Amazon Lightsail..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
APP_NAME="wasfah-backend"
APP_DIR="/var/www/$APP_NAME"
BACKUP_DIR="/var/backups/$APP_NAME"
NGINX_SITES="/etc/nginx/sites-available"
NGINX_ENABLED="/etc/nginx/sites-enabled"

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    print_error "Please run as root (use sudo)"
    exit 1
fi

print_status "Updating system packages..."
apt update && apt upgrade -y

print_status "Installing required packages..."
apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd php8.2-intl php8.2-cli php8.2-common php8.2-opcache php8.2-readline php8.2-sqlite3 php8.2-tokenizer php8.2-xml php8.2-xmlrpc php8.2-xsl php8.2-json composer git unzip

print_status "Configuring MySQL..."
systemctl start mysql
systemctl enable mysql

# Create database and user
mysql -e "CREATE DATABASE IF NOT EXISTS wasfah_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS 'wasfah_user'@'localhost' IDENTIFIED BY 'your_secure_password';"
mysql -e "GRANT ALL PRIVILEGES ON wasfah_backend.* TO 'wasfah_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

print_status "Creating application directory..."
mkdir -p $APP_DIR
mkdir -p $BACKUP_DIR

print_status "Setting up Laravel application..."
cd $APP_DIR

# If this is a fresh deployment, clone the repository
if [ ! -d "$APP_DIR/.git" ]; then
    print_status "Cloning repository..."
    # Replace with your actual repository URL
    git clone https://github.com/yourusername/wasfah-backend.git .
fi

print_status "Pulling latest changes..."
git pull origin main

print_status "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

print_status "Installing NPM dependencies and building assets..."
npm install
npm run build

print_status "Setting up environment file..."
if [ ! -f "$APP_DIR/.env" ]; then
    cp .env.production .env
    print_warning "Please update the .env file with your production settings"
fi

print_status "Generating application key..."
php artisan key:generate

print_status "Running database migrations..."
php artisan migrate --force

print_status "Clearing and caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

print_status "Setting proper permissions..."
chown -R www-data:www-data $APP_DIR
chmod -R 755 $APP_DIR
chmod -R 775 $APP_DIR/storage
chmod -R 775 $APP_DIR/bootstrap/cache

print_status "Configuring Nginx..."
cat > $NGINX_SITES/$APP_NAME << EOF
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    root $APP_DIR/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Enable the site
ln -sf $NGINX_SITES/$APP_NAME $NGINX_ENABLED/
rm -f $NGINX_ENABLED/default

print_status "Testing Nginx configuration..."
nginx -t

print_status "Restarting services..."
systemctl restart nginx
systemctl restart php8.2-fpm
systemctl enable nginx
systemctl enable php8.2-fpm

print_status "Setting up SSL with Let's Encrypt..."
apt install -y certbot python3-certbot-nginx
certbot --nginx -d your-domain.com -d www.your-domain.com

print_status "Setting up automatic backups..."
cat > /etc/cron.daily/backup-wasfah << EOF
#!/bin/bash
DATE=\$(date +%Y%m%d_%H%M%S)
mysqldump wasfah_backend > $BACKUP_DIR/database_\$DATE.sql
tar -czf $BACKUP_DIR/files_\$DATE.tar.gz $APP_DIR
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
EOF

chmod +x /etc/cron.daily/backup-wasfah

print_status "Setting up log rotation..."
cat > /etc/logrotate.d/wasfah << EOF
$APP_DIR/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 644 www-data www-data
}
EOF

print_status "🎉 Deployment completed successfully!"
print_status "Your Laravel application is now running on: https://your-domain.com"
print_warning "Don't forget to:"
print_warning "1. Update your domain name in the Nginx configuration"
print_warning "2. Update the .env file with your production settings"
print_warning "3. Configure your DNS to point to this server's IP address"
print_warning "4. Set up monitoring and alerting"

echo ""
print_status "Useful commands:"
echo "  - View logs: tail -f $APP_DIR/storage/logs/laravel.log"
echo "  - Restart services: systemctl restart nginx php8.2-fpm"
echo "  - Run migrations: cd $APP_DIR && php artisan migrate"
echo "  - Clear cache: cd $APP_DIR && php artisan cache:clear"
