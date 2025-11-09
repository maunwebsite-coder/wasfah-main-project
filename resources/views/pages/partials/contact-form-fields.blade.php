@php($selectedSubject = old('subject', $defaultSubject ?? 'general'))

<input type="hidden" name="source" value="{{ $source ?? 'contact-page' }}">

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الأول</label>
        <input
            type="text"
            name="first_name"
            value="{{ old('first_name') }}"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('first_name') border-red-500 @enderror"
            placeholder="أدخل اسمك الأول"
            required
        >
        @error('first_name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الأخير</label>
        <input
            type="text"
            name="last_name"
            value="{{ old('last_name') }}"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('last_name') border-red-500 @enderror"
            placeholder="أدخل اسمك الأخير"
            required
        >
        @error('last_name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
    <input
        type="email"
        name="email"
        value="{{ old('email') }}"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('email') border-red-500 @enderror"
        placeholder="example@email.com"
        required
    >
    @error('email')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف (اختياري)</label>
    <input
        type="tel"
        name="phone"
        value="{{ old('phone') }}"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('phone') border-red-500 @enderror"
        placeholder="اكتب رقم هاتفك للتواصل (اختياري)"
    >
    @error('phone')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">الموضوع</label>
    <select
        name="subject"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('subject') border-red-500 @enderror"
        required
    >
        @foreach (\App\Models\ContactMessage::SUBJECT_LABELS as $key => $label)
            <option value="{{ $key }}" {{ $selectedSubject === $key ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    @error('subject')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">الرسالة</label>
    <textarea
        name="message"
        rows="5"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('message') border-red-500 @enderror"
        placeholder="اكتب رسالتك هنا..."
        required
    >{{ old('message') }}</textarea>
    @error('message')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="flex items-center justify-between flex-wrap gap-4">
    <p class="text-sm text-gray-500">عادةً ما نرد خلال يوم عمل واحد.</p>
    <button
        type="submit"
        class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-3 rounded-full font-semibold shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2"
    >
        <i class="fas fa-paper-plane"></i>
        إرسال الرسالة
    </button>
</div>
