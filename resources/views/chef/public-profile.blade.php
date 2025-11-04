@extends('layouts.app')

@section('title', 'ملف الشيف ' . ($chef->name ?? ''))

@php
    $totalFollowers = max(0, (int) ($chef->instagram_followers ?? 0)) + max(0, (int) ($chef->youtube_followers ?? 0));
    $bio = $chef->chef_specialty_description
        ?: 'شيف مبدع يشارك وصفاته المميزة مع مجتمع وصفه.';
    $specialty = $chef->chef_specialty_area
        ? 'متخصص في ' . $chef->chef_specialty_area
        : 'عضو في مجتمع وصفه';

    $statsAverage = $stats['average_rating']
        ? number_format($stats['average_rating'], 1)
        : '—';
    $wasfahFollowers = max(
        0,
        (int) data_get(
            $chef,
            'followers_count',
            data_get(
                $chef,
                'wasfah_followers',
                data_get($chef, 'subscribers_count', data_get($chef, 'wasfah_subscribers', 0))
            )
        )
    );

    if ($wasfahFollowers === 0) {
        $maybeFollowersRelation = data_get($chef, 'followers');

        if (is_countable($maybeFollowersRelation)) {
            $wasfahFollowers = count($maybeFollowersRelation);
        }
    }

    $platformFollowers = $totalFollowers;
    $recipesCount = max(0, (int) $stats['recipes_count']);
@endphp

