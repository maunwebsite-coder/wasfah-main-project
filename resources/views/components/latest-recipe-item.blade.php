@props(['recipe'])

<li class="flex items-center space-x-3 rtl:space-x-reverse p-3 rounded-lg hover:bg-gray-50 transition-colors group">
    <a href="{{ route('recipe.show', $recipe->slug) }}" class="flex items-center space-x-3 rtl:space-x-reverse w-full">
        <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 shadow-sm group-hover:shadow-md transition-shadow">
            <img src="{{ $recipe->image_url ?: asset('image/logo.png') }}" 
                 alt="{{ $recipe->title }}" 
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                 onerror="this.src='{{ asset('image/logo.png') }}'; this.alt='صورة افتراضية';">
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 mb-1 group-hover:text-orange-600 transition-colors">
                {{ $recipe->title }}
            </h3>
            <div class="flex items-center space-x-2 rtl:space-x-reverse text-xs text-gray-500">
                <span class="flex items-center">
                    <i class="fas fa-clock ml-1"></i>
                    {{ $recipe->prep_time }} دقيقة
                </span>
                @if($recipe->interactions_avg_rating)
                    <span class="flex items-center">
                        <i class="fas fa-star text-yellow-400 ml-1"></i>
                        {{ number_format($recipe->interactions_avg_rating, 1) }}
                    </span>
                @endif
                @if($recipe->category)
                    <span class="bg-orange-100 text-orange-600 px-2 py-1 rounded-full text-xs">
                        {{ $recipe->category->name }}
                    </span>
                @endif
            </div>
        </div>
    </a>
</li>
