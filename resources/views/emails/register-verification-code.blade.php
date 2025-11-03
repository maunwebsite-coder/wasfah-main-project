@php
    $formattedCode = implode(' ', str_split($code));
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رمز التحقق من البريد الإلكتروني</title>
    <style>
        body {
            font-family: 'Tajawal', Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 2rem auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(249, 115, 22, 0.15);
            border: 1px solid rgba(249, 115, 22, 0.12);
        }
        .header {
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: #ffffff;
            padding: 2rem;
            text-align: center;
        }
        .content {
            padding: 2.5rem;
            color: #1f2937;
        }
        .code {
            margin: 2rem 0;
            font-size: 2rem;
            letter-spacing: 0.5rem;
            font-weight: 800;
            text-align: center;
            color: #ea580c;
        }
        .footer {
            padding: 1.5rem 2.5rem 2.5rem;
            color: #6b7280;
            font-size: 0.85rem;
            line-height: 1.6;
        }
        .highlight {
            color: #ea580c;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>مرحباً {{ $name }} 👋</h1>
            <p>نحن متحمسون لانضمامك إلى منصة وصفة!</p>
        </div>
        <div class="content">
            <p>
                لإكمال إنشاء حسابك والتحقق من ملكية البريد الإلكتروني، يرجى إدخال رمز التحقق المكون من ستة أرقام في الصفحة التي فتحتها للتو.
            </p>

            <div class="code">
                {{ $formattedCode }}
            </div>

            <p>
                ينتهي صلاحية هذا الرمز بعد <span class="highlight">15 دقيقة</span> من وقت الإرسال. إذا لم تكن أنت من طلب إنشاء الحساب، يمكنك تجاهل هذا البريد بأمان.
            </p>
        </div>
        <div class="footer">
            <p>مع خالص التحيات 🌟</p>
            <p>فريق <strong>وصفة</strong></p>
        </div>
    </div>
</body>
</html>

