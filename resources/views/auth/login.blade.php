<x-layouts.auth title="Login">
    <h2 class="text-[24px] font-bold leading-tight tracking-tight text-neutral-800 sm:text-[28px]">Hello Again!</h2>
    <p class="mt-1 text-[15px] text-neutral-600">Welcome Back</p>

    @if (session('status'))
        <x-alert class="mt-6">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-10">
        @csrf

        <div class="space-y-4">
            <x-form.field
                name="email"
                type="email"
                icon="mail"
                placeholder="Email Address"
                autocomplete="email"
                :value="old('email')"
                :error="$errors->first('email')"
            />

            <x-form.field
                name="password"
                type="password"
                icon="lock"
                placeholder="Password"
                autocomplete="current-password"
                :error="$errors->first('password')"
            />
        </div>

        <x-form.button class="mt-7">Login</x-form.button>
    </form>

    <a href="#" class="mt-6 block text-center text-[13px] text-neutral-500 transition hover:text-neutral-700">
        Forgot Password
    </a>

    <p class="mt-3 text-center text-[13px] text-neutral-500">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-medium text-brand-600 transition hover:text-brand-700">Register</a>
    </p>
</x-layouts.auth>
