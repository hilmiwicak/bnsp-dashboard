<x-layouts.auth title="Login">
    <h1 class="text-[34px] font-bold leading-tight tracking-tight text-neutral-900 sm:text-[40px]">Login</h1>
    <p class="mt-3 text-[16px] text-neutral-500">Welcome back! Please enter your details.</p>

    @if (session('status'))
        <x-alert class="mt-6">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-9">
        @csrf

        <div class="space-y-5">
            <x-form.field
                name="email"
                type="email"
                label="Email"
                placeholder="you@company.com"
                autocomplete="email"
                :value="old('email')"
                :error="$errors->first('email')"
            />

            <x-form.field
                name="password"
                type="password"
                label="Password"
                placeholder="Enter your password"
                autocomplete="current-password"
                :error="$errors->first('password')"
            />
        </div>

        <x-form.button class="mt-8">Login</x-form.button>
    </form>

    <p class="mt-8 text-center text-[15px] text-neutral-500">
        Don't have an account?
        <a
            href="{{ route('register') }}"
            class="font-medium text-brand-600 underline-offset-4 transition hover:text-brand-700 hover:underline"
        >
            Sign up
        </a>
    </p>
</x-layouts.auth>
