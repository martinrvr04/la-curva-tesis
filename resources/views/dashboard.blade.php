{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('content')
{{-- Ruta usada por buscarDisponibilidad() en el componente Alpine --}}
<script>window.routeReservasBuscar = "{{ route('reservas.buscar') }}";</script>

<div class="bg-sand-100" x-data="reservaSheet()" x-init="init()">
  <div class="max-w-[1200px] mx-auto">

    {{-- HERO con overlay + tarjeta flotante --}}
    <section class="hero mx-0 sm:mx-6 mt-0 sm:mt-6">
      <img src="{{ $heroUrl ?? asset('storage/hostal/fachada/IMG-20250820-WA0079.jpg') }}"
           alt="Fachada Hostal La Curva"
           class="hero-img">
      <div class="hero-ov"></div>

      <h1 class="hero-title">
        Welcome to<br class="hidden sm:block">La Curva Hostal
      </h1>

      {{-- Tarjeta flotante Next Booking --}}
      <div class="floating">
        <div class="flex items-center justify-between">
          <h2 class="text-amber-900 font-semibold">Next Booking</h2>
          @if(!empty($nextBooking?->estado))
            <span class="badge badge-ok">{{ ucfirst($nextBooking->estado) }}</span>
          @endif
        </div>

        @if($nextBooking)
          <div class="mt-3 space-y-1.5 text-sm text-amber-950">
            <p>Room <span class="font-semibold">{{ $nextBooking->habitacion->numero ?? '—' }}</span></p>
            <p>{{ \Carbon\Carbon::parse($nextBooking->fecha_entrada)->format('M. j') }}</p>
            <p>{{ \Carbon\Carbon::parse($nextBooking->fecha_salida)->format('M. j') }}</p>
          </div>
          <div class="mt-3 flex gap-2">
            <a href="{{ route('reservas.show', $nextBooking) }}" class="btn btn-primary text-xs">Ver detalle</a>
            @php $pend = optional($nextBooking->pagos)->where('estado','pendiente')->count() > 0; @endphp
            @if($pend)
              <a href="{{ url('/pagos/checkout/'.$nextBooking->id) }}" class="btn btn-primary text-xs">Pagar</a>
            @endif
          </div>
        @else
          <p class="mt-2 text-sm text-gray-700">No tienes reservas próximas.</p>
          <a href="#" @click.prevent="reservaOpen = true" class="btn btn-primary mt-3 text-xs">Buscar</a>
        @endif
      </div>
    </section>

    {{-- GRID PRINCIPAL --}}
    <section class="px-4 sm:px-6 pt-12 pb-12">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- GALERÍA --}}
        <div class="card lg:col-span-2">
          <div class="flex items-center justify-between">
            <h3 class="text-amber-900 text-xl font-semibold">Gallery</h3>
            <a href="{{ url('/habitaciones') }}" class="text-sm text-amber-700 hover:underline">Ver todas</a>
          </div>
          <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach ([
              'storage/hostal/fachada/IMG-20250820-WA0079.jpg',
              'storage/hostal/habitaciones/matrimonial/440058600.jpg',
              'storage/hostal/habitaciones/familiar/440058307.jpg',
              'storage/hostal/habitaciones/doble/440058213.jpg',
              'storage/hostal/habitaciones/banos/440058693.jpg',
              'storage/hostal/areas/terraza/420121812.jpg',
            ] as $img)
              <img src="{{ asset($img) }}" alt="Foto hostal"
                   class="w-full h-28 sm:h-32 object-cover rounded-xl border border-amber-100">
            @endforeach
          </div>
        </div>

        {{-- ACCIONES RÁPIDAS --}}
        <div class="card card--light">
          <h3 class="text-amber-900 text-xl font-semibold">Quick Actions</h3>

          <div class="mt-4 grid grid-cols-3 gap-3">
            <a href="#"
               @click.prevent="reservaOpen = true"
               class="rounded-2xl border border-amber-200 bg-white/70 p-4 text-center hover:bg-sand-50">
              <div class="mx-auto h-10 w-8 rounded-md bg-amber-600/10"></div>
              <p class="mt-2 text-xs text-amber-900 font-medium leading-tight">Manage<br>Booking</p>
            </a>
            <a href="{{ url('/contacto') }}"
               class="rounded-2xl border border-amber-200 bg-white/70 p-4 text-center hover:bg-sand-50">
              <div class="mx-auto h-10 w-8 rounded-md bg-amber-600/10"></div>
              <p class="mt-2 text-xs text-amber-900 font-medium leading-tight">Contact<br>Hostal</p>
            </a>
            <a href="{{ url('/pagos') }}"
               class="rounded-2xl border border-amber-200 bg-white/70 p-4 text-center hover:bg-sand-50">
              <div class="mx-auto h-10 w-8 rounded-md bg-amber-600/10"></div>
              <p class="mt-2 text-xs text-amber-900 font-medium leading-tight">Payment<br>Info</p>
            </a>
          </div>

          <div class="mt-4 space-y-2">
            <a href="#"
               @click.prevent="reservaOpen = true"
               class="btn btn-primary w-full">Reservar</a>
            <a href="{{ url('/contacto') }}" class="btn btn-outline w-full">Contactar Hostal</a>
            <a href="{{ url('/pagos') }}" class="btn btn-outline w-full">Información de pago</a>
          </div>
        </div>

        {{-- EVENTOS --}}
        <div class="card card--light">
          <div class="flex items-center justify-between">
            <h3 class="text-amber-900 text-xl font-semibold">Events</h3>
            <a href="{{ url('/eventos') }}" class="text-sm text-amber-700 hover:underline">Ver todos</a>
          </div>
          <div class="mt-4 space-y-3">
            @forelse($events as $ev)
              <article class="rounded-2xl border border-amber-100 p-3 bg-white/70">
                <div class="flex gap-3">
                  @if(!empty($ev->poster_url))
                    <img src="{{ $ev->poster_url }}" class="h-16 w-12 object-cover rounded-md border border-amber-100" alt="Poster">
                  @endif
                  <div class="flex-1">
                    <p class="font-medium text-amber-900">{{ $ev->nombre }}</p>
                    <p class="text-xs text-gray-600">
                      {{ \Carbon\Carbon::parse($ev->fecha)->format('d M') }} · {{ $ev->hora }} · {{ $ev->lugar }}
                    </p>
                    <div class="mt-2 flex items-center justify-between">
                      <span class="text-xs">Cupos: <strong>{{ $ev->cupos_restantes }}</strong></span>
                      <a href="{{ url('/eventos/'.$ev->id) }}" class="btn btn-primary text-xs px-3 py-1.5">Reservar</a>
                    </div>
                  </div>
                </div>
              </article>
            @empty
              <p class="text-gray-600">No hay eventos próximos.</p>
            @endforelse
          </div>
        </div>

        {{-- RESEÑAS --}}
        <div class="card lg:col-span-2">
          <div class="flex items-center justify-between">
            <h3 class="text-amber-900 text-xl font-semibold">Reviews</h3>
            <a href="{{ url('/resenas') }}" class="text-sm text-amber-700 hover:underline">Ver todas</a>
          </div>
          <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($reviews as $rev)
              <div class="rounded-2xl border border-amber-100 p-4 bg-amber-50/60">
                <p class="text-amber-800">
                  {{ str_repeat('★', (int)$rev->calificacion) }}{{ str_repeat('☆', 5 - (int)$rev->calificacion) }}
                </p>
                <p class="mt-1 text-sm">{{ $rev->comentario }}</p>
                <p class="mt-2 text-xs text-gray-600">
                  Reserva {{ $rev->reserva->codigo_reserva ?? '' }} — {{ optional($rev->reserva)->habitacion->tipo ?? '—' }}
                </p>
              </div>
            @empty
              <p class="text-gray-600">Aún no hay reseñas.</p>
            @endforelse
          </div>
        </div>

      </div>
    </section>

  </div>

  {{-- === TOP-SHEET RESERVA === --}}
  <div
    x-cloak
    x-show="reservaOpen"
    x-transition.opacity
    class="fixed inset-0 z-[120]"
    aria-modal="true" role="dialog">

    <div class="absolute inset-0 bg-black/40" @click="closeSheet()" x-cloak></div>

    <div
      x-cloak
      class="absolute left-1/2 -translate-x-1/2 top-0 w-full max-w-4xl bg-white rounded-b-3xl shadow-xl p-5 sm:p-6 z-[130]"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="-translate-y-full"
      x-transition:enter-end="translate-y-0"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="translate-y-0"
      x-transition:leave-end="-translate-y-full"
    >
      <div class="flex items-start justify-between gap-4">
        <h2 class="text-xl font-semibold text-amber-900">Nueva reserva</h2>
        <button @click="closeSheet()" class="rounded-lg border px-3 py-1.5 text-sm">Cerrar</button>
      </div>

      {{-- Formulario --}}
      <form @submit.prevent="buscarDisponibilidad" class="mt-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div>
          <label class="text-sm font-medium">Check-in</label>
          <input type="text" x-ref="checkIn" x-model="form.check_in" required class="w-full rounded-xl border p-2">
        </div>
        <div>
          <label class="text-sm font-medium">Check-out</label>
          <input type="text" x-ref="checkOut" x-model="form.check_out" required class="w-full rounded-xl border p-2">
        </div>
        <div>
          <label class="text-sm font-medium">Huéspedes</label>
          <input type="number" min="1" x-model.number="form.huespedes" class="w-full rounded-xl border p-2">
        </div>
        <div class="flex items-end">
          <button type="submit"
                  class="w-full rounded-xl bg-amber-700 text-white py-2 font-medium"
                  :class="{'opacity-60': loading}">
            <span x-show="!loading">Buscar</span>
            <span x-show="loading">Buscando…</span>
          </button>
        </div>
      </form>

      {{-- Resultados --}}
      <template x-if="buscado">
        <div class="mt-5">
          <h3 class="text-lg font-semibold text-amber-900">Habitaciones disponibles</h3>

          <template x-if="habitaciones.length === 0">
            <div class="mt-3 rounded-2xl border p-4 bg-amber-50/60">No hay disponibilidad para ese rango.</div>
          </template>

          <div class="mt-3 grid gap-4 sm:grid-cols-2" x-show="habitaciones.length > 0">
            <template x-for="hab in habitaciones" :key="hab.id">
              <div class="rounded-2xl border bg-white/70 p-4 shadow-sm">
                <div class="flex items-start justify-between">
                  <div>
                    <h4 class="font-semibold text-lg">Hab. <span x-text="hab.numero ?? '—'"></span></h4>
                    <p class="text-sm text-gray-600">
                      <span class="capitalize" x-text="hab.tipo ?? '—'"></span>
                      · Capacidad <span x-text="hab.capacidad ?? '-'"></span>
                    </p>
                  </div>
                  <div class="text-right">
                    <div class="text-amber-800 font-bold" x-text="money(hab.precio_noche) + ' / noche'"></div>
                    <div class="text-sm text-gray-600" x-text="noches + ' noche(s)'"></div>
                  </div>
                </div>

                <p class="text-sm text-gray-700 mt-2" x-show="hab.descripcion" x-text="hab.descripcion"></p>

                <form method="POST" action="{{ route('reservas.store') }}" class="mt-4">
                  @csrf
                  <input type="hidden" name="habitacion_id" :value="hab.id">
                  <input type="hidden" name="check_in" :value="form.check_in">
                  <input type="hidden" name="check_out" :value="form.check_out">
                  <input type="hidden" name="huespedes" :value="form.huespedes">
                  <button class="w-full rounded-xl bg-emerald-600 text-white py-2 font-medium">
                    Reservar por <span x-text="money(total(hab.precio_noche))"></span>
                  </button>
                </form>
              </div>
            </template>
          </div>
        </div>
      </template>
    </div>
  </div>
  {{-- === /TOP-SHEET RESERVA === --}}
</div>
@endsection
