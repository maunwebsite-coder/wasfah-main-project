<footer class="bg-orange-50 pt-12 pb-6">
    <div class="container mx-auto px-4">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 gap-10 pb-10 border-b border-gray-200 footer-content sm:grid-cols-2 lg:grid-cols-12">
            <!-- Brand -->
            <div class="space-y-4 text-center sm:text-right lg:col-span-3">
                <h3 class="text-sm font-semibold text-orange-500 tracking-wider uppercase">شعار وصفة</h3>
                <div class="flex items-center justify-center sm:justify-end space-x-3 rtl:space-x-reverse">
                    <img src="{{ asset('image/logo.png') }}" alt="شعار وصفة" class="h-12 w-auto">
                </div>
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
                        <span>wasfah99@gmail.com</span>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('contact') }}" class="inline-block hover:text-orange-500 transition-colors">اتصل بنا</a>
                    </div>
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
