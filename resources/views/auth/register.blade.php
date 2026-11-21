<x-layouts.auth title="Register">
    <h2 class="text-[28px] font-bold leading-tight tracking-tight text-neutral-800 sm:text-[32px]">Hello!</h2>
    <p class="mt-2 text-[16px] text-neutral-600">Sign Up to Get Started</p>

    @if (session('status'))
        <x-alert class="mt-6">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('register') }}" class="mt-9">
        @csrf

        <div class="space-y-5">
            <x-form.field
                name="name"
                icon="user"
                placeholder="Full Name"
                autocomplete="name"
                :value="old('name')"
                :error="$errors->first('name')"
            />

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
                autocomplete="new-password"
                :error="$errors->first('password')"
            />
        </div>

        <x-form.button class="mt-8">Register</x-form.button>
    </form>

    <p class="mt-7 text-center text-[14px] text-neutral-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-medium text-brand-600 transition hover:text-brand-700">Login</a>
    </p>
</x-layouts.auth>
