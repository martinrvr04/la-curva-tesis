<x-guest-layout>
  <form method="POST" action="{{ route('register') }}">
    @csrf

    <div>
      <x-input-label for="name" :value="__('Nombres')" />
      <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
      <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label for="apellido" :value="__('Apellidos')" />
      <x-text-input id="apellido" name="apellido" type="text" class="mt-1 block w-full" required />
      <x-input-error :messages="$errors->get('apellido')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label for="telefono" :value="__('Número de teléfono')" />
      <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full" required placeholder="+56XXXXXXXXX" />
      <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label for="email" :value="__('Correo electrónico')" />
      <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
      <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label for="password" :value="__('Contraseña')" />
      <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
      <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div class="mt-4">
      <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" />
      <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
    </div>

    <div class="flex items-center justify-end mt-6">
      <x-primary-button>{{ __('Registrarme') }}</x-primary-button>
    </div>
  </form>
</x-guest-layout>
