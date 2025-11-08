<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وصفة الآن | Wasfah Now</title>
    <meta name="description" content="مركز Wasfah Now يجمع أحدث الورش والوصفات والأدوات والتواصل مع فريق وصفة في مساحة واحدة أنيقة.">
    <meta property="og:title" content="وصفة الآن | Wasfah Now">
    <meta property="og:description" content="كل الروابط المهمة لتجربة وصفة في صفحة واحدة متجددة.">
    <meta property="og:image" content="{{ asset('image/logo.png') }}">
    <meta name="theme-color" content="#030711">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: dark;
            --bg: #030711;
            --bg-alt: #101828;
            --panel: rgba(6, 12, 24, 0.92);
            --panel-border: rgba(255, 255, 255, 0.08);
            --accent: #ff914d;
            --accent-strong: #ffb76b;
            --accent-soft: rgba(255, 145, 77, 0.22);
            --muted: #9baec6;
            --text: #f5f7ff;
            --card: rgba(13, 19, 35, 0.92);
            --card-border: rgba(255, 255, 255, 0.06);
            --success: #4ade80;
            --radius-xl: 36px;
            --radius-lg: 24px;
            --radius-md: 18px;
            --shadow-lg: 0 30px 90px rgba(1, 5, 16, 0.7);
            --shadow-card: 0 20px 45px rgba(2, 6, 23, 0.55);
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(circle at 20% 20%, rgba(255, 145, 77, 0.28), transparent 55%),
                radial-gradient(circle at 80% 0%, rgba(37, 99, 235, 0.35), transparent 45%),
                linear-gradient(180deg, var(--bg) 0%, var(--bg-alt) 100%);
            font-family: 'Tajawal', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text);
            padding: 48px 16px 40px;
            position: relative;
        }

        body::before,
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            mix-blend-mode: screen;
        }

        body::before {
            background: radial-gradient(circle at 10% 50%, rgba(255, 255, 255, 0.08), transparent 55%);
            opacity: 0.4;
        }

        body::after {
            background: radial-gradient(circle at 85% 30%, rgba(255, 145, 77, 0.25), transparent 60%);
            opacity: 0.3;
        }

        .glow {
            position: fixed;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.55;
            z-index: 0;
        }

        .glow-a {
            background: #ff914d;
            top: 12vh;
            right: 10vw;
        }

        .glow-b {
            background: #60a5fa;
            bottom: 6vh;
            left: 12vw;
        }

        main {
            position: relative;
            z-index: 1;
        }

        .shell {
            width: min(960px, 100%);
            margin: 0 auto;
            border-radius: var(--radius-xl);
            padding: 42px clamp(20px, 4vw, 64px) 48px;
            background: var(--panel);
            border: 1px solid var(--panel-border);
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(16px);
        }

        .hero {
            display: flex;
            flex-direction: column;
            gap: 22px;
            text-align: center;
            margin-bottom: 40px;
        }

        .eyebrow {
            align-self: center;
            padding: 8px 24px;
            border-radius: 999px;
            border: 1px solid var(--accent-soft);
            background: rgba(255, 145, 77, 0.08);
            color: var(--accent);
            letter-spacing: 0.22em;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .hero h1 {
            font-size: clamp(2.1rem, 4vw, 3rem);
            margin: 0;
            letter-spacing: 0.06em;
        }

        .hero p {
            margin: 0 auto;
            max-width: 520px;
            color: var(--muted);
            line-height: 1.7;
            font-size: 1.02rem;
        }

        .hero-actions {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            border-radius: 999px;
            padding: 14px 28px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn.primary {
            background: linear-gradient(130deg, var(--accent) 0%, var(--accent-strong) 100%);
            color: #0f172a;
            box-shadow: 0 18px 40px rgba(255, 145, 77, 0.35);
        }

        .btn.secondary {
            border: 1px solid var(--accent-soft);
            color: var(--text);
            background: transparent;
        }

        .btn.tertiary {
            background: rgba(255, 255, 255, 0.08);
            color: var(--text);
            border-radius: var(--radius-md);
            padding-inline: 20px;
        }

        .btn:hover {
            transform: translateY(-3px);
        }

        .hero-stats {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
        }

        .hero-stats li {
            background: var(--card);
            border-radius: var(--radius-md);
            border: 1px solid var(--card-border);
            padding: 16px 18px;
            text-align: center;
        }

        .hero-stats span {
            display: block;
        }

        .stat-label {
            font-size: 1.05rem;
            font-weight: 600;
        }

        .stat-sub {
            font-size: 0.85rem;
            color: var(--muted);
        }

        .feature-card {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 26px;
            padding: 28px;
            border-radius: var(--radius-lg);
            background: linear-gradient(135deg, rgba(255, 145, 77, 0.15), rgba(15, 23, 42, 0.8));
            border: 1px solid rgba(255, 145, 77, 0.25);
            box-shadow: var(--shadow-card);
            margin-bottom: 42px;
            align-items: center;
        }

        .feature-meta h2 {
            margin: 8px 0 14px;
            font-size: 1.5rem;
        }

        .feature-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: var(--accent);
            letter-spacing: 0.08em;
        }

        .feature-tag::before {
            content: '';
            width: 10px;
            height: 10px;
            background: var(--accent);
            border-radius: 999px;
        }

        .feature-meta p {
            margin: 0 0 16px;
            color: var(--muted);
            line-height: 1.6;
        }

        .feature-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-size: 0.95rem;
            color: var(--text);
        }

        .feature-details span {
            color: var(--muted);
        }

        .feature-media {
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35);
            max-height: 260px;
        }

        .feature-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }

        .section-kicker {
            margin: 0;
            font-size: 0.9rem;
            color: var(--accent-strong);
            letter-spacing: 0.1em;
        }

        .section-head h2 {
            margin: 6px 0 0;
            font-size: 1.8rem;
        }

        .hint {
            color: var(--muted);
            font-size: 0.85rem;
        }

        .monthly {
            margin-bottom: 44px;
        }

        .monthly-rail {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .monthly-card {
            border-radius: var(--radius-lg);
            border: 1px solid var(--card-border);
            background: var(--card);
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            box-shadow: var(--shadow-card);
        }

        .monthly-media {
            border-radius: var(--radius-md);
            overflow: hidden;
            aspect-ratio: 4 / 3;
            background: rgba(255, 255, 255, 0.04);
        }

        .monthly-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .monthly-body h3 {
            margin: 0 0 12px;
            font-size: 1.1rem;
        }

        .monthly-body a {
            font-weight: 600;
            color: var(--accent-strong);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .monthly-body svg {
            width: 16px;
            height: 16px;
        }

        .core-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 18px;
        }

        .core-card {
            border-radius: var(--radius-lg);
            padding: 22px;
            border: 1px solid var(--card-border);
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.04), rgba(15, 23, 42, 0.7));
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: var(--shadow-card);
        }

        .core-card h3 {
            margin: 0;
            font-size: 1.2rem;
        }

        .core-card p {
            margin: 0 0 8px;
            color: var(--muted);
            line-height: 1.5;
        }

        .core-card a {
            align-self: flex-start;
            text-decoration: none;
            color: var(--accent-strong);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .social-panel {
            margin-top: 44px;
            border-radius: var(--radius-lg);
            border: 1px solid rgba(96, 165, 250, 0.25);
            background: linear-gradient(125deg, rgba(37, 99, 235, 0.25), rgba(15, 23, 42, 0.85));
            padding: 28px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
        }

        .panel-copy {
            flex: 1 1 280px;
        }

        .panel-copy h2 {
            margin: 10px 0;
            font-size: 1.6rem;
        }

        .panel-copy p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .social-icons {
            display: flex;
            gap: 14px;
        }

        .social-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(15, 23, 42, 0.7);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .social-icon svg {
            width: 22px;
            height: 22px;
            fill: var(--accent-strong);
        }

        .social-icon:hover {
            transform: translateY(-4px);
            border-color: var(--accent);
        }

        footer {
            margin-top: 36px;
            text-align: center;
            color: var(--muted);
            font-size: 0.85rem;
        }

        .footer-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .footer-brand img {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            padding: 6px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.05);
        }

        @media (max-width: 720px) {
            body {
                padding: 32px 12px 28px;
            }

            .shell {
                border-radius: 24px;
                padding: 32px 20px;
            }

            .feature-card {
                padding: 22px;
            }

            .social-icons {
                width: 100%;
                justify-content: flex-start;
            }
        }

        @media (max-width: 520px) {
            .hero-stats {
                grid-template-columns: 1fr;
            }

            .hero-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .monthly-rail,
            .core-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                transition: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="glow glow-a" aria-hidden="true"></div>
    <div class="glow glow-b" aria-hidden="true"></div>
    <main class="shell">
        <header class="hero">
            <span class="eyebrow">Wasfah Now</span>
            <h1>مركز وصفة الحي</h1>
            <p>كل ما تحتاجه من ورش وصفة المباشرة، وصفاتنا المجربة، وأدواتنا المفضلة في مكان واحد بتصميم حديث يجدد تجربتك
                في كل زيارة.</p>
            <div class="hero-actions">
                <a class="btn primary" href="{{ url('/workshops') }}">ورش مباشرة</a>
                <a class="btn secondary" href="{{ url('/recipes') }}">متصفح الوصفات</a>
            </div>
            <ul class="hero-stats">
                <li>
                    <span class="stat-label">+300 وصفة</span>
                    <span class="stat-sub">متاحة للبحث والتجربة</span>
                </li>
                <li>
                    <span class="stat-label">جلسات أسبوعية</span>
                    <span class="stat-sub">ورش تفاعلية أونلاين</span>
                </li>
                <li>
                    <span class="stat-label">مختارات الفريق</span>
                    <span class="stat-sub">أدوات وتجهيزات جاهزة</span>
                </li>
            </ul>
        </header>

        @if ($upcomingWorkshop)
            <section class="feature-card" role="region" aria-label="الورشة القادمة">
                <div class="feature-meta">
                    <p class="feature-tag">حدث مميز</p>
                    <h2>{{ $upcomingWorkshop['title'] }}</h2>
                    <p>جاهزين لجلسة جديدة مع {{ $upcomingWorkshop['instructor'] ?? 'فريق Wasfah' }}. كل ما تحتاجه من وقت،
                        طريقة التواصل، وتجربة الطهي في بطاقة واحدة.</p>
                    <div class="feature-details">
                        @if (!empty($upcomingWorkshop['start_date']))
                            <div>الوقت: <span>{{ $upcomingWorkshop['start_date'] }}</span></div>
                        @endif
                        @if (!empty($upcomingWorkshop['mode']))
                            <div>النمط: <span>{{ $upcomingWorkshop['mode'] }}</span></div>
                        @endif
                    </div>
                    <a class="btn tertiary" href="{{ route('workshop.show', ['workshop' => $upcomingWorkshop['slug']]) }}">
                        تفاصيل الورشة
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="currentColor" d="M13.2 5.3 12 6.5l4.6 4.5H4v1.9h12.6L12 17.5l1.2 1.2L20 12z" />
                        </svg>
                    </a>
                </div>
                <div class="feature-media">
                    <img src="{{ $upcomingWorkshop['image'] }}" alt="صورة الورشة القادمة">
                </div>
            </section>
        @endif

        <section class="monthly" aria-labelledby="monthly-title">
            <div class="section-head">
                <div>
                    <p class="section-kicker" id="monthly-title">مختارات متجددة</p>
                    <h2>قائمة التوصيات السريعة</h2>
                </div>
                <span class="hint">تتغير مع كل زيارة بناءً على وصفات مفعلة</span>
            </div>
            <div class="monthly-rail" role="list">
                @foreach ($monthlySelections as $selection)
                    <article class="monthly-card" role="listitem">
                        <div class="monthly-media">
                            <img src="{{ $selection['image'] }}" alt="{{ $selection['alt'] }}">
                        </div>
                        <div class="monthly-body">
                            <h3>{{ $selection['title'] }}</h3>
                            <a href="{{ $selection['url'] }}">
                                جرب الآن
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill="currentColor" d="M13.2 5.3 12 6.5l4.6 4.5H4v1.9h12.6L12 17.5l1.2 1.2L20 12z" />
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="core-grid" aria-label="روابط Wasfah الأساسية">
            <article class="core-card">
                <h3>ورش الطهي التفاعلية</h3>
                <p>حجوزات مباشرة مع جدول واضح، وصفات PDF، وروابط بث Google Meet جاهزة.</p>
                <a href="{{ url('/workshops') }}">انضم للورشة القادمة</a>
            </article>
            <article class="core-card">
                <h3>دليل الوصفات المتكامل</h3>
                <p>بحث ذكي، فلاتر حسب الوقت والنظام الغذائي، وقوائم تسوق قابلة للطباعة.</p>
                <a href="{{ url('/recipes') }}">استكشف قاعدة الوصفات</a>
            </article>
            <article class="core-card">
                <h3>أدوات المطبخ المختارة</h3>
                <p>منتجات موصى بها مع روابط شراء موثوقة وتجارب فريق Wasfah اليومية.</p>
                <a href="{{ url('/tools') }}">اكتشف الأدوات</a>
            </article>
        </section>

        <section class="social-panel" aria-label="قنوات Wasfah على الشبكات الاجتماعية">
            <div class="panel-copy">
                <p class="section-kicker">تواصل مباشر</p>
                <h2>انضم إلى مجتمع Wasfah</h2>
                <p>لقطات خلف الكواليس، مقاطع تعليمية سريعة، وإعلانات الورش تنشر أولاً على قنواتنا.</p>
            </div>
            <div class="social-icons">
                <a class="social-icon" href="https://www.instagram.com/wasfah.jo/" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24" role="img" aria-hidden="true">
                        <path d="M12 7.3A4.7 4.7 0 1 0 16.7 12 4.7 4.7 0 0 0 12 7.3zm0 7.7A3 3 0 1 1 15 12a3 3 0 0 1-3 3zm6-7.9a1.1 1.1 0 1 1-1.1-1.1 1.1 1.1 0 0 1 1.1 1.1zM12 2c3.2 0 3.6 0 4.8.1a6 6 0 0 1 2.1.4 4.4 4.4 0 0 1 2.4 2.4 6 6 0 0 1 .4 2.1C21.8 8.2 22 8.6 22 12s0 3.6-.1 4.8a6 6 0 0 1-.4 2.1 4.4 4.4 0 0 1-2.4 2.4 6 6 0 0 1-2.1.4c-1.2.1-1.6.1-4.8.1s-3.6 0-4.8-.1a6 6 0 0 1-2.1-.4 4.4 4.4 0 0 1-2.4-2.4 6 6 0 0 1-.4-2.1C2.2 15.8 2 15.4 2 12s0-3.6.1-4.8a6 6 0 0 1 .4-2.1 4.4 4.4 0 0 1 2.4-2.4 6 6 0 0 1 2.1-.4C8.2 2.2 8.6 2 12 2z" />
                    </svg>
                </a>
                <a class="social-icon" href="https://www.youtube.com/@wasfah.jordan" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24" role="img" aria-hidden="true">
                        <path d="M21.6 7.2a2.6 2.6 0 0 0-1.8-1.8C18 5 12 5 12 5s-6 0-7.8.4A2.6 2.6 0 0 0 2.4 7.2 27 27 0 0 0 2 12a27 27 0 0 0 .4 4.8 2.6 2.6 0 0 0 1.8 1.8C6 19 12 19 12 19s6 0 7.8-.4a2.6 2.6 0 0 0 1.8-1.8A27 27 0 0 0 22 12a27 27 0 0 0-.4-4.8zM10 15.2V8.8l5.6 3.2z" />
                    </svg>
                </a>
            </div>
        </section>
    </main>
    <footer>
        <div class="footer-brand">
            <img src="{{ asset('image/logo.png') }}" alt="شعار Wasfah">
            <div>
                <strong>Wasfah Now</strong>
                <div>الاسم الجديد لصفحة الروابط الموحّدة.</div>
            </div>
        </div>
        <div>© {{ now()->year }} Wasfah · جميع الحقوق محفوظة</div>
    </footer>
</body>
</html>
