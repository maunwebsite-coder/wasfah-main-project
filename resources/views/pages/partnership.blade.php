@extends('layouts.app')

@php
    $supportedLocales = [
        'ar' => [
            'label' => 'العربية',
            'dir' => 'rtl',
            'code' => 'ar',
        ],
        'en' => [
            'label' => 'English',
            'dir' => 'ltr',
            'code' => 'en',
        ],
    ];

    $locale = app()->getLocale();
    if (! array_key_exists($locale, $supportedLocales)) {
        $locale = 'ar';
    }

    $translate = function ($value) use ($locale) {
        if (is_array($value) && array_key_exists('ar', $value)) {
            return $value[$locale] ?? $value['ar'];
        }

        return $value;
    };

    $languageUrl = function ($targetLocale) {
        return request()->fullUrlWithQuery(['lang' => $targetLocale]);
    };

    $pageTitle = [
        'ar' => 'شريك وصفة - برنامج الشراكة مع الشيفات',
        'en' => 'Wasfa Partner – Chef Partnership Program',
    ];

    $hero = [
        'badge' => [
            'ar' => 'شريك وصفة',
            'en' => 'Wasfa Partner',
        ],
        'heading' => [
            'ar' => 'انضم إلى شبكة وصفة وابدأ بجني الأرباح من محتوى الطهي الحقيقي',
            'en' => 'Join the Wasfa network and start earning from authentic culinary content',
        ],
        'body' => [
            'ar' => 'وصفة تجمع بين الشيفات، العلامات التجارية، والمحتوى التفاعلي في مكان واحد. عبر برنامج شريك وصفة ستحصل على دخل مستمر، صفحات شخصية جذابة، ولوحة تحكم واضحة تُظهر أرباحك لحظة بلحظة.',
            'en' => 'Wasfa brings chefs, brands, and interactive content together in one place. The Wasfa Partner program gives you recurring income, beautiful profile pages, and a live dashboard that surfaces your earnings instantly.',
        ],
        'primary_cta' => [
            'ar' => 'انضم الآن',
            'en' => 'Join now',
        ],
        'secondary_cta' => [
            'ar' => 'تعرف على المزايا',
            'en' => 'Explore the benefits',
        ],
        'stats' => [
            [
                'value' => '15%',
                'label' => [
                    'ar' => 'حد أعلى للعمولة حسب الحملات',
                    'en' => 'Commission ceiling per campaign',
                ],
            ],
            [
                'value' => '24/7',
                'label' => [
                    'ar' => 'لوحة متابعة فورية للأرباح',
                    'en' => 'Live earnings dashboard',
                ],
            ],
            [
                'value' => '+80',
                'label' => [
                    'ar' => 'شيف يعتمدون Wasfa Links',
                    'en' => 'Chefs rely on Wasfa Links',
                ],
            ],
        ],
    ];

    $partnerInboxCopy = [
        'url' => 'https://wasfah.ae/admin/admin-area',
        'latest' => [
            'ar' => 'أحدث الطلبات',
            'en' => 'Latest requests',
        ],
        'title' => [
            'ar' => 'صندوق رسائل الشركاء',
            'en' => 'Partner inbox',
        ],
        'body' => [
            'ar' => 'اطلع بسرعة على آخر رسائل الشراكات والدعم وحدد ما يحتاج متابعة.',
            'en' => 'Keep a fast view of partnership and support messages so you can prioritize the right follow-ups.',
        ],
        'pending' => [
            'ar' => 'بانتظار المراجعة',
            'en' => 'Pending review',
        ],
        'requests' => [
            'ar' => 'طلبات الشراكة',
            'en' => 'Partnership requests',
        ],
        'footer' => [
            'ar' => 'جميع الرسائل محفوظة ويمكن مراجعتها في أي وقت.',
            'en' => 'All messages stay archived and ready to revisit whenever needed.',
        ],
        'footer_cta' => [
            'ar' => 'إدارة جميع الطلبات',
            'en' => 'Review every request',
        ],
    ];

    $partnerInbox = [
        [
            'name' => 'abdullah daoud',
            'type' => [
                'ar' => 'طلب شراكة أو تعاون',
                'en' => 'Partnership or collaboration request',
            ],
            'time' => [
                'ar' => 'قبل 35 دقيقة',
                'en' => '35 minutes ago',
            ],
            'tag' => [
                'ar' => 'شراكة',
                'en' => 'Partnership',
            ],
            'status' => [
                'ar' => 'تم إشعار الفريق',
                'en' => 'Team notified',
            ],
        ],
        [
            'name' => 'abdullah daoud',
            'type' => [
                'ar' => 'طلب شراكة أو تعاون',
                'en' => 'Partnership or collaboration request',
            ],
            'time' => [
                'ar' => 'قبل 54 دقيقة',
                'en' => '54 minutes ago',
            ],
            'tag' => [
                'ar' => 'شراكة',
                'en' => 'Partnership',
            ],
            'status' => [
                'ar' => 'تم إشعار الفريق',
                'en' => 'Team notified',
            ],
        ],
        [
            'name' => 'abdullah daoud',
            'type' => [
                'ar' => 'طلب شراكة أو تعاون',
                'en' => 'Partnership or collaboration request',
            ],
            'time' => [
                'ar' => 'قبل 55 دقيقة',
                'en' => '55 minutes ago',
            ],
            'tag' => [
                'ar' => 'شراكة',
                'en' => 'Partnership',
            ],
            'status' => [
                'ar' => 'تم إشعار الفريق',
                'en' => 'Team notified',
            ],
        ],
        [
            'name' => 'abdullah daoud',
            'type' => [
                'ar' => 'مشكلة في وصفة',
                'en' => 'Recipe issue',
            ],
            'time' => [
                'ar' => 'قبل ساعة',
                'en' => '1 hour ago',
            ],
            'tag' => null,
            'status' => [
                'ar' => 'تم إشعار الفريق',
                'en' => 'Team notified',
            ],
        ],
        [
            'name' => 'abdullah daoud',
            'type' => [
                'ar' => 'استفسار عام',
                'en' => 'General inquiry',
            ],
            'time' => [
                'ar' => 'قبل يوم',
                'en' => '1 day ago',
            ],
            'tag' => null,
            'status' => [
                'ar' => 'تم إشعار الفريق',
                'en' => 'Team notified',
            ],
        ],
    ];

    $overview = [
        'eyebrow' => [
            'ar' => 'لماذا وصفة؟',
            'en' => 'Why Wasfa?',
        ],
        'title' => [
            'ar' => 'شبكة تفاعلية تربط بين الشيفات، الجمهور، والعلامات التجارية في نظام واحد ذكي',
            'en' => 'An interactive network that connects chefs, audiences, and brands in one smart system',
        ],
        'body' => [
            'ar' => 'برنامج شريك وصفة يمنحك أدوات احترافية لتتبع كل نقرة، كل حجز، وكل ورشة يتم حجزها عبر روابطك. احصل على صفحات Wasfa Links المخصصة للشيفات، حملات متكاملة، ولوحة تحكم شفافة تعرض أرباحك وحالة طلباتك في الوقت الحقيقي.',
            'en' => 'The Wasfa Partner program gives you professional tools to trace every click, booking, and workshop reserved through your campaigns. Provide chefs with Wasfa Links pages, integrated campaigns, and a transparent dashboard that shows earnings and request statuses in real time.',
        ],
        'highlights' => [
            [
                'icon_wrapper' => 'bg-orange-100',
                'icon' => 'fas fa-chart-line text-orange-500 text-xl',
                'title' => [
                    'ar' => 'نظام تتبع ذكي للروابط',
                    'en' => 'Smart tracking for partner links',
                ],
                'body' => [
                    'ar' => 'اعرف من أين أتت كل عملية بيع، وقارن أداء الحملات عبر لوحة تفاصيل دقيقة.',
                    'en' => 'Know exactly where each sale came from and compare campaign performance with precision metrics.',
                ],
            ],
            [
                'icon_wrapper' => 'bg-emerald-100',
                'icon' => 'fas fa-id-badge text-emerald-500 text-xl',
                'title' => [
                    'ar' => 'صفحات مخصصة لكل شيف',
                    'en' => 'Tailored pages for every chef',
                ],
                'body' => [
                    'ar' => 'صمّم تجربة شبيهة بالرابط في السيرة لكن بلمسة وصفة التي تعرض الوصفات، الورش، وروابط التواصل.',
                    'en' => 'Build a Link-in-Bio style experience with Wasfa’s flavor that showcases recipes, workshops, and contact links.',
                ],
            ],
            [
                'icon_wrapper' => 'bg-sky-100',
                'icon' => 'fas fa-gauge-high text-sky-500 text-xl',
                'title' => [
                    'ar' => 'لوحة تحكم شفافة',
                    'en' => 'Transparent dashboard',
                ],
                'body' => [
                    'ar' => 'راقب الأرباح، الحجوزات القادمة، وتوقعات الدخل الشهري في واجهة عربية سهلة القراءة.',
                    'en' => 'Monitor earnings, upcoming bookings, and projected revenue inside an interface that stays clear and data-rich.',
                ],
            ],
        ],
        'updates' => [
            'eyebrow' => [
                'ar' => 'تحديثات الشركاء',
                'en' => 'Partner updates',
            ],
            'headline' => [
                'ar' => 'نجهز حالياً لإطلاق بيانات الأداء بعد تشغيل برنامج الشركاء رسمياً، لتكون كل الأرقام موثقة ودقيقة.',
                'en' => 'We are preparing to publish performance data as soon as the partner program switches on so every number remains verified.',
            ],
            'body' => [
                'ar' => 'ستظهر قصص النجاح ومؤشرات الأداء هنا فور بدء التجارب الأولى، مع تحديثات مستمرة لضمان الشفافية مع جميع الشركاء.',
                'en' => 'Success stories and performance indicators will show up here once the first pilots go live, with continuous updates to keep every partner informed.',
            ],
            'list_title' => [
                'ar' => 'ماذا سيصل إليك قريباً؟',
                'en' => 'What’s landing in your inbox soon?',
            ],
            'bullets' => [
                [
                    'ar' => 'تنبيهات فورية عند تفعيل النظام وإطلاق الدعوات.',
                    'en' => 'Instant alerts when the system activates and invitations roll out.',
                ],
                [
                    'ar' => 'لوحة تحكم مباشرة تعرض كل عملية بيع وعمولتك المستحقة.',
                    'en' => 'A live dashboard that shows every sale and the commission you earned.',
                ],
                [
                    'ar' => 'تقارير قابلة للمشاركة مع فريقك أو شركائك التسويقيين.',
                    'en' => 'Shareable reports for your team or marketing partners.',
                ],
            ],
        ],
    ];

    $benefitCards = [
        [
            'icon' => '💰',
            'tone_class' => 'text-orange-500',
            'title' => [
                'ar' => '1. رابط الشريك والعمولات',
                'en' => '1. Partner link & commissions',
            ],
            'body' => [
                'ar' => 'كل شريك يحصل على رابط فريد داخل موقع وصفة يمكن مشاركته مع الشيفات أو عبر قنوات التسويق الخاصة به، وأي شيف ينشئ حساباً عبر هذا الرابط ويطلق ورشاته من خلال الموقع يُحتسب كعميل تابع لك، وأي حجز يتم على تلك الورشات تضيف العمولة مباشرة إلى حسابك دون أي تدخل يدوي.',
                'en' => 'Every partner receives a unique Wasfa link to share with chefs or across marketing channels. Any chef who signs up and launches workshops through that link is tied to your account, and every booking automatically adds a commission without manual work.',
            ],
            'list_title' => [
                'ar' => 'مميزات نظام الشركاء:',
                'en' => 'Highlights of the partner system:',
            ],
            'bullets' => [
                [
                    'ar' => 'عمولة تبدأ من 5% وتصل إلى 15% حسب نوع الورشة أو الحملة.',
                    'en' => 'Commission starts at 5% and can reach 15% depending on the workshop or campaign.',
                ],
                [
                    'ar' => 'لوحة متابعة فورية تُظهر الأرباح وعدد المشاركات القادمة.',
                    'en' => 'A live insight board showing earnings and upcoming participants.',
                ],
                [
                    'ar' => 'إمكانية ربط الحملات الإعلانية بالرابط الخاص لتتبّع الأداء في الوقت الحقيقي.',
                    'en' => 'Connect ad campaigns to your link to follow performance in real time.',
                ],
            ],
            'border' => 'border-orange-100',
        ],
        [
            'icon' => '🔗',
            'tone_class' => 'text-sky-500',
            'title' => [
                'ar' => '2. صفحة Wasfa Links للشيف',
                'en' => '2. Wasfa Links page for each chef',
            ],
            'body' => [
                'ar' => 'كل شيف يمتلك صفحته الخاصة عبر نظام Wasfa Links؛ صفحة ديناميكية شبيهة بـ Link in Bio تعرض وصفاته، الورش القادمة، وروابط التواصل الخاصة به.',
                'en' => 'Every chef gets a dedicated Wasfa Links page—a dynamic Link-in-Bio style hub that showcases recipes, upcoming workshops, and contact links.',
            ],
            'list_title' => [
                'ar' => 'خصائص صفحة Wasfa Links:',
                'en' => 'What the Wasfa Links page includes:',
            ],
            'bullets' => [
                [
                    'ar' => 'تصميم قابل للتخصيص بالكامل (روابط، صور، ترتيب، أزرار).',
                    'en' => 'Fully customizable design (links, images, order, buttons).',
                ],
                [
                    'ar' => 'إبراز الورشة التالية بزر واضح «احجز مكانك الآن».',
                    'en' => 'Highlights the next workshop with a clear “Reserve your spot” button.',
                ],
                [
                    'ar' => 'تتبّع عدد الزيارات والنقرات لكل رابط.',
                    'en' => 'Track visits and clicks across every link.',
                ],
                [
                    'ar' => 'إمكانية إنشاء أكثر من صفحة للشيف الواحد أو لفروع مختلفة.',
                    'en' => 'Spin up multiple pages for a single chef or different branches.',
                ],
            ],
            'border' => 'border-slate-100',
        ],
        [
            'icon' => '👨‍🍳',
            'tone_class' => 'text-emerald-500',
            'title' => [
                'ar' => '3. ماذا يفعل الشيف داخل وصفة؟',
                'en' => '3. What chefs can do inside Wasfa',
            ],
            'body' => [
                'ar' => 'نقدّم للشيفات لوحة احترافية لإدارة كل ما يخص محتواهم بسهولة واحترافية، لتصبح وصفة منصتهم الأساسية لتضخيم الوجود الرقمي وزيادة المبيعات.',
                'en' => 'Chefs receive a professional console to manage every piece of their content, making Wasfa their primary platform for digital reach and sales.',
            ],
            'list_title' => null,
            'bullets' => [
                [
                    'ar' => 'نشر وصفاتهم مع الصور والفيديوهات.',
                    'en' => 'Publish recipes with photos and video.',
                ],
                [
                    'ar' => 'مشاهدة والتفاعل مع وصفات الشيفات الآخرين.',
                    'en' => 'Engage with recipes from other chefs.',
                ],
                [
                    'ar' => 'حفظ الوصفات المفضلة في مكتبة خاصة.',
                    'en' => 'Save favorite recipes to a personal library.',
                ],
                [
                    'ar' => 'مشاركة الروابط بسهولة عبر إنستغرام وواتساب.',
                    'en' => 'Share links effortlessly across Instagram and WhatsApp.',
                ],
                [
                    'ar' => 'نشر ورشاتهم الخاصة ومتابعة المشاركين والحجوزات مباشرة.',
                    'en' => 'Launch workshops and follow participants plus bookings live.',
                ],
            ],
            'border' => 'border-emerald-100',
        ],
    ];

    $steps = [
        'eyebrow' => [
            'ar' => '🚀 جاهز لتصبح شريك وصفة؟',
            'en' => '🚀 Ready to become a Wasfa Partner?',
        ],
        'title' => [
            'ar' => 'ابدأ اليوم بخطوات بسيطة وواضحة',
            'en' => 'Get started today with simple, clear steps',
        ],
        'body' => [
            'ar' => 'فريق الشراكات سيرافقك خطوة بخطوة. بمجرد إكمال النموذج سيصلك كل ما تحتاجه خلال ثلاثة أيام عمل كحد أقصى.',
            'en' => 'The partnerships team walks with you step by step. Once the form is complete you receive everything you need within three business days.',
        ],
        'items' => [
            [
                'label' => '1',
                'title' => [
                    'ar' => 'عبّئ نموذج الانضمام',
                    'en' => 'Submit the onboarding form',
                ],
                'body' => [
                    'ar' => 'أرسل بياناتك عبر صفحة التواصل وحدد نوع التعاون الذي تبحث عنه.',
                    'en' => 'Share your details through the contact page and specify the partnership format you want.',
                ],
            ],
            [
                'label' => '2',
                'title' => [
                    'ar' => 'استلم رابطك ولوحتك',
                    'en' => 'Receive your link and dashboard',
                ],
                'body' => [
                    'ar' => 'سيصلك رابطك الفريد، بيانات الدخول، ودليل الاستخدام خلال 3 أيام عمل.',
                    'en' => 'We send your unique link, access credentials, and user guide within three business days.',
                ],
            ],
            [
                'label' => '3',
                'title' => [
                    'ar' => 'ابدأ بمشاركة الروابط',
                    'en' => 'Start sharing your links',
                ],
                'body' => [
                    'ar' => 'شارك روابطك مع الشيفات والجمهور، وتتبع أرباحك مباشرة من لوحة التحكم.',
                    'en' => 'Share your links with chefs and audiences, then track earnings directly from the dashboard.',
                ],
            ],
        ],
        'cta' => [
            'ar' => 'انضم الآن إلى شبكة وصفة',
            'en' => 'Join the Wasfa network now',
        ],
    ];

    $contactSection = [
        'eyebrow' => [
            'ar' => 'جاهز للانضمام؟',
            'en' => 'Ready to join?',
        ],
        'title' => [
            'ar' => 'أخبرنا كيف يمكننا مساعدتك',
            'en' => 'Tell us how we can support you',
        ],
        'body' => [
            'ar' => 'املأ التفاصيل التالية لتصل رسالتك إلى الفريق المختص مباشرة. عادةً ما نرد خلال يوم عمل واحد ونزوّدك بخطوات تفعيل الحساب ولوحة الشريك.',
            'en' => 'Complete the form below so your message reaches the partnerships team right away. We usually respond within one business day with the activation steps and partner dashboard access.',
        ],
        'bullets' => [
            [
                'ar' => 'اختَر نوع التعاون أو الشراكة التي تناسبك وأخبرنا عن الجمهور الذي تستهدفه.',
                'en' => 'Choose the collaboration model that fits and tell us about the audience you target.',
            ],
            [
                'ar' => 'بعد استلام الطلب ستظهر بياناتك في لوحة الإدمن لمتابعة الحالة وخطوات الربط التالية.',
                'en' => 'After we receive the request, your data appears inside the admin console so we can track status and next steps.',
            ],
            [
                'ar' => 'يصلُك إشعار عبر البريد عند مراجعة الطلب أو طلب أي مستندات إضافية من فريق الشراكات.',
                'en' => 'You get an email notification when we review the request or need extra documentation.',
            ],
        ],
        'note' => [
            'ar' => '💡 نراجع الطلبات مرتين يومياً، وتظهر حالة كل طلب مباشرة في منطقة الإدمن.',
            'en' => '💡 Requests are reviewed twice per day and their status appears instantly inside the admin area.',
        ],
        'form' => [
            'eyebrow' => [
                'ar' => 'نموذج طلب الشراكة',
                'en' => 'Partnership request form',
            ],
            'title' => [
                'ar' => 'ارسل بياناتك ليصلك رابط الإدمن ولوحة المتابعة',
                'en' => 'Send your details to receive the admin link and tracking board',
            ],
            'body' => [
                'ar' => 'سنقوم بإشعارك فور تسجيل الطلب داخل لوحة التحكم الخاصة بفريق الشراكات.',
                'en' => 'We will notify you as soon as your request is logged inside the partnerships console.',
            ],
        ],
    ];

    $ctaSection = [
        'title' => [
            'ar' => 'انضم الآن وكن جزءاً من شبكة وصفة',
            'en' => 'Join now and become part of the Wasfa network',
        ],
        'body' => [
            'ar' => 'وصفة تجمع الشيفات والمحتوى التفاعلي في عالم واحد. ابدأ اليوم، ضاعف حضورك، وتابع أرباحك بكل شفافية.',
            'en' => 'Wasfa unites chefs and interactive content in a single hub. Start today, grow your presence, and monitor income transparently.',
        ],
        'primary' => [
            'ar' => 'قدّم طلب الشراكة',
            'en' => 'Submit a partnership request',
        ],
        'secondary' => [
            'ar' => 'اطّلع على مزايا البرنامج',
            'en' => 'Review the program benefits',
        ],
    ];
