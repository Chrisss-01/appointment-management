@php
    $step = $step ?? 1;
@endphp

<div class="flex items-center justify-center gap-3">
    @for ($i = 1; $i <= 4; $i++)
        <div class="h-1.5 w-14 rounded-full {{ $i <= $step ? 'bg-[#1392EC]' : 'bg-white/10' }}"></div>
    @endfor
</div>