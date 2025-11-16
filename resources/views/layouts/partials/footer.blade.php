@php
    $year = now()->year;
    $currentLocale = app()->getLocale();
    $alternateLocale = $currentLocale === 'ar' ? 'en' : 'ar';
    $languageCopy = \Illuminate\Support\Facades\Lang::get('navbar.language');
@endphp
<footer class="bg-orange-50 pt-12 pb-6">
    <div class="container mx-auto px-4">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 gap-10 pb-10 border-b border-gray-200 footer-content sm:grid-cols-2 lg:grid-cols-12">
            <!-- Brand -->
            <div class="space-y-4 text-center sm:text-right lg:col-span-3">
                <div class="flex items-center justify-center sm:justify-end space-x-3 rtl:space-x-reverse">
                    <x-optimized-picture
                        base="image/logo"
                        :widths="[96, 192, 384]"
                        alt="{{ __('footer.brand.logo_alt') }}"
                        class="h-12 w-auto"
                        :lazy="false"
                        sizes="96px"
                    />
                </div>
                <p class="text-gray-600 leading-relaxed">
                    {{ __('footer.brand.description') }}
                </p>
            </div>

            <!-- Explore -->
            <div class="space-y-4 text-center sm:text-right lg:col-span-3">
                <h3 class="text-sm font-semibold text-orange-500 tracking-wider uppercase">{{ __('footer.sections.explore.title') }}</h3>
                <ul class="space-y-2 text-gray-600">
                    <li>
                        <a href="{{ route('recipes') }}" class="hover:text-orange-500 transition-colors">{{ __('footer.sections.explore.links.recipes') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('workshops') }}" class="hover:text-orange-500 transition-colors">{{ __('footer.sections.explore.links.workshops') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('baking-tips') }}" class="hover:text-orange-500 transition-colors">{{ __('footer.sections.explore.links.baking_tips') }}</a>
                    </li>
                </ul>
            </div>

            <!-- Quick Guide -->
            <div class="space-y-4 text-center sm:text-right lg:col-span-3">
                <h3 class="text-sm font-semibold text-orange-500 tracking-wider uppercase">{{ __('footer.sections.guide.title') }}</h3>
                <ul class="space-y-2 text-gray-600">
                    <li>
                        <a href="{{ route('tools') }}" class="hover:text-orange-500 transition-colors">{{ __('footer.sections.guide.links.tools') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('search') }}" class="hover:text-orange-500 transition-colors">{{ __('footer.sections.guide.links.search') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="hover:text-orange-500 transition-colors">{{ __('footer.sections.guide.links.about') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('partnership') }}" class="hover:text-orange-500 transition-colors">{{ __('footer.sections.guide.links.partnership') }}</a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="space-y-4 text-center sm:text-right lg:col-span-3">
                <h3 class="text-sm font-semibold text-orange-500 tracking-wider uppercase">{{ __('footer.sections.contact.title') }}</h3>
                <div class="space-y-3 text-gray-600">
                    <div class="flex items-center justify-center space-x-2 rtl:space-x-reverse sm:justify-end">
                        <i class="fas fa-envelope text-orange-500"></i>
                        <span>{{ __('footer.sections.contact.support_notice') }}</span>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('contact') }}" class="inline-block hover:text-orange-500 transition-colors">{{ __('footer.sections.contact.cta') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="pt-6 flex flex-col gap-3 text-sm text-gray-500 footer-bottom md:flex-row md:items-center md:justify-between">
            <div class="text-center md:text-right">
                <span>{{ __('footer.bottom.copyright', ['year' => $year]) }}</span>
            </div>
            <div class="flex flex-col items-center gap-1 text-center md:flex-row md:items-center md:gap-4">
                <span>{{ __('footer.bottom.line_one') }}</span>
                <span class="hidden text-gray-300 md:inline-block">|</span>
                <span>{{ __('footer.bottom.line_two') }}</span>
            </div>
            <div class="flex justify-center md:justify-end">
                <form method="POST" action="{{ route('locale.switch') }}" class="inline-flex">
                    @csrf
                    <input type="hidden" name="locale" value="{{ $alternateLocale }}">
                    <button type="submit" class="flex items-center gap-2 rounded-full border border-orange-200 bg-white px-4 py-2 text-sm font-semibold text-orange-600 shadow-sm transition-all duration-200 hover:border-orange-300 hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-200" aria-label="{{ data_get($languageCopy, 'switch_to.' . $alternateLocale, 'Switch language') }}">
                        <i class="fas fa-globe text-base"></i>
                        <span>{{ data_get($languageCopy, 'short.' . $alternateLocale, strtoupper($alternateLocale)) }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</footer>
