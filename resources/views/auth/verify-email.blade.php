<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-extrabold tracking-tight text-stone-900">Verifikasi Email</h2>
        <p class="mt-1 text-sm text-stone-500">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you?') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert-success mb-4">
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

            <button type="submit" class="text-sm font-semibold text-stone-500 underline-offset-4 hover:text-stone-900 hover:underline focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 rounded-md">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
