<x-guest-layout>

    <div class="flex flex-row justify-between items-start gap-4">
        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400 flex-1">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <x-dark-mode-button></x-dark-mode-button>
            <a data-toolTip="Navigate to gmail" data-position="bottom" aria-label="Navigate to gmail"
                href="https://mail.google.com/mail/?tab=rm&ogbl" target="_blank" rel="noopener noreferrer"
                class="hover:opacity-80 transition-opacity">
                <img src="{{ asset('images/gmail.png') }}" alt="Navigate to gmail" class="w-6 h-6">
            </a>
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit"
                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                {{ __('Log Out') }}
            </button>

        </form>

    </div>
</x-guest-layout>
