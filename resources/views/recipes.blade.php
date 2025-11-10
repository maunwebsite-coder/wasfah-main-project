@extends('layouts.app')

@section('title', __('recipes.meta.title'))

@push('styles')
<style>
    .recipes-page {
        background: #f9fafb;
    }
    .recipes-hero {
        padding: clamp(3rem, 6vw, 4.25rem) 0 clamp(2rem, 5vw, 3.5rem);
        background: linear-gradient(135deg, #fff7ed 0%, #fde68a 65%, #ffffff 100%);
        border-bottom: 1px solid rgba(253, 224, 71, 0.45);
    }
    .recipes-hero__content {
        display: grid;
        gap: clamp(2rem, 4vw, 3rem);
    }
    .recipes-hero__intro {
        display: grid;
        gap: 1.25rem;
        text-align: center;
        max-width: 48rem;
        margin-inline: auto;
        color: #b45309;
    }
    .recipes-hero__badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-inline: auto;
        padding: 0.55rem 1.2rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.7);
        color: #b45309;
        font-weight: 600;
        font-size: 0.95rem;
        border: 1px solid rgba(250, 204, 21, 0.4);
    }
    .recipes-hero__title {
        font-size: clamp(2.25rem, 5.5vw, 3.2rem);
        font-weight: 800;
        color: #7c2d12;
        line-height: 1.25;
    }
    .recipes-hero__subtitle {
        font-size: clamp(1rem, 2.7vw, 1.2rem);
        color: #92400e;
        line-height: 1.8;
    }
    .recipes-hero__meta {
        display: inline-flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.6rem 1rem;
        margin-top: 0.5rem;
        font-size: 0.9rem;
        color: #a16207;
    }
    .recipes-hero__meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.35rem 0.9rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(252, 211, 77, 0.45);
    }
    .recipes-hero__stats {
        display: grid;
        gap: 0.75rem;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
    .recipes-stat {
        background: #ffffff;
        border-radius: 1rem;
        padding: 1rem 1.1rem;
        border: 1px solid rgba(254, 215, 170, 0.6);
        box-shadow: 0 8px 14px rgba(250, 204, 21, 0.1);
        display: grid;
        gap: 0.3rem;
        color: #7c2d12;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .recipes-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 26px rgba(250, 204, 21, 0.16);
    }
    .recipes-stat__icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: rgba(253, 224, 71, 0.2);
        color: #ca8a04;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }
    .recipes-stat__body {
        display: grid;
        gap: 0.25rem;
    }
    .recipes-stat__label {
        margin: 0;
        font-size: 0.8rem;
        font-weight: 700;
        color: #a16207;
    }
    .recipes-stat__value {
        font-size: 1.3rem;
        font-weight: 800;
        color: #7c2d12;
    }
    .recipes-stat__hint {
        margin: 0;
        font-size: 0.75rem;
        color: #b45309;
    }
    @media (max-width: 1024px) {
        .recipes-hero__stats {
            display: flex;
            gap: 0.75rem;
            overflow-x: auto;
            padding-bottom: 0.6rem;
            -webkit-overflow-scrolling: touch;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
            touch-action: pan-x;
        }
        .recipes-hero__stats::-webkit-scrollbar {
            display: none;
        }
        .recipes-stat {
            flex: 0 0 auto;
            min-width: min(220px, 72vw);
            scroll-snap-align: start;
        }
    }
    .recipes-filter-section {
        margin-top: -2rem;
    }
    .recipes-filter-card {
        background: linear-gradient(180deg, #ffffff 0%, #fff7ed 100%);
        border-radius: 1.75rem;
        padding: clamp(1.2rem, 3vw, 2rem);
        box-shadow: 0 28px 60px rgba(249, 115, 22, 0.18);
        border: 1px solid rgba(249, 115, 22, 0.16);
        max-width: 920px;
        margin-inline: auto;
    }
    .recipes-filter-form {
        display: grid;
        gap: 1.15rem;
    }
    .recipes-filter-grid {
        display: flex;
        gap: 0.85rem;
        align-items: stretch;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 0.5rem;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        touch-action: pan-x;
    }
    .recipes-filter-grid::-webkit-scrollbar {
        display: none;
    }
    .recipes-field {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 0 0 auto;
        min-width: 200px;
    }
    .recipes-field--search {
        flex: 1 1 360px;
        min-width: min(360px, 65vw);
    }
    .recipes-field--select {
        flex: 0 0 220px;
        min-width: 220px;
    }
    .recipes-field__label {
        display: inline-flex;
        align-items: center;
        font-weight: 700;
        font-size: 0.85rem;
        color: #b45309;
        letter-spacing: 0.01em;
        white-space: nowrap;
        margin: 0;
    }
    .recipes-input,
    .recipes-select {
        width: 100%;
        border-radius: 1rem;
        border: 1px solid rgba(148, 163, 184, 0.35);
        padding: 0.85rem 1rem;
        font-size: 0.95rem;
        background-color: #ffffff;
        color: #1f2937;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .recipes-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.41.59 6 5.17 10.59.59 12 2l-6 6-6-6L1.41.59Z' fill='%23f97316'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: left 1rem center;
        background-size: 12px 8px;
        padding-inline-end: 2.5rem;
    }
    [dir="rtl"] .recipes-select {
        background-position: right 1rem center;
        padding-inline-start: 2.5rem;
        padding-inline-end: 1rem;
    }
    .recipes-input:focus,
    .recipes-select:focus {
        outline: none;
        border-color: rgba(249, 115, 22, 0.6);
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.14);
    }
    .recipes-field > .recipes-search,
    .recipes-field > .recipes-select {
        flex: 1 1 auto;
    }
    .recipes-search {
        position: relative;
        display: block;
        width: 100%;
    }
    .recipes-search i {
        position: absolute;
        inset-inline-end: 1.1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #f97316;
        font-size: 1rem;
        pointer-events: none;
    }
    [dir="rtl"] .recipes-search i {
        inset-inline-start: 1.1rem;
        inset-inline-end: auto;
    }
    .recipes-search .recipes-input {
        padding-inline-end: 3rem;
    }
    [dir="rtl"] .recipes-search .recipes-input {
        padding-inline-start: 3rem;
        padding-inline-end: 1rem;
    }
    .recipes-actions {
        display: flex;
        flex-wrap: nowrap;
        gap: 0.75rem;
        align-items: center;
        justify-content: flex-start;
        flex: 0 0 auto;
        white-space: nowrap;
    }
    .recipes-submit {
        border: none;
        border-radius: 999px;
        background: linear-gradient(135deg, #f97316, #f59e0b);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 0.9rem 1.8rem;
        box-shadow: 0 16px 32px rgba(249, 115, 22, 0.24);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }
    .recipes-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 20px 40px rgba(249, 115, 22, 0.3);
    }
    .recipes-reset {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        color: #f97316;
        font-weight: 600;
        font-size: 0.9rem;
        background: rgba(249, 115, 22, 0.08);
        padding: 0.75rem 1.3rem;
        border-radius: 999px;
        transition: background 0.2s ease, transform 0.2s ease;
    }
    .recipes-reset:hover {
        background: rgba(249, 115, 22, 0.16);
        transform: translateY(-1px);
    }
    .recipes-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        margin-top: 1rem;
        align-items: center;
        color: #475569;
        font-size: 0.9rem;
    }
    .recipes-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        border-radius: 999px;
        padding: 0.45rem 0.9rem;
        background: #ffffff;
        border: 1px solid rgba(249, 115, 22, 0.25);
        color: #f97316;
        font-weight: 600;
    }
    .recipes-list {
        padding: clamp(3rem, 6vw, 4.5rem) 0;
    }
    .recipes-grid {
        display: grid;
        gap: 1.75rem;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    }
    .recipe-card {
        display: flex;
        flex-direction: column;
        border-radius: 1.5rem;
        overflow: hidden;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.94) 0%, #ffffff 100%);
        border: 1px solid rgba(226, 232, 240, 0.75);
        box-shadow: 0 26px 52px rgba(15, 23, 42, 0.1);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
        isolation: isolate;
    }
    .recipe-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(249, 115, 22, 0.08) 0%, transparent 55%);
        opacity: 0;
        transition: opacity 0.25s ease;
        z-index: -1;
    }
    .recipe-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 36px 60px rgba(15, 23, 42, 0.16);
    }
    .recipe-card:hover::before {
        opacity: 1;
    }
    .recipe-card__media {
        position: relative;
        aspect-ratio: 4 / 3;
        background: #f3f4f6;
        overflow: hidden;
    }
    .recipe-card__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }
    .recipe-card:hover .recipe-card__media img {
        transform: scale(1.05);
    }
    .recipe-card__badge {
        position: absolute;
        inset-inline-end: 1rem;
        top: 1rem;
        border-radius: 999px;
        background: rgba(249, 115, 22, 0.95);
        color: #fff;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4rem 0.9rem;
        box-shadow: 0 14px 24px rgba(249, 115, 22, 0.25);
    }
    .recipe-card__content {
        display: grid;
        gap: 1rem;
        padding: 1.5rem;
        flex: 1;
    }
    .recipe-card__title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.5;
        min-height: 3.3rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .recipe-card__excerpt {
        color: #475569;
        font-size: 0.95rem;
        line-height: 1.75;
        min-height: 4.9rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .recipe-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        row-gap: 0.5rem;
        font-size: 0.85rem;
        color: #64748b;
    }
    .recipe-card__meta span {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.45rem 0.8rem;
        background: rgba(15, 23, 42, 0.05);
        font-weight: 600;
    }
    .recipe-card__stats {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid rgba(226, 232, 240, 0.8);
        padding-top: 0.75rem;
    }
    .recipe-card__rating {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #fb923c;
        font-weight: 600;
    }
    .recipe-card__rating .stars {
        display: inline-flex;
        gap: 0.2rem;
        color: #fbbf24;
    }
    .recipe-card__rating .stars .empty-rating {
        color: #e2e8f0;
    }
    .recipe-card__actions {
        margin-top: auto;
    }
    .recipe-card__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        border-radius: 1rem;
        padding: 0.9rem 1rem;
        font-weight: 700;
        font-size: 0.95rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .recipe-card__btn--primary {
        background: linear-gradient(135deg, #f97316, #f59e0b);
        color: #ffffff;
        box-shadow: 0 14px 28px rgba(249, 115, 22, 0.28);
    }
    .recipe-card__btn--primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 36px rgba(249, 115, 22, 0.32);
    }
    .recipe-card__btn--disabled {
        background: #fde68a;
        color: #92400e;
        cursor: not-allowed;
    }
    .recipe-empty {
        background: #ffffff;
        border-radius: 2rem;
        padding: clamp(3rem, 6vw, 4.5rem);
        text-align: center;
        box-shadow: 0 32px 64px rgba(15, 23, 42, 0.12);
    }
    .recipe-empty__icon {
        width: 110px;
        height: 110px;
        margin: 0 auto 1.75rem;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(249, 115, 22, 0.2), rgba(249, 115, 22, 0.05));
        display: grid;
        place-items: center;
        color: #f97316;
        font-size: 2.6rem;
    }
    .recipe-empty__title {
        font-size: 1.85rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 0.75rem;
    }
    .recipe-empty__subtitle {
        max-width: 36rem;
        margin: 0 auto 2rem;
        color: #64748b;
        line-height: 1.9;
        font-size: 1rem;
    }
    .recipes-empty-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        border-radius: 999px;
        padding: 0.9rem 1.7rem;
        background: linear-gradient(135deg, #f97316, #f59e0b);
        color: #ffffff;
        font-weight: 700;
        box-shadow: 0 18px 36px rgba(249, 115, 22, 0.26);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .recipes-empty-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 22px 44px rgba(249, 115, 22, 0.32);
    }
    @media (max-width: 768px) {
        .recipes-filter-section {
            margin-top: -1.5rem;
        }
        .recipes-actions {
            justify-content: stretch;
        }
        .recipes-submit,
        .recipes-reset {
            width: 100%;
            justify-content: center;
        }
    }
    @media (max-width: 640px) {
        .recipes-hero__stats {
            gap: 0.6rem;
        }
        .recipes-stat {
            padding: 0.75rem 0.9rem;
            border-radius: 0.85rem;
            gap: 0.25rem;
        }
        .recipes-stat__icon {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
        }
        .recipes-stat__label {
            font-size: 0.7rem;
        }
        .recipes-stat__value {
            font-size: 1.15rem;
        }
        .recipes-stat__hint {
            font-size: 0.68rem;
        }
        .recipes-grid {
            gap: 1.25rem;
        }
        .recipe-card {
            border-radius: 1.1rem;
            box-shadow: 0 18px 32px rgba(15, 23, 42, 0.1);
        }
        .recipe-card__media {
            aspect-ratio: 3 / 2;
        }
        .recipe-card__badge {
            inset-inline-end: 0.75rem;
            top: 0.75rem;
            font-size: 0.68rem;
            padding: 0.35rem 0.75rem;
        }
        .recipe-card__content {
            gap: 0.75rem;
            padding: 1rem;
        }
        .recipe-card__title {
            font-size: 0.95rem;
            min-height: 2.6rem;
        }
        .recipe-card__excerpt {
            font-size: 0.8rem;
            line-height: 1.6;
            min-height: 3.6rem;
        }
        .recipe-card__meta {
            font-size: 0.75rem;
            gap: 0.4rem;
        }
        .recipe-card__meta span {
            padding: 0.3rem 0.6rem;
        }
        .recipe-card__stats {
            padding-top: 0.6rem;
        }
        .recipe-card__rating {
            gap: 0.4rem;
            font-size: 0.85rem;
        }
        .recipe-card__rating .stars {
            gap: 0.15rem;
        }
        .recipe-card__btn {
            padding: 0.7rem 0.85rem;
            font-size: 0.82rem;
            border-radius: 0.85rem;
        }
    }
    @media (max-width: 540px) {
        .recipes-hero__meta span {
            width: 100%;
            justify-content: center;
        }
        .recipes-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
    @media (max-width: 480px) {
        .recipes-page .container {
            padding-inline: 0.75rem !important;
        }
        .recipes-hero {
            padding: 1.75rem 0 1.3rem;
        }
        .recipes-hero__content {
            gap: 1.35rem;
        }
        .recipes-hero__intro {
            gap: 1rem;
        }
        .recipes-hero__title {
            font-size: clamp(1.6rem, 7vw, 1.9rem);
        }
        .recipes-hero__subtitle {
            font-size: 0.85rem;
            line-height: 1.6;
        }
        .recipes-hero__stats {
            display: flex;
            gap: 0.45rem;
            overflow-x: auto;
            padding-bottom: 0.35rem;
            margin: 0.35rem auto 0;
            max-width: 100%;
            -webkit-overflow-scrolling: touch;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
        }
        .recipes-hero__stats::-webkit-scrollbar {
            display: none;
        }
        .recipes-stat {
            flex: 0 0 auto;
            min-width: 118px;
            padding: 0.4rem 0.5rem;
            border-radius: 0.7rem;
            gap: 0.16rem;
            scroll-snap-align: start;
        }
        .recipes-stat__icon {
            width: 16px;
            height: 16px;
            font-size: 0.52rem;
        }
        .recipes-stat__label {
            font-size: 0.5rem;
        }
        .recipes-stat__value {
            font-size: 0.78rem;
        }
        .recipes-stat__hint {
            font-size: 0.48rem;
        }
        .recipes-list {
            padding: 1.5rem 0;
        }
        .recipes-grid {
            gap: 0.9rem;
        }
        .recipe-card {
            border-radius: 0.8rem;
            box-shadow: 0 14px 26px rgba(15, 23, 42, 0.09);
        }
        .recipe-card__media {
            aspect-ratio: 5 / 4;
        }
        .recipe-card__badge {
            inset-inline-end: 0.6rem;
            top: 0.6rem;
            font-size: 0.62rem;
            padding: 0.3rem 0.6rem;
        }
        .recipe-card__content {
            gap: 0.55rem;
            padding: 0.75rem;
        }
        .recipe-card__title {
            font-size: 0.82rem;
            min-height: 2rem;
        }
        .recipe-card__excerpt {
            font-size: 0.7rem;
            line-height: 1.5;
            min-height: 2.2rem;
        }
        .recipe-card__meta {
            font-size: 0.65rem;
            gap: 0.35rem;
            row-gap: 0.3rem;
        }
        .recipe-card__meta span {
            padding: 0.25rem 0.45rem;
        }
        .recipe-card__stats {
            padding-top: 0.45rem;
        }
        .recipe-card__rating {
            gap: 0.3rem;
            font-size: 0.78rem;
        }
        .recipe-card__btn {
            padding: 0.6rem 0.75rem;
            font-size: 0.72rem;
            border-radius: 0.75rem;
        }
        .recipes-filter-card {
            padding: 1rem;
            border-radius: 1.25rem;
        }
        .recipes-field__label {
            font-size: 0.78rem;
        }
        .recipes-input,
        .recipes-select {
            padding: 0.7rem 0.85rem;
            font-size: 0.85rem;
            border-radius: 0.8rem;
        }
    }
    @media (max-width: 360px) {
        .recipes-hero__title {
            font-size: clamp(1.4rem, 7.5vw, 1.6rem);
        }
        .recipes-hero__subtitle {
            font-size: 0.8rem;
        }
        .recipes-hero__stats {
            gap: 0.4rem;
            margin-top: 0.3rem;
        }
        .recipes-stat__icon {
            width: 15px;
            height: 15px;
            font-size: 0.5rem;
        }
        .recipes-stat__value {
            font-size: 0.74rem;
        }
        .recipes-stat__hint {
            display: none;
        }
        .recipes-stat {
            min-width: 108px;
        }
        .recipes-grid {
            gap: 0.75rem;
        }
        .recipe-card {
            border-radius: 0.7rem;
        }
        .recipe-card__content {
            padding: 0.65rem;
            gap: 0.5rem;
        }
        .recipe-card__title {
            font-size: 0.78rem;
            min-height: 1.8rem;
        }
        .recipe-card__excerpt {
            font-size: 0.66rem;
            min-height: 1.8rem;
            -webkit-line-clamp: 2;
        }
        .recipe-card__meta {
            font-size: 0.6rem;
        }
        .recipe-card__btn {
            padding: 0.55rem 0.7rem;
            font-size: 0.68rem;
            border-radius: 0.65rem;
        }
    }
</style>
@endpush

@section('content')
@php
    $sortOptions = [
        'created_at' => __('recipes.sort.created_at'),
        'rating' => __('recipes.sort.rating'),
        'saved' => __('recipes.sort.saved'),
    ];
    $difficultyLabels = [
        'easy' => __('recipes.difficulty.easy'),
        'medium' => __('recipes.difficulty.medium'),
        'hard' => __('recipes.difficulty.hard'),
    ];

    $activeFilters = collect([
        'search' => request('search'),
        'category' => request('category'),
    ])->filter(fn ($value) => filled($value));

    $firstItem = $recipes->firstItem();
    $lastItem = $recipes->lastItem();
    $currentSort = request('sort', 'created_at');

    $filterCount = $activeFilters->count();
    if ($currentSort !== 'created_at') {
        $filterCount++;
        $activeFilters->put('sort', $currentSort);
    }

    $limitedSearchTerm = request('search')
        ? \Illuminate\Support\Str::limit(request('search'), 18)
        : null;

    $heroBadge = __('recipes.hero.badge.default');
    if ($selectedCategory = $categories->firstWhere('category_id', (int) request('category'))) {
        $heroBadge = __('recipes.hero.badge.category', ['category' => $selectedCategory->name]);
    } elseif (request('search')) {
        $heroBadge = __('recipes.hero.badge.search', ['term' => request('search')]);
    }

    $heroSubtitle = __('recipes.hero.subtitle.default');
    if ($selectedCategory) {
        $heroSubtitle = __('recipes.hero.subtitle.category', ['category' => $selectedCategory->name]);
    } elseif (request('search')) {
        $heroSubtitle = __('recipes.hero.subtitle.search', ['term' => request('search')]);
    }

    $locale = app()->getLocale() ?? 'ar';
    $isRtl = isset($isRtl) ? $isRtl : $locale === 'ar';
    $latestRecipe = $recipes->first();
    $latestUpdated = $latestRecipe && $latestRecipe->created_at
        ? $latestRecipe->created_at->locale($locale)->diffForHumans(null, null, false, 2)
        : __('recipes.hero.latest_unavailable');

    $heroStats = [
        [
            'icon' => 'fa-book-open',
            'label' => __('recipes.stats.total.label'),
            'value' => number_format($recipes->total()),
            'hint' => __('recipes.stats.total.hint'),
        ],
        [
            'icon' => 'fa-layer-group',
            'label' => __('recipes.stats.current.label'),
            'value' => $recipes->count(),
            'hint' => __('recipes.stats.current.hint', [
                'first' => $firstItem ?? 0,
                'last' => $lastItem ?? 0,
            ]),
        ],
        [
            'icon' => 'fa-sliders-h',
            'label' => __('recipes.stats.filters.label'),
            'value' => $filterCount,
            'hint' => __('recipes.stats.filters.hint.' . ($filterCount ? 'active' : 'default')),
        ],
        [
            'icon' => 'fa-history',
            'label' => __('recipes.stats.latest.label'),
            'value' => $latestUpdated,
            'hint' => __('recipes.stats.latest.hint'),
        ],
    ];
@endphp
<div class="recipes-page min-h-screen pb-16">
    <section class="recipes-hero">
        <div class="container mx-auto px-4">
            <div class="recipes-hero__content">
                <div class="recipes-hero__intro">
                    <span class="recipes-hero__badge">
                        <i class="fas fa-crown"></i>
                        {{ $heroBadge }}
                    </span>
                    <h1 class="recipes-hero__title">{{ __('recipes.hero.title') }}</h1>
                    <p class="recipes-hero__subtitle">{{ $heroSubtitle }}</p>
                @if(request('search'))
                <div class="recipes-hero__meta">
                        <span>
                            <i class="fas fa-search"></i>
                            {{ __('recipes.hero.meta_search', ['term' => $limitedSearchTerm ?? request('search')]) }}
                        </span>
                </div>
                @endif
                </div>
                <div class="recipes-hero__stats">
                    @foreach($heroStats as $stat)
                        <article class="recipes-stat">
                            <span class="recipes-stat__icon">
                                <i class="fas {{ $stat['icon'] }}"></i>
                            </span>
                            <div class="recipes-stat__body">
                                <p class="recipes-stat__label">{{ $stat['label'] }}</p>
                                <div class="recipes-stat__value">{{ $stat['value'] }}</div>
                                <p class="recipes-stat__hint">{{ $stat['hint'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="recipes-filter-section">
        <div class="container mx-auto px-4">
            <div class="recipes-filter-card">
                <form method="GET" action="{{ route('recipes') }}" class="recipes-filter-form" id="recipes-filter-form" autocomplete="off">
                    <div class="recipes-filter-grid">
                        <label class="recipes-field recipes-field--search">
                            <span class="recipes-field__label">{{ __('recipes.filters.search_label') }}</span>
                            <div class="recipes-search">
                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    class="recipes-input"
                                    placeholder="{{ __('recipes.filters.search_placeholder') }}"
                                    dir="{{ $isRtl ? 'rtl' : 'ltr' }}"
                                >
                                <i class="fas fa-search"></i>
                            </div>
                        </label>
                        <label class="recipes-field recipes-field--select">
                            <span class="recipes-field__label">{{ __('recipes.filters.category_label') }}</span>
                            <select name="category" class="recipes-select" onchange="this.form.submit()">
                                <option value="">{{ __('recipes.filters.all_categories') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}" {{ (string) request('category') === (string) $category->category_id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <label class="recipes-field recipes-field--select">
                            <span class="recipes-field__label">{{ __('recipes.filters.sort_label') }}</span>
                            <select name="sort" class="recipes-select" onchange="this.form.submit()">
                                @foreach($sortOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $currentSort === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <div class="recipes-actions">
                            <button type="submit" class="recipes-submit">
                                <i class="fas fa-filter ml-1"></i>
                                {{ __('recipes.filters.submit') }}
                            </button>
                            @if($filterCount > 0)
                                <a href="{{ route('recipes') }}" class="recipes-reset">
                                    <i class="fas fa-rotate-right"></i>
                                    {{ __('recipes.filters.reset') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
                @if($filterCount > 0)
                    <div class="recipes-chips">
                        <span class="font-semibold text-slate-600">
                            <i class="fas fa-sliders-h text-orange-500 ml-1"></i>
                            {{ __('recipes.filters.active_label') }}
                        </span>
                        @if(request('search'))
                            <span class="recipes-chip">
                                <i class="fas fa-search text-xs"></i>
                                {{ __('recipes.filters.chip_search', ['term' => $limitedSearchTerm ?? request('search')]) }}
                            </span>
                        @endif
                        @if($selectedCategory)
                            <span class="recipes-chip">
                                <i class="fas fa-layer-group text-xs"></i>
                                {{ __('recipes.filters.chip_category', ['category' => $selectedCategory->name]) }}
                            </span>
                        @endif
                        @if($currentSort !== 'created_at')
                            <span class="recipes-chip">
                                <i class="fas fa-sort-amount-down text-xs"></i>
                            {{ __('recipes.filters.chip_sort', ['label' => $sortOptions[$currentSort] ?? $currentSort]) }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="recipes-list">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto">
                @if($recipes->count() > 0)
                    <div class="recipes-grid">
                        @foreach($recipes as $recipe)
                            @php
        $imageSource = $recipe->image
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($recipe->image)
            : ($recipe->image_url ?? null);

        if (empty($imageSource)) {
            $imageSource = asset('image/logo.png');
        }

        $difficultyLabel = null;
        if (! empty($recipe->difficulty)) {
            $difficultyLabel = $difficultyLabels[$recipe->difficulty] ?? ucfirst($recipe->difficulty);
        }

        $avgRating = (float) ($recipe->interactions_avg_rating ?? $recipe->rating ?? 0);
@endphp
                            <article class="recipe-card">
                                <div class="recipe-card__media">
                                    <img
                                        src="{{ $imageSource }}"
                                        alt="{{ $recipe->title }}"
                                        loading="lazy"
                                        onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='{{ __('recipes.cards.image_fallback_alt') }}';"
                                    >
                                    <span class="recipe-card__badge">{{ $recipe->category->name ?? __('recipes.cards.category_fallback') }}</span>
                                </div>
                                <div class="recipe-card__content">
                                    <h3 class="recipe-card__title">{{ $recipe->title }}</h3>
                                    <p class="recipe-card__excerpt">
                                        {{ $recipe->description ? \Illuminate\Support\Str::limit(strip_tags($recipe->description), 110) : __('recipes.cards.fallback_excerpt') }}
                                    </p>
                                    <div class="recipe-card__meta">
                                        @if($recipe->prep_time)
                                            <span>
                                                <i class="fas fa-clock text-orange-400"></i>
                                                {{ __('recipes.cards.prep_time', ['minutes' => $recipe->prep_time]) }}
                                            </span>
                                        @endif
                                        @if($recipe->servings)
                                            <span>
                                                <i class="fas fa-user-friends text-orange-400"></i>
                                                {{ trans_choice('recipes.cards.servings', $recipe->servings, ['count' => $recipe->servings]) }}
                                            </span>
                                        @endif
                                        @if($difficultyLabel)
                                            <span>
                                                <i class="fas fa-signal text-orange-400"></i>
                                                {{ $difficultyLabel }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="recipe-card__stats">
                                        <div class="recipe-card__rating">
                                            <div class="stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= round($avgRating) ? '' : 'empty-rating' }}"></i>
                                                @endfor
                                            </div>
                                            <span>{{ number_format($avgRating, 1) }}</span>
                                        </div>
                                        <div class="text-sm text-slate-500 font-semibold flex items-center gap-1">
                                            <i class="fas fa-bookmark text-orange-400"></i>
                                            {{ number_format($recipe->saved_count ?? 0) }}
                                        </div>
                                    </div>
                                    <div class="recipe-card__actions">
                                        @if($recipe->is_registration_closed)
                                            <span class="recipe-card__btn recipe-card__btn--disabled">
                                                <i class="fas fa-clock"></i>
                                                {{ __('recipes.cards.booking_closed') }}
                                            </span>
                                        @else
                                            <a href="{{ route('recipe.show', $recipe->slug) }}" class="recipe-card__btn recipe-card__btn--primary">
                                                <i class="fas fa-utensils"></i>
                                                {{ __('recipes.cards.view_recipe') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="flex flex-col items-center gap-3 mt-12">
                        <div class="text-sm text-slate-500 font-semibold">
                            {{ __('recipes.pagination.summary', [
                                'first' => $firstItem ?? 0,
                                'last' => $lastItem ?? 0,
                                'total' => number_format($recipes->total()),
                            ]) }}
                        </div>
                        {{ $recipes->links('pagination.custom') }}
                    </div>
                @else
                    <div class="recipe-empty">
                        <div class="recipe-empty__icon">
                            <i class="fas fa-ice-cream"></i>
                        </div>
                        <h3 class="recipe-empty__title">{{ __('recipes.empty.title') }}</h3>
                        <p class="recipe-empty__subtitle">
                            {{ __('recipes.empty.subtitle') }}
                        </p>
                        <a href="{{ route('recipes') }}" class="recipes-empty-btn">
                            <i class="fas fa-rotate-right"></i>
                            {{ __('recipes.empty.cta') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput && window.innerWidth > 768) {
        requestAnimationFrame(() => {
            searchInput.focus({ preventScroll: true });
            const valueLength = searchInput.value.length;
            searchInput.setSelectionRange(valueLength, valueLength);
        });
    }

    function convertGoogleDriveUrl(url) {
        if (!url || !url.includes('drive.google.com')) {
            return url;
        }

        try {
            const directMatch = url.match(/\/file\/d\/([a-zA-Z0-9-_]+)/);
            if (directMatch && directMatch[1]) {
                return `https://lh3.googleusercontent.com/d/${directMatch[1]}`;
            }

            if (url.includes('id=')) {
                const urlParams = new URLSearchParams(new URL(url).search);
                const fileId = urlParams.get('id');
                if (fileId) {
                    return `https://lh3.googleusercontent.com/d/${fileId}`;
                }
            }

            if (url.includes('uc?id=')) {
                const urlParams = new URLSearchParams(new URL(url).search);
                const fileId = urlParams.get('id');
                if (fileId) {
                    return `https://lh3.googleusercontent.com/d/${fileId}`;
                }
            }

            const fallbackId = url.match(/[a-zA-Z0-9-_]{25,}/);
            if (fallbackId) {
                return `https://lh3.googleusercontent.com/d/${fallbackId[0]}`;
            }
        } catch (error) {
            console.warn('Error converting Google Drive URL:', error);
        }

        return url;
    }

    document.querySelectorAll('img[src*="drive.google.com"]').forEach(function(img) {
        const converted = convertGoogleDriveUrl(img.src);
        if (converted !== img.src) {
            img.src = converted;
        }
    });
});
</script>
@endpush












