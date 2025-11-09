<footer class="bg-orange-50 pt-12 pb-6">
    <div class="container mx-auto px-4">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 gap-10 pb-10 border-b border-gray-200 footer-content sm:grid-cols-2 lg:grid-cols-12">
            <!-- Brand -->
            <div class="space-y-4 text-center sm:text-right lg:col-span-3">
                    <img src="{{ asset('image/logo.png') }}" alt="شعار وصفة" class="h-12 w-auto">
                               <p class="text-gray-600 leading-relaxed">
                    منصّة وصفة للحلويات الفاخرة والراقية، نرافقك في كل خطوة لتقديم أطيب الحلويات.
                </p>
            </div>

            <!-- Explore -->
            <div class="space-y-4 text-center sm:text-right lg:col-span-3">
                <h3 class="text-sm font-semibold text-orange-500 tracking-wider uppercase">اكتشف</h3>
                <ul class="space-y-2 text-gray-600">
                    <li>
                        <a href="{{ route('recipes') }}" class="hover:text-orange-500 transition-colors">جميع الوصفات</a>
                    </li>
                    <li>
                        <a href="{{ route('workshops') }}" class="hover:text-orange-500 transition-colors">ورشات العمل</a>
                    </li>
                    <li>
                        <a href="{{ route('baking-tips') }}" class="hover:text-orange-500 transition-colors">نصائح الحلويات</a>
                    </li>
                </ul>
            </div>

            <!-- Quick Guide -->
            <div class="space-y-4 text-center sm:text-right lg:col-span-3">
                <h3 class="text-sm font-semibold text-orange-500 tracking-wider uppercase">الدليل السريع</h3>
                <ul class="space-y-2 text-gray-600">
                    <li>
                        <a href="{{ route('tools') }}" class="hover:text-orange-500 transition-colors">أدوات الشيف</a>
                    </li>
                    <li>
                        <a href="{{ route('search') }}" class="hover:text-orange-500 transition-colors">البحث عن وصفة</a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="hover:text-orange-500 transition-colors">من نحن</a>
                    </li>
                    <li>
                        <a href="{{ route('partnership') }}" class="hover:text-orange-500 transition-colors">شراكات الشركات</a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="space-y-4 text-center sm:text-right lg:col-span-3">
                <h3 class="text-sm font-semibold text-orange-500 tracking-wider uppercase">تواصل معنا</h3>
                <div class="space-y-3 text-gray-600">
                    <div class="flex items-center justify-center space-x-2 rtl:space-x-reverse sm:justify-end">
                        <i class="fas fa-envelope text-orange-500"></i>
                        <span>فريق الدعم يرد خلال يوم عمل عند إرسال الطلب عبر نموذج التواصل.</span>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('contact') }}" class="inline-block hover:text-orange-500 transition-colors">اتصل بنا</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Partner CTA -->
        <div class="mt-10 rounded-3xl border border-orange-100 bg-white p-8 shadow-lg space-y-6">
            <div>
                <p class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                    <span>🤝</span>
                    <span>شريك وصفة</span>
                </p>
                <p class="mt-2 text-slate-600 leading-relaxed">
                    انضم إلى شبكة وصفة وابدأ بجني الأرباح من محتوى الطهي الحقيقي. وصفة تجمع بين الشيفات، العلامات التجارية، والمحتوى التفاعلي في مكان واحد. من خلال برنامج شريك وصفة يمكنك تحقيق دخل مستمر عبر نظام تتبع ذكي للروابط، وصفحات مخصصة للشيفات، ولوحة تحكم تعرض أرباحك بكل شفافية.
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <div class="rounded-2xl border border-orange-50 bg-orange-50/60 p-5 shadow-sm">
                    <p class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                        <span>💰</span>
                        <span>1. رابط الشريك والعمولات</span>
                    </p>
                    <p class="mt-3 text-slate-600 text-sm leading-relaxed">
                        كل شريك يحصل على رابط فريد داخل موقع وصفة يمكن مشاركته مع الشيفات أو عبر قنوات التسويق الخاصة به. كل مرة يُحجز فيها مقعد أو ورشة عبر هذا الرابط، تُضاف العمولة مباشرة إلى حسابك.
                    </p>
                    <div class="mt-4 space-y-2 text-sm text-slate-700">
                        <p>• عمولة تبدأ من 5% وتصل إلى 15% حسب نوع الورشة أو الحملة.</p>
                        <p>• لوحة متابعة فورية تُظهر الأرباح وعدد المشاركات القادمة.</p>
                        <p>• إمكانية ربط الحملات الإعلانية بالرابط الخاص لتتبّع الأداء في الوقت الحقيقي.</p>
                    </div>
                </div>
                <div class="rounded-2xl border border-orange-50 bg-orange-50/60 p-5 shadow-sm">
                    <p class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                        <span>🔗</span>
                        <span>2. صفحة Wasfa Links للشيف</span>
                    </p>
                    <p class="mt-3 text-slate-600 text-sm leading-relaxed">
                        كل شيف يمتلك صفحته الخاصة عبر نظام Wasfa Links — صفحة ديناميكية شبيهة بـ link in bio تعرض وصفاته، الورش القادمة، وروابط التواصل الخاصة به.
                    </p>
                    <div class="mt-4 space-y-2 text-sm text-slate-700">
                        <p>• تصميم قابل للتخصيص بالكامل (روابط، صور، ترتيب، أزرار).</p>
                        <p>• إبراز الورشة التالية بزر واضح “احجز مكانك الآن”.</p>
                        <p>• تتبّع عدد الزيارات والنقرات لكل رابط.</p>
                        <p>• إمكانية إنشاء أكثر من صفحة للشيف الواحد أو لفروع مختلفة.</p>
                    </div>
                </div>
                <div class="rounded-2xl border border-orange-50 bg-orange-50/60 p-5 shadow-sm">
                    <p class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                        <span>👨‍🍳</span>
                        <span>3. ماذا يفعل الشيف داخل وصفة؟</span>
                    </p>
                    <p class="mt-3 text-slate-600 text-sm leading-relaxed">
                        نقدّم للشيفات لوحة احترافية لإدارة كل ما يخص محتواهم بسهولة واحترافية.
                    </p>
                    <div class="mt-4 space-y-2 text-sm text-slate-700">
                        <p>• نشر وصفاته مع الصور والفيديوهات.</p>
                        <p>• مشاهدة والتفاعل مع وصفات الشيفات الآخرين.</p>
                        <p>• حفظ الوصفات المفضلة في مكتبته الخاصة.</p>
                        <p>• مشاركة روابطه بسهولة عبر إنستغرام وواتساب.</p>
                        <p>• نشر ورشاته الخاصة ومتابعة المشاركين والحجوزات بشكل مباشر.</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-dashed border-orange-200 bg-white/70 p-6 text-slate-800">
                <p class="text-base leading-relaxed">
                    🚀 جاهز لتصبح شريك وصفة؟ ابدأ اليوم بخطوات بسيطة: عبّئ نموذج الانضمام القصير عبر صفحة التواصل، استلم رابطك ولوحة الشريك خلال 3 أيام عمل، ثم ابدأ مشاركة الروابط وتتبع أرباحك مباشرة. انضم الآن وكن جزءاً من شبكة وصفة التي تجمع الشيفات والمحتوى التفاعلي في عالم واحد.
                </p>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('contact') }}" class="inline-flex items-center justify-center rounded-full bg-orange-500 px-6 py-3 text-white font-semibold shadow hover:bg-orange-600 transition">
                        قدّم طلب الانضمام
                        <i class="fas fa-arrow-left ml-2"></i>
                    </a>
                    <a href="{{ route('partnership') }}" class="inline-flex items-center justify-center rounded-full border border-orange-200 px-6 py-3 text-orange-600 font-semibold hover:border-orange-300 hover:bg-orange-50 transition">
                        تعرّف على تفاصيل الشراكة
                        <i class="fas fa-circle-info ml-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="pt-6 flex flex-col gap-3 text-sm text-gray-500 footer-bottom md:flex-row md:items-center md:justify-between">
            <div class="text-center md:text-right">
                <span>&copy; {{ now()->year }} وصفة. جميع الحقوق محفوظة.</span>
            </div>
            <div class="flex flex-col items-center gap-1 text-center md:flex-row md:items-center md:gap-4">
                <span>موقع وصفة هو جزء من شركة وصفة الأردن.</span>
                <span class="hidden text-gray-300 md:inline-block">|</span>
                <span>نهتم بجودة تفاصيل كل وصفة.</span>
            </div>
        </div>
    </div>
</footer>
