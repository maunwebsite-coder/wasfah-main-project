#!/bin/bash

# Laravel Update Script for Amazon Lightsail
# This script updates the Laravel application with new changes

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
APP_NAME="wasfah-backend"
APP_DIR="/var/www/$APP_NAME"

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

print_status "Starting Laravel application update..."

# Navigate to application directory
cd $APP_DIR

# Create backup before update
print_status "Creating backup..."
BACKUP_DIR="/var/backups/$APP_NAME"
mkdir -p $BACKUP_DIR
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump wasfah_backend > $BACKUP_DIR/database_before_update_$DATE.sql
tar -czf $BACKUP_DIR/files_before_update_$DATE.tar.gz $APP_DIR

print_status "Pulling latest changes from Git..."
git fetch origin
git reset --hard origin/main

print_status "Installing/updating Composer dependencies..."
composer install --no-dev --optimize-autoloader

print_status "Installing/updating NPM dependencies..."
npm install

print_status "Building assets..."
npm run build

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

print_status "Restarting services..."
systemctl restart nginx
systemctl restart php8.2-fpm

print_status "🎉 Update completed successfully!"

print_status "Cleaning up old backups (keeping last 7 days)..."
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

print_status "Update process finished!"
