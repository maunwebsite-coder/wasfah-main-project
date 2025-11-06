@extends('layouts.app')

@section('title', 'موقع وصفه - دليلك لعالم الحلويات')

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap');
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #fcfcfc;
            background: linear-gradient(180deg, rgba(255, 247, 237, 0.4), rgba(255, 255, 255, 0.85) 40%, #ffffff 100%);
            color: #1f2937;
        }

        :root {
            --wasfa-primary: #f97316;
            --wasfa-primary-strong: #ea580c;
            --wasfa-primary-soft: #fef3c7;
            --wasfa-neutral-900: #1f2937;
            --wasfa-neutral-700: #374151;
            --wasfa-neutral-500: #6b7280;
            --wasfa-card-border: rgba(249, 115, 22, 0.12);
            --wasfa-card-shadow: 0 26px 52px rgba(15, 23, 42, 0.08);
        }

        .home-page-wrapper {
            display: flex;
            flex-direction: column;
            gap: clamp(2.75rem, 6vw, 4.5rem);
        }

        .home-section-shell {
            position: relative;
            border-radius: 2.25rem;
            padding: clamp(2rem, 5vw, 3.25rem);
            background: linear-gradient(135deg, rgba(255, 247, 237, 0.88), rgba(255, 255, 255, 0.96));
            box-shadow: var(--wasfa-card-shadow);
            border: 1px solid var(--wasfa-card-border);
            overflow: hidden;
        }
        .home-section-shell:not(.home-hero-shell)::before,
        .home-section-shell:not(.home-hero-shell)::after {
            content: "";
            position: absolute;
            border-radius: 9999px;
            pointer-events: none;
        }
        .home-section-shell:not(.home-hero-shell)::before {
            inset-inline-start: -120px;
            inset-block-start: -110px;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.09), transparent 68%);
        }
        .home-section-shell:not(.home-hero-shell)::after {
            inset-inline-end: -150px;
            inset-block-end: -160px;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.06), transparent 72%);
        }
        .home-section-shell > * {
            position: relative;
            z-index: 1;
        }

        .section-heading {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: clamp(1.5rem, 3vw, 2.75rem);
            text-align: center;
        }
        .section-heading.is-left {
            text-align: start;
            align-items: flex-start;
        }
        .section-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            align-self: center;
            font-size: 0.98rem;
            font-weight: 700;
            color: var(--wasfa-primary-strong);
            background: rgba(249, 115, 22, 0.12);
            border-radius: 9999px;
            padding: 0.5rem 1.35rem;
        }
        .section-heading.is-left .section-eyebrow {
            align-self: flex-start;
        }
        .section-title {
            font-size: clamp(2.1rem, 4.5vw, 2.85rem);
            font-weight: 800;
            color: var(--wasfa-neutral-900);
        }
        .section-subtitle {
            max-width: 52ch;
            margin: 0 auto;
            color: var(--wasfa-neutral-500);
            font-size: clamp(1rem, 2.25vw, 1.15rem);
            line-height: 1.8;
        }
        .section-heading.is-left .section-subtitle {
            margin-inline-start: 0;
        }
        .section-cta {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-weight: 700;
            color: var(--wasfa-primary-strong);
            transition: color 0.2s ease;
        }
        .section-cta:hover {
            color: var(--wasfa-primary);
        }

        /* Hide scrollbar for a cleaner look */
        .swiper-wrapper {
            scrollbar-width: none; /* For Firefox */
        }
        .swiper-wrapper::-webkit-scrollbar {
            display: none; /* For Chrome, Safari, and Opera */
        }

        /* Workshop Cards Styling */
        .workshop-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .workshop-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .featured-workshops-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1.5rem;
        }
        .featured-workshops-grid > * {
            height: 100%;
        }
        @media (min-width: 768px) {
            .featured-workshops-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1280px) {
            .featured-workshops-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        @media (min-width: 1536px) {
            .featured-workshops-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        /* Home tools slider */
        .home-tools-section {
            background: linear-gradient(145deg, rgba(255, 247, 237, 0.45), rgba(255, 255, 255, 0.85));
            border-radius: 2rem;
            box-shadow: 0 30px 60px rgba(249, 115, 22, 0.08);
            border: 1px solid rgba(249, 115, 22, 0.12);
        }
        .home-tools-swiper .swiper-slide {
            height: auto;
        }
        .home-tool-card {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(249, 115, 22, 0.08);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .home-tool-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 28px 55px rgba(249, 115, 22, 0.16);
        }
        .home-tool-card__image {
            position: relative;
            background: linear-gradient(140deg, rgba(255, 237, 213, 0.5), rgba(254, 215, 170, 0.35));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.75rem;
            min-height: 220px;
        }
        .home-tool-card__image img {
            max-width: 100%;
            max-height: 160px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .home-tool-card:hover .home-tool-card__image img {
            transform: scale(1.07);
        }
        .home-tool-card__category {
            position: absolute;
            inset-inline-end: 1.25rem;
            inset-block-start: 1.25rem;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 999px;
            padding: 0.35rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--wasfa-primary-strong);
            box-shadow: 0 8px 18px rgba(249, 115, 22, 0.18);
        }
        .home-tool-card__body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            flex: 1;
        }
        .home-tool-card__title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--wasfa-neutral-900);
            line-height: 1.5;
        }
        .home-tool-card__rating {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 600;
            color: var(--wasfa-primary-strong);
            font-size: 0.9rem;
        }
        .home-tool-card__rating i {
            color: #fbbf24;
        }
        .home-tool-card__price {
            font-size: 1rem;
            font-weight: 700;
            color: var(--wasfa-primary-strong);
        }
        .home-tool-card__actions {
            display: flex;
            gap: 0.75rem;
            margin-top: auto;
            flex-wrap: wrap;
        }
        .home-tool-card__actions a {
            flex: 1;
        }
        .home-tools-pagination {
            margin-top: 1.5rem;
        }
        .home-tools-pagination .swiper-pagination-bullet {
            width: 10px;
            height: 10px;
            background: rgba(249, 115, 22, 0.28);
            opacity: 1;
        }
        .home-tools-pagination .swiper-pagination-bullet-active {
            background: var(--wasfa-primary-strong);
        }
        .home-tools-nav {
            display: flex;
            gap: 0.75rem;
        }
        .home-tools-nav button {
            width: 46px;
            height: 46px;
            border-radius: 999px;
            border: 1px solid rgba(249, 115, 22, 0.25);
            background: #ffffff;
            color: var(--wasfa-primary-strong);
            box-shadow: 0 18px 32px rgba(249, 115, 22, 0.15);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .home-tools-nav button:hover {
            background: var(--wasfa-primary);
            color: #ffffff;
            border-color: transparent;
            transform: translateY(-2px);
        }
        .home-tools-nav button.swiper-button-disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        /* Workshop Card Image Improvements */
        .workshop-card img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            object-position: center;
            transition: transform 0.3s ease;
        }
        
        .workshop-card:hover img {
            transform: scale(1.05);
        }

        /* Home hero layout */
        .home-hero-shell {
            position: relative;
            border-radius: 2rem;
            padding: clamp(1.5rem, 4vw, 2.75rem);
            background: linear-gradient(135deg, #fff7ed 0%, #fef3c7 45%, #ffffff 100%);
            box-shadow: 0 30px 60px rgba(249, 115, 22, 0.12);
            overflow: hidden;
        }
        .home-hero-shell::after {
            content: "";
            position: absolute;
            inset-inline-end: -120px;
            top: -60px;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle at center, rgba(251, 191, 36, 0.35), transparent 70%);
            pointer-events: none;
        }
        .home-hero-grid {
            position: relative;
            display: grid;
            gap: clamp(1.75rem, 4vw, 2.5rem);
            z-index: 1;
        }
        .hero-main-card {
            position: relative;
            background: #ffffff;
            border-radius: 1.8rem;
            padding: 0;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .hero-slider {
            width: 100%;
            height: 100%;
            isolation: isolate;
            margin-bottom: 0;
        }
        .hero-slider .swiper-wrapper {
            align-items: stretch;
        }
        .hero-slider .swiper-slide {
            display: flex;
            height: auto;
        }
        .hero-slide {
            display: grid;
            grid-template-areas:
                "media"
                "content";
            gap: clamp(1.25rem, 3vw, 1.75rem);
            min-height: 100%;
            width: 100%;
            align-content: center;
            flex: 1;
            padding: clamp(1.6rem, 3vw, 2.5rem);
        }
        .hero-media {
            grid-area: media;
            border-radius: 1.5rem;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(255, 237, 213, 0.7), rgba(254, 215, 170, 0.3));
            position: relative;
            isolation: isolate;
            min-height: clamp(220px, 32vw, 320px);
            height: 100%;
            display: flex;
        }
        .hero-media picture {
            width: 100%;
            height: 100%;
            flex: 1;
            display: block;
        }
        .hero-media::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.1), rgba(0, 0, 0, 0.05));
            mix-blend-mode: soft-light;
        }
        .hero-main-image {
            width: 100%;
            height: 100%;
            max-height: 360px;
            object-fit: cover;
            object-position: center;
            display: block;
            border-radius: inherit;
            transform: scale(1.06);
            transform-origin: center;
        }
        .hero-content {
            grid-area: content;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            justify-content: center;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
            font-weight: 700;
            color: #b45309;
            background: linear-gradient(135deg, rgba(254, 215, 170, 0.85), rgba(253, 186, 116, 0.85));
            border-radius: 9999px;
            padding: 0.55rem 1.35rem;
            box-shadow: 0 10px 18px rgba(249, 115, 22, 0.18);
        }
        .hero-title {
            font-size: clamp(1.9rem, 4.8vw, 3rem);
            font-weight: 800;
            color: #1f2937;
            line-height: 1.25;
        }
        .hero-description {
            font-size: clamp(1rem, 2.7vw, 1.125rem);
            color: #4b5563;
            line-height: 1.8;
            max-width: 48ch;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
        }
        .hero-features {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 0.6rem;
        }
        .hero-feature {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            color: #4b5563;
            font-size: 0.95rem;
            line-height: 1.7;
        }
        .hero-feature i {
            color: var(--wasfa-primary-strong);
            font-size: 1rem;
            margin-top: 0.15rem;
        }
        .hero-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border-radius: 9999px;
            font-weight: 700;
            padding: 0.85rem 1.8rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hero-action i {
            font-size: 0.95rem;
        }
        .hero-action:hover {
            transform: translateY(-2px);
        }
        .primary-action {
            background: linear-gradient(135deg, #f97316, #f59e0b);
            color: #ffffff;
            box-shadow: 0 12px 22px rgba(249, 115, 22, 0.25);
        }
        .primary-action:hover {
            box-shadow: 0 16px 30px rgba(249, 115, 22, 0.32);
        }
        .secondary-action {
            border: 2px solid rgba(249, 115, 22, 0.35);
            color: #b45309;
            background: rgba(255, 255, 255, 0.85);
        }
        .secondary-action:hover {
            background: rgba(255, 237, 213, 0.6);
        }
        .accent-action {
            border: 2px dashed rgba(249, 115, 22, 0.55);
            background: rgba(255, 247, 237, 0.85);
            color: #9a3412;
            box-shadow: 0 10px 18px rgba(249, 115, 22, 0.18);
        }
        .accent-action:hover {
            background: rgba(255, 237, 213, 0.92);
            border-color: rgba(234, 88, 12, 0.6);
            color: #7c2d12;
        }
        .hero-slider-pagination {
            position: static;
            margin-top: clamp(1.15rem, 2.5vw, 1.75rem);
            display: flex;
            justify-content: center;
            gap: 0.55rem;
        }
        .hero-slider-pagination .swiper-pagination-bullet {
            width: 10px;
            height: 10px;
            background: rgba(249, 115, 22, 0.28);
            opacity: 1;
        }
        .hero-slider-pagination .swiper-pagination-bullet-active {
            background: var(--wasfa-primary-strong);
        }
        .hero-slider-nav {
            position: absolute;
            inset-inline-end: clamp(1.4rem, 3vw, 2.3rem);
            inset-block-end: clamp(1.2rem, 2.6vw, 1.9rem);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 5;
        }
        .hero-slider-nav button {
            width: 44px;
            height: 44px;
            border-radius: 9999px;
            border: 1px solid rgba(249, 115, 22, 0.28);
            background: #ffffff;
            color: var(--wasfa-primary-strong);
            box-shadow: 0 16px 30px rgba(249, 115, 22, 0.15);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .hero-slider-nav button:hover {
            background: var(--wasfa-primary);
            color: #ffffff;
            border-color: transparent;
            transform: translateY(-2px);
        }
        .hero-slider-nav button.swiper-button-disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .hero-latest-card {
            background: rgba(255, 255, 255, 0.92);
            border-radius: 1.8rem;
            padding: clamp(1.25rem, 3vw, 2rem);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
            display: flex;
            flex-direction: column;
            gap: 1.35rem;
            min-width: 0;
            backdrop-filter: blur(12px);
        }
        .hero-latest-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            border-bottom: 1px solid rgba(229, 231, 235, 0.8);
            padding-bottom: 0.85rem;
        }
        .hero-latest-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }
        .hero-latest-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-weight: 600;
            color: #f97316;
            font-size: 0.95rem;
            transition: color 0.2s ease;
        }
        .hero-latest-link:hover {
            color: #ea580c;
        }
        .hero-latest-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 1rem;
            align-content: start;
            scrollbar-width: none;
        }
        .hero-latest-list::-webkit-scrollbar {
            display: none;
        }
        .hero-latest-list > li {
            width: 100%;
        }
        .hero-latest-empty {
            text-align: center;
            color: #6b7280;
            font-size: 0.95rem;
            padding: 2rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            grid-column: 1 / -1;
        }
        .latest-recipe-mini {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.75rem 0.85rem;
            border-radius: 1.25rem;
            background: linear-gradient(140deg, rgba(255, 255, 255, 0.92), rgba(255, 237, 213, 0.72));
            border: 1px solid rgba(249, 115, 22, 0.08);
            box-shadow: 0 12px 28px rgba(249, 115, 22, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            color: inherit;
            text-decoration: none;
        }
        .latest-recipe-mini:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(249, 115, 22, 0.16);
            border-color: rgba(249, 115, 22, 0.15);
        }
        .latest-recipe-thumb {
            width: 56px;
            height: 56px;
            border-radius: 1.1rem;
            overflow: hidden;
            flex-shrink: 0;
            background: rgba(249, 115, 22, 0.1);
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.12);
        }
        .latest-recipe-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .latest-recipe-mini:hover .latest-recipe-thumb img {
            transform: scale(1.05);
        }
        .latest-recipe-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        .latest-recipe-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
            line-height: 1.35;
            transition: color 0.2s ease;
        }
        .latest-recipe-mini:hover .latest-recipe-title {
            color: #ea580c;
        }
        .latest-recipe-meta {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.8rem;
            color: #6b7280;
            flex-wrap: wrap;
        }
        .latest-recipe-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            white-space: nowrap;
        }
        .latest-recipe-meta-item i {
            font-size: 0.8rem;
            color: #f59e0b;
        }
        .latest-recipe-meta-item.is-rating i {
            color: #fbbf24;
        }
        .latest-recipe-chip {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.7rem;
            border-radius: 9999px;
            background: rgba(249, 115, 22, 0.1);
            color: #ea580c;
            font-size: 0.75rem;
            font-weight: 600;
            align-self: flex-start;
        }
        .hero-latest-empty-icon {
            width: 64px;
            height: 64px;
            border-radius: 9999px;
            background: rgba(229, 231, 235, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 1.35rem;
        }

        .home-highlight-shell {
            background: linear-gradient(135deg, rgba(255, 247, 237, 0.8), rgba(255, 255, 255, 0.96));
        }
        .highlight-card {
            position: relative;
            border-radius: 1.9rem;
            padding: clamp(1.75rem, 4vw, 2.85rem);
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.95), rgba(249, 115, 22, 0.88), rgba(253, 186, 116, 0.85));
            box-shadow: 0 28px 50px rgba(249, 115, 22, 0.28);
            color: #fff;
            overflow: hidden;
        }
        .highlight-card::before,
        .highlight-card::after {
            content: "";
            position: absolute;
            border-radius: 9999px;
            pointer-events: none;
        }
        .highlight-card::before {
            inset-inline-start: -110px;
            inset-block-start: -130px;
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.32), transparent 72%);
            opacity: 0.35;
        }
        .highlight-card::after {
            inset-inline-end: -120px;
            inset-block-end: -150px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.18), transparent 70%);
            opacity: 0.4;
        }
        .highlight-card.is-empty {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.94), rgba(255, 237, 213, 0.94));
            box-shadow: 0 24px 45px rgba(15, 23, 42, 0.08);
            color: var(--wasfa-neutral-900);
        }
        .highlight-card.is-empty::before,
        .highlight-card.is-empty::after {
            background: radial-gradient(circle, rgba(249, 115, 22, 0.1), transparent 70%);
            opacity: 1;
        }
        .highlight-grid {
            position: relative;
            display: grid;
            gap: clamp(1.5rem, 3vw, 2.5rem);
        }
        @media (min-width: 1024px) {
            .highlight-grid {
                grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
                align-items: stretch;
            }
        }
        .highlight-content {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            justify-content: center;
        }
        .highlight-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 700;
            font-size: 0.95rem;
            border-radius: 9999px;
            padding: 0.55rem 1.4rem;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            width: fit-content;
        }
        .highlight-card.is-empty .highlight-badge {
            background: rgba(249, 115, 22, 0.12);
            color: var(--wasfa-primary-strong);
        }
        .highlight-title {
            font-size: clamp(1.85rem, 3.5vw, 2.4rem);
            font-weight: 800;
            line-height: 1.3;
        }
        .highlight-card.is-empty .highlight-title {
            color: var(--wasfa-neutral-900);
        }
        .highlight-description {
            font-size: clamp(1rem, 2.4vw, 1.1rem);
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.85);
        }
        .highlight-card.is-empty .highlight-description {
            color: var(--wasfa-neutral-500);
        }
        .highlight-meta {
            display: grid;
            gap: 0.85rem;
            margin-top: 0.5rem;
        }
        .highlight-meta .meta-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            font-size: 0.98rem;
            font-weight: 600;
        }
        .highlight-meta .meta-item i {
            font-size: 1.05rem;
        }
        .highlight-card.is-empty .highlight-meta .meta-item {
            color: var(--wasfa-neutral-500);
        }
        .highlight-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        .highlight-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 700;
            border-radius: 9999px;
            padding: 0.85rem 1.8rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease, color 0.2s ease;
        }
        .highlight-cta:hover {
            transform: translateY(-2px);
        }
        .highlight-cta.primary {
            background: #ffffff;
            color: #16a34a;
            box-shadow: 0 14px 28px rgba(15, 118, 110, 0.18);
        }
        .highlight-cta.primary:hover {
            background: rgba(255, 255, 255, 0.92);
        }
        .highlight-cta.secondary {
            border: 2px solid rgba(255, 255, 255, 0.35);
            color: #fff;
            background: rgba(255, 255, 255, 0.12);
        }
        .highlight-card.is-empty .highlight-cta.primary {
            background: linear-gradient(135deg, #f97316, #f59e0b);
            color: #ffffff;
            box-shadow: 0 16px 32px rgba(249, 115, 22, 0.25);
        }
        .highlight-card.is-empty .highlight-cta.secondary {
            border-color: rgba(249, 115, 22, 0.25);
            color: var(--wasfa-primary-strong);
            background: rgba(249, 115, 22, 0.08);
        }
        .highlight-cta.accent {
            background: linear-gradient(135deg, #f97316, #f59e0b);
            color: #ffffff;
            box-shadow: 0 16px 32px rgba(249, 115, 22, 0.22);
        }
        .highlight-cta.outline {
            border: 2px solid rgba(249, 115, 22, 0.25);
            color: var(--wasfa-primary-strong);
            background: rgba(249, 115, 22, 0.05);
        }
        .highlight-media {
            position: relative;
            border-radius: 1.6rem;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
        }
        .highlight-card.is-empty .highlight-media {
            background: rgba(255, 255, 255, 0.55);
        }
        .highlight-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            min-height: 260px;
        }
        .highlight-price-badge {
            position: absolute;
            top: 1.5rem;
            inset-inline-end: 1.5rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 9999px;
            padding: 0.5rem 1.2rem;
            font-weight: 700;
        }
        .highlight-card.is-empty .highlight-price-badge {
            display: none;
        }
        .highlight-media .media-icon {
            font-size: clamp(3rem, 6vw, 4.5rem);
            color: rgba(249, 115, 22, 0.35);
        }

        .home-empty-state {
            text-align: center;
            padding: 3rem 2rem;
            border-radius: 1.75rem;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
        }
        .home-empty-state-icon {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.15), rgba(253, 186, 116, 0.22));
            color: var(--wasfa-primary-strong);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.25rem;
        }
        .home-empty-state p {
            color: var(--wasfa-neutral-500);
            font-size: 1.05rem;
            line-height: 1.7;
            max-width: 45ch;
            margin: 0 auto 1.5rem;
        }
        .home-empty-state .section-cta {
            background: linear-gradient(135deg, #f97316, #f59e0b);
            color: #fff;
            padding: 0.85rem 1.8rem;
            border-radius: 9999px;
            box-shadow: 0 16px 32px rgba(249, 115, 22, 0.22);
        }
        .home-empty-state .section-cta:hover {
            color: #fff;
        }

        .feature-grid {
            display: grid;
            gap: 1.8rem;
        }
        @media (min-width: 768px) {
            .feature-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 1024px) {
            .feature-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
        .feature-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.25rem;
            text-align: center;
            background: #ffffff;
            border-radius: 1.6rem;
            padding: 2rem;
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(249, 115, 22, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 48px rgba(249, 115, 22, 0.18);
        }
        .feature-icon {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.15), rgba(253, 186, 116, 0.25));
            color: var(--wasfa-primary-strong);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            box-shadow: inset 0 0 0 1px rgba(249, 115, 22, 0.12);
        }
        .feature-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--wasfa-neutral-900);
        }
        .feature-card p {
            color: var(--wasfa-neutral-500);
            line-height: 1.7;
        }

        .workshop-card {
            background: #ffffff;
            border-radius: 1.8rem;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(249, 115, 22, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .workshop-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 26px 60px rgba(249, 115, 22, 0.18);
        }
        .workshop-image-wrapper {
            position: relative;
        }
        .workshop-status-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.45);
        }
        .workshop-price-chip {
            position: absolute;
            top: 1.25rem;
            inset-inline-end: 1.25rem;
            background: rgba(17, 24, 39, 0.65);
            color: #ffffff;
            padding: 0.45rem 1.1rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.95rem;
            backdrop-filter: blur(10px);
        }
        .workshop-body {
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
            height: 100%;
            gap: 1rem;
        }
        .workshop-meta {
            display: grid;
            gap: 0.6rem;
            color: var(--wasfa-neutral-500);
            font-size: 0.95rem;
        }
        .workshop-meta .meta-line {
            display: flex;
            align-items: center;
            gap: 0.55rem;
        }
        .workshop-meta .meta-line i {
            color: var(--wasfa-primary-strong);
        }
        .workshop-actions {
            margin-top: auto;
            display: flex;
            gap: 0.65rem;
            flex-wrap: wrap;
        }
        .workshop-actions .highlight-cta {
            flex: 1;
            min-width: 0;
        }

        .featured-workshop-card {
            position: relative;
        }
        .featured-workshop-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
            pointer-events: none;
        }
        .featured-workshop-actions > * {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .featured-workshop-actions > *:hover {
            transform: translateY(-2px);
        }

        @media (min-width: 1024px) {
            .home-hero-grid {
                grid-template-columns: minmax(0, 3fr) minmax(0, 2fr);
                align-items: stretch;
            }
            .hero-slide {
                grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr);
                grid-template-areas: "content media";
                align-items: center;
            }
            .hero-media {
                max-height: 100%;
            }
        }

        @media (max-width: 1024px) {
            .home-hero-shell::after {
                inset-inline-end: -160px;
                top: -110px;
                width: 280px;
                height: 280px;
            }
        }

        @media (max-width: 768px) {
            .home-hero-shell {
                padding: 1.75rem;
                border-radius: 1.75rem;
            }
            .home-hero-grid {
                gap: 1.5rem;
            }
            .hero-main-card {
                padding: 1.25rem;
            }
            .hero-slider {
                margin-bottom: 0.8rem;
            }
            .hero-slide {
                padding: 1.25rem;
                gap: 1.2rem;
            }
            .hero-actions {
                flex-direction: column;
            }
            .hero-action {
                width: 100%;
            }
            .hero-slider-nav {
                display: none;
            }
            .hero-slider-pagination {
                margin-top: 1.25rem;
                display: none !important;
            }
            .hero-latest-card {
                padding: 1.5rem;
            }
            .hero-latest-list {
                grid-auto-flow: column;
                grid-template-rows: repeat(2, minmax(0, auto));
                grid-template-columns: calc(100% - 1.25rem);
                grid-auto-columns: calc(100% - 1.25rem);
                gap: 0.75rem;
                overflow-x: auto;
                padding-bottom: 0.5rem;
                scroll-snap-type: x proximity;
                overscroll-behavior-x: contain;
            }
            .hero-latest-list > li {
                scroll-snap-align: start;
                scroll-snap-stop: always;
            }
            .latest-recipe-mini {
                padding: 0.7rem 0.75rem;
            }
            .latest-recipe-thumb {
                width: 52px;
                height: 52px;
            }
            .latest-recipe-title {
                font-size: 0.9rem;
            }
            .featured-workshop-section {
                padding-top: 2.5rem !important;
                padding-bottom: 2.5rem !important;
            }
        }

        @media (max-width: 640px) {
            .home-hero-shell {
                padding: 0.6rem;
                border-radius: 1.5rem;
            }
            .hero-slide {
                padding: 0.75rem;
                gap: 0.65rem;
            }
            .hero-slider {
                margin-bottom: 0.35rem;
            }
            .hero-main-card {
                padding: 0;
                border-radius: 1.5rem;
            }
            .hero-actions {
                gap: 0.75rem;
            }
            .hero-badge {
                font-size: 0.9rem;
                padding: 0.5rem 1.15rem;
            }
            .hero-main-image {
                max-height: 160px;
            }
            .hero-media {
                min-height: 120px;
            }
            .hero-content {
                gap: 0.7rem;
            }
            .hero-title {
                font-size: 1.7rem;
            }
            .hero-description {
                font-size: 0.95rem;
            }
            .hero-features {
                gap: 0.5rem;
            }
            .hero-latest-card {
                border-radius: 1.5rem;
            }
            .home-tools-section {
                border-radius: 1.5rem;
                padding: 1.5rem;
            }
            .home-tool-card__image {
                min-height: 160px;
                padding: 1.25rem;
            }
            .home-tool-card__image img {
                max-height: 120px;
            }
            .home-tool-card__body {
                padding: 1.05rem;
                gap: 0.7rem;
            }
            .home-tool-card__actions {
                flex-direction: column;
            }
            .latest-recipe-mini {
                padding: 0.65rem 0.7rem;
                border-radius: 1.1rem;
            }
            .latest-recipe-title {
                font-size: 0.85rem;
            }
            .latest-recipe-meta {
                gap: 0.5rem;
                font-size: 0.75rem;
            }
            .latest-recipe-chip {
                font-size: 0.7rem;
            }
            .card-container {
                width: 100%;
                height: auto;
            }
            .featured-recipes-swiper .swiper-slide {
                width: min(88vw, 320px);
                height: auto;
            }
            .featured-workshop-card {
                border-radius: 1.5rem;
            }
            .featured-workshop-section {
                padding-top: 2rem !important;
                padding-bottom: 2rem !important;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 1.85rem;
            }
            .hero-description {
                font-size: 0.98rem;
            }
            .hero-latest-header h2 {
                font-size: 1.35rem;
            }
        }
        
        /* Mobile Responsive Images */
        @media (max-width: 768px) {
            .workshop-card img {
                height: 220px;
                object-fit: cover;
                object-position: center;
                min-height: 200px;
                max-height: 250px;
            }
            
            /* Featured workshop image mobile fix */
            .featured-workshop-image {
                height: 250px;
                object-fit: cover;
                object-position: center;
                min-height: 200px;
                max-height: 300px;
            }
        }
        
        @media (max-width: 480px) {
            .workshop-card img {
                height: 200px;
                object-fit: cover;
                object-position: center;
                min-height: 180px;
                max-height: 220px;
            }
            
            /* Featured workshop image mobile fix */
            .featured-workshop-image {
                height: 220px;
                object-fit: cover;
                object-position: center;
                min-height: 180px;
                max-height: 250px;
            }
        }

        /* Flip Card Styles */
        .card-container {
            perspective: 1000px;
            width: clamp(240px, 28vw, 280px);
            height: 400px;
            cursor: pointer;
            margin: 0;
        }

        .card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.6s;
            transform-style: preserve-3d;
        }

        .card-container.is-flipped .card-inner {
            transform: rotateY(180deg);
        }

        .card-front,
        .card-back {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            display: flex; /* Ensure content is centered */
            flex-direction: column;
        }

        .card-front { background: #fff; }
        .card-back { background: #fff; transform: rotateY(180deg); }

        /* Swiper Settings */
        .swiper, .swiper-container {
            padding: 0 !important;
            margin: 0 !important;
        }

        .featured-recipes-swiper .swiper-slide {
            flex: 0 0 auto;
            width: clamp(240px, 28vw, 280px);
            height: 400px;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
        }

        /* Recipe Cards Enhancements - removed hover effects */

        /* Additional card styles for home page */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

    </style>
@endpush

@section('content')
    @php
        $showAdminMetrics = auth()->check() && auth()->user()->isAdmin();
    @endphp
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />

    <!-- قسم المحتوى الرئيسي -->
    <main class="container mx-auto px-4 py-6 lg:py-8">
        <div class="home-hero-shell">
            <div class="home-hero-grid">
                @php
                    $isAuthenticated = auth()->check();
                    $isChefUser = $isAuthenticated && auth()->user()->isChef();
                    $createWorkshopAction = [
                        'label' => $isChefUser ? 'أنشئ ورشتك الآن' : ($isAuthenticated ? 'أكمل ملفك كشيف وأنشئ ورشتك' : 'انضم كشيف وأنشئ ورشتك'),
                        'url' => $isChefUser ? route('chef.workshops.create') : ($isAuthenticated ? route('onboarding.show') : route('login')),
                        'icon' => $isChefUser ? 'fas fa-plus-circle' : 'fas fa-user-plus',
                        'type' => 'accent',
                    ];
                    $createWasfahLinkAction = [
                        'label' => $isChefUser ? 'أنشئ Wasfah Link الآن' : ($isAuthenticated ? 'أكمل ملفك لتفعيل Wasfah Links' : 'سجل وابدأ Wasfah Links'),
                        'url' => $isChefUser ? route('chef.links.edit') : ($isAuthenticated ? route('onboarding.show') : route('register')),
                        'icon' => 'fas fa-link',
                        'type' => 'primary',
                    ];
                    $heroSlides = [
                        [
                            'badge' => 'ورشات العمل',
                            'title' => 'ورشات حلويات احترافية',
                            'description' => 'ورشات مباشرة بخطوات واضحة من شيفات مختصين.',
                            'features' => [
                                'جلسات تفاعلية محدودة العدد',
                                'ملفات تطبيقية وشهادة حضور',
                            ],
                            'image' => data_get($heroMedia ?? [], 'workshop.desktop', asset('image/wterm.png')),
                            'mobile_image' => data_get($heroMedia ?? [], 'workshop.mobile', data_get($heroMedia ?? [], 'workshop.desktop', asset('image/wterm.png'))),
                            'image_alt' => 'ورشة عمل للحلويات الاحترافية',
                            'actions' => [
                                [
                                    'label' => 'استكشف الورشات',
                                    'url' => route('workshops'),
                                    'icon' => 'fas fa-chalkboard-teacher',
                                    'type' => 'primary',
                                ],
                                [
                                    'label' => 'جدول الورشات',
                                    'url' => route('workshops'),
                                    'icon' => 'fas fa-calendar-alt',
                                    'type' => 'secondary',
                                ],
                            ],
                        ],
                        [
                            'badge' => 'للشيفات',
                            'title' => 'أنشئ ورشتك على وصفة',
                            'description' => 'أطلق ورشتك الاحترافية مع نظام حجوزات مدمج وأدوات تسويق مصممة للشيفات.',
                            'features' => [
                                'لوحة تحكم لإدارة الجلسات والمدفوعات',
                                'رابط تسجيل مباشر للمتدربين',
                                'دعم فني وخبراء يساعدونك في كل خطوة',
                            ],
                            'image' => data_get($heroMedia ?? [], 'chef.desktop', data_get($heroMedia ?? [], 'workshop.desktop', asset('image/wterm.png'))),
                            'mobile_image' => data_get($heroMedia ?? [], 'chef.mobile', data_get($heroMedia ?? [], 'chef.desktop', data_get($heroMedia ?? [], 'workshop.desktop', asset('image/wterm.png')))),
                            'image_alt' => 'شيف يطلق ورشته الخاصة',
                            'actions' => [
                                $createWorkshopAction,
                            ],
                        ],
                        [
                            'badge' => 'Wasfah Links',
                            'title' => 'Wasfah Links للشيفات',
                            'description' => 'اجمع ورشاتك وروابطك المهمة في صفحة واحدة قابلة للمشاركة مع متابعيك.',
                            'features' => [
                                'صفحة مخصصة باسمك مع رابط قصير',
                                'تحكم كامل من لوحة الشيف لتحديث المحتوى فوراً',
                                'مثالية لمشاركتها على إنستغرام وواتساب',
                            ],
                            'image' => data_get($heroMedia ?? [], 'links.desktop', asset('image/wasfah-links.png')),
                            'mobile_image' => data_get($heroMedia ?? [], 'links.mobile', data_get($heroMedia ?? [], 'links.desktop', asset('image/wasfah-links.png'))),
                            'image_alt' => 'صفحة Wasfah Links للشيف',
                            'actions' => [
                                $createWasfahLinkAction,
                                [
                                    'label' => 'استعرض Wasfah Links',
                                    'url' => route('links'),
                                    'icon' => 'fas fa-eye',
                                    'type' => 'secondary',
                                ],
                            ],
                        ],
                        [
                            'badge' => 'أدوات الشيف',
                            'title' => 'دليل أدوات الشيف',
                            'description' => 'اختيارات دقيقة لأدوات تساعدك على الإتقان.',
                            'features' => [
                                'قوائم محدثة وروابط موثوقة',
                                'نصائح استخدام وصيانة مختصرة',
                            ],
                            'image' => data_get($heroMedia ?? [], 'tool.desktop', asset('image/tnl.png')),
                            'mobile_image' => data_get($heroMedia ?? [], 'tool.mobile', data_get($heroMedia ?? [], 'tool.desktop', asset('image/tnl.png'))),
                            'image_alt' => 'مجموعة أدوات لتحضير الحلويات',
                            'actions' => [
                                [
                                    'label' => 'استعرض أدوات الشيف',
                                    'url' => route('tools'),
                                    'icon' => 'fas fa-toolbox',
                                    'type' => 'primary',
                                ],
                                [
                                    'label' => 'الأدوات المحفوظة',
                                    'url' => route('saved.index'),
                                    'icon' => 'fas fa-heart',
                                    'type' => 'secondary',
                                ],
                            ],
                        ],
                        [
                            'badge' => 'الوصفات',
                            'title' => 'مكتبة وصفات عالمية',
                            'description' => 'وصفات فاخرة مجرَّبة مع شرح مصوَّر ونصائح مختصرة.',
                            'features' => [
                                'تصنيفات حسب المستوى والمناسبة',
                                'حفظ ومزامنة وصفاتك المفضلة',
                            ],
                            'image' => data_get($heroMedia ?? [], 'recipe.desktop', asset('image/Brownies.png')),
                            'mobile_image' => data_get($heroMedia ?? [], 'recipe.mobile', data_get($heroMedia ?? [], 'recipe.desktop', asset('image/Brownies.png'))),
                            'image_alt' => 'حلى براونيز فاخرة',
                            'actions' => [
                                [
                                    'label' => 'ابدأ اكتشاف الوصفات',
                                    'url' => route('recipes'),
                                    'icon' => 'fas fa-utensils',
                                    'type' => 'primary',
                                ],
                                [
                                    'label' => 'الوصفات المحفوظة',
                                    'url' => route('saved.index'),
                                    'icon' => 'fas fa-bookmark',
                                    'type' => 'secondary',
                                ],
                            ],
                        ],
                    ];
                @endphp
                <!-- القسم الرئيسي -->
                <article class="hero-main-card">
                    <div class="hero-slider swiper">
                        <div class="swiper-wrapper">
                            @foreach($heroSlides as $slide)
                                <div class="swiper-slide">
                                    <div class="hero-slide">
                                        <div class="hero-media">
                                            <picture>
                                                <source media="(max-width: 640px)" srcset="{{ $slide['mobile_image'] ?? $slide['image'] }}">
                                                <img src="{{ $slide['image'] }}" alt="{{ $slide['image_alt'] }}" class="hero-main-image">
                                            </picture>
                                        </div>
                                        <div class="hero-content">
                                            @if(!empty($slide['badge']))
                                                <span class="hero-badge">{{ $slide['badge'] }}</span>
                                            @endif
                                            <h1 class="hero-title">{{ $slide['title'] }}</h1>
                                            <p class="hero-description">{{ $slide['description'] }}</p>
                                            @if(!empty($slide['features']))
                                                <ul class="hero-features">
                                                    @foreach($slide['features'] as $feature)
                                                        <li class="hero-feature">
                                                            <i class="fas fa-check-circle"></i>
                                                            <span>{{ $feature }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                            @if(!empty($slide['actions']))
                                                <div class="hero-actions">
                                                    @foreach($slide['actions'] as $action)
                                                        @php
                                                            $actionType = $action['type'] ?? 'primary';
                                                            $actionClass = $actionType === 'secondary'
                                                                ? 'secondary-action'
                                                                : ($actionType === 'accent' ? 'accent-action' : 'primary-action');
                                                        @endphp
                                                        <a href="{{ $action['url'] }}"
                                                           class="hero-action {{ $actionClass }}">
                                                            <span>{{ $action['label'] }}</span>
                                                            @if(!empty($action['icon']))
                                                                <i class="{{ $action['icon'] }}"></i>
                                                            @endif
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="hero-slider-pagination swiper-pagination"></div>
                    </div>
                    <div class="hero-slider-nav">
                        <button type="button" class="hero-slider-prev" aria-label="الشريحة السابقة">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button type="button" class="hero-slider-next" aria-label="الشريحة التالية">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>
                </article>
                
                <!-- قسم أحدث الوصفات -->
                <aside class="hero-latest-card">
                    <div class="hero-latest-header">
                        <h2>أحدث الوصفات</h2>
                        <a href="{{ route('recipes') }}" class="hero-latest-link">
                            عرض الكل
                            <i class="fas fa-arrow-left text-xs"></i>
                        </a>
                    </div>
                    <ul class="hero-latest-list">
                        @forelse($latestRecipes as $recipe)
                            <x-latest-recipe-item :recipe="$recipe" />
                        @empty
                            <li class="hero-latest-empty">
                                <div class="hero-latest-empty-icon">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <p>لا توجد وصفات متاحة حالياً</p>
                            </li>
                        @endforelse
                    </ul>
                </aside>
            </div>
        </div>
    </main>

    <!-- قسم الورشة المميزة (الورشة القادمة) -->
    @if($featuredWorkshop)
    @php 
        $featuredIsFull = $featuredWorkshop->bookings_count >= $featuredWorkshop->max_participants; 
        $featuredIsRegistrationClosed = !$featuredWorkshop->is_registration_open;
        $featuredIsCompleted = $featuredWorkshop->is_completed;
        $featuredStartDateLabel = $featuredWorkshop->start_date ? $featuredWorkshop->start_date->format('d/m/Y') : 'غير محدد';
        $featuredLocationLabel = $featuredWorkshop->is_online ? 'ورشة أونلاين' : ($featuredWorkshop->location ?? 'ورشة حضورية');
        $featuredDeadlineLabel = $featuredWorkshop->registration_deadline ? $featuredWorkshop->registration_deadline->format('d/m/Y') : 'غير محدد';
        $featuredInstructorLabel = $featuredWorkshop->instructor ?? 'غير محدد';
        $featuredIsBooked = !empty($bookedWorkshopIds) && in_array($featuredWorkshop->id, $bookedWorkshopIds, true);
    @endphp
    <section class="container mx-auto px-4 py-12 featured-workshop-section">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl overflow-hidden shadow-xl featured-workshop-card">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <!-- المحتوى النصي -->
                <div class="p-6 lg:p-9 text-white flex flex-col justify-center">
                    <div class="mb-5">
                        <span class="bg-white/20 text-white text-sm font-semibold px-4 py-2 rounded-full inline-block mb-4">
                            الورشة القادمة
                        </span>
                        <h2 class="text-2xl lg:text-3xl font-bold mb-3 leading-tight">
                            {{ $featuredWorkshop->title }}
                        </h2>
                        <p class="text-base text-amber-100 mb-5 leading-relaxed">
                            {{ $featuredWorkshop->featured_description ?: $featuredWorkshop->description }}
                        </p>
                    </div>
                    
                    <!-- تفاصيل الورشة -->
                    <div class="space-y-3 mb-5">
                        <div class="flex items-center text-amber-100">
                            <i class="fas fa-calendar-alt w-5 text-center ml-3"></i>
                            <span class="font-medium">{{ $featuredWorkshop->start_date ? $featuredWorkshop->start_date->format('d/m/Y') : 'غير محدد' }}</span>
                        </div>
                        <div class="flex items-center text-amber-100">
                            <i class="fas {{ $featuredWorkshop->is_online ? 'fa-video' : 'fa-map-marker-alt' }} w-5 text-center ml-3"></i>
                            <span class="font-medium">{{ $featuredWorkshop->is_online ? 'ورشة أونلاين' : ($featuredWorkshop->location ?? 'ورشة حضورية') }}</span>
                        </div>
                        <div class="flex items-center text-amber-100">
                            <i class="fas fa-user w-5 text-center ml-3"></i>
                            <span class="font-medium">مع {{ $featuredWorkshop->instructor }}</span>
                        </div>
                        @if($showAdminMetrics)
                            <div class="flex items-center text-amber-100">
                                <i class="fas fa-users w-5 text-center ml-3"></i>
                                <span class="font-medium">{{ $featuredWorkshop->bookings_count }}/{{ $featuredWorkshop->max_participants }} مشارك</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- أزرار الإجراءات -->
                    <div class="flex flex-col sm:flex-row gap-3 featured-workshop-actions">
                        @if($featuredIsCompleted)
                            <button type="button" class="bg-gray-400 text-gray-600 font-bold py-3 px-6 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg w-full sm:w-auto">
                                <i class="fas fa-check-circle ml-2 text-xl"></i>
                                الورشة مكتملة
                            </button>
                        @elseif($featuredIsBooked)
                            <button type="button" class="bg-green-500 text-white font-bold py-3 px-6 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg w-full sm:w-auto" disabled>
                                <i class="fas fa-check ml-2 text-xl booking-button-icon"></i>
                                <span class="booking-button-label">تم الحجز بالفعل</span>
                            </button>
                        @elseif($featuredIsFull)
                            <button type="button" class="bg-gray-400 text-gray-600 font-bold py-3 px-6 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg w-full sm:w-auto">
                                <i class="fas fa-lock ml-2 text-xl"></i>
                                الورشة مكتملة
                            </button>
                        @elseif($featuredIsRegistrationClosed)
                            <button type="button" class="bg-yellow-400 text-yellow-800 font-bold py-3 px-6 rounded-xl cursor-not-allowed flex items-center justify-center shadow-lg w-full sm:w-auto">
                                <i class="fas fa-clock ml-2 text-xl"></i>
                                انتهى التسجيل
                            </button>
                        @else
                            <button type="button"
                                    class="js-whatsapp-booking bg-white text-green-600 hover:bg-green-50 font-bold py-3 px-6 rounded-xl transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl w-full sm:w-auto"
                                    data-workshop-id="{{ $featuredWorkshop->id }}"
                                    data-title="{{ e($featuredWorkshop->title) }}"
                                    data-price="{{ e($featuredWorkshop->formatted_price) }}"
                                    data-date="{{ e($featuredStartDateLabel) }}"
                                    data-instructor="{{ e($featuredInstructorLabel) }}"
                                    data-location="{{ e($featuredLocationLabel) }}"
                                    data-deadline="{{ e($featuredDeadlineLabel) }}"
                                    data-is-booked="false">
                                <i class="fab fa-whatsapp ml-2 text-xl booking-button-icon"></i>
                                <span class="booking-button-label">احجز مقعدك الآن</span>
                            </button>
                        @endif
                        <a href="{{ route('workshop.show', $featuredWorkshop->slug) }}" 
                           class="border-2 border-white text-white hover:bg-white hover:text-amber-600 font-bold py-3 px-6 rounded-xl transition-all duration-300 flex items-center justify-center w-full sm:w-auto">
                            <i class="fas fa-info-circle ml-2"></i>
                            تفاصيل أكثر
                        </a>
                    </div>
                </div>
                
                <!-- الصورة -->
                <div class="relative h-56 lg:h-auto overflow-hidden">
                    <img src="{{ $featuredWorkshop->image ? asset('storage/' . $featuredWorkshop->image) : 'https://placehold.co/800x600/f87171/FFFFFF?text=ورشة+فاخرة' }}" 
                         alt="{{ $featuredWorkshop->title }}" 
                         class="w-full h-full object-cover featured-workshop-image"
                         loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-l from-transparent to-amber-500/20"></div>
                </div>
            </div>
        </div>
    </section>
    @else
    <!-- رسالة عدم وجود ورشات قادمة -->
    <section class="container mx-auto px-4 py-12 featured-workshop-section">
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-3xl overflow-hidden shadow-xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <!-- المحتوى النصي -->
                <div class="p-6 lg:p-9 text-white flex flex-col justify-center">
                    <div class="mb-5">
                        <span class="bg-white/20 text-white text-sm font-semibold px-4 py-2 rounded-full inline-block mb-4">
                            الورشة القادمة
                        </span>
                        <h2 class="text-2xl lg:text-3xl font-bold mb-3 leading-tight">
                            لا توجد ورشات قادمة الآن
                        </h2>
                        <p class="text-base text-amber-100 mb-6 leading-relaxed">
                            نحن نعمل على إعداد ورشات جديدة ومميزة لك. انتظرونا في الورشة القادمة!
                        </p>
                    </div>
                    
                    <!-- أزرار الإجراءات -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('workshops') }}" 
                           class="bg-white text-amber-600 hover:bg-amber-50 font-bold py-3 px-6 rounded-xl transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl">
                            <i class="fas fa-list ml-2 text-xl"></i>
                            تصفح جميع الورشات
                        </a>
                        <a href="{{ route('recipes') }}" 
                           class="border-2 border-white text-white hover:bg-white hover:text-amber-600 font-bold py-3 px-6 rounded-xl transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-utensils ml-2"></i>
                            اكتشف الوصفات
                        </a>
                    </div>
                </div>
                
                <!-- الصورة/الأيقونة -->
                <div class="relative h-56 lg:h-auto overflow-hidden flex items-center justify-center">
                    <div class="w-full h-full flex items-center justify-center">
                        <div class="relative w-56 h-56 bg-white/10 rounded-full flex items-center justify-center">
                            <div class="absolute inset-4 bg-white/20 rounded-full"></div>
                            <div class="absolute inset-12 bg-white/10 rounded-full"></div>
                            <i class="fas fa-calendar-alt text-white text-5xl"></i>
                        </div>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-l from-transparent to-amber-500/20"></div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- قسم أدوات الشيف -->
    <section class="container mx-auto px-4 py-10 lg:py-14">
        <div class="home-tools-section p-6 lg:p-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8 lg:mb-10">
                <div class="text-center lg:text-right max-w-2xl mx-auto lg:mx-0">
                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-amber-600 bg-amber-100/70 px-4 py-2 rounded-full mb-3">
                        <i class="fas fa-toolbox text-sm"></i>
                        مختارات الأدوات
                    </span>
                    <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3">أدوات الشيف الموصى بها</h2>
                    <p class="text-gray-600 text-base lg:text-lg">
                        أدوات مختارة بعناية تساعدك على تنفيذ وصفاتك باحترافية، مع تقييمات المستخدمين وخيارات شراء مباشرة.
                    </p>
                </div>
                @if($homeTools->count() > 0)
                    <div class="home-tools-nav hidden lg:flex">
                        <button type="button" class="home-tools-prev" aria-label="الأداة السابقة">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <button type="button" class="home-tools-next" aria-label="الأداة التالية">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                    </div>
                @endif
            </div>

            @if($homeTools->count() > 0)
                <div class="swiper home-tools-swiper">
                    <div class="swiper-wrapper">
                        @foreach($homeTools as $tool)
                            <div class="swiper-slide">
                                <article class="home-tool-card">
                                    <div class="home-tool-card__image">
                                        <img src="{{ $tool->image_url }}" alt="{{ $tool->name }}" loading="lazy" onerror="this.src='{{ asset('image/logo.png') }}'">
                                        @if(!empty($tool->category))
                                            <span class="home-tool-card__category">{{ $tool->category }}</span>
                                        @endif
                                    </div>
                                    <div class="home-tool-card__body">
                                        <h3 class="home-tool-card__title line-clamp-2">{{ $tool->name }}</h3>
                                        <div class="home-tool-card__rating">
                                            <i class="fas fa-star"></i>
                                            <span>{{ number_format($tool->rating ?? 0, 1) }}</span>
                                            <span class="text-gray-400 text-sm">/ 5</span>
                                        </div>
                                        @if(!is_null($tool->price))
                                            <div class="home-tool-card__price">{{ number_format($tool->price, 2) }} د.إ</div>
                                        @endif
                                        <div class="home-tool-card__actions">
                                            <a href="{{ route('tools.show', $tool->id) }}" class="hero-action secondary-action text-center text-sm">
                                                <span>تفاصيل الأداة</span>
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                            @if($tool->amazon_url)
                                                <a href="{{ $tool->amazon_url }}" target="_blank" rel="nofollow noopener" class="hero-action primary-action text-center text-sm">
                                                    <span>شراء الآن</span>
                                                    <i class="fas fa-shopping-cart"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('tools') }}" class="hero-action primary-action text-center text-sm">
                                                    <span>استعرض المزيد</span>
                                                    <i class="fas fa-arrow-left"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="home-tools-pagination swiper-pagination mt-6 lg:hidden"></div>
            @else
                <div class="text-center py-16">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-amber-100 flex items-center justify-center text-amber-500">
                        <i class="fas fa-toolbox text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">لا توجد أدوات مميزة حالياً</h3>
                    <p class="text-gray-600 max-w-lg mx-auto mb-6">
                        نعمل على تحديث قائمة أدوات الشيف المختارة بشكل مستمر. عد لاحقاً للاطلاع على أحدث التوصيات.
                    </p>
                    <a href="{{ route('tools') }}" class="hero-action primary-action">
                        <span>تصفح جميع الأدوات</span>
                        <i class="fas fa-toolbox"></i>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- قسم الوصفات المميزة -->
    <section class="container mx-auto px-4 py-6">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">ابدأ بحفظ هذه الوصفات</h2>
            <p class="text-gray-600 mt-2">اكتشف وصفات حلويات فاخرة من أفضل الشيفات العالميين</p>
        </div>
        
        @if($featuredRecipes->count() > 0)
            <!-- Swiper Container -->
            <div class="swiper featured-recipes-swiper">
                <div class="swiper-wrapper py-4">
                    @foreach($featuredRecipes as $recipe)
                        <div class="swiper-slide">
                            <x-featured-recipe-card :recipe="$recipe" />
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-20 bg-white rounded-2xl shadow-lg">
                <div class="w-24 h-24 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-utensils text-4xl text-amber-500"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-700 mb-3">لا توجد وصفات متاحة حالياً</h3>
                <p class="text-gray-500 text-lg max-w-md mx-auto mb-6">نحن نعمل على إضافة وصفات جديدة ومميزة. تحقق مرة أخرى قريباً!</p>
                <a href="{{ route('recipes') }}" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300">
                    تصفح جميع الوصفات
                </a>
            </div>
        @endif
    </section>

    <!-- قسم لماذا تختار موقع وصفة؟ -->
    @php
        $whyChooseItems = [
            [
                'icon' => 'fas fa-crown',
                'title' => 'وصفات متنوعة',
                'description' => 'نخبة وصفات عالمية فاخرة.',
                'card_gradient' => 'from-amber-50 via-white to-orange-50',
                'card_border' => 'border-amber-200/70',
                'icon_gradient' => 'from-amber-400 to-orange-500',
            ],
            [
                'icon' => 'fas fa-gem',
                'title' => 'تقنيات احترافية',
                'description' => 'مهارات شيفات بخبرة عالمية.',
                'card_gradient' => 'from-amber-50 via-white to-orange-50',
                'card_border' => 'border-amber-200/70',
                'icon_gradient' => 'from-amber-400 to-orange-500',
            ],
            [
                'icon' => 'fas fa-award',
                'title' => 'مواد أولية فاخرة',
                'description' => 'مكونات بلجيكية وإيطالية مميزة.',
                'card_gradient' => 'from-amber-50 via-white to-orange-50',
                'card_border' => 'border-amber-200/70',
                'icon_gradient' => 'from-amber-400 to-orange-500',
            ],
            [
                'icon' => 'fas fa-magic',
                'title' => 'ورشات متخصصة',
                'description' => 'ورشات حصرية مع خبراء.',
                'card_gradient' => 'from-amber-50 via-white to-orange-50',
                'card_border' => 'border-amber-200/70',
                'icon_gradient' => 'from-amber-400 to-orange-500',
            ],
        ];
    @endphp
    <section class="pt-4 pb-4 md:pt-6 md:pb-8 bg-gradient-to-br from-amber-50 to-orange-50">
        <div class="container mx-auto px-3 md:px-6 text-center">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-1 md:mb-3">لماذا تختار منصّة وصفة ؟</h2>
            <p class="text-gray-600 text-sm md:text-base mb-0 md:mb-2 max-w-2xl mx-auto">نحن نقدم لك تجربة فريدة في عالم الحلويات مع وصفات حصرية وتقنيات احترافية</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6 max-w-4xl mx-auto mt-4 md:mt-6">
                @foreach($whyChooseItems as $item)
                    @php
                        $cardGradient = $item['card_gradient'] ?? 'from-amber-50 via-white to-orange-50';
                        $cardBorder = $item['card_border'] ?? 'border-amber-200/60';
                        $iconGradient = $item['icon_gradient'] ?? 'from-amber-400 to-orange-500';
                    @endphp
                    <div class="relative flex w-full items-center justify-center mx-auto max-w-[160px] md:max-w-none transition-transform duration-300 hover:-translate-y-1" style="aspect-ratio: 1 / 1;">
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br {{ $cardGradient }}"></div>
                        <div class="absolute inset-[1px] rounded-3xl border {{ $cardBorder }} opacity-70"></div>
                        <div class="absolute inset-0 rounded-3xl bg-white/60 backdrop-blur-[2px]"></div>
                        <div class="relative flex flex-col items-center justify-center gap-2 px-4 md:px-6 py-4 md:py-6 text-center">
                            <div class="flex items-center justify-center h-14 w-14 md:h-16 md:w-16 rounded-2xl md:rounded-3xl bg-gradient-to-br {{ $iconGradient }} text-white shadow-lg ring-4 ring-white/70">
                                <i class="{{ $item['icon'] }} text-2xl md:text-3xl"></i>
                            </div>
                            <h3 class="text-sm md:text-lg font-bold text-gray-800">{{ $item['title'] }}</h3>
                            <p class="text-xs md:text-sm text-gray-600 leading-relaxed">{{ $item['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    
    <!-- قسم الورشات المحسن -->
    <section class="py-12 bg-gradient-to-br from-gray-50 to-white">
        <div class="container mx-auto px-4">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">ورشات الحلويات الفاخرة</h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto mb-6">انضم إلى ورشاتنا الاحترافية الحصرية وتعلم أسرار صنع أرقى الحلويات العالمية</p>
                <a href="{{ route('workshops') }}" class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-semibold text-lg transition-colors">
                    عرض جميع الورشات
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
            </div>
            
            @if($workshops->count() > 0)
                <div class="featured-workshops-grid">
                    @foreach($workshops as $workshop)
                        @php
                            $isFull = $workshop->bookings_count >= $workshop->max_participants;
                            $isRegistrationClosed = !$workshop->is_registration_open;
                            $isCompleted = $workshop->is_completed;
                            $isBooked = !empty($bookedWorkshopIds) && in_array($workshop->id, $bookedWorkshopIds, true);
                            $startDateLabel = $workshop->start_date ? $workshop->start_date->format('d/m/Y') : 'غير محدد';
                            $bookingLocationLabel = $workshop->is_online ? 'ورشة أونلاين' : ($workshop->location ?? 'ورشة حضورية');
                            $bookingDeadlineLabel = $workshop->registration_deadline ? $workshop->registration_deadline->format('d/m/Y') : 'غير محدد';
                            $bookingInstructor = $workshop->instructor ?? 'غير محدد';
                            $bookingButtonStateClasses = $isBooked
                                ? 'bg-green-500 text-white cursor-not-allowed'
                                : 'bg-green-500 hover:bg-green-600 text-white';

                            // Debug information (remove in production)
                            // Uncomment the line below to debug registration status
                            // dd([
                            //     'workshop_id' => $workshop->id,
                            //     'title' => $workshop->title,
                            //     'registration_deadline' => $workshop->registration_deadline,
                            //     'is_upcoming' => $workshop->is_upcoming,
                            //     'is_registration_open' => $workshop->is_registration_open,
                            //     'isRegistrationClosed' => $isRegistrationClosed,
                            //     'now' => now(),
                            // ]);

                            // Temporary debug display (remove in production)
                            // Uncomment the lines below to see debug information
                            // @if($isRegistrationClosed)
                            //     <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-2">
                            //         DEBUG: Registration is closed for {{ $workshop->title }}
                            //     </div>
                            // @endif
                        @endphp
                        <div class="flex">
                            <div class="workshop-card bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-full w-full">
                                        <div class="relative">
                                            <img src="{{ $workshop->image ? asset('storage/' . $workshop->image) : 'https://placehold.co/600x400/f87171/FFFFFF?text=ورشة' }}" 
                                                 alt="{{ $workshop->title }}"
                                                 onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                                            @if($isFull)
                                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                                <span class="text-white font-bold text-lg bg-red-500 px-4 py-2 rounded-full">اكتمل العدد</span>
                                            </div>
                                            @elseif($isRegistrationClosed)
                                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                                <span class="text-white font-bold text-lg bg-yellow-500 px-4 py-2 rounded-full">
                                                    <i class="fas fa-clock ml-2"></i>
                                                    انتهى التسجيل
                                                </span>
                                            </div>
                                            @endif
                                            <div class="absolute top-4 right-4 bg-orange-500 text-white text-sm font-semibold px-3 py-1 rounded-full">{{ $workshop->price }} {{ $workshop->currency }}</div>
                                        </div>
                                        
                                        <div class="p-6 flex flex-col flex-grow">
                                            <div class="mb-2">
                                                <span class="text-sm font-semibold {{ $workshop->is_online ? 'text-blue-600' : 'text-green-600' }}">
                                                    {{ $workshop->is_online ? 'اونلاين' : 'حضوري' }}
                                                </span>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $workshop->title }}</h3>
                                            <p class="text-gray-600 mb-4">مع {{ $workshop->instructor }}</p>
                                            <div class="flex items-center text-gray-500 text-sm mb-4">
                                                <i class="fas fa-calendar-alt mr-2 rtl:ml-2"></i> {{ $workshop->start_date ? $workshop->start_date->format('d/m/Y') : 'غير محدد' }}
                                            </div>
                                            <div class="flex items-center text-gray-500 text-sm mb-4">
                                                <i class="fas fa-map-marker-alt mr-2 rtl:ml-2"></i> {{ $workshop->is_online ? 'اونلاين (مباشر)' : ($workshop->location ?? 'غير محدد') }}
                                            </div>
                                            @if($showAdminMetrics)
                                                <div class="flex items-center text-gray-500 text-sm mb-4">
                                                    <i class="fas fa-users mr-2 rtl:ml-2"></i> {{ $workshop->bookings_count }}/{{ $workshop->max_participants }} مشارك
                                                </div>
                                            @endif
                                            
                                            <!-- الأزرار المثبتة في الأسفل -->
                                            <div class="mt-auto pt-4 border-t border-gray-100">
                                                <div class="flex gap-3 items-center">
                                                    @if($isCompleted)
                                                    <button type="button" class="flex-1 bg-gray-400 text-white font-bold py-3 px-4 rounded-full cursor-not-allowed flex items-center justify-center text-sm">
                                                        <i class="fas fa-check-circle ml-2"></i>
                                                        الورشة مكتملة
                                                    </button>
                                                    @elseif($isBooked)
                                                    <button type="button" class="flex-1 bg-green-500 text-white font-bold py-3 px-4 rounded-full cursor-not-allowed flex items-center justify-center text-sm" disabled>
                                                        <i class="fas fa-check ml-2 booking-button-icon"></i>
                                                        <span class="booking-button-label">تم الحجز بالفعل</span>
                                                    </button>
                                                    @elseif($isFull)
                                                    <button type="button" class="flex-1 bg-gray-400 text-white font-bold py-3 px-4 rounded-full cursor-not-allowed flex items-center justify-center text-sm">
                                                        <i class="fas fa-lock ml-2"></i>
                                                        الورشة مكتملة
                                                    </button>
                                                    @elseif($isRegistrationClosed)
                                                        <button type="button" class="flex-1 bg-yellow-400 text-yellow-800 font-bold py-3 px-4 rounded-full cursor-not-allowed flex items-center justify-center text-sm">
                                                            <i class="fas fa-clock ml-2"></i>
                                                            انتهى التسجيل
                                                        </button>
                                                    @else
                                                        <button type="button"
                                                                class="js-whatsapp-booking flex-1 text-center font-bold py-3 px-4 rounded-full transition-colors flex items-center justify-center text-sm {{ $bookingButtonStateClasses }}"
                                                                data-workshop-id="{{ $workshop->id }}"
                                                                data-title="{{ e($workshop->title) }}"
                                                                data-price="{{ e($workshop->formatted_price) }}"
                                                                data-date="{{ e($startDateLabel) }}"
                                                                data-instructor="{{ e($bookingInstructor) }}"
                                                                data-location="{{ e($bookingLocationLabel) }}"
                                                                data-deadline="{{ e($bookingDeadlineLabel) }}"
                                                                data-is-booked="false">
                                                            <i class="fab fa-whatsapp ml-2 booking-button-icon"></i>
                                                            <span class="booking-button-label">احجز الآن</span>
                                                        </button>
                                                    @endif
                                                    <a href="{{ route('workshop.show', $workshop->slug) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-full transition-colors flex items-center justify-center group">
                                                        <i class="fas fa-info-circle text-sm ml-2 group-hover:text-orange-500 transition-colors"></i>
                                                        <span class="text-sm">تفاصيل</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-20 bg-white rounded-2xl shadow-lg">
                    <div class="w-24 h-24 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-coffee text-4xl text-amber-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-3">لا توجد ورشات متاحة حالياً</h3>
                    <p class="text-gray-500 text-lg max-w-md mx-auto mb-6">نحن نعمل على تحضير ورشات جديدة ومميزة. تحقق مرة أخرى قريباً!</p>
                    <button class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300">
                        اشترك في التنبيهات
                    </button>
                </div>
            @endif
        </div>
    </section>


@endsection

@push('scripts')
@vite(['resources/js/whatsapp-booking.js'])
<script>
document.addEventListener('DOMContentLoaded', function() {
    WhatsAppBooking.configure({
        isLoggedIn: @json(auth()->check()),
        whatsappNumber: '962790553680',
        bookingEndpoint: @json(route('bookings.store')),
        loginUrl: @json(route('login')),
        registerUrl: @json(route('register')),
        user: {
            name: @json(optional(auth()->user())->name ?? 'مستخدم'),
            phone: @json(optional(auth()->user())->phone ?? 'غير محدد'),
            email: @json(optional(auth()->user())->email ?? 'غير محدد'),
        },
    });
    WhatsAppBooking.initButtons();

    // Initialize hero swiper
    const heroSliderEl = document.querySelector('.hero-slider');
    if (heroSliderEl) {
        const heroInitialSlide = window.innerWidth <= 640 ? 1 : 0;
        new Swiper(heroSliderEl, {
            loop: true,
            speed: 700,
            initialSlide: heroInitialSlide,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            grabCursor: true,
            navigation: {
                nextEl: '.hero-slider-next',
                prevEl: '.hero-slider-prev',
            },
            pagination: {
                el: '.hero-slider-pagination',
                clickable: true,
            },
        });
    }

    // Initialize Swiper for recipe cards
    const featuredSwiperEl = document.querySelector('.featured-recipes-swiper');
    if (featuredSwiperEl) {
        new Swiper(featuredSwiperEl, {
            slidesPerView: 'auto',
            spaceBetween: 16,
            grabCursor: true,
            navigation: { 
                nextEl: '#nextBtn', 
                prevEl: '#prevBtn' 
            },
        });
    }

    // Initialize Swiper for tools slider
    const toolsSwiperEl = document.querySelector('.home-tools-swiper');
    if (toolsSwiperEl) {
        new Swiper(toolsSwiperEl, {
            slidesPerView: 1.1,
            spaceBetween: 16,
            grabCursor: true,
            watchOverflow: true,
            navigation: {
                nextEl: '.home-tools-next',
                prevEl: '.home-tools-prev',
            },
            pagination: {
                el: '.home-tools-pagination',
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 1.5,
                    spaceBetween: 18,
                },
                768: {
                    slidesPerView: 2.2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 24,
                },
                1280: {
                    slidesPerView: 3.5,
                    spaceBetween: 28,
                },
                1536: {
                    slidesPerView: 4,
                    spaceBetween: 32,
                },
            },
        });
    }

    // Enable flip interaction on recipe cards
    const recipeCards = document.querySelectorAll('.card-container');
    if (recipeCards.length) {
        recipeCards.forEach((card) => {
            card.addEventListener('click', (event) => {
                // Avoid flipping when clicking actionable elements
                if (event.target.closest('.save-recipe-btn') || event.target.closest('a')) {
                    return;
                }

                recipeCards.forEach((otherCard) => {
                    if (otherCard !== card) {
                        otherCard.classList.remove('is-flipped');
                    }
                });

                card.classList.toggle('is-flipped');
            });
        });

        // Close card when clicking outside
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.card-container')) {
                recipeCards.forEach((card) => card.classList.remove('is-flipped'));
            }
        });
    }

    // Initialize save buttons using the global function from save-recipe.js
    if (typeof window.SaveRecipe !== 'undefined' && window.SaveRecipe.initializeSaveButtons) {
        window.SaveRecipe.initializeSaveButtons();
    } else {
        // Fallback if save-recipe.js is not loaded
        console.warn('save-recipe.js not loaded, using fallback initialization');
        initializeSaveButtonsFallback();
    }
});

