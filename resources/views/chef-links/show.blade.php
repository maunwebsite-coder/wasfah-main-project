<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->headline ?? ('روابط الشيف ' . ($page->user->name ?? '')) }}</title>
    <meta name="description" content="{{ $page->subheadline ?? 'كل الروابط المهمة للشيف في مكان واحد.' }}">
    <meta property="og:title" content="{{ $page->headline ?? 'Wasfah Links' }}">
    <meta property="og:description" content="{{ $page->subheadline ?? 'تابع أحدث الروابط الخاصة بالشيف.' }}">
    <meta property="og:image" content="{{ $page->avatar_url ?? asset('image/logo.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="theme-color" content="{{ $accentColor }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer" />
    <style>
        :root {
            --accent: {{ $accentColor }};
            --accent-soft: {{ $accentColor }}1a;
            --solid-bg: #fff7ed;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --card-bg: rgba(255, 255, 255, 0.96);
            --border: rgba(0, 0, 0, 0.05);
            font-family: 'Tajawal', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 32px 16px 48px;
            background: linear-gradient(180deg, rgba(255, 247, 237, 0.9) 0%, #ffffff 55%, #ffffff 100%);
            color: var(--text-main);
        }

        .page-card {
            width: min(420px, 100%);
            background: var(--card-bg);
            border-radius: 36px;
            border: 1px solid var(--border);
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.12);
            padding: 32px 28px 36px;
            position: relative;
            overflow: hidden;
        }

        .page-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(110% 130% at 50% -10%, rgba(249, 115, 22, 0.16) 0%, transparent 65%);
            pointer-events: none;
        }

        header {
            text-align: center;
            margin-bottom: 24px;
            position: relative;
            z-index: 2;
        }

        .hero-image {
            width: 140px;
            height: 140px;
            border-radius: 32px;
            object-fit: cover;
            display: block;
            margin: 0 auto 16px;
            box-shadow: 0 20px 45px rgba(249, 115, 22, 0.18);
            border: 4px solid rgba(249, 115, 22, 0.15);
            background: #fff;
        }

        h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        p.subtitle {
            margin: 8px 0 0;
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .bio {
            margin: 18px auto 0;
            padding: 14px 18px;
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            background: rgba(255, 255, 255, 0.8);
            color: var(--text-muted);
            font-size: 0.9rem;
            line-height: 1.7;
        }

        .cta-button {
            margin: 20px 0 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 52px;
            padding: 0 28px;
            border-radius: 999px;
            border: none;
            background: var(--accent);
            color: #fff;
            font-weight: 600;
            font-size: 0.98rem;
            text-decoration: none;
            box-shadow: 0 18px 40px rgba(249, 115, 22, 0.25);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 50px rgba(249, 115, 22, 0.3);
        }

        .links-stack {
            display: flex;
            flex-direction: column;
            gap: 14px;
            position: relative;
            z-index: 2;
        }

        .link-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px 20px;
            border-radius: 22px;
            border: 1px solid rgba(249, 115, 22, 0.12);
            background: rgba(255, 255, 255, 0.95);
            color: inherit;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .link-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 28px rgba(249, 115, 22, 0.12);
        }

        .link-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: var(--accent-soft);
            color: var(--accent);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .link-text {
            flex: 1;
        }

        .link-title {
            font-weight: 600;
            font-size: 1rem;
            margin: 0;
        }

        .link-subtitle {
            margin: 4px 0 0;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        footer {
            margin-top: 28px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
            position: relative;
            z-index: 2;
        }

        footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        .manage-bar {
            position: fixed;
            inset-inline-end: 16px;
            inset-block-end: 16px;
            background: var(--accent);
            color: #fff;
            border-radius: 999px;
            padding: 12px 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            box-shadow: 0 18px 40px rgba(249, 115, 22, 0.25);
        }

        .manage-bar:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <main class="page-card">
        <header>
            <img src="{{ $page->avatar_url ?? asset('image/logo.png') }}" alt="صورة {{ $page->user->name ?? 'الشيف' }}" class="hero-image">
            <h1>{{ $page->headline ?? ('روابط الشيف ' . ($page->user->name ?? '')) }}</h1>
            @if ($page->subheadline)
                <p class="subtitle">{{ $page->subheadline }}</p>
            @endif
            @if ($page->bio)
                <div class="bio">{{ $page->bio }}</div>
            @endif
            @if ($page->cta_label && $page->cta_url)
                <a class="cta-button" href="{{ $page->cta_url }}" target="_blank" rel="noopener">
                    <i class="fas fa-arrow-up-right-from-square"></i>
                    {{ $page->cta_label }}
                </a>
            @endif
        </header>

        <section class="links-stack">
            @forelse ($page->items as $item)
                <a href="{{ $item->url }}" target="_blank" rel="noopener" class="link-card">
                    @if ($item->icon)
                        <span class="link-icon">
                            <i class="{{ $item->icon }}"></i>
                        </span>
                    @endif
                    <span class="link-text">
                        <span class="link-title">{{ $item->title }}</span>
                        @if ($item->subtitle)
                            <span class="link-subtitle">{{ $item->subtitle }}</span>
                        @endif
                    </span>
                    <i class="fas fa-angle-left" style="color: var(--accent);"></i>
                </a>
            @empty
                <div class="link-card" style="justify-content: center; background: rgba(249, 115, 22, 0.06); border-style: dashed;">
                    <span class="link-text" style="text-align: center;">
                        <span class="link-title">لا توجد روابط متاحة حالياً</span>
                        <span class="link-subtitle">سيقوم الشيف بتحديث الصفحة قريباً.</span>
                    </span>
                </div>
            @endforelse
        </section>

        <footer>
            <p>صفحة روابط Wasfah powered by <a href="{{ url('/') }}">Wasfah</a></p>
        </footer>
    </main>

    @auth
        @if (auth()->id() === $page->user_id || auth()->user()->isAdmin())
            <a href="{{ route('chef.links.edit') }}" class="manage-bar">
                <i class="fas fa-pen"></i>
                تعديل صفحتي
            </a>
        @endif
    @endauth
</body>
</html>
