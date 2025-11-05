<div class="flex flex-col items-start gap-2 text-xs text-slate-200" id="meetingStateLabel">
    @if ($started)
        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-500/20 px-3 py-1 font-semibold text-emerald-200">
            <i class="fas fa-check-circle"></i>
            تم بدء الاجتماع {{ $startedAtHuman }}
        </span>
    @else
        <p class="text-sm text-slate-300">
            تأكد من إعداد اسمك كما سيظهر للمشاركين وأنك المضيف الحقيقي قبل فتح الغرفة.
        </p>
        <label class="mt-2 inline-flex items-start gap-2 text-sm text-slate-200">
            <input
                type="checkbox"
                wire:model.live="confirmHost"
                class="mt-1 h-4 w-4 rounded border-slate-500 bg-transparent text-emerald-400 focus:ring-emerald-300"
            >
            <span>
                أؤكد أنني المضيف وأن الاسم الذي سيظهر للمشاركين هو الاسم الصحيح الخاص بي.
            </span>
        </label>
        <button
            type="button"
            id="startMeetingBtn"
            wire:click="startMeeting"
            wire:target="startMeeting"
            wire:loading.attr="disabled"
            @disabled(! $confirmHost)
            class="mt-3 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-500 px-5 py-2 text-sm font-semibold text-slate-900 shadow hover:from-emerald-500 hover:to-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-300 disabled:cursor-not-allowed disabled:opacity-60"
        >
            <span class="inline-flex items-center gap-2" wire:loading.remove wire:target="startMeeting">
                <i class="fas fa-play"></i>
                بدء الاجتماع الآن
            </span>
            <span class="inline-flex items-center gap-2" wire:loading wire:target="startMeeting">
                <i class="fas fa-spinner fa-spin"></i>
                يتم البدء...
            </span>
        </button>
        <span class="mt-2 text-slate-400">
            سيظهر الزر كمؤشر للمشاركين بأن الغرفة مفتوحة بمجرد البدء.
        </span>
    @endif

    @if ($errorMessage)
        <span class="mt-2 text-sm text-rose-300" role="alert">
            {{ $errorMessage }}
        </span>
    @endif
</div>
