<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Log in to your account') }}
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

       <div class="mb-4">
    <label class="block text-gray-700 text-sm font-bold mb-2">Email or Mobile Number</label>
    <input type="text" name="login" value="{{ old('login') }}" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
    @error('login') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
</div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
            <input id="password" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm" type="password" name="password" required />
            @error('password')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
</x-guest-layout>