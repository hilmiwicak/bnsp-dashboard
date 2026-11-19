<x-layouts.auth title="Dashboard">
    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-600">Signed In</p>

    <h2 class="mt-3 text-[28px] font-bold leading-tight tracking-tight text-neutral-800">
        Hello, {{ auth()->user()->name }}!
    </h2>
    <p class="mt-1 text-[17px] text-neutral-600">{{ auth()->user()->email }}</p>

    @if (session('status'))
        <x-alert class="mt-6">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('logout') }}" class="mt-8">
        @csrf
        <x-form.button>Logout</x-form.button>
    </form>
</x-layouts.auth>