function initializeSaveButtonsFallback() {
    const saveButtons = document.querySelectorAll('.save-recipe-btn');
    
    saveButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent card flip
            e.stopImmediatePropagation(); // Prevent other event handlers
            
            const recipeId = this.dataset.recipeId;
            const recipeName = this.dataset.recipeName;
            const isCurrentlySaved = this.dataset.saved === 'true';
            
            // Toggle visual state immediately
            if (isCurrentlySaved) {
                // Change from saved (green) to not saved (orange)
                this.classList.remove('bg-green-500', 'hover:bg-green-600');
                this.classList.add('bg-orange-500', 'hover:bg-orange-600');
                this.querySelector('span').textContent = 'حفظ';
                this.dataset.saved = 'false';
            } else {
                // Change from not saved (orange) to saved (green)
                this.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                this.classList.add('bg-green-500', 'hover:bg-green-600');
                this.querySelector('span').textContent = 'محفوظة';
                this.dataset.saved = 'true';
            }
            
            // Make API call
            fetch('/api/interactions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'include',
                body: JSON.stringify({
                    recipe_id: recipeId,
                    is_saved: !isCurrentlySaved
                })
            })
            .then(response => {
                if (response.status === 401) {
                    // User not logged in, redirect to login
                    window.location.href = '/login';
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show notification
                    showNotification(
                        isCurrentlySaved ? 'تم إلغاء حفظ الوصفة' : 'تم حفظ الوصفة بنجاح',
                        'success'
                    );
                    
                    // تحديث عداد الحفظ في صفحة الوصفة فورياً (فقط للمستخدمين المسجلين)
                    if (typeof window.SaveRecipe !== 'undefined' && window.SaveRecipe.updateRecipePageSaveCount) {
                        window.SaveRecipe.updateRecipePageSaveCount(!isCurrentlySaved);
                    }
                } else {
                    // Revert visual state on error
                    if (isCurrentlySaved) {
                        // Revert back to saved (green)
                        this.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                        this.classList.add('bg-green-500', 'hover:bg-green-600');
                        this.querySelector('span').textContent = 'محفوظة';
                        this.dataset.saved = 'true';
                    } else {
                        // Revert back to not saved (orange)
                        this.classList.remove('bg-green-500', 'hover:bg-green-600');
                        this.classList.add('bg-orange-500', 'hover:bg-orange-600');
                        this.querySelector('span').textContent = 'حفظ';
                        this.dataset.saved = 'false';
                    }
                    
                    showNotification('حدث خطأ أثناء حفظ الوصفة', 'error');
                }
            })
            .catch(error => {
                // Revert visual state on error
                if (isCurrentlySaved) {
                    // Revert back to saved (green)
                    this.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                    this.classList.add('bg-green-500', 'hover:bg-green-600');
                    this.querySelector('span').textContent = 'محفوظة';
                    this.dataset.saved = 'true';
                } else {
                    // Revert back to not saved (orange)
                    this.classList.remove('bg-green-500', 'hover:bg-green-600');
                    this.classList.add('bg-orange-500', 'hover:bg-orange-600');
                    this.querySelector('span').textContent = 'حفظ';
                    this.dataset.saved = 'false';
                }
                
                showNotification('حدث خطأ أثناء حفظ الوصفة', 'error');
            });
        });
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// دالة عرض التنبيه المخصص
function showCustomAlert(message, type = 'info') {
    // إزالة أي تنبيهات سابقة
    const existingAlert = document.getElementById('custom-alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    // تحديد الألوان والأيقونات حسب النوع
    let bgColor, textColor, icon, borderColor;
    switch(type) {
        case 'success':
            bgColor = 'bg-green-50';
            textColor = 'text-green-800';
            icon = 'fas fa-check-circle';
            borderColor = 'border-green-200';
            break;
        case 'error':
            bgColor = 'bg-red-50';
            textColor = 'text-red-800';
            icon = 'fas fa-exclamation-circle';
            borderColor = 'border-red-200';
            break;
        case 'warning':
            bgColor = 'bg-yellow-50';
            textColor = 'text-yellow-800';
            icon = 'fas fa-exclamation-triangle';
            borderColor = 'border-yellow-200';
            break;
        default:
            bgColor = 'bg-blue-50';
            textColor = 'text-blue-800';
            icon = 'fas fa-info-circle';
            borderColor = 'border-blue-200';
    }

    // إنشاء التنبيه
    const alertHTML = `
        <div id="custom-alert" class="fixed top-4 right-4 z-50 max-w-sm w-full mx-4 transform transition-all duration-300 ease-in-out">
            <div class="${bgColor} ${borderColor} border-l-4 rounded-lg shadow-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="${icon} ${textColor} text-xl"></i>
                    </div>
                    <div class="mr-3 flex-1">
                        <p class="${textColor} text-sm font-medium leading-5">
                            ${message}
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <button onclick="closeCustomAlert()" class="${textColor} hover:opacity-75 focus:outline-none">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // إضافة التنبيه للصفحة
    document.body.insertAdjacentHTML('beforeend', alertHTML);

    // إظهار التنبيه مع تأثير
    setTimeout(() => {
        const alert = document.getElementById('custom-alert');
        if (alert) {
            alert.style.transform = 'translateX(0)';
            alert.style.opacity = '1';
        }
    }, 100);

    // إزالة التنبيه تلقائياً بعد 5 ثوان
    setTimeout(() => {
        closeCustomAlert();
    }, 5000);
}

// دالة إغلاق التنبيه
function closeCustomAlert() {
    const alert = document.getElementById('custom-alert');
    if (alert) {
        alert.style.transform = 'translateX(100%)';
        alert.style.opacity = '0';
        setTimeout(() => {
            alert.remove();
        }, 300);
    }
}

window.showCustomAlert = showCustomAlert;
window.closeCustomAlert = closeCustomAlert;
</script>
@endpush
