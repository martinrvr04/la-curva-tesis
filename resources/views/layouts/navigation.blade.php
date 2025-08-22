{{-- Navbar cálido tipo mockup --}}
<nav
  x-data="{ open:false }"
  class="sticky top-0 z-40 w-full bg-[rgb(205,163,102)]/92 backdrop-blur supports-[backdrop-filter]:bg-[rgb(205,163,102)]/80 shadow-soft">

  <div class="max-w-[1200px] mx-auto px-4 sm:px-6 h-14 flex items-center justify-between">

    {{-- Logo + nombre --}}
    <a href="{{ url('/') }}" class="flex items-center gap-2 group">
      {{-- Si tienes un logo, cámbialo aquí --}}
      <svg class="h-6 w-6 text-white/90 group-hover:text-white" viewBox="0 0 24 24" fill="currentColor">
        <path d="M3 3h7v7H3V3zm0 11h7v7H3v-7zm11-11h7v7h-7V3zm0 11h7v7h-7v-7z"/>
      </svg>
      <span class="font-display text-white/95 text-lg leading-none">La Curva <span class="opacity-90">Hostal</span></span>
    </a>

    {{-- Menú desktop --}}
    <div class="hidden md:flex items-center gap-6">
      @php
        $nav = [
          ['label' => 'Home',   'href' => url('/')],
          ['label' => 'About',  'href' => url('/about')],
          ['label' => 'Rooms',  'href' => url('/habitaciones')],
          ['label' => 'Contact','href' => url('/contacto')],
        ];
      @endphp

      @foreach($nav as $item)
        <a href="{{ $item['href'] }}"
           class="text-[15px] text-white/90 hover:text-white transition font-medium">
          {{ $item['label'] }}
        </a>
      @endforeach

      {{-- Separador fino --}}
      <span class="h-5 w-px bg-white/20"></span>

      {{-- Usuario / sesión --}}
      @auth
        <div x-data="{openUser:false}" class="relative">
          <button @click="openUser=!openUser"
                  class="text-white/90 hover:text-white text-sm font-medium">
            {{ Str::limit(auth()->user()->name, 12) }} ▾
          </button>
          <div x-cloak x-show="openUser" @click.outside="openUser=false"
               class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-card border border-amber-100 overflow-hidden">
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-sand-50">Perfil</a>
            <a href="{{ url('/reservas') }}" class="block px-4 py-2 text-sm hover:bg-sand-50">Mis reservas</a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="w-full text-left px-4 py-2 text-sm hover:bg-sand-50">Cerrar sesión</button>
            </form>
          </div>
        </div>
      @else
        <a href="{{ route('login') }}" class="text-white/90 hover:text-white text-sm">Iniciar sesión</a>
      @endauth
    </div>

    {{-- Botón móvil --}}
    <button @click="open=!open" class="md:hidden inline-flex items-center justify-center h-9 w-9 rounded-lg hover:bg-white/10">
      <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>

  {{-- Menú móvil desplegable --}}
  <div x-cloak x-show="open" class="md:hidden border-t border-white/20">
    <div class="px-4 py-3 space-y-2">
      <a href="{{ url('/') }}" class="block text-white/90 hover:text-white text-base">Home</a>
      <a href="{{ url('/about') }}" class="block text-white/90 hover:text-white text-base">About</a>
      <a href="{{ url('/habitaciones') }}" class="block text-white/90 hover:text-white text-base">Rooms</a>
      <a href="{{ url('/contacto') }}" class="block text-white/90 hover:text-white text-base">Contact</a>

      @auth
        <div class="h-px bg-white/20 my-2"></div>
        <a href="{{ route('profile.edit') }}" class="block text-white/90 hover:text-white text-base">Perfil</a>
        <a href="{{ url('/reservas') }}" class="block text-white/90 hover:text-white text-base">Mis reservas</a>
        <form method="POST" action="{{ route('logout') }}" class="pt-1">
          @csrf
          <button class="text-left text-white/90 hover:text-white text-base">Cerrar sesión</button>
        </form>
      @else
        <div class="h-px bg-white/20 my-2"></div>
        <a href="{{ route('login') }}" class="block text-white/90 hover:text-white text-base">Iniciar sesión</a>
      @endauth
    </div>
  </div>
</nav>
