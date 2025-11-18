@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $textAlignClass = $isRtl ? 'text-right' : 'text-left';
    $textAlignOppositeClass = $isRtl ? 'text-left' : 'text-right';
    $arrowIcon = $isRtl ? 'fa-arrow-left' : 'fa-arrow-right';
    $notSpecifiedLabel = __('workshops.details.not_specified');
    $levelLabels = [
        'beginner' => __('workshops.details.sidebar.levels.beginner'),
        'advanced' => __('workshops.details.sidebar.levels.advanced'),
    ];
    $difficultyLabels = [
        'easy' => __('workshops.details.recipes.difficulty.easy'),
        'medium' => __('workshops.details.recipes.difficulty.medium'),
        'hard' => __('workshops.details.recipes.difficulty.hard'),
    ];
@endphp

@section('title', $workshop->title . ' - ' . __('workshops.details.title_suffix'))

@push('styles')
<style>
    .workshop-details-wrapper {
        font-family: 'Cairo', 'Tajawal', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        color: #0f172a;
    }

    html[dir="ltr"] .workshop-details-wrapper .text-right {
        text-align: left;
    }

    html[dir="ltr"] .workshop-details-wrapper .justify-end {
        justify-content: flex-start;
    }

    .workshop-hero {
        position: relative;
        isolation: isolate;
        background: linear-gradient(120deg, #ff7a18, #f97316 55%, #c2410c);
        color: white;
        border-radius: 1.5rem;
        margin: 2rem auto 0 auto;
        max-width: 1200px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(234, 88, 12, 0.45);
    }

    .workshop-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(255, 237, 213, 0.5), transparent 55%);
        opacity: 0.75;
        pointer-events: none;
    }

    .workshop-hero .grid > * {
        position: relative;
        z-index: 1;
    }
    
    .workshop-hero-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }

    .hero-detail-card {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem;
        border-radius: 1.25rem;
        background: linear-gradient(135deg, rgba(255, 247, 237, 0.98), rgba(255, 210, 165, 0.6));
        border: 1px solid rgba(255, 171, 94, 0.7);
        box-shadow: 0 20px 45px -18px rgba(234, 88, 12, 0.35);
        backdrop-filter: blur(16px);
        transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        color: #0f172a;
        overflow: hidden;
    }

    .hero-detail-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: linear-gradient(120deg, rgba(255, 255, 255, 0.6), transparent 55%);
        pointer-events: none;
        z-index: 0;
    }

    .hero-detail-card:hover {
        transform: translateY(-4px);
        border-color: rgba(249, 115, 22, 0.85);
        box-shadow: 0 30px 60px -24px rgba(249, 115, 22, 0.45);
    }

    .hero-detail-card > * {
        position: relative;
        z-index: 1;
    }

    .hero-detail-icon {
        width: 3.25rem;
        height: 3.25rem;
        border-radius: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: #fff;
        box-shadow: 0 12px 25px -10px rgba(124, 45, 18, 0.45);
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }

    .hero-detail-icon::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top, rgba(255, 255, 255, 0.45), transparent 55%);
        opacity: 0.7;
        pointer-events: none;
    }

    .hero-detail-icon--date {
        background: linear-gradient(135deg, #ffe0b2, #f97316);
    }

    .hero-detail-icon--time {
        background: linear-gradient(135deg, #ffd8aa, #fb923c);
    }

    .hero-detail-icon--instructor {
        background: linear-gradient(135deg, #ffb86c, #ea580c);
    }

    .hero-detail-icon--online {
        background: linear-gradient(135deg, #ff9248, #d97706);
    }

    .hero-detail-icon--location {
        background: linear-gradient(135deg, #fb923c, #b45309);
    }

    .hero-detail-content {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .hero-detail-value {
        display: block;
        font-size: 1.05rem;
        font-weight: 600;
        color: #0f172a;
        letter-spacing: 0.01em;
        line-height: 1.4;
    }

    .hero-detail-extra {
        display: block;
        font-size: 0.8rem;
        color: #9a3412;
        line-height: 1.5;
    }

    .workshop-hero-meta-label {
        display: block;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        color: #9a3412;
        margin-bottom: 0.2rem;
        font-weight: 700;
    }

    /* Add margin to prevent edge sticking */
    .workshop-hero-container {
        padding: 0 1rem;
    }
    
    /* Mobile Responsive Improvements */
    @media (max-width: 768px) {
        .workshop-hero-container {
            padding: 0 1.5rem;
        }
        
        .workshop-hero {
            margin: 1rem auto 0 auto;
            border-radius: 1rem;
        }
        
        .workshop-hero-content {
            padding: 1.5rem;
        }
        
        .workshop-hero-title {
            font-size: 1.75rem;
            line-height: 1.3;
        }
        
        .workshop-hero-description {
            font-size: 1rem;
            line-height: 1.5;
        }
        
        .workshop-hero-details {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.85rem;
        }

        .hero-detail-card {
            padding: 1rem;
        }

        .hero-detail-icon {
            width: 3rem;
            height: 3rem;
            font-size: 1.15rem;
        }

        .hero-detail-value {
            font-size: 1rem;
        }

        .hero-detail-extra {
            font-size: 0.75rem;
        }
        
        .workshop-image {
            height: 250px;
            object-fit: cover;
            object-position: center;
        }
        
        .content-card {
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .sidebar-card {
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .booking-section {
            position: static !important;
            top: auto !important;
        }
        
        .info-item {
            padding: 0.5rem 0;
        }
        
        .info-icon {
            width: 2rem;
            height: 2rem;
            font-size: 1rem;
        }
        
        /* Additional mobile improvements */
        .workshop-details-section {
            padding: 2rem 0;
        }
        
        .workshop-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .workshop-main-content {
            order: 1;
        }
        
        .workshop-sidebar {
            order: 2;
        }
        
        .workshop-content-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .workshop-content-text {
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .booking-button {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
        }
        
        .instructor-card {
            margin-bottom: 1rem;
        }
        
        .instructor-avatar {
            width: 4rem;
            height: 4rem;
        }
        
        .instructor-name {
            font-size: 1.1rem;
        }
        
        .instructor-bio {
            font-size: 0.9rem;
        }
        
        .related-workshops-section {
            padding: 2rem 0;
        }
        
        .related-workshops-title {
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
        }
        
        .related-workshops-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .related-workshop-card {
            margin-bottom: 1rem;
        }
        
        .related-workshop-image {
            height: 180px;
        }
        
        .related-workshop-content {
            padding: 1rem;
        }
        
        .related-workshop-title {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .related-workshop-instructor {
            font-size: 0.9rem;
        }
        
        .related-workshop-price {
            font-size: 1.1rem;
        }
        
        .related-workshop-button {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
    }
    
    @media (max-width: 480px) {
        .workshop-hero-container {
            padding: 0 1rem;
        }
        
        .workshop-hero {
            margin: 0.75rem auto 0 auto;
            border-radius: 0.75rem;
        }
        
        .workshop-hero-content {
            padding: 1rem;
        }
        
        .workshop-hero-title {
            font-size: 1.5rem;
        }
        
        .workshop-hero-description {
            font-size: 0.9rem;
        }
        
        .workshop-hero-details {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        
        .hero-detail-card {
            padding: 0.9rem 1rem;
            gap: 0.75rem;
        }

        .hero-detail-icon {
            width: 2.65rem;
            height: 2.65rem;
            font-size: 1rem;
        }

        .hero-detail-value {
            font-size: 0.98rem;
        }

        .hero-detail-extra {
            font-size: 0.72rem;
        }

        .workshop-hero-meta-label {
            font-size: 0.62rem;
            letter-spacing: 0.16em;
        }
        
        .workshop-image {
            height: 220px;
            object-fit: cover;
            object-position: center;
        }
        
        .content-card {
            padding: 1rem;
        }
        
        .sidebar-card {
            padding: 1rem;
        }
        
        .workshop-details-section {
            padding: 1.5rem 0;
        }
        
        .workshop-content-title {
            font-size: 1.25rem;
        }
        
        .workshop-content-text {
            font-size: 0.9rem;
        }
        
        .booking-button {
            padding: 0.875rem;
            font-size: 0.95rem;
        }
        
        .instructor-avatar {
            width: 3.5rem;
            height: 3.5rem;
        }
        
        .instructor-name {
            font-size: 1rem;
        }
        
        .instructor-bio {
            font-size: 0.85rem;
        }
        
        .related-workshops-title {
            font-size: 1.5rem;
        }
        
        .related-workshop-image {
            height: 160px;
        }
        
        .related-workshop-content {
            padding: 0.875rem;
        }
        
        .related-workshop-title {
            font-size: 0.95rem;
        }
        
        .related-workshop-instructor {
            font-size: 0.85rem;
        }
        
        .related-workshop-price {
            font-size: 1rem;
        }
        
        .related-workshop-button {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }
    }
    
    .workshop-image {
        transition: all 0.3s ease;
        border-radius: 0;
        box-shadow: none;
        border: none;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
    
    .workshop-image:hover {
        transform: none;
        box-shadow: none;
        border-color: transparent;
    }
    
    /* Mobile Image Improvements */
    @media (max-width: 768px) {
        .workshop-image {
            min-height: 250px;
            max-height: 300px;
            object-fit: cover;
            object-position: center;
            width: 100%;
        }
        
        .workshop-hero .relative {
            height: 250px !important;
        }
    }
    
    @media (max-width: 480px) {
        .workshop-image {
            min-height: 220px;
            max-height: 280px;
            object-fit: cover;
            object-position: center;
            width: 100%;
        }
        
        .workshop-hero .relative {
            height: 220px !important;
        }
    }
    
    /* Ensure images maintain aspect ratio */
    .workshop-image {
        aspect-ratio: 16/9;
    }
    
    @media (max-width: 768px) {
        .workshop-image {
            aspect-ratio: 4/3;
        }
    }
    
    .instructor-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .instructor-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border-color: #f97316;
    }
    
    .related-workshop {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .related-workshop:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        border-color: #f97316;
    }
    
    .booking-btn {
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        border: none;
        position: relative;
        overflow: hidden;
    }
    
    .booking-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .booking-btn:hover::before {
        left: 100%;
    }
    
    .booking-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(37, 211, 102, 0.4);
        background: linear-gradient(135deg, #128C7E 0%, #075E54 100%);
    }
    
    .booking-btn:active {
        transform: translateY(0);
        box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
    }
    
    .prose ul > li::before {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    }
    
    
    .content-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }
    
    .content-card:hover {
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    
    .sidebar-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }
    
    .sidebar-card:hover {
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }
    
    .info-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }
    
    .info-item:hover {
        background: #f8fafc;
        padding-right: 1rem;
        border-radius: 0.5rem;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-inline-end: 1rem;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        font-size: 1.1rem;
        box-shadow: 0 4px 15px rgba(249, 115, 22, 0.3);
    }

    .booking-card {
        padding: 0;
        overflow: hidden;
        border: 1px solid rgba(249, 115, 22, 0.15);
        border-radius: 1.75rem;
        box-shadow: 0 25px 45px rgba(15, 23, 42, 0.08);
        background: #fff;
        width: 100%;
        max-width: 520px;
        margin-inline: auto;
    }

    .booking-card-header {
        background: radial-gradient(circle at top right, rgba(254, 215, 170, 0.85), rgba(249, 115, 22, 0.95));
        padding: clamp(1.5rem, 2.8vw, 2.25rem);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: clamp(1rem, 2.5vw, 1.5rem);
        color: #fff;
        flex-wrap: wrap;
    }

    .booking-card-eyebrow {
        font-size: clamp(0.7rem, 1.2vw, 0.85rem);
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 0.5rem;
    }

    .booking-card-title {
        font-size: clamp(1.25rem, 2vw, 1.75rem);
        font-weight: 700;
        margin: 0;
    }

    .booking-card-subtitle {
        margin-top: 0.5rem;
        font-size: clamp(0.85rem, 1.5vw, 0.95rem);
        color: rgba(255, 255, 255, 0.85);
    }

    .booking-status-pill {
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 999px;
        padding: 0.35rem 1.25rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        font-size: clamp(0.75rem, 1.4vw, 0.95rem);
        white-space: nowrap;
    }

    .booking-card-highlight {
        padding: clamp(1.25rem, 2.6vw, 2rem) clamp(1.25rem, 2.6vw, 2.25rem) clamp(1rem, 2vw, 1.5rem);
        background: linear-gradient(120deg, rgba(254, 249, 195, 0.8), rgba(255, 247, 237, 0.65));
    }

    .booking-price-label {
        font-size: clamp(0.75rem, 1.3vw, 0.9rem);
        color: #92400e;
    }

    .booking-price {
        font-size: clamp(1.8rem, 3vw, 2.4rem);
        font-weight: 800;
        color: #ea580c;
        margin: 0.35rem 0;
    }

    .booking-price-hint {
        font-size: clamp(0.75rem, 1.3vw, 0.85rem);
        color: #78350f;
    }

    .booking-highlight-meta {
        margin-top: 1.5rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: clamp(0.75rem, 2vw, 1rem);
    }

    .booking-meta-label {
        font-size: clamp(0.7rem, 1.1vw, 0.8rem);
        color: #92400e;
        letter-spacing: 0.05em;
    }

    .booking-meta-value {
        font-size: clamp(1rem, 1.8vw, 1.15rem);
        font-weight: 700;
        color: #0f172a;
        margin-top: 0.35rem;
    }

    .booking-card-body {
        padding: clamp(1.25rem, 2.6vw, 2rem) clamp(1.25rem, 2.6vw, 2.25rem) 0;
    }

    .booking-methods-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.25rem;
    }

    .booking-info-card {
        border: 1px solid #f1f5f9;
        border-radius: 1.25rem;
        padding: clamp(0.75rem, 2vw, 1rem);
        background: #fff;
        display: flex;
        align-items: center;
        gap: clamp(0.75rem, 1.8vw, 0.85rem);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        text-align: right;
    }

    .booking-info-card:hover {
        border-color: rgba(249, 115, 22, 0.4);
        box-shadow: 0 15px 25px rgba(15, 23, 42, 0.08);
    }

    .booking-info-icon {
        width: clamp(2.5rem, 3vw, 3rem);
        height: clamp(2.5rem, 3vw, 3rem);
        border-radius: 0.9rem;
        background: linear-gradient(135deg, #fed7aa, #fb923c);
        color: #9a3412;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(1rem, 1.6vw, 1.2rem);
        box-shadow: 0 10px 20px rgba(249, 115, 22, 0.25);
        margin-left: 0.75rem;
    }

    .booking-info-label {
        font-size: clamp(0.75rem, 1.2vw, 0.85rem);
        color: #64748b;
        margin-bottom: 0.15rem;
    }

    .booking-info-value {
        font-size: clamp(0.95rem, 1.6vw, 1.05rem);
        font-weight: 700;
        color: #0f172a;
    }

    .booking-card-actions {
        padding: clamp(1.25rem, 2.6vw, 2rem) clamp(1.25rem, 2.6vw, 2.25rem) clamp(1.75rem, 3vw, 2.5rem);
        border-top: 1px solid #f1f5f9;
        background: #fff;
    }

    .booking-action-hint {
        margin-top: 0.75rem;
        font-size: clamp(0.8rem, 1.3vw, 0.9rem);
        color: #6b7280;
        text-align: center;
        line-height: 1.55;
    }

    .floating-booking-bar {
        position: fixed;
        left: 50%;
        bottom: var(--floating-booking-desktop-offset, 1rem);
        transform: translate(-50%, 0);
        width: min(620px, calc(100% - 2.5rem));
        z-index: 60;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.9rem;
        border-radius: 1.25rem;
        background: #fff;
        box-shadow: 0 30px 65px rgba(15, 23, 42, 0.25);
        border: 1px solid rgba(15, 23, 42, 0.08);
        margin-bottom: env(safe-area-inset-bottom, 0);
        transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease, bottom 0.3s ease;
    }

    .floating-booking-bar.is-hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translate(-50%, 1.5rem);
    }

    .floating-booking-price {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0;
        min-width: 0;
    }

    .floating-booking-price-label {
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #a16207;
    }

    .floating-booking-price-value {
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .floating-booking-meta {
        font-size: 0.78rem;
        color: #475569;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .floating-booking-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 999px;
        background: linear-gradient(135deg, #fb923c, #ea580c);
        color: #fff;
        font-weight: 700;
        font-size: 0.85rem;
        text-decoration: none;
        min-width: 120px;
        line-height: 1;
        box-shadow: 0 18px 35px rgba(249, 115, 22, 0.35);
    }

    .floating-booking-btn:focus-visible {
        outline: 3px solid rgba(249, 115, 22, 0.35);
        outline-offset: 3px;
    }

    html[dir="rtl"] .floating-booking-bar {
        flex-direction: row-reverse;
        text-align: right;
    }

    html[dir="rtl"] .floating-booking-btn i {
        transform: rotate(180deg);
    }

    html[dir="rtl"] .floating-booking-price {
        align-items: flex-end;
    }

    @media (max-width: 1024px) {
        .floating-booking-bar {
            display: flex;
        }

        .floating-booking-meta {
            font-size: 0.8rem;
        }

        .workshop-details-wrapper {
            padding-bottom: 6.25rem;
        }
    }

    @media (max-width: 768px) {
        .booking-card-header,
        .booking-card-highlight,
        .booking-card-body,
        .booking-card-actions {
            padding: 1.5rem;
        }

        .booking-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .booking-card-title {
            font-size: 1.5rem;
        }

        .booking-highlight-meta {
            grid-template-columns: 1fr;
        }

        .floating-booking-bar {
            bottom: calc(var(--floating-booking-mobile-offset, 4.5rem) + env(safe-area-inset-bottom, 0));
            padding: 0.75rem 1.25rem;
        }

        .floating-booking-btn {
            padding: 0.7rem 1.35rem;
            min-width: 145px;
        }
    }
    
</style>
@endpush

@section('content')
@php
    $notSpecifiedLabel = $notSpecifiedLabel ?? __('workshops.details.not_specified');
    $showAdminMetrics = auth()->check() && auth()->user()->isAdmin();
    $stripePublicKey = config('services.stripe.public_key');
    $stripeEnabled = $stripePublicKey && config('services.stripe.secret_key');
    $onlinePaymentsEnabled = $stripeEnabled;
    $whatsappBookingEnabled = data_get($whatsappBookingConfig ?? [], 'enabled', false);
    $whatsappBookingPayload = [
        'isLoggedIn' => data_get($whatsappBookingConfig ?? [], 'isLoggedIn', false),
        'whatsappNumber' => data_get($whatsappBookingConfig ?? [], 'number'),
        'bookingEndpoint' => data_get($whatsappBookingConfig ?? [], 'bookingEndpoint'),
        'bookingNotes' => data_get($whatsappBookingConfig ?? [], 'notes'),
        'loginUrl' => data_get($whatsappBookingConfig ?? [], 'loginUrl'),
        'registerUrl' => data_get($whatsappBookingConfig ?? [], 'registerUrl'),
        'user' => data_get($whatsappBookingConfig ?? [], 'user', []),
    ];
    $whatsappPriceLabel = trim($workshop->formatted_price ?? ($workshop->price.' '.$workshop->currency));
    $whatsappDateLabel = $workshop->start_date
        ? $workshop->start_date->format('d/m/Y h:i A')
        : $notSpecifiedLabel;
    $whatsappDeadlineLabel = $workshop->registration_deadline
        ? $workshop->registration_deadline->format('d/m/Y')
        : $notSpecifiedLabel;
    $whatsappInstructorLabel = $workshop->instructor ?? $notSpecifiedLabel;
    $whatsappLocationLabel = $workshop->is_online
        ? __('workshops.labels.online_workshop')
        : ($workshop->location ?? $notSpecifiedLabel);
    $whatsappTopicsLabel = $workshop->what_you_will_learn
        ? (string) \Illuminate\Support\Str::of($workshop->what_you_will_learn)->stripTags()->squish()
        : $notSpecifiedLabel;
    $whatsappRequirementsLabel = $workshop->requirements
        ? (string) \Illuminate\Support\Str::of($workshop->requirements)->stripTags()->squish()
        : $notSpecifiedLabel;
    $whatsappDurationLabel = $workshop->duration
        ? $workshop->formatted_duration
        : $notSpecifiedLabel;
    $legalTermsUrl = config('legal.terms_url') ?: route('legal.terms');
    $whatsappTermsLabel = __('workshops.whatsapp.terms_fallback', ['url' => $legalTermsUrl]);
    $isWorkshopFull = $workshop->max_participants && $workshop->bookings_count >= $workshop->max_participants;
    $userBookedViaWhatsapp = (bool) ($userBooking?->is_whatsapp_booking ?? false);
    $whatsappPendingApproval = $userBookedViaWhatsapp && ($userBooking?->status !== 'confirmed');
    $whatsappButtonAvailable = $whatsappBookingEnabled
        && !$workshop->is_completed
        && $workshop->is_registration_open
        && !$isWorkshopFull
        && (empty($userBooking) || $userBookedViaWhatsapp);
    $showFloatingBookingButton = !$userBooking
        && !$workshop->is_completed
        && $workshop->is_registration_open
        && !($isWorkshopFull || $workshop->is_fully_booked);
    $workshopDateLabel = $workshop->start_date ? $workshop->start_date->format('d/m/Y') : $notSpecifiedLabel;
    $workshopStartTimeLabel = $workshop->start_date ? $workshop->start_date->format('g:i A') : $notSpecifiedLabel;
    $workshopEndTimeLabel = $workshop->end_date ? $workshop->end_date->format('g:i A') : $notSpecifiedLabel;
    $workshopStartDateTimeLabel = $workshop->start_date ? $workshop->start_date->format('m/d/Y g:i A') : $notSpecifiedLabel;
    $workshopEndDateTimeLabel = $workshop->end_date ? $workshop->end_date->format('m/d/Y g:i A') : $notSpecifiedLabel;
    $workshopStartIso = optional($workshop->start_date)->toIso8601String();
    $workshopEndIso = optional($workshop->end_date)->toIso8601String();
    $workshopDeadlineLabel = $workshop->registration_deadline ? $workshop->registration_deadline->format('d/m/Y') : $notSpecifiedLabel;
    $workshopLocationLabel = $whatsappLocationLabel;
    $bookingStatusPill = [
        'label' => __('workshops.details.booking_status.open'),
        'classes' => 'bg-white/20 text-white border-white/30',
    ];
    if ($workshop->is_completed) {
        $bookingStatusPill = [
            'label' => __('workshops.details.booking_status.completed'),
            'classes' => 'bg-gray-900/30 text-white border-white/20',
        ];
    } elseif ($isWorkshopFull || $workshop->is_fully_booked) {
        $bookingStatusPill = [
            'label' => __('workshops.details.booking_status.full'),
            'classes' => 'bg-rose-100 text-rose-700 border-rose-200',
        ];
    } elseif (! $workshop->is_registration_open) {
        $bookingStatusPill = [
            'label' => __('workshops.details.booking_status.closed'),
            'classes' => 'bg-amber-100 text-amber-700 border-amber-200',
        ];
    } elseif ($userBooking) {
        $bookingStatusPill = [
            'label' => __('workshops.details.booking_status.booked'),
            'classes' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        ];
    }
@endphp
<div class="workshop-details-wrapper min-h-screen" style="background-color: #f3f4f6;">
    <!-- Workshop Hero Section -->
    <section class="workshop-hero-container">
        <div class="workshop-hero">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                <!-- Workshop Info -->
                <div class="p-8 lg:p-12 text-white flex flex-col justify-center workshop-hero-content">
                    <div class="mb-8 space-y-4">
                        <span class="bg-white/20 text-white text-sm font-semibold px-4 py-2 rounded-full inline-flex items-center gap-2">
                            @if($workshop->is_featured)
                                <i class="fas fa-star text-yellow-300"></i>
                                {{ __('workshops.details.hero.featured_badge') }}
                            @else
                                {{ __('workshops.details.hero.default_badge') }}
                            @endif
                        </span>
                        <h1 class="text-3xl lg:text-4xl font-bold leading-tight workshop-hero-title">
                            {{ $workshop->title }}
                        </h1>
                        @if(! empty($workshop->description))
                            <p class="text-lg text-amber-100 leading-relaxed workshop-hero-description">
                                {{ $workshop->description }}
                            </p>
                        @endif
                    </div>

                    <!-- Compact Hero Details -->
                    <div class="mb-8 workshop-hero-details">
                        <div class="hero-detail-card">
                            <div class="hero-detail-icon hero-detail-icon--date">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div class="hero-detail-content">
                                <span class="workshop-hero-meta-label">{{ __('workshops.details.hero.date_label') }}</span>
                                <span class="hero-detail-value">{{ $workshopDateLabel }}</span>
                            </div>
                        </div>
                        <div class="hero-detail-card">
                            <div class="hero-detail-icon hero-detail-icon--time">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="hero-detail-content">
                                <span class="workshop-hero-meta-label">{{ __('workshops.details.booking_card.hours_label') }}</span>
                                <span class="hero-detail-value">{{ $workshopStartTimeLabel }} - {{ $workshopEndTimeLabel }}</span>
                                @if($workshopStartIso)
                                    <span
                                        class="hero-detail-extra"
                                        data-local-time
                                        data-source-time="{{ $workshopStartIso }}"
                                        data-label="{{ __('workshops.details.timezones.viewer_label') }}"
                                        data-template="{{ __('workshops.details.timezones.viewer_timezone_template') }}"
                                        data-fallback-timezone="{{ __('workshops.details.timezones.viewer_timezone_fallback') }}"
                                        data-placeholder="{{ __('workshops.details.timezones.viewer_placeholder') }}"
                                        data-locale="{{ app()->getLocale() }}"
                                        data-format="datetime-full"
                                    >{{ __('workshops.details.timezones.viewer_placeholder') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="hero-detail-card">
                            <div class="hero-detail-icon hero-detail-icon--instructor">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="hero-detail-content">
                                <span class="workshop-hero-meta-label">{{ __('workshops.details.hero.instructor_label') }}</span>
                                <span class="hero-detail-value">{{ $workshop->instructor ?? $notSpecifiedLabel }}</span>
                            </div>
                        </div>
                        <div class="hero-detail-card">
                            <div class="hero-detail-icon {{ $workshop->is_online ? 'hero-detail-icon--online' : 'hero-detail-icon--location' }}">
                                <i class="fas {{ $workshop->is_online ? 'fa-video' : 'fa-map-marker-alt' }}"></i>
                            </div>
                            <div class="hero-detail-content">
                                <span class="workshop-hero-meta-label">{{ __('workshops.details.hero.format_label') }}</span>
                                <span class="hero-detail-value">{{ $workshop->is_online ? __('workshops.labels.online_workshop') : ($workshop->location ?? __('workshops.labels.offline_workshop')) }}</span>
                                @if(!$workshop->is_online && $workshop->address)
                                    <span class="hero-detail-extra">{{ $workshop->address }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 pt-2">
                        <div class="text-3xl lg:text-4xl font-bold text-white">
                            {{ $workshop->formatted_price }}
                        </div>
                        <a href="#booking-methods"
                           class="inline-flex items-center justify-center gap-3 px-8 py-3 rounded-full bg-white/15 hover:bg-white/25 text-white font-semibold text-lg transition sm:w-auto"
                           data-scroll-target="#booking-methods"
                           data-scroll-fallback="#workshop-booking">
                            <span>{{ __('workshops.details.booking_card.title') }}</span>
                            <i class="fas {{ $arrowIcon }} text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- Workshop Image -->
                <div class="relative h-64 lg:h-auto overflow-hidden">
                    <img src="{{ $workshop->image ? asset('storage/' . $workshop->image) : 'https://placehold.co/800x600/f87171/FFFFFF?text=' . urlencode(__('workshops.labels.featured_placeholder_text')) }}" 
                         alt="{{ $workshop->title }}" 
                         class="workshop-image w-full h-full object-cover"
                        onerror="this.src='{{ \App\Support\BrandAssets::logoAsset('webp') }}'; this.alt='{{ __('workshops.labels.fallback_image_alt') }}';"
                         loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-l from-transparent to-amber-500/20"></div>
                    @if($workshop->is_fully_booked)
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                            <span class="text-white text-2xl font-bold bg-red-500 px-6 py-3 rounded-full">
                                {{ __('workshops.details.booking_status.full') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Workshop Details -->
    <section class="py-16 workshop-details-section">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 workshop-grid">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-8 workshop-main-content">
                        <!-- About Workshop -->
                        <div class="content-card p-8 sm:p-10">
                            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-right flex items-center workshop-content-title">
                                <div class="info-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                {{ __('workshops.details.sections.about') }}
                            </h2>
                            <div class="prose prose-lg max-w-none text-gray-700 text-right leading-relaxed workshop-content-text">
                                {!! $workshop->content !!}
                            </div>
                        </div>

                        @if($workshop->what_you_will_learn)
                            <div class="content-card p-8 sm:p-10">
                                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-right flex items-center workshop-content-title">
                                    <div class="info-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    {{ __('workshops.details.sections.learn') }}
                                </h2>
                                <div class="prose prose-lg max-w-none text-gray-700 text-right leading-relaxed workshop-content-text">
                                    {!! $workshop->what_you_will_learn !!}
                                </div>
                            </div>
                        @endif

                        @if($workshop->requirements)
                            <div class="content-card p-8 sm:p-10">
                                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-right flex items-center workshop-content-title">
                                    <div class="info-icon">
                                        <i class="fas fa-list-check"></i>
                                    </div>
                                    {{ __('workshops.details.sections.requirements') }}
                                </h2>
                                <div class="prose prose-lg max-w-none text-gray-700 text-right leading-relaxed workshop-content-text">
                                    {!! $workshop->requirements !!}
                                </div>
                            </div>
                        @endif

                        @if($workshop->materials_needed)
                            <div class="content-card p-8 sm:p-10">
                                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-right flex items-center workshop-content-title">
                                    <div class="info-icon">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    {{ __('workshops.details.sections.materials') }}
                                </h2>
                                <div class="prose prose-lg max-w-none text-gray-700 text-right leading-relaxed workshop-content-text">
                                    {!! $workshop->materials_needed !!}
                                </div>
                            </div>
                        @endif

                        @if($workshop->recipes && $workshop->recipes->count() > 0)
                            <div class="content-card p-8 sm:p-10">
                                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-right flex items-center workshop-content-title">
                                    <div class="info-icon">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                    {{ __('workshops.details.sections.recipes') }}
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($workshop->recipes as $recipe)
                                        @php
                                            $recipeMinutes = (int) ($recipe->prep_time ?? 0) + (int) ($recipe->cook_time ?? 0);
                                        @endphp
                                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                                            <a href="{{ route('recipe.show', $recipe->slug) }}" class="block group">
                                                <div class="relative overflow-hidden">
                                                    <img src="{{ $recipe->image_url ?: 'https://placehold.co/400x300/f87171/FFFFFF?text=' . urlencode(__('workshops.details.recipes.placeholder')) }}" 
                                                         alt="{{ $recipe->title }}" 
                                                         class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                                                        onerror="this.src='{{ \App\Support\BrandAssets::logoAsset('webp') }}'; this.alt='{{ __('workshops.labels.fallback_image_alt') }}';" loading="lazy">
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                                    <div class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                        <div class="bg-white/90 backdrop-blur-sm rounded-full p-2">
                                                            <i class="fas fa-arrow-left text-orange-500"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="p-6">
                                                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors line-clamp-2">
                                                        {{ $recipe->title }}
                                                    </h3>
                                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                                        {{ Str::limit($recipe->description, 100) }}
                                                    </p>
                                                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                                        <div class="flex items-center">
                                                            <i class="fas fa-clock text-orange-500 ml-1"></i>
                                                            <span>{{ __('workshops.details.recipes.minutes', ['count' => $recipeMinutes]) }}</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <i class="fas fa-users text-orange-500 ml-1"></i>
                                                            <span>
                                                                {{ $recipe->servings
                                                                    ? __('workshops.details.recipes.servings', ['count' => (int) $recipe->servings])
                                                                    : $notSpecifiedLabel }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <i class="fas fa-signal text-orange-500 ml-1"></i>
                                                            <span>{{ $difficultyLabels[$recipe->difficulty] ?? $difficultyLabels['medium'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center text-orange-500">
                                                            <i class="fas fa-user text-sm ml-1"></i>
                                                            <span class="text-sm font-medium">{{ $recipe->author }}</span>
                                                        </div>
                                                        <span class="text-orange-500 font-semibold">
                                                            <i class="fas {{ $arrowIcon }} text-xs {{ $isRtl ? 'mr-1' : 'ml-1' }}"></i>
                                                            {{ __('workshops.details.recipes.view') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-8 workshop-sidebar">
                        <div class="sidebar-card p-8" id="workshop-summary">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-right flex items-center">
                                <div class="info-icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                {{ __('workshops.details.booking_card.summary.title') }}
                            </h3>

                            <div class="space-y-4">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">{{ __('workshops.details.booking_card.summary.date') }}</p>
                                        <p class="font-semibold text-gray-900">{{ $workshopDateLabel }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">{{ __('workshops.details.booking_card.summary.start_time') }}</p>
                                        <p class="font-semibold text-gray-900">{{ $workshopStartTimeLabel }} - {{ $workshopEndTimeLabel }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">{{ __('workshops.details.booking_card.summary.location') }}</p>
                                        <p class="font-semibold text-gray-900">{{ $workshopLocationLabel }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">{{ __('workshops.details.booking_card.summary.instructor') }}</p>
                                        <p class="font-semibold text-gray-900">{{ $workshop->instructor ?? $notSpecifiedLabel }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">{{ __('workshops.details.booking_card.summary.cost') }}</p>
                                        <p class="font-semibold text-gray-900">{{ $workshop->formatted_price }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="workshop-booking" class="sidebar-card booking-section booking-card">
                            <div class="booking-card-header text-right w-full">
                                <div class="w-full">
                                    <p class="booking-card-eyebrow">{{ __('workshops.details.booking_card.eyebrow') }}</p>
                                    <h3 class="booking-card-title flex items-center gap-3 justify-end">
                                        <i class="fas fa-calendar-check text-white text-xl"></i>
                                        <span>{{ __('workshops.details.booking_card.title') }}</span>
                                    </h3>
                                    <p class="booking-card-subtitle">{{ __('workshops.details.booking_card.subtitle') }}</p>
                                </div>
                                <span class="booking-status-pill shrink-0 {{ $bookingStatusPill['classes'] }}">
                                    <i class="fas fa-circle text-xs"></i>
                                    {{ $bookingStatusPill['label'] }}
                                </span>
                            </div>
                            <div class="booking-card-highlight text-right">
                                <div>
                                    <span class="booking-price-label">{{ __('workshops.details.booking_card.price_label') }}</span>
                                    <p class="booking-price">{{ $workshop->formatted_price }}</p>
                                    <p class="booking-price-hint">{{ __('workshops.details.booking_card.price_hint') }}</p>
                                </div>
                                <div class="booking-highlight-meta text-right">
                                    <div>
                                        <span class="booking-meta-label">{{ __('workshops.details.booking_card.deadline_label') }}</span>
                                        <p class="booking-meta-value">{{ $workshopDeadlineLabel }}</p>
                                    </div>
                                    <div>
                                        <span class="booking-meta-label">{{ __('workshops.details.hero.duration_label') }}</span>
                                        <p class="booking-meta-value">{{ $workshop->formatted_duration ?: $notSpecifiedLabel }}</p>
                                    </div>
                                    @if($showAdminMetrics)
                                        <div>
                                            <span class="booking-meta-label">{{ __('workshops.details.booking_card.confirmed_label') }}</span>
                                            <p class="booking-meta-value">{{ $workshop->bookings_count }}/{{ $workshop->max_participants ?? '—' }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="booking-card-body text-right">
                                <div class="booking-info-card text-right">
                                    <div class="booking-info-icon">
                                        <i class="fas fa-door-open"></i>
                                    </div>
                                    <div>
                                        <p class="booking-info-label">{{ __('workshops.details.booking_card.join_label') }}</p>
                                        <p class="booking-info-value">{{ $workshop->is_online ? __('workshops.labels.online_workshop') : __('workshops.labels.offline_workshop') }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $workshop->is_online ? __('workshops.details.booking_card.join_hint_online') : __('workshops.details.booking_card.join_hint_offline') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="booking-card-actions" id="booking-methods">
                                <div id="booking-primary-action">
                                @if($workshop->is_completed)
                                    <button class="w-full bg-gray-300 text-gray-500 font-bold py-4 px-6 rounded-xl cursor-not-allowed text-lg booking-button">
                                        <i class="fas fa-check-circle mr-2 rtl:ml-2"></i>
                                        {{ __('workshops.details.booking_card.cta_completed') }}
                                    </button>
                                @elseif($userBooking)
                                    @if($userBooking->status === 'confirmed')
                                        @if($workshop->is_online && $workshop->meeting_link && $userBooking->public_code)
                                            <a href="{{ $userBooking->secure_join_url }}" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl text-lg booking-button flex items-center justify-center gap-2">
                                                <i class="fas fa-video mr-2 rtl:ml-2"></i>
                                                <span>{{ __('workshops.details.booking_card.cta_join') }}</span>
                                            </a>
                                            <p class="booking-action-hint">
                                                {{ __('workshops.details.booking_card.join_room_hint') }}
                                            </p>
                                        @else
                                            <button class="w-full bg-green-500 text-white font-bold py-4 px-6 rounded-xl cursor-not-allowed text-lg booking-button" disabled>
                                                <i class="fas fa-check mr-2 rtl:ml-2 booking-button-icon"></i>
                                                <span class="booking-button-label">{{ __('workshops.details.booking_card.cta_confirmed') }}</span>
                                            </button>
                                            <p class="booking-action-hint">
                                                {{ __('workshops.details.booking_card.confirmation_hint') }}
                                            </p>
                                        @endif
                                    @else
                                        <button class="w-full bg-yellow-400 text-yellow-900 font-bold py-4 px-6 rounded-xl cursor-not-allowed text-lg booking-button" disabled>
                                            <i class="fas fa-hourglass-half mr-2 rtl:ml-2 booking-button-icon"></i>
                                            <span class="booking-button-label">{{ __('workshops.details.booking_card.cta_pending') }}</span>
                                        </button>
                                        <p class="booking-action-hint">
                                            {{ __('workshops.details.booking_card.pending_hint') }}
                                        </p>
                                    @endif
                                @elseif($workshop->is_fully_booked)
                                    <button class="w-full bg-gray-300 text-gray-500 font-bold py-4 px-6 rounded-xl cursor-not-allowed text-lg booking-button">
                                        <i class="fas fa-times-circle mr-2 rtl:ml-2"></i>
                                        {{ __('workshops.details.booking_card.cta_full') }}
                                    </button>
                                @elseif(! $workshop->is_registration_open)
                                    <button class="w-full bg-yellow-400 text-yellow-800 font-bold py-4 px-6 rounded-xl cursor-not-allowed text-lg booking-button">
                                        <i class="fas fa-clock mr-2 rtl:ml-2"></i>
                                        {{ __('workshops.details.booking_card.cta_closed') }}
                                    </button>
                                @else
                                    @guest
                                        <button class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-4 px-6 rounded-xl text-lg booking-button transition-all duration-300 transform hover:scale-105"
                                                onclick="showLoginRequiredModal({{ $workshop->id }})">
                                            <i class="fas fa-sign-in-alt mr-2 rtl:ml-2"></i>
                                            {{ __('workshops.details.booking_card.login_required') }}
                                        </button>
                                        <p class="booking-action-hint">
                                            {{ __('workshops.details.booking_card.login_hint') }}
                                        </p>
                                    @else
                                        @if(! $onlinePaymentsEnabled)
                                            <div class="bg-yellow-50 border border-yellow-100 rounded-2xl p-5 text-right">
                                                <p class="text-yellow-900 font-semibold mb-1">{{ __('workshops.details.booking_card.payments_disabled_title') }}</p>
                                                <p class="text-sm text-yellow-800">{{ __('workshops.details.booking_card.payments_disabled_hint') }}</p>
                                            </div>
                                        @else
                                            <div class="space-y-6 text-right">
                                                <div class="booking-methods-grid" id="booking-methods-grid">
                                                    @if($stripeEnabled && ! $userBookedViaWhatsapp)
                                                    <div class="rounded-2xl border border-indigo-100 bg-white p-6 shadow-sm space-y-5" id="stripe-checkout-card">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <div>
                                                                <p class="text-sm text-gray-500">{{ __('workshops.stripe.label') }}</p>
                                                                <p class="text-lg font-bold text-gray-900">{{ __('workshops.stripe.title') }}</p>
                                                            </div>
                                                            <div class="text-indigo-500">
                                                                <i class="fas fa-credit-card text-2xl"></i>
                                                            </div>
                                                        </div>
                                                        <p class="text-sm text-gray-500">
                                                            {{ __('workshops.stripe.description') }}
                                                        </p>
                                                        <div class="space-y-4">
                                                            <div id="stripe-wallet-section" class="hidden rounded-xl border border-indigo-100 bg-indigo-50/60 p-4 space-y-3">
                                                                <div class="flex items-center justify-between text-sm text-indigo-900 font-semibold">
                                                                    <span>{{ __('workshops.stripe.wallet_label') }}</span>
                                                                    <span class="text-xs font-normal text-indigo-500">{{ __('workshops.stripe.wallet_hint') }}</span>
                                                                </div>
                                                                <div id="stripe-wallet-button" class="min-h-[48px] flex items-center justify-center"></div>
                                                                <p id="stripe-wallet-hint" class="hidden text-xs text-gray-500 text-center">
                                                                    {{ __('workshops.stripe.wallet_hint') }}
                                                                </p>
                                                                <p id="stripe-wallet-unavailable" class="hidden text-xs text-amber-600 text-center">
                                                                    {{ __('workshops.stripe.wallet_unavailable') }}
                                                                </p>
                                                            </div>
                                                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-inner">
                                                                <div id="stripe-payment-element" class="min-h-[140px]"></div>
                                                            </div>
                                                            <p id="stripe-card-errors" class="hidden text-sm text-red-600 text-center" role="alert"></p>
                                                            <p id="stripe-success-message" class="hidden text-sm text-green-600 text-center" role="status"></p>
                                                            <button type="button"
                                                                    id="stripe-submit-button"
                                                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 px-4 rounded-xl booking-button flex items-center justify-center gap-2 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                                                <span id="stripe-submit-label">{{ __('workshops.stripe.pay_button') }}</span>
                                                                <span id="stripe-submit-spinner" class="hidden items-center justify-center gap-2 text-sm">
                                                                    <i class="fas fa-spinner fa-spin ml-1"></i>
                                                                    {{ __('workshops.stripe.processing') }}
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @if($whatsappButtonAvailable)
                                                        <div class="rounded-2xl border border-green-100 bg-white p-6 shadow-sm space-y-4">
                                                            <div class="js-whatsapp-pending-alert {{ $whatsappPendingApproval ? '' : 'hidden' }} rounded-2xl border border-amber-200 bg-amber-50/60 p-3 text-right flex items-start gap-3" data-workshop-id="{{ $workshop->id }}">
                                                                <span class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                                                    <i class="fas fa-hourglass-half"></i>
                                                                </span>
                                                                <div class="flex-1">
                                                                    <p class="text-sm font-semibold text-amber-800">{{ __('workshops.whatsapp.pending_badge') }}</p>
                                                                    <p class="text-xs text-amber-700 mt-1">{{ __('workshops.whatsapp.pending_helper') }}</p>
                                                                </div>
                                                            </div>
                                                            <p class="text-sm font-semibold text-gray-900 text-right flex items-center justify-end gap-2">
                                                                <span class="js-whatsapp-section-label"
                                                                      data-default-label="{{ __('workshops.whatsapp.button') }}"
                                                                      data-followup-label="{{ __('workshops.whatsapp.followup_title') }}">
                                                                    {{ $userBookedViaWhatsapp ? __('workshops.whatsapp.followup_title') : __('workshops.whatsapp.button') }}
                                                            </span>
                                                            <i class="fab fa-whatsapp text-green-500 text-lg"></i>
                                                        </p>
                                                            <div class="js-whatsapp-booking-section {{ $userBookedViaWhatsapp ? 'hidden' : '' }}" data-workshop-id="{{ $workshop->id }}">
                                                                <button type="button"
                                                                        class="w-full bg-white border border-green-200 text-green-700 font-bold py-3.5 px-4 rounded-xl booking-button flex items-center justify-center gap-2 js-whatsapp-booking"
                                                                        data-workshop-id="{{ $workshop->id }}"
                                                                        data-title="{{ e($workshop->title) }}"
                                                                        data-price="{{ $whatsappPriceLabel }}"
                                                                        data-date="{{ $whatsappDateLabel }}"
                                                                        data-instructor="{{ $whatsappInstructorLabel }}"
                                                                        data-location="{{ $whatsappLocationLabel }}"
                                                                        data-deadline="{{ $whatsappDeadlineLabel }}"
                                                                        data-topics="{{ e($whatsappTopicsLabel) }}"
                                                                        data-requirements="{{ e($whatsappRequirementsLabel) }}"
                                                                        data-duration="{{ e($whatsappDurationLabel) }}"
                                                                        data-terms="{{ e($whatsappTermsLabel) }}">
                                                                    <i class="fab fa-whatsapp text-xl booking-button-icon"></i>
                                                                    <span class="booking-button-label">{{ __('workshops.whatsapp.button') }}</span>
                                                                </button>
                                                                <p class="text-xs text-gray-500 text-center">{{ __('workshops.whatsapp.helper') }}</p>
                                                                <p class="text-xs text-amber-600 text-center">{{ __('workshops.whatsapp.note') }}</p>
                                                            </div>
                                                            <div class="js-whatsapp-inquiry-section {{ $userBookedViaWhatsapp ? '' : 'hidden' }} space-y-2" data-workshop-id="{{ $workshop->id }}">
                                                                <button type="button"
                                                                        class="w-full bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold py-3.5 px-4 rounded-xl booking-button flex items-center justify-center gap-2 js-whatsapp-inquiry-button"
                                                                        data-workshop-id="{{ $workshop->id }}"
                                                                        data-workshop-title="{{ e($workshop->title) }}"
                                                                        data-booking-code="{{ optional($userBooking)->public_code ?? '' }}">
                                                                    <i class="fas fa-comments text-xl booking-button-icon"></i>
                                                                    <span class="booking-button-label">{{ __('workshops.whatsapp.inquiry_button') }}</span>
                                                                </button>
                                                                <p class="text-xs text-gray-500 text-center">{{ __('workshops.whatsapp.inquiry_helper') }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endguest
                                @endif
                                </div>
                                @livewire('bookings.whatsapp-booking-verification', [
                                    'workshopId' => $workshop->id,
                                    'initialHasWhatsappBooking' => $userBookedViaWhatsapp,
                                    'initialBookingId' => optional($userBooking)->id,
                                    'stripeElementId' => 'stripe-checkout-card',
                                ], key('whatsapp-verification-'.$workshop->id))
                            </div>
                        </div>
                        <div class="sidebar-card p-8" id="additional-details">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-right flex items-center">
                                <div class="info-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                {{ __('workshops.details.sidebar.title') }}
                            </h3>
                            
                            <div class="space-y-4">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">{{ __('workshops.details.sidebar.category') }}</p>
                                        <p class="font-semibold text-gray-900">{{ $workshop->category ?? $notSpecifiedLabel }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-signal"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">{{ __('workshops.details.sidebar.level') }}</p>
                                        <p class="font-semibold text-gray-900">{{ $levelLabels[$workshop->level] ?? $levelLabels['beginner'] }}</p>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-eye"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-500">{{ __('workshops.details.sidebar.views') }}</p>
                                        <p class="font-semibold text-gray-900">{{ $workshop->views_count }}</p>
                                    </div>
                                </div>
                                @if($workshop->address)
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-500">{{ __('workshops.details.sidebar.address') }}</p>
                                            <p class="font-semibold text-gray-900">{{ $workshop->address }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($relatedWorkshops->count() > 0)
        <section class="py-20 related-workshops-section" style="background-color: #f3f4f6;">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-4xl font-bold text-gray-900 mb-4 related-workshops-title">{{ __('workshops.details.related.title') }}</h2>
                        <div class="w-24 h-1 bg-gradient-to-r from-orange-500 to-orange-600 mx-auto rounded-full"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 related-workshops-grid">
                        @foreach($relatedWorkshops as $related)
                             @php 
                                $isFull = $related->bookings_count >= $related->max_participants; 
                            @endphp
                            <div class="related-workshop bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col {{ $isFull ? 'opacity-70' : '' }} related-workshop-card">
                                <a href="{{ route('workshop.show', $related->slug) }}" class="block group">
                                    <div class="relative overflow-hidden">
                                        <img src="{{ $related->image ? asset('storage/' . $related->image) : 'https://placehold.co/600x400/f87171/FFFFFF?text=' . urlencode(__('workshops.labels.card_placeholder_text')) }}" 
                                             alt="{{ $related->title }}" 
                                             class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300 related-workshop-image"
                                            onerror="this.src='{{ \App\Support\BrandAssets::logoAsset('webp') }}'; this.alt='{{ __('workshops.labels.fallback_image_alt') }}';" loading="lazy">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        @if($isFull)
                                            <span class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">{{ __('workshops.details.related.badge_full') }}</span>
                                        @elseif($related->is_online)
                                            <span class="absolute top-3 left-3 bg-orange-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">{{ __('workshops.labels.online_short') }}</span>
                                        @else
                                            <span class="absolute top-3 left-3 bg-orange-600 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">{{ __('workshops.labels.onsite_short') }}</span>
                                        @endif
                                        <div class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <div class="bg-white/90 backdrop-blur-sm rounded-full p-2">
                                                <i class="fas {{ $arrowIcon }} text-orange-500"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <div class="p-6 flex flex-col flex-grow related-workshop-content">
                                    <h3 class="text-lg font-bold text-gray-900 mb-3 hover:text-orange-600 transition-colors line-clamp-2 related-workshop-title">
                                        <a href="{{ route('workshop.show', $related->slug) }}">{{ $related->title }}</a>
                                    </h3>
                                    <div class="flex items-center text-sm text-gray-500 mb-4">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-orange-500 text-xs"></i>
                                        </div>
                                        <span class="related-workshop-instructor">{{ $related->instructor }}</span>
                                    </div>
                                    <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                                        <div class="text-xl font-bold text-orange-500 related-workshop-price">
                                            {{ $related->price }} <span class="text-sm font-medium text-gray-500">{{ $related->currency }}</span>
                                        </div>
                                        <a href="{{ route('workshop.show', $related->slug) }}" 
                                           class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold text-sm transition-colors duration-300 related-workshop-button">
                                            {{ __('workshops.details.related.details') }} <i class="fas {{ $arrowIcon }} {{ $isRtl ? 'ml-1' : 'mr-1' }}"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>

@if($showFloatingBookingButton)
    <div class="floating-booking-bar" role="region" aria-label="{{ __('workshops.details.booking_card.title') }}">
        <div class="floating-booking-price">
            <span class="floating-booking-price-label">{{ __('workshops.details.booking_card.price_label') }}</span>
            <span class="floating-booking-price-value">{{ $workshop->formatted_price }}</span>
            <span class="floating-booking-meta">
                @if($workshop->max_participants)
                    {{ $workshop->bookings_count }}/{{ $workshop->max_participants }} {{ __('workshops.labels.participants') }}
                @else
                    {{ $workshopDateLabel }} · {{ $workshopStartTimeLabel }}
                @endif
            </span>
        </div>
        <a
            href="#stripe-checkout-card"
            class="floating-booking-btn"
            aria-label="{{ __('workshops.cards.button_book') }}"
            data-scroll-target="#stripe-checkout-card"
            data-scroll-fallback="#workshop-booking"
        >
            <span>{{ __('workshops.cards.button_book') }}</span>
            <i class="fas {{ $arrowIcon }}" aria-hidden="true"></i>
        </a>
    </div>
@endif
@endsection

@if($stripeEnabled)
    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
    @endpush
@endif

@push('scripts')
<script>
const csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
const bookingCsrfToken = csrfMetaTag ? csrfMetaTag.getAttribute('content') : null;

const bookingConfig = {
    workshopId: {{ $workshop->id }},
    workshop: {
        title: @json($workshop->title),
        price: @json((float) $workshop->price),
        currency: @json(strtoupper($workshop->currency ?? config('finance.default_currency', 'USD'))),
    },
    stripe: {
        enabled: @json((bool) $stripeEnabled),
        publishableKey: @json($stripePublicKey),
        createIntentUrl: "{{ $stripeEnabled ? route('payments.stripe.intent') : '' }}",
        confirmUrl: "{{ $stripeEnabled ? route('payments.stripe.confirm') : '' }}",
        paymentCountry: @json(config('services.stripe.payment_country', 'SA')),
        messages: {
            disabled: @json(__('workshops.stripe.disabled')),
            genericError: @json(__('workshops.stripe.generic_error')),
            initError: @json(__('workshops.stripe.init_error')),
            validationError: @json(__('workshops.stripe.validation_error')),
            successMessage: @json(__('workshops.stripe.success_message')),
            notReady: @json(__('workshops.stripe.not_ready')),
            intentError: @json(__('workshops.stripe.intent_error')),
            walletUnavailable: @json(__('workshops.stripe.wallet_unavailable')),
        },
    },
};

const workshopMessages = {
    unexpected: @json(__('workshops.details.messages.unexpected_error')),
    paymentSuccess: @json(__('workshops.details.messages.payment_success')),
    stripeError: @json(__('workshops.details.messages.stripe_error')),
    stripeCancelled: @json(__('workshops.details.messages.stripe_cancelled')),
    joinLinkReady: @json(__('workshops.details.messages.join_link_ready')),
    joinLinkAction: @json(__('workshops.details.messages.join_link_action')),
};

const bookingUi = {
    joinLabel: @json(__('workshops.details.booking_card.cta_join')),
    joinHint: @json(__('workshops.details.booking_card.join_room_hint')),
    joinButtonClasses: 'w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl text-lg booking-button flex items-center justify-center gap-2',
    joinIconClasses: 'fas fa-video mr-2 rtl:ml-2',
};

const loginModalTexts = {
    title: @json(__('workshops.details.modal.title')),
    description: @json(__('workshops.details.modal.description')),
    hint: @json(__('workshops.details.modal.hint')),
    login: @json(__('workshops.details.modal.login')),
    register: @json(__('workshops.details.modal.register')),
};

let stripeInstance = null;
let stripeElements = null;
let stripePaymentElement = null;
let stripeClientSecret = null;
let stripePaymentIntentId = null;
let stripeInitAttempts = 0;
let stripeEventsBound = false;
let stripeElementReady = false;
let stripeIsLoading = false;
let stripePaymentRequest = null;
let stripeWalletElement = null;
let stripeWalletReady = false;
let stripeIntentAmount = null;
let stripeIntentCurrency = null;
const STRIPE_MAX_INIT_ATTEMPTS = 5;
let loginModalKeyListener = null;

function getJsonHeaders() {
    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': bookingCsrfToken,
    };
}

function handleJsonResponse(response) {
    return response.text().then(text => {
        let data = {};
        let errorMessage = null;

        if (text) {
            try {
                data = JSON.parse(text);
            } catch (error) {
                console.warn('Failed to parse JSON response', error);
            }
        }

        if (!response.ok) {
            if (data && data.errors) {
                const firstErrorGroup = Object.values(data.errors)[0];
                if (Array.isArray(firstErrorGroup) && firstErrorGroup.length > 0) {
                    errorMessage = firstErrorGroup[0];
                }
            }

            const message = errorMessage || data.message || workshopMessages.unexpected;
            const error = new Error(message);
            error.payload = data;
            throw error;
        }

        return data;
    });
}

function updateBookingPrimaryAction(joinUrl) {
    if (!joinUrl) {
        return false;
    }

    const primaryAction = document.getElementById('booking-primary-action');

    if (!primaryAction) {
        return false;
    }

    const button = document.createElement('a');
    button.href = joinUrl;
    button.className = bookingUi.joinButtonClasses;
    button.innerHTML = `
        <i class="${bookingUi.joinIconClasses}"></i>
        <span>${bookingUi.joinLabel}</span>
    `;

    primaryAction.innerHTML = '';
    primaryAction.appendChild(button);

    const hint = document.createElement('p');
    hint.className = 'booking-action-hint';
    hint.textContent = bookingUi.joinHint;
    primaryAction.appendChild(hint);

    return true;
}

function scheduleBookingStatusRefresh(delay = 2500) {
    const refreshDelay = Number.isFinite(delay) ? delay : Number(delay);
    const finalDelay = Number.isFinite(refreshDelay) && refreshDelay >= 0 ? refreshDelay : 2500;

    if (window.__bookingStatusRefreshTimer__) {
        clearTimeout(window.__bookingStatusRefreshTimer__);
    }

    window.__bookingStatusRefreshTimer__ = window.setTimeout(() => {
        window.location.reload();
    }, finalDelay);
}

function handlePaymentSuccess(payload) {
    const baseMessage = payload.message || workshopMessages.paymentSuccess;
    let alertMessage = baseMessage;

    if (payload.join_url) {
        const joinNotice = workshopMessages.joinLinkReady || '';
        const joinActionLabel = workshopMessages.joinLinkAction || '';
        const joinLink = payload.join_url;

        alertMessage += `
            <span class="block mt-2 text-xs font-semibold">${joinNotice}</span>
            <a class="mt-2 inline-flex items-center gap-2 rounded-md bg-white/80 px-3 py-1.5 text-xs font-semibold text-green-700 underline hover:text-green-900"
               href="${joinLink}"
               target="_blank"
               rel="noopener noreferrer">
                <i class="fas fa-link"></i>
                ${joinActionLabel}
            </a>
        `;

        showCustomAlert(alertMessage, 'success');
        const actionUpdated = updateBookingPrimaryAction(joinLink);

        if (!actionUpdated) {
            scheduleBookingStatusRefresh();
        }

        return;
    }

    showCustomAlert(alertMessage, 'success');

    const redirectTarget = payload.redirect_url;

    if (redirectTarget) {
        setTimeout(() => {
            window.location.href = redirectTarget;
        }, 1200);
    } else {
        setTimeout(() => window.location.reload(), 1500);
    }
}

function updateStripeButtonState() {
    const button = document.getElementById('stripe-submit-button');

    if (!button) {
        return;
    }

    const disabled = stripeIsLoading || !stripeElementReady;
    button.disabled = disabled;
    button.classList.toggle('opacity-50', disabled);
    button.classList.toggle('cursor-not-allowed', disabled);
}

function toggleStripeLoading(isLoading) {
    stripeIsLoading = isLoading;
    const button = document.getElementById('stripe-submit-button');
    const spinner = document.getElementById('stripe-submit-spinner');
    const label = document.getElementById('stripe-submit-label');

    updateStripeButtonState();

    if (spinner) {
        spinner.classList.toggle('hidden', !isLoading);
        spinner.classList.toggle('flex', isLoading);
    }

    if (label) {
        label.classList.toggle('hidden', isLoading);
    }
}

function showStripeError(message) {
    const errorEl = document.getElementById('stripe-card-errors');

    if (!errorEl) {
        return;
    }

    if (!message) {
        errorEl.classList.add('hidden');
        errorEl.textContent = '';
        return;
    }

    errorEl.textContent = message;
    errorEl.classList.remove('hidden');
}

function setStripeSuccess(message) {
    const successEl = document.getElementById('stripe-success-message');

    if (!successEl) {
        return;
    }

    if (!message) {
        successEl.classList.add('hidden');
        successEl.textContent = '';
        return;
    }

    successEl.textContent = message;
    successEl.classList.remove('hidden');
}

function hideStripeWalletSection() {
    const walletSection = document.getElementById('stripe-wallet-section');
    const walletUnavailable = document.getElementById('stripe-wallet-unavailable');

    if (walletSection) {
        walletSection.classList.add('hidden');
    }

    if (walletUnavailable) {
        walletUnavailable.classList.add('hidden');
    }
}

function resetStripeWalletElements() {
    if (stripeWalletElement) {
        try {
            stripeWalletElement.unmount();
        } catch (error) {
            console.warn('Failed to unmount wallet element', error);
        }
    }

    stripePaymentRequest = null;
    stripeWalletElement = null;
    stripeWalletReady = false;
    hideStripeWalletSection();
}

async function fetchStripeIntent() {
    if (!bookingConfig.stripe.createIntentUrl) {
        throw new Error(bookingConfig.stripe.messages.disabled);
    }

    return fetch(bookingConfig.stripe.createIntentUrl, {
        method: 'POST',
        headers: getJsonHeaders(),
        body: JSON.stringify({
            workshop_id: bookingConfig.workshopId,
        }),
    }).then(handleJsonResponse);
}

async function initializeStripeCheckout(forceRecreate = false) {
    if (!bookingConfig.stripe.enabled) {
        return;
    }

    const container = document.getElementById('stripe-payment-element');

    if (!container) {
        return;
    }

    resetStripeWalletElements();

    if (!bookingConfig.stripe.publishableKey) {
        container.innerHTML = `<p class="text-sm text-red-500 text-center">${bookingConfig.stripe.messages.disabled}</p>`;
        return;
    }

    if (typeof Stripe === 'undefined') {
        if (stripeInitAttempts < STRIPE_MAX_INIT_ATTEMPTS) {
            stripeInitAttempts += 1;
            setTimeout(() => initializeStripeCheckout(forceRecreate), 500);
            return;
        }

        container.innerHTML = `<p class="text-sm text-red-500 text-center">${bookingConfig.stripe.messages.initError}</p>`;
        return;
    }

    if (!stripeInstance || forceRecreate) {
        stripeInstance = Stripe(bookingConfig.stripe.publishableKey);
    }

    if (stripePaymentElement) {
        try {
            stripePaymentElement.unmount();
        } catch (error) {
            console.warn('Failed to unmount Stripe element', error);
        }
        stripePaymentElement = null;
    }

    stripeElementReady = false;
    updateStripeButtonState();
    toggleStripeLoading(true);
    showStripeError('');
    setStripeSuccess('');

    try {
        const payload = await fetchStripeIntent();
        stripeClientSecret = payload.client_secret || null;
        stripePaymentIntentId = payload.payment_intent_id || null;

        if (!stripeClientSecret) {
            throw new Error(bookingConfig.stripe.messages.genericError);
        }

        stripeElements = stripeInstance.elements({
            clientSecret: stripeClientSecret,
        });

        stripePaymentElement = stripeElements.create('payment', { layout: 'tabs' });

        stripePaymentElement.on('ready', () => {
            stripeElementReady = true;
            updateStripeButtonState();
            toggleStripeLoading(false);
        });

        stripePaymentElement.on('loaderror', (event) => {
            console.error('Stripe element load error', event);
            stripeElementReady = false;
            updateStripeButtonState();
            showStripeError(event.error?.message || bookingConfig.stripe.messages.genericError);
        });

        stripePaymentElement.mount('#stripe-payment-element');

        stripeIntentAmount = payload.amount ?? null;
        stripeIntentCurrency = payload.currency ?? bookingConfig.workshop.currency;
        await setupStripeWalletButton();
    } catch (error) {
        console.error(error);
        showStripeError(error.message || bookingConfig.stripe.messages.genericError);
        stripeElementReady = false;
        updateStripeButtonState();
    } finally {
        if (!stripeElementReady) {
            toggleStripeLoading(false);
        }
    }
}

async function setupStripeWalletButton() {
    const walletSection = document.getElementById('stripe-wallet-section');
    const walletUnavailable = document.getElementById('stripe-wallet-unavailable');
    const walletHint = document.getElementById('stripe-wallet-hint');

    if (!walletSection) {
        return;
    }

    hideStripeWalletSection();

    if (!window.PaymentRequest || !stripeInstance || !stripeElements || !stripeClientSecret) {
        walletUnavailable?.classList.remove('hidden');
        return;
    }

    if (!stripeIntentAmount || !stripeIntentCurrency) {
        walletUnavailable?.classList.remove('hidden');
        return;
    }

    const paymentRequestAllowedCountries = [
        'AE', 'AT', 'AU', 'BE', 'BG', 'BR', 'CA', 'CH', 'CI', 'CR', 'CY', 'CZ', 'DE', 'DK', 'DO',
        'EE', 'ES', 'FI', 'FR', 'GB', 'GI', 'GR', 'GT', 'HK', 'HR', 'HU', 'ID', 'IE', 'IN', 'IT',
        'JP', 'LI', 'LT', 'LU', 'LV', 'MT', 'MX', 'MY', 'NL', 'NO', 'NZ', 'PE', 'PH', 'PL', 'PT',
        'RO', 'SE', 'SG', 'SI', 'SK', 'SN', 'TH', 'TT', 'US', 'UY',
    ];
    const configuredPaymentCountry = (bookingConfig.stripe.paymentCountry || '').toUpperCase();
    const paymentCountry = paymentRequestAllowedCountries.includes(configuredPaymentCountry)
        ? configuredPaymentCountry
        : 'AE';

    const paymentRequest = stripeInstance.paymentRequest({
        country: paymentCountry,
        currency: (stripeIntentCurrency || bookingConfig.workshop.currency || 'USD').toLowerCase(),
        total: {
            label: bookingConfig.workshop.title || 'Workshop booking',
            amount: stripeIntentAmount,
        },
        requestPayerName: true,
        requestPayerEmail: true,
    });

    const paymentRequestButton = stripeElements.create('paymentRequestButton', {
        paymentRequest,
        style: {
            paymentRequestButton: {
                type: 'default',
                theme: 'dark',
                height: '48px',
            },
        },
    });

    try {
        const result = await paymentRequest.canMakePayment();

        if (!result) {
            walletUnavailable?.classList.remove('hidden');
            return;
        }
    } catch (error) {
        console.warn('PaymentRequest not available', error);
        walletUnavailable?.classList.remove('hidden');
        return;
    }

    paymentRequest.on('paymentmethod', async (event) => {
        toggleStripeLoading(true);
        showStripeError('');

        try {
            const confirmResult = await stripeInstance.confirmCardPayment(
                stripeClientSecret,
                {
                    payment_method: event.paymentMethod.id,
                },
                {
                    handleActions: false,
                }
            );

            if (confirmResult.error) {
                event.complete('fail');
                throw new Error(confirmResult.error.message || bookingConfig.stripe.messages.genericError);
            }

            let paymentIntent = confirmResult.paymentIntent;

            event.complete('success');

            if (paymentIntent && paymentIntent.status === 'requires_action') {
                const nextStep = await stripeInstance.confirmCardPayment(stripeClientSecret);

                if (nextStep.error) {
                    throw new Error(nextStep.error.message || bookingConfig.stripe.messages.genericError);
                }

                paymentIntent = nextStep.paymentIntent;
            }

            await finalizeStripeIntent(paymentIntent?.id || stripePaymentIntentId);
        } catch (error) {
            console.error(error);
            event.complete('fail');
            showStripeError(error.message || bookingConfig.stripe.messages.genericError);
        } finally {
            toggleStripeLoading(false);
        }
    });

    paymentRequestButton.mount('#stripe-wallet-button');
    walletSection.classList.remove('hidden');
    walletHint?.classList.remove('hidden');
    walletUnavailable?.classList.add('hidden');

    stripePaymentRequest = paymentRequest;
    stripeWalletElement = paymentRequestButton;
    stripeWalletReady = true;
}

async function finalizeStripeIntent(intentId) {
    if (!intentId) {
        throw new Error(bookingConfig.stripe.messages.intentError || bookingConfig.stripe.messages.genericError);
    }

    if (!bookingConfig.stripe.confirmUrl) {
        throw new Error(bookingConfig.stripe.messages.disabled);
    }

    const payload = await fetch(bookingConfig.stripe.confirmUrl, {
        method: 'POST',
        headers: getJsonHeaders(),
        body: JSON.stringify({
            workshop_id: bookingConfig.workshopId,
            payment_intent_id: intentId,
        }),
    }).then(handleJsonResponse);

    setStripeSuccess(payload.message || bookingConfig.stripe.messages.successMessage);
    handlePaymentSuccess(payload);
    return payload;
}

async function submitStripePayment(event) {
    if (event) {
        event.preventDefault();
    }

    if (!bookingConfig.stripe.enabled) {
        return;
    }

    if (!bookingConfig.stripe.confirmUrl) {
        showStripeError(bookingConfig.stripe.messages.disabled);
        return;
    }

    if (!stripeInstance || !stripeElements || !stripePaymentElement || !stripeClientSecret) {
        await initializeStripeCheckout(true);
    }

    if (!stripeInstance || !stripeElements || !stripePaymentElement) {
        showStripeError(bookingConfig.stripe.messages.initError);
        return;
    }

    toggleStripeLoading(true);
    showStripeError('');
    setStripeSuccess('');

    try {
        if (!stripeElementReady) {
            throw new Error(bookingConfig.stripe.messages.notReady);
        }

        const { error, paymentIntent } = await stripeInstance.confirmPayment({
            elements: stripeElements,
            redirect: 'if_required',
            confirmParams: {
                return_url: window.location.href,
            },
        });

        if (error) {
            throw new Error(error.message || bookingConfig.stripe.messages.genericError);
        }

        const intentId = paymentIntent?.id || stripePaymentIntentId;

        await finalizeStripeIntent(intentId);
    } catch (error) {
        console.error(error);
        showStripeError(error.message || bookingConfig.stripe.messages.genericError);
    } finally {
        toggleStripeLoading(false);
    }
}

function bindStripeEvents() {
    if (stripeEventsBound) {
        return;
    }

    const button = document.getElementById('stripe-submit-button');

    if (button) {
        button.addEventListener('click', submitStripePayment);
        stripeEventsBound = true;
    }
}

function initWorkshopDetailsBooking() {
    if (bookingConfig.stripe.enabled) {
        bindStripeEvents();
        initializeStripeCheckout();
    }
}

// Custom alert helper
function showCustomAlert(message, type = 'info') {
    const existingAlert = document.getElementById('custom-alert');
    if (existingAlert) {
        existingAlert.remove();
    }

    let bgColor, textColor, icon, borderColor;
    switch (type) {
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

    document.body.insertAdjacentHTML('beforeend', alertHTML);

    setTimeout(() => {
        const alert = document.getElementById('custom-alert');
        if (alert) {
            alert.style.transform = 'translateX(0)';
            alert.style.opacity = '1';
        }
    }, 100);

    setTimeout(() => {
        closeCustomAlert();
    }, 5000);
}

// Remove the alert with a subtle animation
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

// Prompt unauthenticated users to sign in before booking
function showLoginRequiredModal(workshopId = null) {
    const existingModal = document.getElementById('login-required-modal');
    if (existingModal) {
        existingModal.remove();
    }

    if (workshopId) {
        localStorage.setItem('pending_workshop_booking', workshopId);
    }

    const modalHTML = `
        <div id="login-required-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" onclick="closeLoginRequiredModal(event)">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 transform transition-all duration-300 scale-100 relative" onclick="event.stopPropagation()">
                <button onclick="closeLoginRequiredModal()" class="absolute top-4 left-4 text-gray-400 hover:text-gray-600 transition-colors p-2 rounded-full hover:bg-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </button>
                
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-amber-400 to-orange-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-user-lock text-white text-3xl"></i>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">${loginModalTexts.title}</h3>
                    
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-6">
                        <p class="text-gray-700 text-lg leading-relaxed">
                            ${loginModalTexts.description}
                        </p>
                        <p class="text-gray-600 text-sm mt-2">
                            ${loginModalTexts.hint}
                        </p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button onclick="redirectToLoginWithWorkshop()" class="flex-1 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fas fa-sign-in-alt {{ $isRtl ? 'ml-2' : 'mr-2' }}"></i>
                            ${loginModalTexts.login}
                        </button>
                        <button onclick="redirectToRegisterWithWorkshop()" class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fas fa-user-plus {{ $isRtl ? 'ml-2' : 'mr-2' }}"></i>
                            ${loginModalTexts.register}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);

    if (loginModalKeyListener) {
        document.removeEventListener('keydown', loginModalKeyListener);
    }

    loginModalKeyListener = function(event) {
        if (event.key === 'Escape') {
            closeLoginRequiredModal();
        }
    };

    document.addEventListener('keydown', loginModalKeyListener);
}

// Close the login modal, optionally when the backdrop is clicked
function closeLoginRequiredModal(event) {
    if (event && event.target && event.target.id !== 'login-required-modal') {
        return;
    }

    const modal = document.getElementById('login-required-modal');
    if (modal) {
        modal.remove();
    }

    if (loginModalKeyListener) {
        document.removeEventListener('keydown', loginModalKeyListener);
        loginModalKeyListener = null;
    }
}

// Redirect helper when the user wants to sign in
function redirectToLoginWithWorkshop() {
    const workshopId = localStorage.getItem('pending_workshop_booking');
    if (workshopId) {
        window.location.href = `{{ route('login') }}?pending_workshop_booking=${workshopId}`;
    } else {
        window.location.href = '{{ route('login') }}';
    }
}

// Redirect helper when the user wants to register
function redirectToRegisterWithWorkshop() {
    const workshopId = localStorage.getItem('pending_workshop_booking');
    if (workshopId) {
        window.location.href = `{{ route('register') }}?pending_workshop_booking=${workshopId}`;
    } else {
        window.location.href = '{{ route('register') }}';
    }
}

// Expose helpers globally for inline handlers
window.closeLoginRequiredModal = closeLoginRequiredModal;
window.showLoginRequiredModal = showLoginRequiredModal;
window.redirectToLoginWithWorkshop = redirectToLoginWithWorkshop;
window.redirectToRegisterWithWorkshop = redirectToRegisterWithWorkshop;

function handleWorkshopHideStripeEvent(event) {
    if (typeof bookingConfig === 'undefined') {
        return;
    }

    const detail = event?.detail || {};
    const targetId = detail.elementId || 'stripe-checkout-card';
    const targetWorkshopId = Number(detail.workshopId || detail.workshop_id || 0);

    if (Number(bookingConfig.workshopId) && targetWorkshopId && targetWorkshopId !== Number(bookingConfig.workshopId)) {
        return;
    }

    const target = document.getElementById(targetId);

    if (!target) {
        return;
    }

    target.classList.add('hidden', 'opacity-0', 'pointer-events-none');
    target.setAttribute('aria-hidden', 'true');
}

function registerWorkshopHideStripeListener() {
    if (window.__workshopHideStripeListenerRegistered) {
        return;
    }

    window.addEventListener('workshop-hide-stripe', handleWorkshopHideStripeEvent);
    window.__workshopHideStripeListenerRegistered = true;
}

if (window.Livewire) {
    registerWorkshopHideStripeListener();
} else {
    document.addEventListener('livewire:init', registerWorkshopHideStripeListener, { once: true });
}

function scrollToElementWithOffset(element) {
    if (!element) {
        return false;
    }

    const header = document.querySelector('[data-sticky-header]');
    const headerOffset = header ? header.getBoundingClientRect().height : 96;
    const scrollTarget = element.getBoundingClientRect().top + window.pageYOffset - (headerOffset + 24);

    window.scrollTo({
        top: Math.max(scrollTarget, 0),
        behavior: 'smooth',
    });

    return true;
}

function handleScrollTriggerClick(event) {
    const trigger = event.currentTarget;

    if (!trigger) {
        return;
    }

    event.preventDefault();

    const targetSelector = trigger.getAttribute('data-scroll-target');
    const fallbackSelector = trigger.getAttribute('data-scroll-fallback');
    const hrefSelector = trigger.getAttribute('href');

    const target =
        (targetSelector ? document.querySelector(targetSelector) : null) ||
        (fallbackSelector ? document.querySelector(fallbackSelector) : null) ||
        (hrefSelector ? document.querySelector(hrefSelector) : null);

    if (!scrollToElementWithOffset(target) && hrefSelector && hrefSelector.startsWith('#')) {
        window.location.hash = hrefSelector;
    }
}

function initFloatingBookingBarScroll() {
    if (window.__floatingBookingBarScrollInitialized) {
        return;
    }

    const triggers = document.querySelectorAll('[data-scroll-target]');

    if (!triggers.length) {
        return;
    }

    triggers.forEach(trigger => {
        trigger.addEventListener('click', handleScrollTriggerClick);
    });

    window.__floatingBookingBarScrollInitialized = true;
}

function initFloatingBookingBarFooterObserver() {
    if (window.__floatingBookingBarFooterObserverInitialized) {
        return;
    }

    const floatingBar = document.querySelector('.floating-booking-bar');
    const footer = document.querySelector('footer');

    if (!floatingBar || !footer) {
        return;
    }

    const toggleBar = shouldHide => {
        floatingBar.classList.toggle('is-hidden', shouldHide);
    };

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                toggleBar(entry.isIntersecting);
            });
        });

        observer.observe(footer);
    } else {
        const handleScroll = () => {
            const footerRect = footer.getBoundingClientRect();
            toggleBar(footerRect.top < window.innerHeight);
        };

        window.addEventListener('scroll', handleScroll);
        handleScroll();
    }

    window.__floatingBookingBarFooterObserverInitialized = true;
}

function initFloatingBookingBarTabBarSync() {
    if (window.__floatingBookingBarTabBarSyncInitialized) {
        return;
    }

    const floatingBar = document.querySelector('.floating-booking-bar');
    const mobileTabBar = document.querySelector('[data-mobile-tab-bar]');

    if (!floatingBar || !mobileTabBar || typeof window === 'undefined') {
        return;
    }

    const mediaQuery = typeof window.matchMedia === 'function'
        ? window.matchMedia('(max-width: 768px)')
        : null;
    const BASE_SPACING = 16;

    const updateOffset = () => {
        if (!mediaQuery || !mediaQuery.matches) {
            floatingBar.style.removeProperty('--floating-booking-mobile-offset');
            return;
        }

        const navRect = mobileTabBar.getBoundingClientRect();
        const navHeight = navRect && navRect.height ? navRect.height : 0;
        const isHidden = mobileTabBar.classList.contains('mobile-tab-bar--hidden');
        const offset = isHidden ? BASE_SPACING : Math.round(navHeight + BASE_SPACING);

        floatingBar.style.setProperty('--floating-booking-mobile-offset', `${offset}px`);
    };

    if (typeof MutationObserver === 'function') {
        const observer = new MutationObserver(updateOffset);
        observer.observe(mobileTabBar, { attributes: true, attributeFilter: ['class'] });
    } else {
        window.addEventListener('scroll', updateOffset, { passive: true });
    }

    window.addEventListener('resize', updateOffset);

    if (mediaQuery) {
        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', updateOffset);
        } else if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(updateOffset);
        }
    }

    updateOffset();
    window.__floatingBookingBarTabBarSyncInitialized = true;
}

function bootWorkshopDetailsScripts() {
    initWorkshopDetailsBooking();
    initFloatingBookingBarScroll();
    initFloatingBookingBarFooterObserver();
    initFloatingBookingBarTabBarSync();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootWorkshopDetailsScripts);
} else {
    bootWorkshopDetailsScripts();
}
</script>
@endpush

@if($whatsappBookingEnabled)
    @push('scripts')
        <script>
(function bootstrapWhatsAppBooking(config) {
    if (window.WhatsAppBooking) {
        window.WhatsAppBooking.configure(config);
        window.WhatsAppBooking.initButtons();
        window.WhatsAppBooking.initInquiryButtons();
        return;
    }

    window.__WHATSAPP_BOOKING_PENDING__ = window.__WHATSAPP_BOOKING_PENDING__ || [];
    window.__WHATSAPP_BOOKING_PENDING__.push(function(instance) {
        instance.configure(config);
        instance.initButtons();
        instance.initInquiryButtons();
    });
})(@json($whatsappBookingPayload));
        </script>
    @endpush
@endif


