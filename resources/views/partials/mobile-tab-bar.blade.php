@php
    $authUser = Auth::user();
    $tabBarLabels = trans('navbar.tab_bar');
    $isChef = $authUser?->isChef();

    $tabItems = [
        [
            'key' => 'home',
            'href' => route('home'),
            'icon' => 'fa-solid fa-house-chimney',
            'label' => data_get($tabBarLabels, 'home', __('navbar.links.home')),
            'active' => request()->routeIs('home', 'home.*'),
        ],
        [
            'key' => 'bookings',
            'href' => route('bookings.index'),
            'icon' => 'fa-solid fa-calendar-check',
            'label' => data_get($tabBarLabels, 'bookings', __('navbar.account_menu.links.bookings')),
            'active' => request()->routeIs('bookings*'),
        ],
        [
            'key' => 'workshops',
            'href' => route('workshops'),
            'icon' => 'fa-solid fa-graduation-cap',
            'label' => data_get($tabBarLabels, 'workshops', __('navbar.links.workshops')),
            'active' => request()->routeIs('workshops', 'workshops.*', 'workshop.*'),
        ],
    ];

    $isChefRoute = $isChef
        ? request()->routeIs('chef.*') && ! request()->routeIs('chef.public.*')
        : false;

    $tabItems[] = [
        'key' => $isChef ? 'chef' : 'profile',
        'href' => $isChef ? route('chef.dashboard') : route('profile'),
        'icon' => $isChef ? 'fa-solid fa-hat-chef' : 'fa-solid fa-user',
        'label' => data_get(
            $tabBarLabels,
            $isChef ? 'chef' : 'profile',
            $isChef ? data_get($tabBarLabels, 'chef', __('navbar.chef_links.dashboard')) : __('navbar.account_menu.links.profile')
        ),
        'active' => $isChef ? $isChefRoute : request()->routeIs('profile*'),
    ];

    $loaderLabel = data_get($tabBarLabels, 'loading_label', 'Updating your page');
    $loaderHint = data_get($tabBarLabels, 'loading_hint', 'Hang tight for a moment.');
@endphp

<nav class="mobile-tab-bar md:hidden" data-mobile-tab-bar aria-label="{{ data_get($tabBarLabels, 'sr_label', __('navbar.mobile_nav_label')) }}" aria-busy="false">
    <div class="mobile-tab-bar__loading-line" role="status" aria-live="polite">
        <span class="sr-only">{{ $loaderLabel }} — {{ $loaderHint }}</span>
    </div>
    <div class="mobile-tab-bar__inner">
        @foreach ($tabItems as $tab)
            <a href="{{ $tab['href'] }}"
               class="mobile-tab-bar__item {{ $tab['active'] ? 'is-active' : '' }}"
               @if ($tab['active']) aria-current="page" @endif>
                <span class="mobile-tab-bar__icon" aria-hidden="true">
                    <i class="{{ $tab['icon'] }}"></i>
                </span>
                <span class="mobile-tab-bar__label">{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
