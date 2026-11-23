@php
    $showPeriod = $showPeriodRequest ?? false;
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if (! in_array(strtoupper($method), ['GET', 'POST'], true))
        @method($method)
    @endif

    <x-tenants.form-fields
        :tenant="$tenant"
        :show-period-request="$showPeriod"
        :detail-note="$showPeriod
            ? 'Status keanggotaan dan periode sewa awal tenant. Kontrak berikutnya dikelola di menu Kontrak Sewa.'
            : 'Status keanggotaan tenant.'"
    />

    <div class="flex flex-wrap items-center justify-end gap-3">
        <x-dashboard.button variant="secondary" :href="route('tenants.index')">Batal</x-dashboard.button>
        <x-dashboard.button type="submit">{{ $submitLabel }}</x-dashboard.button>
    </div>
</form>
