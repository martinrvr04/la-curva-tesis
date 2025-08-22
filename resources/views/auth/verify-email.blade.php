<x-guest-layout>
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-4 text-sm text-gray-600">
        {{ __('Antes de continuar, revisa tu correo y haz clic en el enlace de verificación. Si no te llegó, puedes reenviarlo.') }}
    </div>

    <div class="mt-4 flex items-center gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                {{ __('Reenviar correo de verificación') }}
            </x-primary-button>
        </form>

        @if (session('message'))
            <span class="text-sm text-green-600">{{ session('message') }}</span>
        @endif
    </div>
</x-guest-layout>
