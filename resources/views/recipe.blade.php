@extends('layouts.app')

@section('title', $recipe->title)

@push('styles')
    <style>
      .serving-size-btn.bg-orange-500 {
        background-color: #f97316 !important;
      }
      .serving-size-btn.text-white {
        color: #fff !important;
      }
      .star-rating {
        unicode-bidi: bidi-override;
        direction: rtl;
        text-align: center;
        display: flex;
        justify-content: center;
        gap: 5px;
        flex-direction: row; /* إزالة row-reverse */
      }
      .star-rating input {
        display: none;
      }
      .star-rating label {
        display: inline-block;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        padding: 5px;
        position: relative;
      }
      .star-rating .star {
        font-size: 2.5rem;
        color: #e5e7eb; /* نجوم رمادية في الوضع العادي */
        display: block;
        transition: all 0.2s ease-in-out;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        line-height: 1;
      }
      
      /* النجوم المحددة */
      .star-rating input:checked ~ label .star {
        color: #eab308 !important;
        transform: scale(1.1);
        text-shadow: 0 2px 4px rgba(234, 179, 8, 0.3);
      }
      
      /* تأثير hover - إضاءة النجم المحوم عليه والنجوم التي تأتي بعده */
      .star-rating label:hover .star {
        color: #eab308 !important;
        transform: scale(1.1);
        text-shadow: 0 2px 4px rgba(234, 179, 8, 0.3);
      }
      
      /* إضاءة النجوم التي تأتي بعد النجم المحوم عليه (من اليمين لليسار) */
      .star-rating label:hover ~ label .star {
        color: #eab308 !important;
        transform: scale(1.1);
        text-shadow: 0 2px 4px rgba(234, 179, 8, 0.3);
      }
      
      /* إعادة تعيين النجوم التي تأتي قبل النجم المحوم عليه */
      .star-rating label:hover + label .star {
        color: #e5e7eb !important;
        transform: scale(1);
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
      }
      
      .star-rating label:hover {
        transform: scale(1.05);
      }
      
      /* إزالة CSS الذي يسبب تظليل جميع النجوم */
      
      /* ضمان أن النجوم المحددة تبقى ذهبية */
      .star-rating input:checked ~ label .star {
        color: #eab308 !important;
        transform: scale(1.1);
        text-shadow: 0 2px 4px rgba(234, 179, 8, 0.3);
      }
      .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.25rem;
        border-radius: 9999px; /* rounded-full */
        font-weight: 600; /* font-semibold */
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
      }

      .btn.save-recipe-btn {
        background-color: #10b981; /* bg-green-500 - افتراضي أخضر */
        color: #ffffff;
        min-width: 140px; /* الحفاظ على عرض ثابت */
        min-height: 48px; /* الحفاظ على ارتفاع ثابت */
      }

      .btn.save-recipe-btn:hover {
        background-color: #059669; /* hover:bg-green-600 */
      }
  
      .btn.save-recipe-btn.bg-orange-500 {
        background-color: #f97316 !important; /* bg-orange-500 */
      }
  
      .btn.save-recipe-btn.bg-orange-500:hover {
        background-color: #ea580c !important; /* hover:bg-orange-600 */
      }
      
      .btn.save-recipe-btn.bg-green-500 {
        background-color: #10b981 !important; /* bg-green-500 */
      }
  
      .btn.save-recipe-btn.bg-green-500:hover {
        background-color: #059669 !important; /* hover:bg-green-600 */
      }

      .recipe-hero {
        position: relative;
        background: linear-gradient(135deg, rgba(254, 243, 199, 0.85) 0%, rgba(255, 251, 235, 0.92) 45%, #ffffff 100%);
        border: 1px solid rgba(249, 115, 22, 0.08);
        box-shadow: 0 25px 55px rgba(15, 23, 42, 0.07);
        overflow: hidden;
      }

      .recipe-hero::before,
      .recipe-hero::after {
        content: "";
        position: absolute;
        border-radius: 9999px;
        background: radial-gradient(circle at center, rgba(249, 115, 22, 0.18), transparent 65%);
        pointer-events: none;
        transition: transform 0.5s ease;
      }

      .recipe-hero::before {
        width: 380px;
        height: 380px;
        top: -160px;
        left: -120px;
        opacity: 0.45;
      }

      .recipe-hero::after {
        width: 320px;
        height: 320px;
        bottom: -140px;
        right: -80px;
        opacity: 0.35;
      }

      .recipe-hero:hover::before,
      .recipe-hero:hover::after {
        transform: scale(1.05);
      }

      .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(249, 115, 22, 0.25);
        color: #ea580c;
        font-weight: 600;
        font-size: 0.95rem;
        backdrop-filter: blur(6px);
      }

      .hero-title {
        font-size: clamp(2rem, 5vw, 3.2rem);
        line-height: 1.2;
        font-weight: 800;
        color: #1f2937;
        letter-spacing: -0.015em;
      }

      .hero-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        align-items: stretch;
      }

      .hero-stat {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 1rem 1.25rem;
        border-radius: 1.5rem;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(249, 115, 22, 0.12);
        box-shadow: 0 12px 30px rgba(249, 115, 22, 0.08);
      }

      .hero-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 9999px;
        background: rgba(249, 115, 22, 0.12);
        color: #f97316;
        font-size: 1.25rem;
      }

      .hero-stat-label {
        display: block;
        color: #6b7280;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
      }

      .hero-stat-value {
        color: #111827;
        font-size: 1.1rem;
        font-weight: 700;
      }

      .hero-stat-sub {
        display: inline-block;
        margin-right: 0.75rem;
        color: #9ca3af;
        font-size: 0.75rem;
        font-weight: 600;
      }

      .hero-panel {
        background: rgba(255, 255, 255, 0.88);
        border: 1px solid rgba(249, 115, 22, 0.15);
        border-radius: 1.5rem;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        backdrop-filter: blur(8px);
      }

      .hero-panel .author-meta span {
        display: block;
        color: #4b5563;
        font-size: 0.95rem;
      }

      .hero-panel .author-meta span + span {
        margin-top: 0.35rem;
      }

      .hero-actions {
        display: grid;
        gap: 0.75rem;
      }

      @media (min-width: 640px) {
        .hero-actions {
          grid-template-columns: repeat(2, minmax(0, 1fr));
        }
      }

      @media (min-width: 1024px) {
        .hero-panel {
          min-width: 18rem;
        }
      }

      .media-card {
        background: #ffffff;
        border-radius: 1.5rem;
        overflow: hidden;
        border: 1px solid rgba(249, 115, 22, 0.12);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
      }

      .media-card .thumbnail {
        border-radius: 1rem;
        border: 2px solid transparent;
        opacity: 0.65;
      }

      .media-card .thumbnail.active,
      .media-card .thumbnail:hover {
        border-color: #f97316;
        opacity: 1;
      }

      .media-card .main-image-wrapper {
        height: 400px;
      }

      .media-card .thumbnail-strip {
        gap: 0.75rem;
      }

      .section-card {
        position: relative;
        background: linear-gradient(160deg, rgba(255, 255, 255, 0.96) 0%, rgba(255, 247, 237, 0.75) 80%, #ffffff 100%);
        border-radius: 1.5rem;
        padding: 2rem;
        border: 1px solid rgba(249, 115, 22, 0.12);
        box-shadow: 0 22px 48px rgba(15, 23, 42, 0.07);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
      }

      .section-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 30px 60px rgba(15, 23, 42, 0.1);
      }

      .section-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.75rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 1.5rem;
      }

      .section-title i {
        color: #f97316;
        font-size: 1.4rem;
      }

      .info-highlight-grid {
        display: grid;
        gap: 1rem;
      }

      .info-highlight {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 1rem 1.25rem;
        border-radius: 1.25rem;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(229, 231, 235, 0.6);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.05);
      }

      .info-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 1rem;
        background: rgba(249, 115, 22, 0.12);
        color: #f97316;
        font-size: 1.3rem;
      }

      .info-label {
        display: block;
        color: #6b7280;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
      }

      .info-value {
        color: #111827;
        font-size: 1.25rem;
        font-weight: 700;
      }

      .info-unit {
        margin-right: 0.5rem;
        color: #9ca3af;
        font-size: 0.85rem;
        font-weight: 600;
      }

      .serving-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
      }

      .serving-size-btn {
        min-width: 68px;
        padding: 0.65rem 1.25rem;
        border-radius: 9999px;
        font-weight: 600;
        border: 1px solid rgba(249, 115, 22, 0.25);
        background: rgba(255, 255, 255, 0.95);
        color: #f97316;
        transition: all 0.2s ease;
      }

      .serving-size-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(249, 115, 22, 0.15);
      }

      .serving-size-hint {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.85rem 1.1rem;
        border-radius: 1rem;
        background: rgba(255, 255, 255, 0.9);
        border: 1px dashed rgba(249, 115, 22, 0.3);
      }

      .ingredient-list {
        display: grid;
        gap: 0.75rem;
      }

      .ingredient-item {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        padding: 0.85rem 1.1rem;
        border-radius: 1.25rem;
        background: rgba(248, 250, 252, 0.95);
        border: 1px solid rgba(226, 232, 240, 0.7);
        font-weight: 600;
        color: #374151;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
      }

      .ingredient-item:hover {
        transform: translateX(-4px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
      }

      .ingredient-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.9rem;
        background: rgba(249, 115, 22, 0.12);
        color: #f97316;
        font-size: 1rem;
      }

      .section-card .tool-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(229, 231, 235, 0.8);
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
      }

      .section-card .tool-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
      }

      .step-list {
        counter-reset: steps;
        display: grid;
        gap: 1.25rem;
      }

      .step-item {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        padding: 1.25rem 1.4rem;
        border-radius: 1.4rem;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(226, 232, 240, 0.7);
        box-shadow: 0 16px 35px rgba(15, 23, 42, 0.06);
        font-size: 1.05rem;
        line-height: 1.8;
        color: #374151;
      }

      .step-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, #f97316, #fb923c);
        color: #ffffff;
        font-weight: 700;
        font-size: 1.1rem;
        flex-shrink: 0;
        box-shadow: 0 12px 24px rgba(249, 115, 22, 0.3);
      }

      .step-text {
        flex: 1;
      }

      .callout-card {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        background: linear-gradient(140deg, rgba(249, 115, 22, 0.12) 0%, rgba(253, 186, 116, 0.08) 45%, rgba(255, 255, 255, 0.85) 100%);
        border: 1px solid rgba(249, 115, 22, 0.14);
      }

      @media (min-width: 768px) {
        .callout-card {
          flex-direction: row;
          align-items: center;
          justify-content: space-between;
        }
      }

      .callout-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 1.1rem;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(249, 115, 22, 0.3);
        color: #f97316;
        font-weight: 700;
        font-size: 0.9rem;
      }

      .callout-card .callout-meta {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
      }

      .callout-card .callout-meta p {
        color: #4b5563;
        font-size: 1rem;
        line-height: 1.7;
      }

      .callout-card .cta-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
      }

      @media (min-width: 768px) {
        .callout-card .cta-buttons {
          flex-direction: row;
          align-items: center;
        }
      }

      .rating-card {
        background: linear-gradient(160deg, rgba(255, 255, 255, 0.96) 0%, rgba(254, 215, 170, 0.4) 100%);
        border: 1px solid rgba(249, 115, 22, 0.15);
      }

      .rating-card .section-title {
        margin-bottom: 1.5rem;
      }

      .rating-card .star-rating {
        margin-bottom: 1rem;
      }

      .rating-card .rating-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        width: 100%;
      }

      @media (min-width: 768px) {
        .rating-card .rating-actions {
          flex-direction: row;
          width: auto;
        }
      }

      .related-section .related-recipe-card {
        border-radius: 1.25rem;
        border: 1px solid rgba(229, 231, 235, 0.8);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.06);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
      }

      .related-section .related-recipe-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 28px 52px rgba(15, 23, 42, 0.1);
      }

      @media (max-width: 640px) {
        .recipe-hero {
          padding: 1.75rem 1.5rem;
          border-radius: 2rem;
        }

        .hero-title {
          font-size: 1.9rem;
        }

        .hero-stats-grid {
          grid-template-columns: unset;
          grid-auto-flow: column;
          grid-auto-columns: minmax(200px, 1fr);
          overflow-x: auto;
          padding: 1rem 1rem 0.75rem;
          margin: 1.75rem -1rem 0;
          scroll-snap-type: x mandatory;
          -webkit-overflow-scrolling: touch;
        }

        .hero-stats-grid::-webkit-scrollbar {
          display: none;
        }

        .hero-stat {
          scroll-snap-align: start;
          min-width: 200px;
        }

        .hero-panel {
          width: 100%;
          padding: 1.5rem;
          margin-top: 1.25rem;
        }

        .hero-actions {
          grid-template-columns: 1fr;
        }

        .section-card {
          padding: 1.75rem !important;
          border-radius: 1.25rem;
        }

        .info-highlight-grid {
          gap: 0.75rem;
        }

        .info-highlight {
          padding: 0.9rem 1rem;
        }

        .serving-controls {
          display: grid;
          grid-template-columns: repeat(3, minmax(0, 1fr));
          gap: 0.75rem;
        }

        .serving-size-btn {
          width: 100%;
        }

        .ingredient-item {
          padding: 0.75rem 1rem;
        }

        .media-card {
          border-radius: 1.5rem;
        }

        .media-card .main-image-wrapper {
          height: 260px;
        }

        .media-card .thumbnail-strip {
          gap: 0.5rem;
          padding-inline: 0.25rem;
        }

        .step-item {
          flex-direction: column;
          align-items: flex-start;
        }

        .step-number {
          margin-bottom: 0.75rem;
        }

        .callout-card {
          gap: 1.25rem;
          padding: 1.75rem;
        }

        .callout-card .cta-buttons {
          width: 100%;
        }

        .callout-card .cta-buttons button {
          width: 100%;
        }

        .rating-card {
          padding: 1.75rem !important;
        }

        .slider-track {
          display: flex !important;
          flex-wrap: nowrap;
          overflow-x: auto;
          gap: 0.85rem;
          padding-inline: 0.75rem;
          margin-inline: -0.75rem;
          scroll-snap-type: x mandatory;
          -webkit-overflow-scrolling: touch;
          scroll-padding-inline-start: 0.75rem;
        }

        .slider-track::-webkit-scrollbar {
          display: none;
        }

        .slider-track > * {
          flex: 0 0 78%;
          max-width: 78%;
          scroll-snap-align: start;
        }

        .tools-slider > * {
          flex-basis: 72%;
          max-width: 72%;
        }

        .related-slider > * {
          flex-basis: 80%;
          max-width: 80%;
        }

        .related-slider {
          padding-bottom: 0.5rem;
        }
      }
      
      /* Print Styles */
      @media print {
        .no-print {
          display: none !important;
        }
        
        body {
          font-size: 12pt;
          line-height: 1.6;
          color: #000;
          background: white;
          direction: rtl;
          font-family: 'Arial', 'Tahoma', 'Segoe UI', sans-serif;
        }
        
        .container {
          max-width: none;
          margin: 0;
          padding: 0;
        }
        
        .bg-white {
          background: white !important;
          box-shadow: none !important;
          border: 1px solid #ddd !important;
        }
        
        .text-orange-500 {
          color: #f97316 !important;
        }
        
        .shadow-lg {
          box-shadow: none !important;
        }
        
        .rounded-xl {
          border-radius: 8px !important;
        }
        
        .p-6 {
          padding: 20px !important;
        }
        
        .mb-8, .mb-6, .mb-4 {
          margin-bottom: 20px !important;
        }
        
        .mt-8, .mt-4 {
          margin-top: 20px !important;
        }
        
        .grid {
          display: block !important;
        }
        
        .flex {
          display: block !important;
        }
        
        .hidden {
          display: none !important;
        }
        
        h1, h2, h3 {
          page-break-after: avoid;
          color: #f97316 !important;
          font-weight: bold;
        }
        
        h1 {
          font-size: 2.2em;
          text-align: center;
          margin-bottom: 20px;
          border-bottom: 3px solid #f97316;
          padding-bottom: 15px;
        }
        
        h2 {
          font-size: 1.8em;
          margin-bottom: 15px;
          border-bottom: 2px solid #f97316;
          padding-bottom: 8px;
          text-align: center;
        }
        
        .print-section {
          page-break-inside: avoid;
          margin-bottom: 25px;
        }
        
        .print-ingredients {
          background: #f8f9fa !important;
          padding: 20px !important;
          border: 1px solid #dee2e6 !important;
          border-radius: 8px !important;
        }
        
        .print-steps {
          background: white !important;
          padding: 20px !important;
          border: 1px solid #dee2e6 !important;
          border-radius: 8px !important;
        }
        
        .print-info {
          background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
          padding: 15px !important;
          border: 1px solid #dee2e6 !important;
          margin-bottom: 20px !important;
          border-radius: 8px !important;
          display: flex !important;
          justify-content: space-around !important;
        }
        
        .print-info-item {
          text-align: center;
          font-weight: 600;
          font-size: 1.1em;
        }
        
        .print-info-item i {
          color: #f97316;
          margin-left: 8px;
          font-size: 1.2em;
        }
        
        ul, ol {
          margin: 15px 0;
          padding-right: 25px;
        }
        
        ul li {
          margin: 8px 0;
          padding: 8px 0;
          border-bottom: 1px solid #eee;
          position: relative;
          padding-right: 20px;
        }
        
        ul li:before {
          content: "•";
          color: #f97316;
          font-weight: bold;
          position: absolute;
          right: 0;
          font-size: 1.3em;
        }
        
        ol li {
          margin: 10px 0;
          padding: 10px 0;
          border-bottom: 1px solid #f0f0f0;
          position: relative;
          padding-right: 30px;
        }
        
        ol li:before {
          content: counter(step-counter);
          counter-increment: step-counter;
          position: absolute;
          right: -25px;
          top: 10px;
          background: #f97316;
          color: white;
          width: 22px;
          height: 22px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-weight: bold;
          font-size: 0.8em;
        }
        
        ol {
          counter-reset: step-counter;
        }
        
        .print-footer {
          text-align: center;
          margin-top: 40px;
          padding-top: 20px;
          border-top: 2px solid #f97316;
          font-size: 11pt;
          color: #666;
          background: #f8f9fa;
          padding: 20px;
          border-radius: 8px;
        }
        
      .print-footer p {
        margin: 5px 0;
      }
      
      /* أنماط كروت المعدات */
      .tool-card {
        background: linear-gradient(135deg, #fef3e7 0%, #fed7aa 100%);
        border: 1px solid #fb923c;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
      }
      
      .tool-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #f97316, #fb923c, #fbbf24);
      }
      
      .tool-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(249, 115, 22, 0.15);
        border-color: #f97316;
      }
      
      .tool-icon {
        width: 3rem;
        height: 3rem;
        background: linear-gradient(135deg, #f97316, #fb923c);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
      }
      
      .tool-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        text-align: center;
      }
      
      .tool-divider {
        width: 2rem;
        height: 3px;
        background: linear-gradient(90deg, #f97316, #fb923c);
        border-radius: 2px;
        margin: 0 auto;
      }

      /* تحسينات إضافية لكروت المعدات في صفحة الوصفة */
      #tools-container .tool-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
      }
      
      #tools-container .tool-card::before {
        display: none;
      }
      
      #tools-container .tool-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-color: #f97316;
      }
      
      #tools-container .tool-card .p-3,
      #tools-container .tool-card .p-4 {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
      }
      
      #tools-container .tool-card h3 {
        min-height: 3.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
      }
      
      #tools-container .tool-card .mt-auto {
        margin-top: auto;
      }
      
      #tools-container .tool-card .rating-stars {
        color: #fbbf24;
      }
      
      #tools-container .tool-card .empty-rating {
        color: #d1d5db;
      }
      
      #tools-container .tool-card .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
      }
      
      /* تحسينات للهواتف */
      @media (max-width: 640px) {
        #tools-container .tool-card {
          margin-bottom: 0.75rem;
          min-height: 280px;
        }
        
        #tools-container .tool-card .p-3 {
          padding: 0.75rem;
        }
        
        #tools-container .tool-card h3 {
          font-size: 0.75rem;
          line-height: 1.2rem;
          min-height: 3rem;
          display: -webkit-box;
          -webkit-line-clamp: 4;
          -webkit-box-orient: vertical;
          overflow: hidden;
          text-overflow: ellipsis;
        }
        
        #tools-container .tool-card .text-sm {
          font-size: 0.875rem;
        }
        
        #tools-container .tool-card .text-lg {
          font-size: 1rem;
        }
        
        #tools-container .tool-card .text-xl {
          font-size: 1.125rem;
        }
        
        #tools-container .tool-card button {
          font-size: 0.75rem;
          padding: 0.5rem 0.75rem;
        }
        
        #tools-container .tool-card .rating-stars {
          font-size: 0.625rem;
        }
      }
      
      /* تحسينات للشاشات المتوسطة */
      @media (min-width: 641px) and (max-width: 1024px) {
        #tools-container .tool-card {
          margin-bottom: 1.25rem;
        }
      }
      
      /* تحسين الأزرار */
      #tools-container .save-for-later-btn:active {
        transform: scale(0.98);
      }
      
      /* تحسين أزرار Amazon */
      #tools-container .tool-card a[href*="amazon"]:active {
        transform: scale(0.98);
      }
      
      #tools-container .tool-card a[href*="amazon"]:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
      }
      
      /* تحسين الشارات */
      #tools-container .category-badge {
        backdrop-filter: blur(10px);
        background: rgba(249, 115, 22, 0.9);
      }
      
      /* ضمان التناسق على الهواتف */
      @media (max-width: 640px) {
        #tools-container {
          gap: 0.75rem;
        }
        
        #tools-container .tool-card {
          width: 100%;
          max-width: 100%;
        }
        
        #tools-container .tool-card img {
          max-height: 120px;
        }
      }
      
      /* أنماط الوصفات المشابهة */
      .related-recipe-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
      }
      
      .related-recipe-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      }
      
      .related-recipe-card .recipe-image {
        position: relative;
        overflow: hidden;
      }
      
      .related-recipe-card .recipe-image::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 0%, rgba(249, 115, 22, 0.1) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
      }
      
      .related-recipe-card:hover .recipe-image::after {
        opacity: 1;
      }
      
      .related-recipe-card .recipe-title {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.4;
        min-height: 2.8rem;
      }
      
      .related-recipe-card .category-badge {
        background: linear-gradient(135deg, #fed7aa 0%, #fb923c 100%);
        color: #9a3412;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(255, 255, 255, 0.3);
        box-shadow: 0 2px 4px rgba(249, 115, 22, 0.2);
      }
      
      .related-recipe-card .rating-stars {
        color: #fbbf24;
        text-shadow: 0 1px 2px rgba(251, 191, 36, 0.3);
      }
      
      .related-recipe-card .empty-rating {
        color: #d1d5db;
      }
      
      .related-recipe-card .save-recipe-btn {
        min-width: 44px;
        min-height: 44px;
        border-radius: 8px;
        transition: all 0.2s ease;
      }
      
      .related-recipe-card .save-recipe-btn:hover {
        transform: scale(1.05);
      }
      
      .related-recipe-card .save-recipe-btn:active {
        transform: scale(0.95);
      }
      
      /* تحسينات للهواتف */
      @media (max-width: 640px) {
        .related-recipe-card {
          margin-bottom: 1rem;
        }
        
        .related-recipe-card .recipe-image {
          height: 200px;
        }
        
        .related-recipe-card .recipe-title {
          font-size: 1rem;
          min-height: 2.4rem;
        }
        
        .related-recipe-card .recipe-info {
          font-size: 0.875rem;
        }
        
        .related-recipe-card .action-buttons {
          flex-direction: column;
          gap: 0.5rem;
        }
        
        .related-recipe-card .save-recipe-btn {
          width: 100%;
          min-height: 40px;
        }
      }
      
      /* تحسينات للشاشات المتوسطة */
      @media (min-width: 641px) and (max-width: 1024px) {
        .related-recipe-card .recipe-image {
          height: 220px;
        }
      }
        
        /* تحسين عرض الصور */
        img {
          max-width: 100% !important;
          height: auto !important;
          page-break-inside: avoid;
        }
        
        /* تحسين عرض الجداول */
        table {
          width: 100%;
          border-collapse: collapse;
          margin: 15px 0;
        }
        
        th, td {
          border: 1px solid #ddd;
          padding: 8px;
          text-align: right;
        }
        
        th {
          background-color: #f8f9fa;
          font-weight: bold;
        }
      }
