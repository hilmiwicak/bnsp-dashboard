<x-layouts.auth title="Register">
    <h1 class="text-[34px] font-bold leading-tight tracking-tight text-neutral-900 sm:text-[40px]">Register</h1>
    <p class="mt-3 text-[16px] text-neutral-500">Sign up to get started.</p>

    @if (session('status'))
        <x-alert class="mt-6">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('register') }}" class="mt-9">
        @csrf

        <div class="space-y-5">
            <x-form.field
                name="name"
                label="Full name"
                placeholder="Jane Doe"
                autocomplete="name"
                :value="old('name')"
                :error="$errors->first('name')"
            />

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
                placeholder="Create a password"
                autocomplete="new-password"
                :error="$errors->first('password')"
            />
        </div>

        <x-form.button class="mt-8">Register</x-form.button>
    </form>

    <p class="mt-8 text-center text-[15px] text-neutral-500">
        Already have an account?
        <a
            href="{{ route('login') }}"
            class="font-medium text-brand-600 underline-offset-4 transition hover:text-brand-700 hover:underline"
        >
            Login
        </a>
    </p>
</x-layouts.auth>
