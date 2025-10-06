# إعدادات البريد الإلكتروني

لتفعيل إرسال الإيميلات من نموذج الاتصال، يجب إضافة الإعدادات التالية في ملف `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=wasfah99@gmail.com
MAIL_PASSWORD=your_app_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=wasfah99@gmail.com
MAIL_FROM_NAME="موقع وصفة"
```

## خطوات الإعداد:

1. **إنشاء كلمة مرور التطبيق لـ Gmail:**
   - اذهب إلى إعدادات Google Account
   - Security > 2-Step Verification > App passwords
   - أنشئ كلمة مرور جديدة للتطبيق
   - استخدم هذه الكلمة في `MAIL_PASSWORD`

2. **تحديث ملف .env:**
   - ضع الإعدادات أعلاه في ملف `.env`
   - استبدل `your_app_password_here` بكلمة مرور التطبيق

3. **اختبار الإرسال:**
   - اذهب إلى صفحة الاتصال
   - املأ النموذج وأرسل رسالة
   - تحقق من وصول الإيميل إلى wasfah99@gmail.com

## ملاحظات:
- تأكد من تفعيل 2-Step Verification في Gmail
- استخدم App Password وليس كلمة مرور الحساب العادية
- يمكن استخدام خدمات بريد أخرى مثل Mailgun أو SendGrid