@endphp

@section('title', $translate($pageTitle))

@section('content')
<div class="bg-slate-50" lang="{{ $locale }}" dir="{{ $supportedLocales[$locale]['dir'] }}">
    <!-- Hero -->
    <section class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-orange-600 to-rose-600 text-white">
        <div class="absolute inset-0 opacity-20 mix-blend-soft-light">
            <div class="absolute -top-32 -right-20 w-96 h-96 bg-white/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -left-20 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-4 py-20 relative z-10">
            <div class="flex justify-end mb-8">
                <div class="inline-flex items-center gap-1 rounded-full bg-white/10 px-1 py-1 text-xs md:text-sm backdrop-blur">
                    @foreach($supportedLocales as $code => $meta)
                        <a href="{{ $languageUrl($code) }}"
                           class="px-3 py-1 rounded-full transition {{ $locale === $code ? 'bg-white text-orange-600 font-semibold shadow text-xs md:text-sm' : 'text-white/80 hover:text-white' }}">
                            {{ $meta['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="max-w-3xl mx-auto text-center space-y-6">
                <p class="inline-flex items-center gap-2 px-5 py-2 border border-white/40 rounded-full text-sm tracking-wider uppercase">
                    <span class="text-xl">🤝</span>
                    {{ $translate($hero['badge']) }}
                </p>
                <h1 class="text-4xl md:text-5xl font-black leading-snug">
                    {{ $translate($hero['heading']) }}
                </h1>
                <p class="text-lg md:text-xl text-orange-50/90 leading-relaxed">
                    {{ $translate($hero['body']) }}
                </p>

                <div class="flex flex-wrap justify-center gap-4 pt-4">
                    <a href="{{ route('contact', ['lang' => $locale]) }}" class="px-8 py-3 bg-white text-orange-600 font-semibold rounded-full hover:bg-orange-50 transition-shadow shadow-lg shadow-orange-900/20">
                        {{ $translate($hero['primary_cta']) }}
                    </a>
                    <a href="#partner-benefits" class="px-8 py-3 border border-white/40 rounded-full hover:bg-white/10 transition">
                        {{ $translate($hero['secondary_cta']) }}
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-10 text-sm">
                    @foreach($hero['stats'] as $stat)
                        <div class="bg-white/10 rounded-2xl p-4 backdrop-blur">
                            <p class="text-3xl font-bold mb-1">{{ $stat['value'] }}</p>
                            <p class="text-orange-100">{{ $translate($stat['label']) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Partner Inbox Snapshot -->
    <section class="container mx-auto px-4 -mt-8 md:-mt-12 pb-12">
        <div class="max-w-4xl mx-auto">
            <div class="bg-slate-900 text-white rounded-3xl border border-white/10 shadow-2xl shadow-slate-900/30 overflow-hidden">
                <div class="p-6 md:p-8 space-y-6">
                    <div class="flex flex-wrap items-start justify-between gap-6">
                        <div class="space-y-2">
                            <p class="text-[11px] uppercase tracking-[0.3em] text-orange-200/80">{{ $partnerInboxCopy['url'] }}</p>
                            <p class="text-sm text-slate-200/80">{{ $translate($partnerInboxCopy['latest']) }}</p>
                            <h2 class="text-2xl font-bold">{{ $translate($partnerInboxCopy['title']) }}</h2>
                            <p class="text-sm text-slate-300">
                                {{ $translate($partnerInboxCopy['body']) }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3 text-right text-sm">
                            <div class="bg-white/5 rounded-2xl px-4 py-3 min-w-[130px]">
                                <p class="text-xs text-orange-200/80">{{ $translate($partnerInboxCopy['pending']) }}</p>
                                <p class="text-3xl font-black leading-tight">5</p>
                            </div>
                            <div class="bg-white/5 rounded-2xl px-4 py-3 min-w-[130px]">
                                <p class="text-xs text-orange-200/80">{{ $translate($partnerInboxCopy['requests']) }}</p>
                                <p class="text-3xl font-black leading-tight">3</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/5 rounded-2xl divide-y divide-white/10">
                        @foreach($partnerInbox as $message)
                            <div class="flex flex-wrap items-center justify-between gap-4 p-4">
                                <div class="space-y-1">
                                    <p class="font-semibold text-white">{{ $message['name'] }}</p>
                                    <p class="text-xs md:text-sm text-slate-200 flex flex-wrap items-center gap-2">
                                        <span>{{ $translate($message['type']) }}</span>
                                        <span class="text-white/40">•</span>
                                        <span>{{ $translate($message['time']) }}</span>
                                    </p>
                                    <p class="text-xs text-slate-400">{{ $translate($message['status']) }}</p>
                                </div>
                                @if(!empty($message['tag']))
                                    <span class="px-3 py-1 rounded-full text-xs bg-orange-500/10 text-orange-200 border border-orange-400/30">
                                        {{ $translate($message['tag']) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-4 text-xs md:text-sm text-slate-300">
                        <p>{{ $translate($partnerInboxCopy['footer']) }}</p>
                        <a href="{{ $partnerInboxCopy['url'] }}" target="_blank" rel="noreferrer" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 hover:bg-white/20 transition">
                            {{ $translate($partnerInboxCopy['footer_cta']) }}
                            <i class="fas fa-external-link-alt text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partner Overview -->
    <section class="container mx-auto px-4 py-16">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <div class="p-10 lg:p-12">
                    <p class="text-sm font-semibold text-orange-500 mb-3">{{ $translate($overview['eyebrow']) }}</p>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">
                        {{ $translate($overview['title']) }}
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-8">
                        {{ $translate($overview['body']) }}
                    </p>
                    <div class="space-y-4">
                        @foreach($overview['highlights'] as $highlight)
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-2xl {{ $highlight['icon_wrapper'] }} flex items-center justify-center">
                                    <i class="{{ $highlight['icon'] }}"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-1">{{ $translate($highlight['title']) }}</h3>
                                    <p class="text-gray-600 text-sm leading-relaxed">{{ $translate($highlight['body']) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-10 lg:p-12 flex flex-col justify-center">
                    <div class="space-y-6">
                        <p class="text-sm text-orange-300 mb-2">{{ $translate($overview['updates']['eyebrow']) }}</p>
                        <p class="text-3xl font-bold leading-snug">
                            {{ $translate($overview['updates']['headline']) }}
                        </p>
                        <p class="text-slate-300 text-sm leading-relaxed">
                            {{ $translate($overview['updates']['body']) }}
                        </p>
                        <div class="bg-white/5 rounded-2xl p-5 text-sm text-slate-200 leading-relaxed">
                            <p class="font-semibold text-orange-200 mb-2">{{ $translate($overview['updates']['list_title']) }}</p>
                            <ul class="list-disc list-inside space-y-1 text-slate-100/90">
                                @foreach($overview['updates']['bullets'] as $bullet)
                                    <li>{{ $translate($bullet) }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partner Benefits -->
    <section id="partner-benefits" class="container mx-auto px-4 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @foreach($benefitCards as $card)
                <div class="bg-white rounded-2xl shadow-lg p-8 border {{ $card['border'] }}">
                    <div class="flex items-center gap-3 {{ $card['tone_class'] }} font-semibold text-sm mb-6">
                        <span class="text-2xl">{{ $card['icon'] }}</span>
                        {{ $translate($card['title']) }}
                    </div>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        {{ $translate($card['body']) }}
                    </p>
                    @if(!empty($card['list_title']))
                        <h4 class="font-bold text-gray-900 mb-4">{{ $translate($card['list_title']) }}</h4>
                    @endif
                    <ul class="space-y-3 text-gray-600 text-sm">
                        @foreach($card['bullets'] as $bullet)
                            <li class="flex items-start gap-2"><i class="fas fa-check text-green-500 mt-1"></i> {{ $translate($bullet) }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>
    <!-- Steps -->
    <section class="container mx-auto px-4 pb-16">
        <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-3xl text-white p-10 md:p-14">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-10">
                <div class="max-w-2xl space-y-4">
                    <p class="text-sm font-semibold text-orange-300">{{ $translate($steps['eyebrow']) }}</p>
                    <h2 class="text-3xl font-bold leading-relaxed">{{ $translate($steps['title']) }}</h2>
                    <p class="text-slate-200 leading-relaxed">
                        {{ $translate($steps['body']) }}
                    </p>
                </div>
                <div class="bg-white/10 rounded-2xl p-6 backdrop-blur w-full lg:w-auto">
                    <div class="space-y-6">
                        @foreach($steps['items'] as $item)
                            <div class="flex gap-4">
                                <span class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold">{{ $item['label'] }}</span>
                                <div>
                                    <h3 class="font-semibold text-lg">{{ $translate($item['title']) }}</h3>
                                    <p class="text-sm text-slate-200">{{ $translate($item['body']) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mt-10 flex flex-wrap gap-4">
                <a href="{{ route('contact', ['lang' => $locale]) }}" class="px-8 py-3 bg-white text-slate-900 rounded-full font-semibold shadow-lg hover:-translate-y-0.5 transition">
                    {{ $translate($steps['cta']) }}
                </a>
            </div>
        </div>
    </section>

    <!-- Partner Contact -->
    <section id="partner-contact" class="container mx-auto px-4 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="bg-gradient-to-br from-orange-50 to-white border border-orange-100 rounded-3xl p-10 shadow-lg">
                <p class="text-sm font-semibold text-orange-600 mb-3">{{ $translate($contactSection['eyebrow']) }}</p>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $translate($contactSection['title']) }}</h2>
                <p class="text-lg text-gray-600 leading-relaxed mb-6">
                    {{ $translate($contactSection['body']) }}
                </p>
                <ul class="space-y-4 text-gray-700">
                    @foreach($contactSection['bullets'] as $bullet)
                        <li class="flex items-start gap-3">
                            <span class="text-orange-500 mt-0.5">•</span>
                            {{ $translate($bullet) }}
                        </li>
                    @endforeach
                </ul>
                <div class="mt-8 p-4 bg-white border border-dashed border-orange-200 rounded-2xl text-sm text-gray-600">
                    <p>{{ $translate($contactSection['note']) }}</p>
                </div>
            </div>
            <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 p-8">
                <div class="mb-6">
                    <p class="text-sm font-semibold text-orange-500 mb-1">{{ $translate($contactSection['form']['eyebrow']) }}</p>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $translate($contactSection['form']['title']) }}</h3>
                    <p class="text-gray-500">{{ $translate($contactSection['form']['body']) }}</p>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-check-circle ml-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-exclamation-triangle ml-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.send') }}" class="space-y-6">
                    @csrf
                    @include('pages.partials.contact-form-fields', [
                        'defaultSubject' => 'partnership',
                        'source' => 'partnership-page',
                        'locale' => $locale,
                    ])
                </form>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="container mx-auto px-4 pb-20">
        <div class="bg-white border border-dashed border-orange-200 rounded-3xl p-10 md:p-14 text-center shadow-lg shadow-orange-100/40">
            <div class="max-w-3xl mx-auto space-y-6">
                <h2 class="text-3xl font-bold text-gray-900">{{ $translate($ctaSection['title']) }}</h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    {{ $translate($ctaSection['body']) }}
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('contact', ['lang' => $locale]) }}" class="px-10 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-full shadow-lg hover:shadow-xl transition">
                        {{ $translate($ctaSection['primary']) }}
                    </a>
                    <a href="#partner-benefits" class="px-10 py-3 border border-orange-200 text-orange-600 font-semibold rounded-full hover:bg-orange-50 transition">
                        {{ $translate($ctaSection['secondary']) }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
