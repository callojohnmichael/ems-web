<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('otp.verify.store') }}">
        @csrf

        <div>
            <x-input-label for="otp" :value="__('Verification code')" />
            <x-text-input
                id="otp"
                class="block mt-1 w-full text-center tracking-[0.5em] font-mono text-lg"
                type="text"
                name="otp"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="6"
                placeholder="000000"
                required
                autofocus
                autocomplete="one-time-code"
            />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
            <p class="mt-1 text-sm text-gray-600">{{ __('Enter the 6-digit code sent to your email. It expires in 60 seconds.') }}</p>
        </div>

        <div class="flex items-center justify-between mt-6">
            <x-primary-button>
                {{ __('Verify') }}
            </x-primary-button>
            <form method="POST" action="{{ route('otp.resend') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    {{ __('Resend code') }}
                </button>
            </form>
        </div>
    </form>
</x-guest-layout>
