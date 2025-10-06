# دليل رفع مشروع Laravel على Amazon Lightsail

## الخطوة 1: إنشاء Lightsail Instance

1. اذهب إلى [Amazon Lightsail Console](https://lightsail.aws.amazon.com/)
2. اضغط على "Create instance"
3. اختر:
   - **Platform**: Linux/Unix
   - **Blueprint**: Ubuntu 22.04 LTS
   - **Instance plan**: اختر الخطة المناسبة (أصغر خطة تكفي للتطوير)
   - **Instance name**: `wasfah-backend`
4. اضغط "Create instance"

## الخطوة 2: الاتصال بالخادم

1. في صفحة الـ instance، اضغط على "Connect using SSH"
2. أو استخدم SSH من محطة الأوامر:
```bash
ssh -i LightsailDefaultKey-us-east-1.pem ubuntu@YOUR_INSTANCE_IP
```

## الخطوة 3: رفع الملفات

### الطريقة الأولى: استخدام Git (مستحسن)
```bash
# على الخادم
sudo apt update
sudo apt install git -y

# استنساخ المشروع
sudo git clone https://github.com/yourusername/wasfah-backend.git /var/www/wasfah-backend
cd /var/www/wasfah-backend
```

### الطريقة الثانية: رفع الملفات يدوياً
```bash
# على الكمبيوتر المحلي
scp -i LightsailDefaultKey-us-east-1.pem -r . ubuntu@YOUR_INSTANCE_IP:/home/ubuntu/wasfah-backend/

# على الخادم
sudo mv /home/ubuntu/wasfah-backend /var/www/
```

## الخطوة 4: تشغيل سكريبت النشر

```bash
# جعل السكريبت قابل للتنفيذ
sudo chmod +x /var/www/wasfah-backend/deploy.sh

# تشغيل السكريبت
sudo /var/www/wasfah-backend/deploy.sh
```

## الخطوة 5: إعداد قاعدة البيانات

```bash
# الدخول إلى MySQL
sudo mysql -u root

# إنشاء قاعدة البيانات والمستخدم
CREATE DATABASE wasfah_backend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'wasfah_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON wasfah_backend.* TO 'wasfah_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## الخطوة 6: إعداد متغيرات البيئة

```bash
cd /var/www/wasfah-backend
sudo nano .env
```

أضف الإعدادات التالية:
```env
APP_NAME="Wasfah Backend"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wasfah_backend
DB_USERNAME=wasfah_user
DB_PASSWORD=your_secure_password
```

## الخطوة 7: تشغيل التطبيق

```bash
cd /var/www/wasfah-backend

# توليد مفتاح التطبيق
sudo php artisan key:generate

# تشغيل المايجريشن
sudo php artisan migrate --force

# تحسين الأداء
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache

# إعادة تشغيل الخدمات
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

## الخطوة 8: إعداد SSL (HTTPS)

```bash
# تثبيت Certbot
sudo apt install certbot python3-certbot-nginx -y

# الحصول على شهادة SSL
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

## الخطوة 9: إعداد النطاق (Domain)

1. اذهب إلى إعدادات النطاق في Lightsail
2. أضف النطاق الخاص بك
3. اربط النطاق بالـ instance
4. حدث إعدادات DNS في مزود النطاق

## الخطوة 10: إعداد النسخ الاحتياطي

```bash
# إنشاء سكريبت النسخ الاحتياطي
sudo nano /etc/cron.daily/backup-wasfah
```

أضف المحتوى التالي:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump wasfah_backend > /var/backups/wasfah-backend/database_$DATE.sql
tar -czf /var/backups/wasfah-backend/files_$DATE.tar.gz /var/www/wasfah-backend
find /var/backups/wasfah-backend -name "*.sql" -mtime +7 -delete
find /var/backups/wasfah-backend -name "*.tar.gz" -mtime +7 -delete
```

```bash
sudo chmod +x /etc/cron.daily/backup-wasfah
```

## أوامر مفيدة

```bash
# عرض السجلات
sudo tail -f /var/www/wasfah-backend/storage/logs/laravel.log

# إعادة تشغيل الخدمات
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm

# تشغيل المايجريشن
cd /var/www/wasfah-backend && sudo php artisan migrate

# مسح الكاش
cd /var/www/wasfah-backend && sudo php artisan cache:clear

# عرض حالة الخدمات
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
```

## استكشاف الأخطاء

### مشاكل شائعة:

1. **خطأ 500**: تحقق من صلاحيات الملفات
```bash
sudo chown -R www-data:www-data /var/www/wasfah-backend
sudo chmod -R 755 /var/www/wasfah-backend
sudo chmod -R 775 /var/www/wasfah-backend/storage
sudo chmod -R 775 /var/www/wasfah-backend/bootstrap/cache
```

2. **خطأ قاعدة البيانات**: تحقق من إعدادات .env
```bash
sudo php artisan config:clear
sudo php artisan config:cache
```

3. **مشاكل Nginx**: تحقق من الإعدادات
```bash
sudo nginx -t
sudo systemctl reload nginx
```

## المراقبة والصيانة

1. **مراقبة الأداء**:
```bash
htop
df -h
free -h
```

2. **تحديث النظام**:
```bash
sudo apt update && sudo apt upgrade -y
```

3. **مراقبة السجلات**:
```bash
sudo journalctl -u nginx -f
sudo journalctl -u php8.2-fpm -f
```

## الأمان

1. **تحديث كلمات المرور**:
```bash
sudo mysql_secure_installation
```

2. **إعداد Firewall**:
```bash
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
```

3. **تحديث النظام بانتظام**:
```bash
sudo apt update && sudo apt upgrade -y
```

---

**ملاحظة**: تأكد من استبدال `your-domain.com` و `your_secure_password` بالقيم الفعلية الخاصة بك.
