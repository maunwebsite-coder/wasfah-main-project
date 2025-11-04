@extends('layouts.app')

@section('title', 'إطلاق ورشة جديدة')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-orange-50/70 to-white py-10">
    <div class="container mx-auto px-4">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-orange-500">منطقة الشيف</p>
                <h1 class="mt-1 text-3xl font-bold text-slate-900">إطلاق ورشة أونلاين جديدة</h1>
                <p class="mt-2 text-sm text-slate-600">بمجرد الحفظ سنجهز لك رابط Jitsi قابل للمشاركة مع الحضور.</p>
            </div>
            <a href="{{ route('chef.workshops.index') }}"
               class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 shadow-sm hover:border-slate-300 hover:text-slate-800">
                <i class="fas fa-arrow-right"></i>
                العودة لقائمة الورش
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-3xl border border-red-200 bg-red-50 p-5 text-red-700">
                <p class="font-semibold">يرجى مراجعة الحقول التالية:</p>
                <ul class="list-inside list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $formWorkshop = new \App\Models\Workshop();
            $formWorkshop->is_online = true;
            $formWorkshop->currency = 'JOD';
            $formWorkshop->level = 'beginner';
        @endphp

        <form method="POST" action="{{ route('chef.workshops.store') }}" enctype="multipart/form-data" class="space-y-10">
            @csrf

            @include('chef.workshops.form', ['workshop' => $formWorkshop])

            <div class="sticky bottom-4 z-10 flex flex-wrap items-center justify-between gap-3 rounded-3xl border border-slate-100 bg-white/90 px-5 py-4 shadow-lg backdrop-blur">
                <div class="text-sm text-slate-500">
                    سيتم حفظ الورشة في حسابك ويمكنك تعديلها أو نشرها متى شئت.
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-3 text-white shadow hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-200">
                    <i class="fas fa-save"></i>
                    حفظ الورشة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
