<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Error Message -->
    @if (session('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
        x-transition:leave="transition ease-in duration-1000"
        class="bg-red-500 text-white font-bold p-3 rounded mb-4 text-center">
        {{ session('error') }}
    </div>
    @endif

    <div class="text-center">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-200">Welcome to Helm by LocalSpark! Log in to tap into our powerful tools for creating high-converting lead generation and money-making websites. With user-friendly features and customizable templates, Helm helps you attract clients, boost your online presence, and drive revenue effortlessly. Get started on turning your website into a profitable asset today!
        </h1>
    </div>

    <div class="mt-4 mb-4 items-center justify-center">
        <div class="mt-4 mb-4 flex items-center justify-center">
            <a href="{{ route('login.azure') }}" style="text-decoration: none;">
                <div class="bg-white" style="padding: 10px 15px; border: 1px solid #8C8C8C;">
                    <button
                        class="bg-white border-0 font-semibold" style="color: #5E5E5E; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; align-items: center; cursor: pointer;">
                        <img src="{{ asset('images/ms-symbol19.svg') }}" alt="Microsoft Logo" class="w-5 h-5"
                            style="margin-right: 10px">
                        Sign in with Microsoft
                    </button>
                </div>
            </a>
        </div>

        <div class="mt-4 mb-4 flex items-center justify-center">
            <a href="{{ route('login.google') }}" style="text-decoration: none;">
                <div class="bg-white" style="padding: 10px 15px; border: 1px solid #8C8C8C;">
                    <button
                        class="bg-white border-0 font-semibold" style="color: #5E5E5E; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; align-items: center; cursor: pointer;">
                        <img src="{{ asset('images/google-logo.png') }}" alt="Microsoft Logo" class="w-5 h-5"
                            style="margin-right: 10px">
                        Sign in with Google
                    </button>
                </div>
            </a>
        </div>
    </div>

    {{-- <form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- Email Address -->
    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <!-- Password -->
    <div class="mt-4">
        <x-input-label for="password" :value="__('Password')" />

        <x-text-input id="password" class="block mt-1 w-full"
            type="password"
            name="password"
            required autocomplete="current-password" />

        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <!-- Remember Me -->
    <div class="block mt-4">
        <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
        </label>
    </div>

    <div class="flex items-center justify-end mt-4">
        @if (Route::has('password.request'))
        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
            {{ __('Forgot your password?') }}
        </a>
        @endif

        <x-primary-button class="ms-3">
            {{ __('Log in') }}
        </x-primary-button>
    </div>
    </form> --}}
</x-guest-layout>