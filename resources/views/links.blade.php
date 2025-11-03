<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>روابط وصفة | Wasfah Links</title>
    <meta name="description" content="اكتشف أهم الروابط لخدمة Wasfah من مكان واحد؛ دورات، وصفات، متجر الهدايا، والنشرة البريدية.">
    <meta property="og:title" content="روابط وصفة | Wasfah Links">
    <meta property="og:description" content="كل ما تحتاجه من Wasfah في صفحة واحدة.">
    <meta property="og:image" content="{{ asset('image/logo.png') }}">
    <meta name="theme-color" content="#0f212d">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --bg: linear-gradient(180deg, #fff7ed 0%, #fef3c7 45%, #ffffff 100%);
            --panel: linear-gradient(160deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 250, 245, 0.92) 100%);
            --panel-border: rgba(249, 115, 22, 0.16);
            --accent: #f97316;
            --accent-soft: rgba(249, 115, 22, 0.12);
            --heading: #1f2937;
            --muted: #6b7280;
            --card: rgba(255, 255, 255, 0.86);
            --card-border: rgba(249, 115, 22, 0.1);
            --shadow: 0 32px 70px rgba(249, 115, 22, 0.15);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: var(--bg);
            font-family: 'Tajawal', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--heading);
            padding: 32px 12px 24px;
        }

        .page-frame {
            width: min(420px, 100%);
            border-radius: 36px;
            padding: 28px 26px 30px;
            background: var(--panel);
            border: 1px solid var(--panel-border);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .page-frame::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(120% 160% at 50% -20%, rgba(249, 115, 22, 0.16) 0%, transparent 65%);
            pointer-events: none;
        }

        .header {
            text-align: center;
            margin-bottom: 22px;
            position: relative;
            z-index: 2;
        }

        .wordmark {
            display: inline-block;
            padding: 6px 18px;
            border-radius: 999px;
            border: 1px solid var(--accent-soft);
            background: rgba(255, 247, 237, 0.85);
            color: var(--accent);
            letter-spacing: 0.18em;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .hero-image {
            width: 148px;
            height: 148px;
            border-radius: 32px;
            object-fit: cover;
            display: block;
            margin: 0 auto 18px;
            border: 4px solid rgba(249, 115, 22, 0.15);
            box-shadow: 0 22px 40px rgba(249, 115, 22, 0.18);
        }

        .title {
            font-size: clamp(1.95rem, 4vw, 2.5rem);
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin: 0 0 10px;
        }

        .subtitle {
            margin: 0 auto;
            max-width: 260px;
            line-height: 1.6;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .socials {
            display: flex;
            justify-content: center;
            gap: 14px;
            margin: 24px 0 28px;
            position: relative;
            z-index: 2;
        }

        .socials a {
            width: 42px;
            height: 42px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            border-radius: 14px;
            border: 1px solid var(--accent-soft);
            background: rgba(255, 255, 255, 0.9);
            transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
        }

        .socials a:hover {
            transform: translateY(-4px);
            border-color: rgba(249, 115, 22, 0.35);
            background: rgba(255, 255, 255, 1);
        }

        .socials svg {
            width: 20px;
            height: 20px;
            fill: var(--accent);
        }

        .section-title {
            font-size: 1.05rem;
            font-weight: 600;
            margin: 0 0 14px;
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            color: var(--heading);
        }

        .section-title span {
            color: var(--muted);
            font-size: 0.78rem;
            font-weight: 400;
        }

        .carousel {
            display: flex;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 6px;
            scrollbar-width: thin;
        }

        .carousel::-webkit-scrollbar {
            height: 6px;
        }

        .carousel::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 999px;
        }

        .carousel-card {
            min-width: 132px;
            border-radius: 20px;
            padding: 14px;
            background: var(--card);
            border: 1px solid var(--card-border);
            display: flex;
            flex-direction: column;
            gap: 12px;
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .carousel-card:hover {
            transform: translateY(-5px);
            border-color: rgba(249, 115, 22, 0.24);
            box-shadow: 0 18px 36px rgba(249, 115, 22, 0.18);
        }

        .carousel-photo {
            height: 92px;
            border-radius: 16px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.6);
        }

        .carousel-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-caption {
            font-size: 0.88rem;
            font-weight: 500;
        }

        .links-stack {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin: 28px 0;
            position: relative;
            z-index: 2;
        }

        .link-card {
            padding: 20px;
            border-radius: 22px;
            border: 1px solid var(--card-border);
            background: rgba(255, 255, 255, 0.94);
            display: grid;
            gap: 14px;
            box-shadow: 0 20px 40px rgba(249, 115, 22, 0.12);
        }

        .link-card h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.04rem;
        }

        .link-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
            font-size: 0.91rem;
        }

        .link-card .cta {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 12px 0;
            border-radius: 16px;
            background: linear-gradient(135deg, #f97316 0%, #f59e0b 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .link-card .cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 32px rgba(249, 115, 22, 0.35);
        }

        .link-card .cta svg {
            width: 18px;
            height: 18px;
            fill: #ffffff;
        }

        .page-footer {
            margin-top: 32px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            color: var(--heading);
        }

        .footer-inner {
            padding: 20px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid var(--card-border);
            display: flex;
            flex-direction: column;
            gap: 18px;
            box-shadow: 0 24px 48px rgba(249, 115, 22, 0.1);
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .footer-brand img {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 12px 24px rgba(249, 115, 22, 0.18);
            padding: 6px;
        }

        .footer-brand strong {
            display: block;
            font-size: 1.12rem;
            letter-spacing: 0.04em;
        }

        .footer-brand span {
            display: block;
            color: var(--muted);
            font-size: 0.85rem;
            margin-top: 2px;
        }

        .footer-links {
            display: flex;
            gap: 12px;
        }

        .footer-links a {
            flex: 1;
            text-align: center;
            padding: 12px 0;
            border-radius: 16px;
            background: rgba(249, 115, 22, 0.08);
            color: var(--heading);
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        .footer-links a:hover {
            background: linear-gradient(135deg, #f97316 0%, #f59e0b 100%);
            color: #ffffff;
            transform: translateY(-3px);
        }

        .footer-meta {
            text-align: center;
            font-size: 0.78rem;
            color: rgba(107, 114, 128, 0.85);
            letter-spacing: 0.05em;
        }

        @media (max-width: 480px) {
            body {
                padding: 22px 10px;
            }

            .page-frame {
                border-radius: 32px;
                padding: 24px;
            }

            .hero-image {
                width: 132px;
                height: 132px;
            }

            .socials {
                gap: 12px;
            }

            .carousel-card {
                min-width: 120px;
            }
        }
    </style>
</head>
<body>
    @php
        $favorites = [
            [
                'title' => 'صندوق وصفة الموسمي',
                'image' => asset('image/tnl.png'),
                'alt' => 'صندوق وصفة الموسمي',
                'url' => url('/recipes'),
            ],
            [
                'title' => 'ورش عمل مباشرة عبر Google Meet',
                'image' => asset('image/wterm.png'),
                'alt' => 'ورش عمل مباشرة عبر Google Meet',
                'url' => url('/workshops'),
            ],
            [
                'title' => 'أدوات المطبخ المختارة',
                'image' => asset('image/term.png'),
                'alt' => 'أدوات المطبخ المختارة',
                'url' => url('/tools'),
            ],
        ];
        shuffle($favorites);
    @endphp
    <main class="page-frame">
        <header class="header">
            <span class="wordmark">Wasfah Links</span>
            <img class="hero-image" src="{{ asset('image/Brownies.png') }}" alt="صورة من وصفات وصفة">
            <h1 class="title">WASFAH</h1>
            <p class="subtitle">كل الروابط التي تحتاجها لتجربة وصفة: ورش عمل، صناديق موسمية، أدوات، ونشرة ملهمة.</p>
        </header>

        <nav class="socials" aria-label="روابط التواصل الاجتماعي">
            <a href="https://www.instagram.com/wasfah.jo/" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24" role="img" aria-hidden="true">
                    <path d="M12 7.3A4.7 4.7 0 1 0 16.7 12 4.7 4.7 0 0 0 12 7.3zm0 7.7A3 3 0 1 1 15 12a3 3 0 0 1-3 3zm6-7.9a1.1 1.1 0 1 1-1.1-1.1 1.1 1.1 0 0 1 1.1 1.1zM12 2c3.2 0 3.6 0 4.8.1a6 6 0 0 1 2.1.4 4.4 4.4 0 0 1 2.4 2.4 6 6 0 0 1 .4 2.1C21.8 8.2 22 8.6 22 12s0 3.6-.1 4.8a6 6 0 0 1-.4 2.1 4.4 4.4 0 0 1-2.4 2.4 6 6 0 0 1-2.1.4c-1.2.1-1.6.1-4.8.1s-3.6 0-4.8-.1a6 6 0 0 1-2.1-.4 4.4 4.4 0 0 1-2.4-2.4 6 6 0 0 1-.4-2.1C2.2 15.8 2 15.4 2 12s0-3.6.1-4.8a6 6 0 0 1 .4-2.1 4.4 4.4 0 0 1 2.4-2.4 6 6 0 0 1 2.1-.4C8.2 2.2 8.6 2 12 2z"></path>
                </svg>
            </a>
            <a href="https://www.youtube.com/@wasfah.jordan" target="_blank" rel="noopener">
                <svg viewBox="0 0 24 24" role="img" aria-hidden="true">
                    <path d="M21.6 7.2a2.6 2.6 0 0 0-1.8-1.8C18 5 12 5 12 5s-6 0-7.8.4A2.6 2.6 0 0 0 2.4 7.2 27 27 0 0 0 2 12a27 27 0 0 0 .4 4.8 2.6 2.6 0 0 0 1.8 1.8C6 19 12 19 12 19s6 0 7.8-.4a2.6 2.6 0 0 0 1.8-1.8A27 27 0 0 0 22 12a27 27 0 0 0-.4-4.8zM10 15.2V8.8l5.6 3.2z"></path>
                </svg>
            </a>
        </nav>

        <section aria-labelledby="favorites" style="position: relative; z-index: 2;">
            <div class="section-title">
                <span id="favorites">مفضلات المجتمع</span>
                <span>مختارات هذا الشهر</span>
            </div>
            <div class="carousel" role="list">
                @foreach ($favorites as $favorite)
                    <a class="carousel-card" role="listitem" href="{{ $favorite['url'] }}">
                        <div class="carousel-photo">
                            <img src="{{ $favorite['image'] }}" alt="{{ $favorite['alt'] }}">
                        </div>
                        <div class="carousel-caption">{{ $favorite['title'] }}</div>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="links-stack" aria-label="روابط رئيسية">
            <article class="link-card">
                <div>
                    <h3>ورش عمل الطهي المباشرة</h3>
                    <p>حجز مقعدك في الجلسات القادمة مع أمهر الشيفات، بث مباشر ودليل مكونات جاهز للطباعة.</p>
                </div>
                <a class="cta" href="{{ url('/workshops') }}">
                    انضم الآن
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M13.2 5.3 12 6.5l4.6 4.5H4v1.9h12.6L12 17.5l1.2 1.2L20 12z"></path>
                    </svg>
                </a>
            </article>
            <article class="link-card">
                <div>
                    <h3>جرّب وصفات Wasfah</h3>
                    <p>أكثر من 300 وصفة مصنفة حسب الوقت، المناسبة الغذائية، والمستوى. تصفح وابحث بسهولة.</p>
                </div>
                <a class="cta" href="{{ url('/recipes') }}">
                    تصفح الوصفات
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M13.2 5.3 12 6.5l4.6 4.5H4v1.9h12.6L12 17.5l1.2 1.2L20 12z"></path>
                    </svg>
                </a>
            </article>
            <article class="link-card">
                <div>
                    <h3>مكتبة الأدوات المفضلة لدينا</h3>
                    <p>اختيارات فريق وصفة من الأدوات التي نستخدمها يوميًا في المطبخ، مع روابط شراء موثوقة.</p>
                </div>
                <a class="cta" href="{{ url('/tools') }}">
                    اكتشف الأدوات
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M13.2 5.3 12 6.5l4.6 4.5H4v1.9h12.6L12 17.5l1.2 1.2L20 12z"></path>
                    </svg>
                </a>
            </article>
        </section>
    </main>
    <footer class="page-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <img src="{{ asset('image/logo.png') }}" alt="شعار وصفة">
                <div>
                    <strong>Wasfah</strong>
                    <span>وصفات، ورش، وأدوات تلهم مطبخك اليومي.</span>
                </div>
            </div>
            <div class="footer-links">
                <a href="{{ url('/') }}">الرئيسية</a>
                <a href="{{ url('/workshops') }}">ورش العمل</a>
                <a href="{{ url('/recipes') }}">الوصفات</a>
            </div>
        </div>
        <div class="footer-meta">© {{ now()->year }} Wasfah · جميع الحقوق محفوظة</div>
    </footer>
</body>
</html>