@push('styles')
    <style>
        .chef-profile-page {
            position: relative;
            min-height: 100vh;
            background: linear-gradient(180deg, rgba(255, 247, 237, 0.75), rgba(255, 255, 255, 0.9) 55%, #fdfcfb 100%);
            overflow-x: hidden;
            padding-block: clamp(3.5rem, 7vw, 6rem) clamp(4rem, 8vw, 6.5rem);
            --chef-primary: #f97316;
            --chef-primary-strong: #ea580c;
            --chef-neutral-900: #111827;
            --chef-neutral-700: #374151;
            --chef-neutral-500: #6b7280;
            --chef-neutral-200: #e5e7eb;
            --chef-card-shadow: 0 35px 60px -28px rgba(15, 23, 42, 0.22);
        }

        .chef-profile-page::before,
        .chef-profile-page::after {
            content: "";
            position: absolute;
            inset-inline-start: -170px;
            inset-block-start: -150px;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(234, 88, 12, 0.18), transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .chef-profile-page::after {
            inset-inline-start: unset;
            inset-inline-end: -140px;
            inset-block-start: 55%;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.14), transparent 72%);
        }

        .chef-profile-shell {
            position: relative;
            z-index: 1;
            max-width: 1180px;
            margin-inline: auto;
            padding-inline: clamp(1.25rem, 4vw, 2.75rem);
        }

        .chef-profile-hero {
            position: relative;
            border-radius: 2.75rem;
            padding: clamp(2.5rem, 5vw, 3.5rem);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.94), rgba(255, 248, 238, 0.98));
            border: 1px solid rgba(249, 115, 22, 0.18);
            box-shadow: var(--chef-card-shadow);
            overflow: hidden;
        }

        .chef-profile-hero::before,
        .chef-profile-hero::after {
            content: "";
            position: absolute;
            border-radius: 9999px;
            background: linear-gradient(160deg, rgba(249, 115, 22, 0.2), rgba(249, 115, 22, 0));
            pointer-events: none;
        }

        .chef-profile-hero::before {
            width: 220px;
            height: 220px;
            inset-inline-end: -60px;
            inset-block-start: -70px;
            opacity: 0.55;
        }

        .chef-profile-hero::after {
            width: 360px;
            height: 360px;
            inset-inline-start: -150px;
            inset-block-end: -200px;
            opacity: 0.35;
        }

        .chef-profile-hero__grid {
            position: relative;
            display: grid;
            gap: clamp(2rem, 4vw, 3.2rem);
            grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
            align-items: center;
        }

        .chef-profile-hero__avatar {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chef-profile-hero__avatar::before {
            content: "";
            position: absolute;
            inset: clamp(-1.8rem, -3vw, -1.3rem);
            border-radius: 50%;
            background: conic-gradient(from 90deg, rgba(249, 115, 22, 0.24), rgba(249, 115, 22, 0));
            z-index: 0;
        }

        .chef-profile-hero__avatar img {
            position: relative;
            width: clamp(115px, 18vw, 170px);
            height: clamp(115px, 18vw, 170px);
            border-radius: 50%;
            border: 5px solid #ffffff;
            object-fit: cover;
            box-shadow: 0 20px 34px -30px rgba(15, 23, 42, 0.5);
            z-index: 1;
        }

        .chef-profile-hero__details {
            display: flex;
            flex-direction: column;
            gap: clamp(1.5rem, 3vw, 2.25rem);
        }

        .chef-profile-hero__identity {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            align-items: flex-end;
            text-align: right;
        }

        .chef-profile-hero__identity h1 {
            margin: 0;
            font-size: clamp(1.8rem, 3.5vw, 2.4rem);
            font-weight: 800;
            color: var(--chef-neutral-900);
        }

        .chef-profile-hero__bio-block {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .chef-profile-hero__bio {
            margin: 0;
            color: var(--chef-neutral-500);
            font-size: clamp(1.05rem, 2.25vw, 1.18rem);
            line-height: 1.9;
        }

        .chef-profile-hero__subtitle {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--chef-primary-strong);
        }

        .chef-profile-hero__meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1.15rem;
        }

        .chef-meta-card {
            background: #ffffff;
            border-radius: 1.25rem;
            padding: 0.85rem 1.1rem;
            border: none;
            box-shadow: 0 16px 35px -32px rgba(15, 23, 42, 0.55);
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .chef-meta-card span {
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--chef-neutral-500);
            letter-spacing: 0.02em;
        }

        .chef-meta-card strong {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--chef-neutral-900);
            line-height: 1.1;
        }

        .chef-profile-hero__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
        }

        .chef-follow-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.85rem 2.3rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 1.05rem;
            color: #ffffff;
            background: linear-gradient(135deg, var(--chef-primary), var(--chef-primary-strong));
            border: none;
            cursor: pointer;
            box-shadow: 0 20px 45px rgba(249, 115, 22, 0.26);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .chef-follow-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 50px rgba(234, 88, 12, 0.28);
            filter: brightness(1.05);
        }

        .chef-follow-btn.is-following {
            background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
            box-shadow: 0 20px 45px rgba(29, 78, 216, 0.24);
        }

        .chef-profile-hero__social {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .chef-profile-hero__social li {
            margin: 0;
        }

        .chef-profile-hero__social .social-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.2rem;
            border-radius: 999px;
            border: 1px solid rgba(249, 115, 22, 0.18);
            color: var(--chef-primary-strong);
            background: rgba(249, 115, 22, 0.08);
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .chef-profile-hero__social .social-link:hover {
            background: rgba(249, 115, 22, 0.16);
            transform: translateY(-2px);
        }

        .chef-profile-tabs {
            margin-top: clamp(3rem, 7vw, 4.5rem);
        }

        .chef-tab-nav {
            display: inline-flex;
            gap: 0.6rem;
            padding: 0.4rem;
            border-radius: 999px;
            background: rgba(249, 115, 22, 0.08);
            border: 1px solid rgba(249, 115, 22, 0.1);
            margin-bottom: 2.2rem;
        }

        .chef-tab-btn {
            border: none;
            border-radius: 999px;
            padding: 0.85rem 1.75rem;
            font-weight: 700;
            font-size: 1rem;
            color: var(--chef-neutral-500);
            background: transparent;
            cursor: pointer;
            transition: color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .chef-tab-btn.is-active {
            color: var(--chef-neutral-900);
            background: #ffffff;
            box-shadow: 0 20px 45px rgba(249, 115, 22, 0.16);
        }

        .chef-tab-panel {
            display: none;
            animation: fadeIn 0.35s ease forwards;
        }

        .chef-tab-panel.is-active {
            display: block;
        }

        .chef-recipes-grid {
            display: grid;
            gap: 1.75rem;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        }

        .chef-recipe-card {
            position: relative;
            border-radius: 1.85rem;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid rgba(249, 115, 22, 0.1);
            box-shadow: 0 28px 55px -35px rgba(15, 23, 42, 0.45);
            display: flex;
            flex-direction: column;
            min-height: 100%;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .chef-recipe-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 32px 60px -32px rgba(249, 115, 22, 0.48);
        }

        .chef-recipe-card__cover {
            position: relative;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            background: #fff7ed;
        }

        .chef-recipe-card__cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .chef-recipe-card:hover .chef-recipe-card__cover img {
            transform: scale(1.05);
        }

        .chef-recipe-card__tag {
            position: absolute;
            inset-inline-end: 1.1rem;
            inset-block-start: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            background: rgba(17, 24, 39, 0.78);
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .chef-recipe-card__body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
            flex: 1;
        }

        .chef-recipe-card__title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--chef-neutral-900);
        }

        .chef-recipe-card__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
            align-items: center;
        }

        .chef-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 0.9rem;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--chef-neutral-500);
            background: rgba(249, 115, 22, 0.08);
        }

        .chef-recipe-card__footer {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chef-recipe-card__cta {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            color: var(--chef-primary-strong);
            text-decoration: none;
            transition: gap 0.2s ease, transform 0.2s ease;
        }

        .chef-recipe-card__cta:hover {
            gap: 0.7rem;
            transform: translateX(-2px);
        }

        .chef-empty-state {
            border-radius: 2rem;
            border: 1px dashed rgba(249, 115, 22, 0.24);
            padding: 3rem 2rem;
            background: rgba(255, 248, 238, 0.65);
            text-align: center;
            color: var(--chef-neutral-500);
            font-size: 1.05rem;
            font-weight: 600;
        }

        .chef-carousel-section {
            margin-top: clamp(3.5rem, 7vw, 5.2rem);
            padding: clamp(2.6rem, 5vw, 3.6rem);
            border-radius: 2.5rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.94), rgba(255, 243, 221, 0.88));
            border: 1px solid rgba(249, 115, 22, 0.12);
            box-shadow: 0 30px 60px -40px rgba(15, 23, 42, 0.42);
        }

        .chef-carousel-header {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            margin-bottom: 2.1rem;
        }

        .chef-carousel-header h2 {
            margin: 0;
            font-size: clamp(1.9rem, 3vw, 2.4rem);
            font-weight: 800;
            color: var(--chef-neutral-900);
        }

        .chef-carousel-header span {
            color: var(--chef-neutral-500);
            font-size: 1rem;
            line-height: 1.7;
        }

        .chef-popular-swiper {
            padding-bottom: 3.1rem;
        }

        .chef-popular-card {
            border-radius: 2rem;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid rgba(249, 115, 22, 0.12);
            box-shadow: 0 28px 55px -35px rgba(15, 23, 42, 0.4);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .chef-popular-card__cover {
            position: relative;
            aspect-ratio: 16 / 11;
            background: #fff1e6;
            overflow: hidden;
        }

        .chef-popular-card__cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .chef-popular-card:hover .chef-popular-card__cover img {
            transform: scale(1.04);
        }

        .chef-popular-card__body {
            padding: 1.6rem;
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
            flex: 1;
        }

        .chef-popular-card__title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--chef-neutral-900);
        }

        .chef-popular-card__stats {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .chef-carousel-pagination {
            position: static;
            margin-top: 1.5rem;
        }

        .chef-carousel-pagination .swiper-pagination-bullet {
            width: 10px;
            height: 10px;
            background: rgba(249, 115, 22, 0.35);
            opacity: 1;
        }

        .chef-carousel-pagination .swiper-pagination-bullet-active {
            background: var(--chef-primary-strong);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 1024px) {
            .chef-profile-hero__grid {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .chef-profile-hero__avatar::before {
                inset: clamp(-1.3rem, -3vw, -0.9rem);
            }

            .chef-profile-hero__meta {
                justify-items: center;
            }

            .chef-profile-hero__actions {
                justify-content: center;
            }

            .chef-profile-hero__social {
                justify-content: center;
            }
        }

        @media (max-width: 640px) {
            .chef-profile-page {
                padding-inline: 0.9rem;
            }

            .chef-profile-hero {
                padding: 1.75rem 1.35rem;
                border-radius: 2rem;
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(255, 248, 238, 0.98));
                border: 1px solid rgba(249, 115, 22, 0.18);
                color: var(--chef-neutral-900);
            }

            .chef-profile-hero__grid {
                gap: 1.15rem 1rem;
                grid-template-columns: minmax(0, 1fr) auto;
                grid-template-areas:
                    "stats avatar"
                    "name name"
                    "bio bio"
                    "actions actions"
                    "social social";
                align-items: flex-start;
            }

            .chef-profile-hero__details {
                display: contents;
            }

            .chef-profile-hero__avatar {
                grid-area: avatar;
                justify-self: end;
            }

            .chef-profile-hero__avatar img {
                width: 95px;
                height: 95px;
                border-width: 3px;
                box-shadow: 0 18px 26px -24px rgba(15, 23, 42, 0.85);
            }

            .chef-profile-hero__meta {
                grid-area: stats;
                direction: rtl;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0.85rem;
                align-items: stretch;
            }

            .chef-meta-card {
                padding: 0.75rem 0.85rem;
                background: #ffffff;
                border-radius: 1rem;
                text-align: right;
                box-shadow: 0 12px 30px -28px rgba(15, 23, 42, 0.25);
            }

            .chef-meta-card--rating {
                display: none;
            }

            .chef-meta-card span {
                font-size: 0.68rem;
                color: var(--chef-neutral-500);
            }

            .chef-meta-card strong {
                font-size: 1.15rem;
                color: var(--chef-neutral-900);
            }

            .chef-profile-hero__identity {
                grid-area: name;
                align-items: flex-end;
                text-align: right;
                gap: 0.45rem;
            }

            .chef-profile-hero__identity h1 {
                font-size: 1.2rem;
                letter-spacing: 0.01em;
                color: var(--chef-neutral-900);
            }

            .chef-profile-hero__subtitle {
                font-size: 0.82rem;
                color: var(--chef-primary-strong);
            }

            .chef-profile-hero__bio-block {
                grid-area: bio;
                text-align: right;
                gap: 0.35rem;
            }

            .chef-profile-hero__bio {
                font-size: 0.95rem;
                line-height: 1.6;
                max-width: 100%;
                color: var(--chef-neutral-500);
            }

            .chef-profile-hero__actions {
                grid-area: actions;
                flex-direction: column;
                align-items: stretch;
                gap: 0.8rem;
                width: 100%;
            }

            .chef-follow-btn {
                width: 100%;
                justify-content: center;
                padding: 0.9rem 1.1rem;
                font-size: 0.95rem;
                border-radius: 999px;
                box-shadow: 0 18px 30px -18px rgba(234, 88, 12, 0.25);
            }

            .chef-profile-hero__social {
                grid-area: social;
                width: 100%;
                justify-content: center;
            }

            .chef-profile-hero__social .social-link {
                flex: 1 1 auto;
                justify-content: center;
                padding-inline: 1rem;
                min-width: 0;
                background: #ffffff;
                border-color: rgba(249, 115, 22, 0.2);
                color: var(--chef-primary-strong);
                box-shadow: 0 12px 24px -20px rgba(15, 23, 42, 0.25);
            }

            .chef-profile-hero__social .social-link span.text-xs {
                color: var(--chef-primary-strong);
            }

            .chef-tab-btn {
                padding-inline: 1.2rem;
                font-size: 0.92rem;
            }

            .chef-recipe-card__body {
                padding: 1.2rem;
            }

            .chef-recipe-card__title {
                font-size: 1.05rem;
            }

            .chef-popular-card__body {
                padding: 1.25rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="chef-profile-page">
        <div class="chef-profile-shell">
            <section class="chef-profile-hero">
                <div class="chef-profile-hero__grid">
                    <div class="chef-profile-hero__avatar">
                        <img src="{{ $avatarUrl }}" alt="صورة الشيف {{ $chef->name }}">
                    </div>
                    <div class="chef-profile-hero__details">
                        <div class="chef-profile-hero__identity">
                            <h1>الشيف {{ $chef->name }}</h1>
                        </div>
                        <div class="chef-profile-hero__bio-block">
                            <p class="chef-profile-hero__bio">{{ $bio }}</p>
                            <span class="chef-profile-hero__subtitle">{{ $specialty }}</span>
                        </div>
                        <div class="chef-profile-hero__meta">
                            <div class="chef-meta-card chef-meta-card--followers">
                                <strong>{{ number_format($wasfahFollowers) }}</strong>
                                <span>عدد المشتركين</span>
                            </div>
                            <div class="chef-meta-card chef-meta-card--platforms">
                                <strong>{{ number_format($platformFollowers) }}</strong>
                                <span>مشتركو المنصات الأخرى</span>
                            </div>
                            <div class="chef-meta-card chef-meta-card--recipes">
                                <strong>{{ number_format($recipesCount) }}</strong>
                                <span>عدد الوصفات</span>
                            </div>
                            <div class="chef-meta-card chef-meta-card--rating">
                                <strong>{{ $statsAverage }}</strong>
                                <span>متوسط التقييم</span>
                            </div>
                        </div>
                        <div class="chef-profile-hero__actions">
                            @if (! $isOwner)
                                @auth
                                    <button type="button" class="chef-follow-btn" data-follow-button>
                                        <i class="fa-solid fa-plus"></i>
                                        متابعة
                                    </button>
                                @else
                                    <a href="{{ route('login') }}" class="chef-follow-btn">
                                        <i class="fa-solid fa-plus"></i>
                                        متابعة
                                    </a>
                                @endauth
                            @endif
                        </div>

                        @if ($socialLinks->isNotEmpty())
                            <ul class="chef-profile-hero__social">
                                @foreach ($socialLinks as $link)
                                    <li>
                                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener" class="social-link">
                                            <i class="{{ $link['icon'] }}"></i>
                                            <span>{{ $link['label'] }}</span>
                                            @if (! empty($link['followers']))
                                                <span class="text-xs font-semibold text-orange-500">
                                                    {{ number_format($link['followers']) }}
                                                </span>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </section>

            <section class="chef-profile-tabs">
                <div class="chef-tab-nav" role="tablist">
                    <button class="chef-tab-btn is-active" data-tab="public" type="button" role="tab" aria-selected="true">
                        الوصفات العامة
                    </button>
                    @if ($canViewExclusive)
                        <button class="chef-tab-btn" data-tab="exclusive" type="button" role="tab" aria-selected="false">
                            الوصفات الخاصة
                        </button>
                    @endif
                </div>

                <div class="chef-tab-panels">
                    <div class="chef-tab-panel is-active" data-panel="public" role="tabpanel">
                        @if ($publicRecipes->isEmpty())
                            <div class="chef-empty-state">
                                لا توجد وصفات عامة منشورة بعد. ترقب وصفات الشيف الجديدة قريباً!
                            </div>
                        @else
                            <div class="chef-recipes-grid">
                                @foreach ($publicRecipes as $recipe)
                                    <article class="chef-recipe-card">
                                        <a href="{{ route('recipe.show', ['recipe' => $recipe->slug]) }}" class="chef-recipe-card__cover">
                                            <img src="{{ $recipe->image_url ?? asset('image/Brownies.png') }}" alt="{{ $recipe->title }}">
                                            @if ($recipe->category)
                                                <span class="chef-recipe-card__tag">
                                                    <i class="fa-solid fa-tag"></i>
                                                    {{ $recipe->category->category_name ?? 'وصفة' }}
                                                </span>
                                            @endif
                                        </a>
                                        <div class="chef-recipe-card__body">
                                            <h3 class="chef-recipe-card__title">{{ $recipe->title }}</h3>
                                            <div class="chef-recipe-card__meta">
                                                <span class="chef-chip">
                                                    <i class="fa-solid fa-star text-amber-400"></i>
                                                    {{ $recipe->interactions_avg_rating ? number_format($recipe->interactions_avg_rating, 1) : 'لا يوجد تقييم بعد' }}
                                                </span>
                                                <span class="chef-chip">
                                                    <i class="fa-solid fa-bookmark"></i>
                                                    {{ number_format($recipe->saved_count ?? 0) }} حفظ
                                                </span>
                                                <span class="chef-chip">
                                                    <i class="fa-solid fa-heart"></i>
                                                    {{ number_format($recipe->rating_count ?? 0) }} إعجاب
                                                </span>
                                            </div>
                                            <div class="chef-recipe-card__footer">
                                                <a href="{{ route('recipe.show', ['recipe' => $recipe->slug]) }}" class="chef-recipe-card__cta">
                                                    عرض الوصفة
                                                    <i class="fa-solid fa-arrow-left"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if ($canViewExclusive)
                        <div class="chef-tab-panel" data-panel="exclusive" role="tabpanel">
                            @if ($exclusiveRecipes->isEmpty())
                                <div class="chef-empty-state">
                                    لم تضف وصفات خاصة بعد. شارك وصفاتك الحصرية هنا لتكون مرجعك الشخصي.
                                </div>
                            @else
                                <div class="chef-recipes-grid">
                                    @foreach ($exclusiveRecipes as $recipe)
                                        <article class="chef-recipe-card">
                                            <a href="{{ route('recipe.show', ['recipe' => $recipe->slug]) }}" class="chef-recipe-card__cover">
                                                <img src="{{ $recipe->image_url ?? asset('image/Brownies.png') }}" alt="{{ $recipe->title }}">
                                                <span class="chef-recipe-card__tag">
                                                    <i class="fa-solid fa-lock"></i>
                                                    وصفة خاصة
                                                </span>
                                            </a>
                                            <div class="chef-recipe-card__body">
                                                <h3 class="chef-recipe-card__title">{{ $recipe->title }}</h3>
                                                <div class="chef-recipe-card__meta">
                                                    <span class="chef-chip">
                                                        <i class="fa-solid fa-star text-amber-400"></i>
                                                        {{ $recipe->interactions_avg_rating ? number_format($recipe->interactions_avg_rating, 1) : 'لا يوجد تقييم بعد' }}
                                                    </span>
                                                    <span class="chef-chip">
                                                        <i class="fa-solid fa-book-open"></i>
                                                        خطوات تفصيلية مميزة
                                                    </span>
                                                    <span class="chef-chip">
                                                        <i class="fa-solid fa-shield-halved"></i>
                                                        مشاهدة خاصة
                                                    </span>
                                                </div>
                                                <div class="chef-recipe-card__footer">
                                                    <a href="{{ route('recipe.show', ['recipe' => $recipe->slug]) }}" class="chef-recipe-card__cta">
                                                        عرض الوصفة
                                                        <i class="fa-solid fa-arrow-left"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </section>

            @if ($popularRecipes->isNotEmpty())
                <section class="chef-carousel-section">
                    <div class="chef-carousel-header">
                        <h2>وصفات الشيف الأكثر مشاهدة</h2>
                        <span>استكشف أبرز الوصفات التي خطفت قلوب عشاق الطهي والمتابعين.</span>
                    </div>
                    <div class="swiper chef-popular-swiper">
                        <div class="swiper-wrapper">
                            @foreach ($popularRecipes as $recipe)
                                <div class="swiper-slide">
                                    <article class="chef-popular-card">
                                        <a href="{{ route('recipe.show', ['recipe' => $recipe->slug]) }}" class="chef-popular-card__cover">
                                            <img src="{{ $recipe->image_url ?? asset('image/Brownies.png') }}" alt="{{ $recipe->title }}">
                                        </a>
                                        <div class="chef-popular-card__body">
                                            <h3 class="chef-popular-card__title">{{ $recipe->title }}</h3>
                                            <div class="chef-popular-card__stats">
                                                <span class="chef-chip">
                                                    <i class="fa-solid fa-star text-amber-400"></i>
                                                    {{ $recipe->interactions_avg_rating ? number_format($recipe->interactions_avg_rating, 1) : '—' }}
                                                </span>
                                                <span class="chef-chip">
                                                    <i class="fa-solid fa-bookmark"></i>
                                                    {{ number_format($recipe->saved_count ?? 0) }} حفظ
                                                </span>
                                                <span class="chef-chip">
                                                    <i class="fa-solid fa-heart"></i>
                                                    {{ number_format($recipe->rating_count ?? 0) }} إعجاب
                                                </span>
                                            </div>
                                            <a href="{{ route('recipe.show', ['recipe' => $recipe->slug]) }}" class="chef-recipe-card__cta">
                                                اكتشف التفاصيل
                                                <i class="fa-solid fa-arrow-left"></i>
                                            </a>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                        <div class="chef-carousel-pagination swiper-pagination"></div>
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabButtons = document.querySelectorAll('.chef-tab-btn');
            const tabPanels = document.querySelectorAll('.chef-tab-panel');

            tabButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const target = button.getAttribute('data-tab');

                    tabButtons.forEach((btn) => {
                        const isActive = btn === button;
                        btn.classList.toggle('is-active', isActive);
                        btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    });

                    tabPanels.forEach((panel) => {
                        panel.classList.toggle('is-active', panel.getAttribute('data-panel') === target);
                    });
                });
            });

            const followButton = document.querySelector('[data-follow-button]');
            if (followButton) {
                followButton.addEventListener('click', () => {
                    const isFollowing = followButton.classList.toggle('is-following');
                    followButton.innerHTML = isFollowing
                        ? '<i class="fa-solid fa-check"></i> تمّت المتابعة'
                        : '<i class="fa-solid fa-plus"></i> متابعة';
                });
            }

            const swiperElement = document.querySelector('.chef-popular-swiper');
            if (swiperElement && typeof Swiper !== 'undefined') {
                new Swiper(swiperElement, {
                    slidesPerView: 1.1,
                    spaceBetween: 20,
                    breakpoints: {
                        640: { slidesPerView: 1.5, spaceBetween: 22 },
                        768: { slidesPerView: 2.1, spaceBetween: 24 },
                        1024: { slidesPerView: 2.7, spaceBetween: 26 },
                        1280: { slidesPerView: 3.2, spaceBetween: 28 }
                    },
                    loop: false,
                    grabCursor: true,
                    keyboard: { enabled: true },
                    pagination: {
                        el: '.chef-carousel-pagination',
                        clickable: true
                    }
                });
            }
        });
    </script>
@endpush
