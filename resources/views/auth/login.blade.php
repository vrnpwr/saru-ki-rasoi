<x-guest-layout>
    <div class="mb-4">
        <h2 class="text-2xl font-bold text-gray-900 text-center">Login</h2>
        <p class="text-sm text-gray-600 text-center mt-1">Welcome back! Please enter your details.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-medium" />
            <x-text-input id="email"
                class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3"
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                placeholder="Enter your email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="relative">
            <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />

            <x-text-input id="password"
                class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm py-3"
                type="password" name="password" required autocomplete="current-password"
                placeholder="Enter your password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-600 hover:text-indigo-800 font-medium" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Actions -->
        <div>
            <x-primary-button class="w-full justify-center py-3 text-base">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <div class="relative flex py-2 items-center">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="flex-shrink-0 mx-4 text-gray-400 text-sm">Or continue with</span>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <div>
            <a href="{{ route('auth.google') }}"
                class="flex items-center justify-center w-full bg-white border border-gray-300 rounded-lg shadow-sm px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <svg class="h-5 w-5 mr-3" viewBox="0 0 48 48">
                    <path fill="#4285F4"
                        d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z">
                    </path>
                    <path fill="#34A853"
                        d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z">
                    </path>
                    <path fill="#FBBC05"
                        d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z">
                    </path>
                    <path fill="#EA4335"
                        d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z">
                    </path>
                </svg>
                Sign in with Google
            </a>
        </div>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-600">Don't have an account?
                <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Sign up</a>
            </p>
        </div>
    </form>
</x-guest-layout>