</style>
@endpush

<!-- Recipe data is now loaded server-side -->

@section('content')

    <main class="container mx-auto px-4 py-8">
      <!-- بداية تصميم البطل للوصفة -->
      <section class="recipe-hero rounded-3xl px-6 py-8 md:px-10 md:py-10 mb-12">
        @php
          $totalTime = (int) ($recipe->prep_time ?? 0) + (int) ($recipe->cook_time ?? 0);
        @endphp
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-start gap-10">
          <div class="flex-1">
            <span class="hero-badge">
              <i class="fas fa-fire ml-1 text-sm"></i>
              وصفة من منصة وصفة
            </span>
            <h1 class="hero-title mt-4">
              {{ $recipe->title }}
            </h1>
            <p class="text-gray-700 text-base md:text-lg leading-relaxed mt-4 max-w-3xl">
              {{ $recipe->description }}
            </p>
            @if($recipe->is_registration_closed)
              <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded-2xl mt-6 no-print flex items-start gap-3">
                <i class="fas fa-clock mt-1 ml-2"></i>
                <span class="font-semibold">انتهت مهلة الحجز لهذه الوصفة</span>
              </div>
            @endif
            <div class="hero-stats-grid mt-8 gap-3 sm:gap-4">
              <div class="hero-stat">
                <span class="hero-stat-icon">
                  <i class="fas fa-star"></i>
                </span>
                <div>
                  <span class="hero-stat-label">التقييم العام</span>
                  <span class="hero-stat-value">
                    @if($recipe->interactions_avg_rating)
                      {{ number_format($recipe->interactions_avg_rating, 1) }}
                      <span class="hero-stat-sub">تقييمات المستخدمين</span>
                    @else
                      لا توجد تقييمات بعد
                    @endif
                  </span>
                </div>
              </div>
              <div class="hero-stat">
                <span class="hero-stat-icon">
                  <i class="fas fa-bookmark"></i>
                </span>
                <div>
                  <span class="hero-stat-label">المستخدمون الذين حفظوا</span>
                  <span class="hero-stat-value" id="recipe-save-count">
                    {{ $recipe->saved_count }} شخص حفظوا هذه الوصفة
                  </span>
                </div>
              </div>
              @if($recipe->servings || $totalTime)
                <div class="hero-stat">
                  <span class="hero-stat-icon">
                    <i class="fas fa-stopwatch"></i>
                  </span>
                  <div>
                    <span class="hero-stat-label">
                      @if($totalTime)
                        الوقت الإجمالي
                      @else
                        الكمية تكفي
                      @endif
                    </span>
                    <span class="hero-stat-value">
                      @if($totalTime)
                        {{ $totalTime }} دقيقة
                        @if($recipe->prep_time && $recipe->cook_time)
                          <span class="hero-stat-sub">تحضير {{ (int) $recipe->prep_time }}د • طبخ {{ (int) $recipe->cook_time }}د</span>
                        @elseif($recipe->prep_time)
                          <span class="hero-stat-sub">تحضير {{ (int) $recipe->prep_time }} دقيقة</span>
                        @elseif($recipe->cook_time)
                          <span class="hero-stat-sub">طبخ {{ (int) $recipe->cook_time }} دقيقة</span>
                        @endif
                      @elseif($recipe->servings)
                        يكفي لـ {{ (int) $recipe->servings }} أشخاص
                      @endif
                    </span>
                  </div>
                </div>
              @endif
            </div>
          </div>
          <div class="hero-panel lg:w-80 xl:w-96 p-6 md:p-7">
            <div class="flex items-center gap-4 pb-5 border-b border-orange-100/60">
              <img
                src="/image/tnl.png"
                alt="صورة مؤلف"
                class="rounded-full w-16 h-16 shadow-lg border-2 border-white"
              />
              <div class="author-meta">
                <span>بواسطة {{ $recipe->author ?? 'مجهول' }}</span>
                <span class="text-sm text-gray-500">آخر تحديث: {{ $recipe->updated_at->format('Y-m-d') }}</span>
              </div>
            </div>
            <div class="hero-actions mt-6 no-print">
              <button 
                id="save-recipe-page-btn"
                class="btn save-recipe-btn {{ $recipe->is_saved ? 'bg-green-500 hover:bg-green-600' : 'bg-orange-500 hover:bg-orange-600' }}" 
                data-recipe-id="{{ $recipe->recipe_id }}" 
                data-saved="{{ $recipe->is_saved ? 'true' : 'false' }}"
                data-user-id="{{ Auth::id() }}">
                <i class="fas fa-bookmark ml-2"></i>
                <span>{{ $recipe->is_saved ? 'محفوظة' : 'حفظ' }}</span>
              </button>
              <button
                id="rating-scroll-btn"
                class="flex items-center justify-center p-3 text-base border border-gray-200 rounded-full font-semibold text-gray-700 bg-white hover:bg-gray-100 transition-colors {{ $recipe->user_rating ? 'bg-green-50 border-green-300 text-green-700' : '' }}"
              >
                <i class="fas fa-star ml-2"></i>
                <span id="rating-btn-text">
                  @if($recipe->user_rating)
                    تم التقييم
                  @else
                    تقييم
                  @endif
                </span>
              </button>
              <button
                id="print-recipe-btn"
                class="flex items-center justify-center p-3 text-base border border-gray-200 rounded-full font-semibold text-gray-700 bg-white hover:bg-gray-100 transition-colors"
              >
                <i class="fas fa-print ml-2"></i>
                طباعة
              </button>
              <button
                id="share-recipe-btn-1"
                class="flex items-center justify-center p-3 text-base border border-gray-200 rounded-full font-semibold text-gray-700 bg-white hover:bg-gray-100 transition-colors"
              >
                <i class="fas fa-share-alt ml-2"></i>
                مشاركة
              </button>
            </div>
          </div>
        </div>
      </section>
      <!-- *******************************
           نهاية تصميم البطل للوصفة
           ******************************* -->


      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Media Container for Images or Video -->
        <div class="media-card">
            @if($recipe->getAllImages() && count($recipe->getAllImages()) > 0)
                <!-- Image Gallery -->
                <div class="relative">
                    <!-- Main Image Display -->
                    <div class="main-image-wrapper relative overflow-hidden">
                        <img id="main-recipe-image" class="w-full h-full object-cover transition-opacity duration-300" 
                             src="{{ $recipe->getAllImages()[0] }}" alt="صورة الوصفة"
                             onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                        
                        <!-- Navigation Arrows -->
                        @if(count($recipe->getAllImages()) > 1)
                            <button onclick="previousImage()" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition-all">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button onclick="nextImage()" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition-all">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif
                        
                        <!-- Image Counter -->
                        @if(count($recipe->getAllImages()) > 1)
                            <div class="absolute bottom-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                                <span id="image-counter">1</span> / {{ count($recipe->getAllImages()) }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Thumbnail Strip -->
                    @if(count($recipe->getAllImages()) > 1)
                        <div class="thumbnail-strip flex space-x-2 rtl:space-x-reverse p-4 bg-gray-50 overflow-x-auto">
                            @foreach($recipe->getAllImages() as $index => $imageUrl)
                                <img onclick="showImage({{ $index }})" 
                                     class="w-16 h-16 object-cover rounded-xl cursor-pointer border-2 transition-all thumbnail {{ $index === 0 ? 'active' : '' }}" 
                                     src="{{ $imageUrl }}" 
                                     alt="صورة {{ $index + 1 }}"
                                     onerror="this.src='{{ asset('image/logo.png') }}';">
                            @endforeach
                        </div>
                    @endif
                </div>
            @elseif($recipe->video_url)
                <iframe
                    class="w-full aspect-video"
                    src="{{ $recipe->video_url }}"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                ></iframe>
            @else
                <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-image text-4xl text-gray-400"></i>
                </div>
            @endif
        </div>

        <div class="section-card info-card px-6 py-6 md:px-8 md:py-8">
          <h2 class="section-title">
            <i class="fas fa-info-circle"></i>
            <span>معلومات أساسية</span>
          </h2>
          <div class="info-highlight-grid">
            <div class="info-highlight">
              <span class="info-icon">
                <i class="fas fa-clock"></i>
              </span>
              <div>
                <span class="info-label">وقت التحضير</span>
                <span class="info-value">
                  <span data-prep-time>{{ (int)$recipe->prep_time }}</span>
                  <span class="info-unit">دقيقة</span>
                </span>
              </div>
            </div>
            <div class="info-highlight">
              <span class="info-icon">
                <i class="fas fa-fire"></i>
              </span>
              <div>
                <span class="info-label">وقت الطهي</span>
                <span class="info-value">
                  <span data-cook-time>{{ (int)$recipe->cook_time }}</span>
                  <span class="info-unit">دقيقة</span>
                </span>
              </div>
            </div>
            <div class="info-highlight">
              <span class="info-icon">
                <i class="fas fa-utensils"></i>
              </span>
              <div>
                <span class="info-label">تكفي لـ</span>
                <span class="info-value">
                  <span data-servings>{{ (int)$recipe->servings }}</span>
                  <span class="info-unit">أشخاص</span>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="section-card px-6 py-6 md:px-8 md:py-8">
          <h2 class="section-title">
            <i class="fas fa-list"></i>
            <span>المكونات</span>
          </h2>

          <div class="serving-controls mb-6 no-print">
            <button
              class="serving-size-btn"
              data-multiplier="0.5"
            >
              1/2x
            </button>
            <button
              class="serving-size-btn bg-orange-500 text-white"
              data-multiplier="1"
            >
              1x
            </button>
            <button
              class="serving-size-btn"
              data-multiplier="2"
            >
              2x
            </button>
          </div>

          <div class="serving-size-hint no-print">
            <span class="text-sm text-gray-600">
              الوصفة الأصلية (1X) تنتج حصتين
            </span>
            <i
              id="info-icon"
              class="fas fa-question-circle text-gray-400 cursor-pointer"
            ></i>
            <div
              id="info-tooltip"
              class="absolute bottom-full right-0 mb-2 p-4 bg-white rounded-xl shadow-xl max-w-xs w-64 z-10 hidden transform origin-bottom-right transition-opacity duration-300 border border-orange-100"
            >
              <p class="text-sm text-gray-700 leading-relaxed">
                تم تطوير هذه الوصفة بإنتاجيتها الأصلية. يتم تعديل كميات المكونات
                تلقائيًا، ولكن أوقات الطهي والخطوات تظل كما هي. لاحظ أنه ليست كل
                الوصفات تتناسب بشكل مثالي.
              </p>
            </div>
          </div>

          <ul class="ingredient-list mt-6">
            @foreach($recipe->ingredients as $ingredient)
              <li class="ingredient-item" 
                  data-original-quantity="{{ $ingredient->quantity ?? '' }}"
                  data-name="{{ $ingredient->name }}">
                <span class="ingredient-icon">
                  <i class="fas fa-check"></i>
                </span>
                <span class="full-ingredient-text">{{ $ingredient->quantity ?? 'كمية حسب الحاجة' }} {{ $ingredient->name }}</span>
              </li>
            @endforeach
          </ul>
        </div>

        <!-- قسم المعدات -->
        <div class="section-card px-6 py-6 md:px-8 md:py-8">
          <h2 class="section-title mb-6">
            <i class="fas fa-tools"></i>
            <span>المعدات المستخدمة</span>
          </h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 slider-track tools-slider">
            @if((is_array($recipe->tools) ? count($recipe->tools) : $recipe->tools->count()) > 0)
              @foreach($recipe->tools as $tool)
                <div class="tool-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                  <!-- Tool Image -->
                  <div class="h-36 bg-gray-100 flex items-center justify-center">
                    @if(isset($tool['image']) && $tool['image'])
                      <img src="{{ asset('storage/' . $tool['image']) }}" alt="{{ $tool['name'] }}" class="w-full h-full object-cover">
                    @else
                      <i class="fas fa-tools text-4xl text-gray-400"></i>
                    @endif
                  </div>
                  
                  <!-- Tool Content -->
                  <div class="p-4">
                    <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2">{{ $tool['name'] }}</h3>
                    
                    <!-- Rating -->
                    <div class="flex items-center mb-3">
                      <div class="flex space-x-1 rtl:space-x-reverse">
                        @for($i = 1; $i <= 5; $i++)
                          <i class="fas fa-star {{ $i <= $tool['rating'] ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                        @endfor
                      </div>
                      <span class="text-sm text-gray-500 mr-2">{{ number_format($tool['rating'], 1) }}</span>
                    </div>
                    
                    <!-- Price -->
                    <div class="text-lg font-bold text-orange-500 mb-3">
                      {{ $tool['price'] ? 'د.إ ' . number_format($tool['price'], 2) : 'غير محدد' }}
                    </div>
                    
                    <!-- Save Button -->
                    <button class="save-for-later-btn w-full bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2 rtl:space-x-reverse" 
                            data-tool-id="{{ $tool['id'] }}" 
                            data-tool-name="{{ $tool['name'] }}" 
                            data-tool-price="{{ $tool['price'] }}">
                      <i class="fas fa-bookmark"></i>
                      <span class="btn-text">حفظ للشراء لاحقاً</span>
                      <i class="fas fa-spinner fa-spin loading-icon hidden"></i>
                    </button>
                    
                    <!-- Amazon Link -->
                    @if($tool['amazon_url'])
                      <a href="{{ $tool['amazon_url'] }}" target="_blank" 
                         class="mt-2 w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center space-x-2 rtl:space-x-reverse">
                        <i class="fab fa-amazon"></i>
                        <span>عرض في Amazon</span>
                      </a>
                    @endif
                  </div>
                </div>
              @endforeach
            @else
              <div class="text-center text-gray-500 italic col-span-full py-8">
                <i class="fas fa-tools text-4xl text-gray-300 mb-3"></i>
                <p>لا توجد معدات محددة لهذه الوصفة</p>
              </div>
            @endif
          </div>
        </div>
      </div>

      <div class="mt-8">
        <div class="section-card px-6 py-6 md:px-8 md:py-8">
          <h2 class="section-title">
            <i class="fas fa-clipboard-check"></i>
            <span>خطوات التحضير</span>
          </h2>
          <ol class="step-list">
            @foreach($recipe->steps as $index => $step)
              <li class="step-item">
                <span class="step-number">{{ $index + 1 }}</span>
                <span class="step-text">{{ $step }}</span>
              </li>
            @endforeach
          </ol>
        </div>
      </div>
      </div>

      <div class="section-card callout-card mt-8 no-print">
        <div class="callout-meta">
          <span class="callout-badge">
            <i class="fas fa-users ml-1"></i>
            تفاعل المجتمع
          </span>
          <h3 id="question-text" class="font-semibold text-gray-800 text-lg md:text-xl">
            هل جربت هذه الوصفة؟
          </h3>
          <p id="made-it-count" class="text-gray-600 text-sm md:text-base">
            @if($recipe->made_count > 0)
              <span class="font-bold text-orange-500 text-lg">{{ $recipe->made_count }}</span> شخص جربوا هذه الوصفة!
            @else
              كن أول من يجرب هذه الوصفة! 🚀
            @endif
          </p>
        </div>
        <div class="cta-buttons">
          <button
            id="made-recipe-btn"
            class="flex items-center justify-center px-6 py-3 rounded-full font-semibold text-white transition-colors {{ $recipe->is_made ? 'bg-green-500 hover:bg-green-600' : 'bg-orange-500 hover:bg-orange-600' }}"
            data-recipe-id="{{ $recipe->recipe_id }}"
            data-user-id="{{ Auth::id() }}"
            data-made="{{ $recipe->is_made ? 'true' : 'false' }}"
          >
            <i class="fas fa-check-circle ml-2"></i>
            <span id="made-btn-text">{{ $recipe->is_made ? 'جربتها!' : 'لقد جربتها!' }}</span>
          </button>
          <button
            id="share-recipe-btn-2"
            class="flex items-center justify-center px-6 py-3 border border-orange-200 text-orange-600 rounded-full font-semibold bg-white/90 hover:bg-white transition-colors"
          >
            <i class="fas fa-share-alt ml-2"></i>
            مشاركة الوصفة
          </button>
        </div>
      </div>

      <section id="rating-section" class="py-8 no-print">
        <div class="section-card rating-card px-6 py-6 md:px-8 md:py-8">
          <h2 class="section-title">
            <i class="fas fa-star"></i>
            <span>تقييم الوصفة</span>
          </h2>
          <div class="flex flex-col items-center text-center gap-4">
            <div class="star-rating">
              <input type="radio" id="star5" name="rating" value="5" {{ $recipe->user_rating == 5 ? 'checked' : '' }} /><label for="star5" title="5 نجوم">
                <span class="star">★</span></label
              >
              <input type="radio" id="star4" name="rating" value="4" {{ $recipe->user_rating == 4 ? 'checked' : '' }} /><label for="star4" title="4 نجوم">
                <span class="star">★</span></label
              >
              <input type="radio" id="star3" name="rating" value="3" {{ $recipe->user_rating == 3 ? 'checked' : '' }} /><label for="star3" title="3 نجوم">
                <span class="star">★</span></label
              >
              <input type="radio" id="star2" name="rating" value="2" {{ $recipe->user_rating == 2 ? 'checked' : '' }} /><label for="star2" title="نجمتان">
                <span class="star">★</span></label
              >
              <input type="radio" id="star1" name="rating" value="1" {{ $recipe->user_rating == 1 ? 'checked' : '' }} /><label for="star1" title="نجمة واحدة">
                <span class="star">★</span></label
              >
            </div>
            <p id="user-rating-text" class="text-center text-gray-500 dark-text mt-2 mb-4">
              @if(Auth::check())
                @if($recipe->user_rating)
                  تقييمك: {{ $recipe->user_rating }} نجوم
                @else
                  الرجاء تقييم الوصفة
                @endif
              @else
                <a href="/login" class="text-orange-500 hover:text-orange-600 underline">سجل الدخول لتقييم الوصفة</a>
              @endif
            </p>
            @if(Auth::check())
              <div class="rating-actions">
                <button
                  id="submit-rating-btn"
                  class="flex-1 px-6 py-3 rounded-full font-semibold text-white bg-orange-500 hover:bg-orange-600 transition-colors"
                >
                  <i class="fas fa-paper-plane ml-2"></i>
                  أرسل التقييم
                </button>
                @if($recipe->user_rating)
                  <button
                    id="remove-rating-btn"
                    class="flex-1 px-6 py-3 rounded-full font-semibold text-red-500 bg-transparent border-2 border-red-500 hover:bg-red-50 hover:text-red-600 transition-colors"
                  >
                    <i class="fas fa-trash ml-2"></i>
                    إلغاء التقييم
                  </button>
                @endif
              </div>
            @else
              <a href="/login" class="inline-flex items-center justify-center px-6 py-3 rounded-full font-semibold text-white bg-orange-500 hover:bg-orange-600 transition-colors">
                <i class="fas fa-sign-in-alt ml-2"></i>
                سجل الدخول للتقييم
              </a>
            @endif
          </div>
        </div>
      </section>

      <section class="mt-8 no-print related-section">
        <div class="section-card px-6 py-6 md:px-8 md:py-8">
          <h2 class="section-title">
            <i class="fas fa-utensils"></i>
            <span>وصفات مشابهة من نفس التصنيف</span>
          </h2>
          
          @if($relatedRecipes && $relatedRecipes->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 slider-track related-slider">
              @foreach($relatedRecipes as $relatedRecipe)
                <div class="related-recipe-card bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                  <!-- Recipe Image -->
                  <div class="recipe-image h-48 bg-gray-100 flex items-center justify-center">
                    @if($relatedRecipe->image_url)
                      <img src="{{ $relatedRecipe->image_url }}" alt="{{ $relatedRecipe->title }}" 
                           class="w-full h-full object-cover"
                           onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
                    @else
                      <i class="fas fa-image text-4xl text-gray-400"></i>
                    @endif
                  </div>
                  
                  <!-- Recipe Content -->
                  <div class="p-4">
                    <h3 class="recipe-title font-semibold text-gray-800 mb-2 text-lg">
                      {{ Str::limit($relatedRecipe->title, 50) }}
                    </h3>
                    
                    <!-- Category Badge -->
                    @if($relatedRecipe->category)
                      <div class="mb-3">
                        <span class="category-badge inline-block text-xs font-semibold px-2 py-1 rounded-full">
                          {{ $relatedRecipe->category->name }}
                        </span>
                      </div>
                    @endif
                    
                    <!-- Recipe Info -->
                    <div class="recipe-info flex items-center justify-between text-sm text-gray-600 mb-3">
                      <div class="flex items-center">
                        <i class="fas fa-clock text-orange-500 ml-1"></i>
                        <span>{{ (int)$relatedRecipe->prep_time }} دقيقة</span>
                      </div>
                      <div class="flex items-center">
                        <i class="fas fa-users text-orange-500 ml-1"></i>
                        <span>{{ (int)$relatedRecipe->servings }} أشخاص</span>
                      </div>
                    </div>
                    
                    <!-- Rating -->
                    <div class="flex items-center mb-4">
                      <div class="flex space-x-1 rtl:space-x-reverse">
                        @for($i = 1; $i <= 5; $i++)
                          <i class="fas fa-star {{ $i <= ($relatedRecipe->interactions_avg_rating ?? 0) ? 'rating-stars' : 'empty-rating' }} text-sm"></i>
                        @endfor
                      </div>
                      <span class="text-sm text-gray-500 mr-2">
                        @if($relatedRecipe->interactions_avg_rating)
                          {{ number_format($relatedRecipe->interactions_avg_rating, 1) }}
                        @else
                          لا توجد تقييمات
                        @endif
                      </span>
                    </div>
                    
                    <!-- Save Count -->
                    <div class="flex items-center text-sm text-gray-600 mb-4">
                      <i class="fas fa-bookmark text-orange-500 ml-1"></i>
                      <span>{{ $relatedRecipe->saved_count }} شخص حفظوا هذه الوصفة</span>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons flex space-x-2 rtl:space-x-reverse">
                      <a href="{{ route('recipe.show', $relatedRecipe->slug) }}" 
                         class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg transition-colors text-center text-sm font-semibold">
                        <i class="fas fa-eye ml-1"></i>
                        عرض الوصفة
                      </a>

                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center text-gray-500 py-8">
              <i class="fas fa-utensils text-4xl text-gray-300 mb-3"></i>
              <p>لا توجد وصفات مشابهة في نفس التصنيف</p>
            </div>
          @endif
        </div>
      </section>
      
    </main>
    
    <!-- Confirmation Modal -->
    <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <div class="flex items-center justify-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
            </div>
            
            <div class="text-center mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-2">تأكيد الإلغاء</h3>
                <p class="text-gray-600 leading-relaxed">
                    هل أنت متأكد من إلغاء التقييم؟<br>
                    <span class="text-sm text-gray-500">لن يتم حذف الحفظ أو حالة "جربتها"</span>
                </p>
            </div>
            
            <div class="flex space-x-3 rtl:space-x-reverse">
                <button id="confirm-cancel" class="flex-1 p-3 rounded-lg font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
                    إلغاء
                </button>
                <button id="confirm-delete" class="flex-1 p-3 rounded-lg font-semibold text-white bg-red-500 hover:bg-red-600 transition-colors">
                    <i class="fas fa-trash ml-2"></i>
                    نعم، ألغِ التقييم
                </button>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div id="share-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">مشاركة الوصفة</h3>
                <button id="close-share-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <!-- Recipe Info -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 mb-2">{{ $recipe->title }}</h4>
                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($recipe->description, 100) }}</p>
                    <div class="flex items-center space-x-4 rtl:space-x-reverse text-sm text-gray-500">
                        <span><i class="fas fa-clock ml-1"></i> {{ (int)$recipe->prep_time }} دقيقة</span>
                        <span><i class="fas fa-users ml-1"></i> {{ (int)$recipe->servings }} أشخاص</span>
                    </div>
                </div>
                
                <!-- Share Options -->
                <div class="space-y-3">
                    <h5 class="font-semibold text-gray-700">اختر طريقة المشاركة:</h5>
                    
                    <!-- Copy Link -->
                    <button id="copy-link-btn" class="w-full flex items-center justify-center p-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                        <i class="fas fa-copy ml-2"></i>
                        نسخ الرابط
                    </button>
                    
                    <!-- Social Media -->
                    <div class="grid grid-cols-2 gap-3">
                        <a id="whatsapp-share" href="#" target="_blank" class="flex items-center justify-center p-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                            <i class="fab fa-whatsapp ml-2"></i>
                            واتساب
                        </a>
                        <a id="telegram-share" href="#" target="_blank" class="flex items-center justify-center p-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fab fa-telegram ml-2"></i>
                            تليجرام
                        </a>
                    </div>
                </div>
                
                <!-- Success Message -->
                <div id="copy-success" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    <i class="fas fa-check-circle ml-2"></i>
                    تم نسخ الرابط بنجاح!
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
        @vite(['resources/js/made-recipe.js', 'resources/js/share-recipe.js'])
        <script>
            // معالجة إضافية لروابط Google Drive في صفحة الوصفة
            document.addEventListener('DOMContentLoaded', function() {
                // معالجة الصور الموجودة في الصفحة
                function processRecipeImages() {
                    const images = document.querySelectorAll('img[src*="drive.google.com"]');
                    images.forEach(function(img) {
                        const originalSrc = img.src;
                        const convertedSrc = convertGoogleDriveUrl(originalSrc);
                        if (convertedSrc !== originalSrc) {
                            img.src = convertedSrc;
                        }
                    });
                }
                
                // معالجة فورية
                processRecipeImages();
                
                // معالجة دورية كل ثانيتين للتأكد من معالجة الصور المحملة ديناميكياً
                const interval = setInterval(function() {
                    processRecipeImages();
                }, 2000);
                
                // إيقاف المعالجة الدورية بعد 10 ثوانٍ
                setTimeout(function() {
                    clearInterval(interval);
                }, 10000);

                // Load saved status for tools
                loadToolsSavedStatus();
                
                // Load saved count for the badge
                loadSavedCount();
                
                // Initialize serving size functionality
                initializeServingSize();
                
                // Initialize rating functionality
                initializeRating();
                
                // Initialize rating scroll functionality
                initializeRatingScroll();
                
                // Initialize remove rating functionality
                initializeRemoveRating();
                
                // Initialize modal close functionality
                initializeModalClose();
            });

            // Load saved status for tools
            function loadToolsSavedStatus() {
                fetch('/saved/status')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update each tool's saved status
                            document.querySelectorAll('.save-for-later-btn').forEach(btn => {
                                const toolId = btn.dataset.toolId;
                                const isSaved = data.saved_tools.includes(parseInt(toolId));
                                
                                if (isSaved) {
                                    btn.querySelector('.btn-text').textContent = 'محفوظ';
                                    btn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                                    btn.classList.add('bg-green-500', 'hover:bg-green-600', 'saved');
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading saved status:', error);
                    });
            }

            // Update saved count UI
            function updateSavedCountUI(count) {
                // تحديث العداد في الهيدر
                const savedCountEl = document.getElementById('saved-count');
                if (savedCountEl) {
                    if (count > 0) {
                        savedCountEl.textContent = count;
                        savedCountEl.classList.remove('hidden');
                        console.log('Updated navbar counter to:', count);
                    } else {
                        savedCountEl.classList.add('hidden');
                        console.log('Hidden navbar counter');
                    }
                }
            }

            // Load saved count for the badge
            function loadSavedCount() {
                console.log('Loading saved count from recipe page...');
                fetch('/saved/count')
                    .then(response => {
                        console.log('Recipe page response status:', response.status);
                        if (response.status === 401) {
                            console.log('User not authenticated in recipe page');
                            updateSavedCountUI(0);
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data) {
                            console.log('Recipe page saved count data:', data);
                            updateSavedCountUI(data.count);
                            
                            // تحديث فوري للعداد في الهاتف المحمول (السلة)
                            const mobileCartCountEl = document.getElementById('mobile-cart-count');
                            if (mobileCartCountEl) {
                                if (data.count > 0) {
                                    mobileCartCountEl.textContent = data.count;
                                    mobileCartCountEl.classList.remove('hidden');
                                    console.log('Updated mobile cart count (saved tools) from recipe page:', data.count);
                                } else {
                                    mobileCartCountEl.classList.add('hidden');
                                    console.log('Hidden mobile cart count (saved tools) from recipe page');
                                }
                            }
                            
                            // تحديث فوري للعداد في الهاتف المحمول (الأدوات المحفوظة)
                            const mobileSavedCountEl = document.getElementById('saved-count-mobile');
                            if (mobileSavedCountEl) {
                                if (data.count > 0) {
                                    mobileSavedCountEl.textContent = data.count;
                                    mobileSavedCountEl.classList.remove('hidden');
                                    console.log('Updated mobile saved count from recipe page:', data.count);
                                } else {
                                    mobileSavedCountEl.classList.add('hidden');
                                    console.log('Hidden mobile saved count from recipe page');
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error loading saved count from recipe page:', error);
                        updateSavedCountUI(0);
                        
                        // تحديث فوري للعداد في الهاتف المحمول عند الخطأ
                        const mobileCartCountEl = document.getElementById('mobile-cart-count');
                        if (mobileCartCountEl) {
                            mobileCartCountEl.classList.add('hidden');
                            console.log('Hidden mobile cart count (saved tools) due to error');
                        }
                        
                        const mobileSavedCountEl = document.getElementById('saved-count-mobile');
                        if (mobileSavedCountEl) {
                            mobileSavedCountEl.classList.add('hidden');
                            console.log('Hidden mobile saved count due to error');
                        }
                    });
            }

            // Save for Later Functionality for tools
            document.addEventListener('click', function(e) {
                if (e.target.closest('.save-for-later-btn')) {
                    e.preventDefault();
                    const btn = e.target.closest('.save-for-later-btn');
                    const toolId = btn.dataset.toolId;
                    const toolName = btn.dataset.toolName;
                    const toolPrice = btn.dataset.toolPrice;
                    
                    // Check if item is already saved
                    if (btn.classList.contains('saved')) {
                        // Item is saved, remove it
                        removeFromSaved(btn, toolId);
                        return;
                    }
                    
                    // Show loading state
                    btn.disabled = true;
                    btn.querySelector('.btn-text').textContent = 'جاري الحفظ...';
                    btn.querySelector('.loading-icon').classList.remove('hidden');
                    
                    // Save for later
                    fetch('/saved/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            tool_id: toolId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success state - Item is now saved
                            btn.querySelector('.btn-text').textContent = 'محفوظ';
                            btn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                            btn.classList.add('bg-green-500', 'hover:bg-green-600');
                            btn.classList.add('saved');
                            btn.disabled = false;
                            
                            // Update saved count in header
                            const currentCount = parseInt(document.getElementById('saved-count')?.textContent || '0');
                            updateSavedCountUI(currentCount + 1);
                            console.log('Updated counter after adding item:', currentCount + 1);
                            
                            // تحديث فوري للعداد في الهاتف المحمول (السلة)
                            const mobileCartCountEl = document.getElementById('mobile-cart-count');
                            if (mobileCartCountEl) {
                                const currentCartCount = parseInt(mobileCartCountEl.textContent || '0');
                                mobileCartCountEl.textContent = currentCartCount + 1;
                                mobileCartCountEl.classList.remove('hidden');
                                console.log('Updated mobile cart count (saved tools) after adding item from recipe page:', currentCartCount + 1);
                            }
                            
                            // تحديث فوري للعداد في الهاتف المحمول (الأدوات المحفوظة)
                            const mobileSavedCountEl = document.getElementById('saved-count-mobile');
                            if (mobileSavedCountEl) {
                                const currentMobileCount = parseInt(mobileSavedCountEl.textContent || '0');
                                mobileSavedCountEl.textContent = currentMobileCount + 1;
                                mobileSavedCountEl.classList.remove('hidden');
                                console.log('Updated mobile saved count after adding item from recipe page:', currentMobileCount + 1);
                            }
                            
                            // Show success animation on the button
                            btn.style.transform = 'scale(1.05)';
                            setTimeout(() => {
                                btn.style.transform = 'scale(1)';
                            }, 200);
                            
                            // Show toast notification
                            showToast('تم حفظ المنتج للشراء لاحقاً!', 'success');
                            
                            // Hide loading icon
                            btn.querySelector('.loading-icon').classList.add('hidden');
                        } else {
                            // Show error state
                            btn.querySelector('.btn-text').textContent = 'خطأ في الحفظ';
                            btn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                            btn.classList.add('bg-red-500');
                            
                            showToast(data.message || 'حدث خطأ أثناء حفظ المنتج', 'error');
                            
                            // Reset button after 2 seconds
                            setTimeout(() => {
                                btn.disabled = false;
                                btn.querySelector('.btn-text').textContent = 'حفظ للشراء لاحقاً';
                                btn.querySelector('.loading-icon').classList.add('hidden');
                                btn.classList.remove('bg-red-500');
                                btn.classList.add('bg-orange-500', 'hover:bg-orange-600');
                            }, 2000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        btn.disabled = false;
                        btn.querySelector('.btn-text').textContent = 'حفظ للشراء لاحقاً';
                        btn.querySelector('.loading-icon').classList.add('hidden');
                        showToast('حدث خطأ أثناء حفظ المنتج', 'error');
                    });
                }
            });

            // Remove from saved function
            function removeFromSaved(button, toolId) {
                // Show loading state
                button.disabled = true;
                button.querySelector('.btn-text').textContent = 'جاري الحذف...';
                button.querySelector('.loading-icon').classList.remove('hidden');
                
                // Remove from saved
                fetch('/saved/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        tool_id: toolId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success state - Item removed
                        button.querySelector('.btn-text').textContent = 'حفظ للشراء لاحقاً';
                        button.classList.remove('bg-green-500', 'hover:bg-green-600', 'saved');
                        button.classList.add('bg-orange-500', 'hover:bg-orange-600');
                        button.disabled = false;
                        
                        // Update saved count
                        const currentCount = parseInt(document.getElementById('saved-count')?.textContent || '0');
                        updateSavedCountUI(Math.max(0, currentCount - 1));
                        console.log('Updated counter after removing item:', Math.max(0, currentCount - 1));
                        
                        // تحديث فوري للعداد في الهاتف المحمول (السلة)
                        const mobileCartCountEl = document.getElementById('mobile-cart-count');
                        if (mobileCartCountEl) {
                            const currentCartCount = parseInt(mobileCartCountEl.textContent || '0');
                            const newCartCount = Math.max(0, currentCartCount - 1);
                            mobileCartCountEl.textContent = newCartCount;
                            if (newCartCount === 0) {
                                mobileCartCountEl.classList.add('hidden');
                            } else {
                                mobileCartCountEl.classList.remove('hidden');
                            }
                            console.log('Updated mobile cart count (saved tools) after removing item from recipe page:', newCartCount);
                        }
                        
                        // تحديث فوري للعداد في الهاتف المحمول (الأدوات المحفوظة)
                        const mobileSavedCountEl = document.getElementById('saved-count-mobile');
                        if (mobileSavedCountEl) {
                            const currentMobileCount = parseInt(mobileSavedCountEl.textContent || '0');
                            const newCount = Math.max(0, currentMobileCount - 1);
                            mobileSavedCountEl.textContent = newCount;
                            if (newCount === 0) {
                                mobileSavedCountEl.classList.add('hidden');
                            } else {
                                mobileSavedCountEl.classList.remove('hidden');
                            }
                            console.log('Updated mobile saved count after removing item from recipe page:', newCount);
                        }
                        
                        // Show success animation
                        button.style.transform = 'scale(1.05)';
                        setTimeout(() => {
                            button.style.transform = 'scale(1)';
                        }, 200);
                        
                        // Show success message
                        showToast('تم حذف المنتج من المحفوظات!', 'success');
                        
                        // Hide loading icon
                        button.querySelector('.loading-icon').classList.add('hidden');
                    } else {
                        throw new Error(data.message || 'حدث خطأ أثناء حذف المنتج');
                    }
                })
                .catch(error => {
                    console.error('Error removing from saved:', error);
                    button.disabled = false;
                    button.querySelector('.btn-text').textContent = 'محفوظ';
                    button.querySelector('.loading-icon').classList.add('hidden');
                    showToast('حدث خطأ أثناء حذف المنتج من المحفوظات', 'error');
                });
            }

            // Toast notification function
            function showToast(message, type = 'info') {
                // Remove existing toasts
                const existingToasts = document.querySelectorAll('.toast');
                existingToasts.forEach(toast => toast.remove());
                
                // Create toast element
                const toast = document.createElement('div');
                toast.className = `toast fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium transition-all duration-300 transform translate-x-full`;
                
                // Set color based on type
                if (type === 'success') {
                    toast.classList.add('bg-green-500');
                } else if (type === 'error') {
                    toast.classList.add('bg-red-500');
                } else if (type === 'warning') {
                    toast.classList.add('bg-yellow-500');
                } else {
                    toast.classList.add('bg-blue-500');
                }
                
                toast.textContent = message;
                document.body.appendChild(toast);
                
                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                }, 100);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 3000);
            }

            // Serving size functionality
            function initializeServingSize() {
                const servingButtons = document.querySelectorAll('.serving-size-btn');
                const ingredientItems = document.querySelectorAll('.ingredient-item');
                
                servingButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        // Remove active class from all buttons
                        servingButtons.forEach(btn => {
                            btn.classList.remove('bg-orange-500', 'text-white');
                            btn.classList.add('bg-white', 'text-gray-700');
                        });
                        
                        // Add active class to clicked button
                        this.classList.remove('bg-white', 'text-gray-700');
                        this.classList.add('bg-orange-500', 'text-white');
                        
                        // Get multiplier
                        const multiplier = parseFloat(this.dataset.multiplier);
                        
                        // Update ingredient quantities
                        ingredientItems.forEach(item => {
                            const originalQuantity = item.dataset.originalQuantity;
                            const name = item.dataset.name || '';
                            
                            if (multiplier === 1) {
                                // Reset to original quantity
                                const fullTextSpan = item.querySelector('.full-ingredient-text');
                                if (fullTextSpan) {
                                    fullTextSpan.textContent = originalQuantity + ' ' + name;
                                }
                            } else {
                                // Try to extract number from quantity string (e.g., "2 كوب" -> 2)
                                const quantityMatch = originalQuantity.match(/(\d+(?:\.\d+)?)/);
                                if (quantityMatch) {
                                    const originalAmount = parseFloat(quantityMatch[1]);
                                    const newAmount = originalAmount * multiplier;
                                    
                                    // Format the new amount
                                    let displayAmount;
                                    if (newAmount === 0.25) displayAmount = "1/4";
                                    else if (newAmount === 0.5) displayAmount = "1/2";
                                    else if (newAmount === 0.75) displayAmount = "3/4";
                                    else if (newAmount % 1 === 0) displayAmount = newAmount.toString();
                                    else displayAmount = newAmount.toFixed(2).replace(/\.00$/, '');
                                    
                                    // Extract the unit part (everything after the number)
                                    const unitPart = originalQuantity.replace(/(\d+(?:\.\d+)?)/, '').trim();
                                    
                                    // Update the full text span with new amount + unit + name
                                    const fullTextSpan = item.querySelector('.full-ingredient-text');
                                    if (fullTextSpan) {
                                        fullTextSpan.textContent = displayAmount + ' ' + unitPart + ' ' + name;
                                    }
                                } else {
                                    // If no number found, keep original
                                    const fullTextSpan = item.querySelector('.full-ingredient-text');
                                    if (fullTextSpan) {
                                        fullTextSpan.textContent = originalQuantity + ' ' + name;
                                    }
                                }
                            }
                        });
                    });
                });
            }

            // Rating functionality
            function initializeRating() {
                const ratingInputs = document.querySelectorAll('input[name="rating"]');
                const submitBtn = document.getElementById('submit-rating-btn');
                const userRatingText = document.getElementById('user-rating-text');
                const recipeId = document.querySelector('[data-recipe-id]')?.dataset.recipeId;

                // Handle star clicks
                ratingInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        const rating = this.value;
                        userRatingText.textContent = `تقييمك: ${rating} نجوم`;
                        
                        // Reset submit button if it was previously submitted
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-paper-plane ml-2"></i>أرسل التقييم';
                            submitBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                            submitBtn.classList.add('bg-orange-500', 'hover:bg-orange-600');
                        }
                    });
                });

                // Handle submit button
                if (submitBtn && recipeId) {
                    submitBtn.addEventListener('click', function() {
                        const selectedRating = document.querySelector('input[name="rating"]:checked');
                        
                        if (!selectedRating) {
                            showToast('الرجاء اختيار تقييم قبل الإرسال', 'warning');
                            return;
                        }

                        const rating = selectedRating.value;
                        
                        // Check if user is authenticated
                        const userId = document.querySelector('[data-user-id]')?.dataset.userId;
                        if (!userId || userId === '' || userId === 'null') {
                            showToast('يجب تسجيل الدخول لتقييم الوصفة', 'warning');
                            // Redirect to login page
                            setTimeout(() => {
                                window.location.href = '/login';
                            }, 2000);
                            return;
                        }
                        
                        // Show loading state
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري الإرسال...';

                        // Send rating to server
                        const saveRecipeBtn = document.getElementById('save-recipe-page-btn');
                        const madeRecipeBtn = document.getElementById('made-recipe-btn');

                        const requestPayload = {
                            recipe_id: recipeId,
                            rating: parseInt(rating)
                        };

                        if (saveRecipeBtn && typeof saveRecipeBtn.dataset.saved !== 'undefined') {
                            requestPayload.is_saved = saveRecipeBtn.dataset.saved === 'true';
                        }

                        if (madeRecipeBtn && typeof madeRecipeBtn.dataset.made !== 'undefined') {
                            requestPayload.is_made = madeRecipeBtn.dataset.made === 'true';
                        }

                        fetch('/api/interactions', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(requestPayload)
                        })
                        .then(response => {
                            if (response.status === 401) {
                                throw new Error('يجب تسجيل الدخول لتقييم الوصفة');
                            }
                            return response.json();
                        })
                        .then(data => {
                            showToast('تم إرسال التقييم بنجاح!', 'success');
                            userRatingText.textContent = `تقييمك: ${rating} نجوم`;
                            
                            // Update button text
                            submitBtn.innerHTML = '<i class="fas fa-check ml-2"></i>تم الإرسال';
                            submitBtn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                            submitBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                            
                            // Update rating button text and style
                            const ratingBtn = document.getElementById('rating-scroll-btn');
                            const ratingBtnText = document.getElementById('rating-btn-text');
                            if (ratingBtn && ratingBtnText) {
                                ratingBtnText.textContent = 'تم التقييم';
                                ratingBtn.classList.add('bg-green-50', 'border-green-300', 'text-green-700');
                                ratingBtn.classList.remove('text-gray-700');
                            }
                        })
                        .catch(error => {
                            console.error('Error submitting rating:', error);
                            showToast(error.message || 'حدث خطأ أثناء إرسال التقييم', 'error');
                            
                            // Reset button
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-paper-plane ml-2"></i>أرسل التقييم';
                        });
                    });
                }
            }

            // Image Gallery Functions
            let currentImageIndex = 0;
            const images = @json($recipe->getAllImages() ?? []);
            
            function showImage(index) {
                if (index >= 0 && index < images.length) {
                    currentImageIndex = index;
                    const mainImage = document.getElementById('main-recipe-image');
                    const imageCounter = document.getElementById('image-counter');
                    const thumbnails = document.querySelectorAll('.thumbnail');
                    
                    if (mainImage) {
                        mainImage.src = images[index];
                    }
                    
                    if (imageCounter) {
                        imageCounter.textContent = index + 1;
                    }
                    
                    // Update thumbnail state
                    thumbnails.forEach((thumb, i) => {
                        thumb.classList.remove('active');
                        if (i === index) {
                            thumb.classList.add('active');
                        }
                    });
                }
            }
            
            function nextImage() {
                const nextIndex = (currentImageIndex + 1) % images.length;
                showImage(nextIndex);
            }
            
            function previousImage() {
                const prevIndex = currentImageIndex === 0 ? images.length - 1 : currentImageIndex - 1;
                showImage(prevIndex);
            }
            
            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (images.length > 1) {
                    if (e.key === 'ArrowRight') {
                        nextImage();
                    } else if (e.key === 'ArrowLeft') {
                        previousImage();
                    }
                }
            });

            // Rating scroll functionality
            function initializeRatingScroll() {
                const ratingScrollBtn = document.getElementById('rating-scroll-btn');
                const ratingSection = document.getElementById('rating-section');
                
                if (ratingScrollBtn && ratingSection) {
                    ratingScrollBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        // Smooth scroll to rating section
                        ratingSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        
                        // Add a subtle highlight effect to the rating section
                        ratingSection.style.transition = 'box-shadow 0.3s ease';
                        ratingSection.style.boxShadow = '0 0 20px rgba(249, 115, 22, 0.3)';
                        
                        // Remove highlight after 2 seconds
                        setTimeout(() => {
                            ratingSection.style.boxShadow = '';
                        }, 2000);
                    });
                }
            }

            // Remove rating functionality
            function initializeRemoveRating() {
                const removeRatingBtn = document.getElementById('remove-rating-btn');
                const confirmModal = document.getElementById('confirm-modal');
                const confirmCancel = document.getElementById('confirm-cancel');
                const confirmDelete = document.getElementById('confirm-delete');
                const recipeId = document.querySelector('[data-recipe-id]')?.dataset.recipeId;
                
                if (removeRatingBtn && recipeId) {
                    removeRatingBtn.addEventListener('click', function() {
                        // Show custom confirmation modal
                        confirmModal.classList.remove('hidden');
                        confirmModal.classList.add('flex');
                    });
                }
                
                // Handle cancel button
                if (confirmCancel) {
                    confirmCancel.addEventListener('click', function() {
                        confirmModal.classList.add('hidden');
                        confirmModal.classList.remove('flex');
                    });
                }
                
                // Handle confirm delete button
                if (confirmDelete && recipeId) {
                    confirmDelete.addEventListener('click', function() {
                        // Hide modal first
                        confirmModal.classList.add('hidden');
                        confirmModal.classList.remove('flex');
                        
                        // Show loading state
                        const originalText = removeRatingBtn.innerHTML;
                        removeRatingBtn.disabled = true;
                        removeRatingBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري الإلغاء...';
                        
                        // Send request to remove rating
                        fetch('/api/interactions/remove', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                recipe_id: recipeId
                            })
                        })
                        .then(response => {
                            if (response.status === 401) {
                                throw new Error('يجب تسجيل الدخول لإلغاء التقييم');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                showToast('تم إلغاء التقييم بنجاح!', 'success');
                                
                                // Reset rating inputs
                                document.querySelectorAll('input[name="rating"]').forEach(input => {
                                    input.checked = false;
                                });
                                
                                // Update user rating text
                                const userRatingText = document.getElementById('user-rating-text');
                                if (userRatingText) {
                                    userRatingText.textContent = 'الرجاء تقييم الوصفة';
                                }
                                
                                // Hide remove rating button
                                removeRatingBtn.style.display = 'none';
                                
                                // Update rating button in header
                                const ratingBtn = document.getElementById('rating-scroll-btn');
                                const ratingBtnText = document.getElementById('rating-btn-text');
                                if (ratingBtn && ratingBtnText) {
                                    ratingBtnText.textContent = 'تقييم';
                                    ratingBtn.classList.remove('bg-green-50', 'border-green-300', 'text-green-700');
                                    ratingBtn.classList.add('text-gray-700');
                                }
                                
                                // Reset submit button
                                const submitBtn = document.getElementById('submit-rating-btn');
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = '<i class="fas fa-paper-plane ml-2"></i>أرسل التقييم';
                                    submitBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                                    submitBtn.classList.add('bg-orange-500', 'hover:bg-orange-600');
                                }
                            } else {
                                throw new Error(data.message || 'حدث خطأ أثناء إلغاء التقييم');
                            }
                        })
                        .catch(error => {
                            console.error('Error removing rating:', error);
                            showToast(error.message || 'حدث خطأ أثناء إلغاء التقييم', 'error');
                            
                            // Reset button
                            removeRatingBtn.disabled = false;
                            removeRatingBtn.innerHTML = originalText;
                        });
                    });
                }
            }
        </script>
    @endpush
@endsection
