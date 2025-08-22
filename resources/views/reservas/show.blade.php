@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-6xl py-10">

  {{-- Mensaje flash de confirmación --}}
  @if(session('ok'))
    <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800">
      {{ session('ok') }}
    </div>
  @endif

  <h1 class="text-2xl font-bold mb-6">Reserva #{{ $reserva->id }}</h1>

  <div class="grid md:grid-cols-3 gap-6">
    {{-- Columna izquierda: detalle de la habitación --}}
    <div class="md:col-span-2 bg-white border rounded-2xl shadow-sm overflow-hidden">
      <img src="{{ $reserva->habitacion->imagen ?? asset('img/demo-room.jpg') }}"
           alt="Habitación"
           class="w-full h-64 object-cover">

      <div class="p-6">
        <h2 class="text-xl font-semibold">
          Habitación {{ $reserva->habitacion->numero }}
          <span class="text-slate-500">({{ $reserva->habitacion->tipo }})</span>
        </h2>
        <p class="mt-2 text-slate-600">
          {{ $reserva->habitacion->descripcion ?? 'Acogedora habitación con baño privado y WiFi.' }}
        </p>

        {{-- Amenidades --}}
        <div class="mt-5">
          <h3 class="font-semibold mb-2">Amenidades</h3>
          @php
            $amenidades = $reserva->habitacion->amenidades ?? ['WiFi','Baño privado','TV','Toallas','Desayuno'];
          @endphp
          <ul class="grid grid-cols-2 gap-x-6 gap-y-1 text-sm text-slate-700">
            @foreach($amenidades as $a)
              <li>• {{ $a }}</li>
            @endforeach
          </ul>
        </div>

        {{-- Opiniones de ejemplo --}}
        <div class="mt-5 text-sm text-slate-700">
          ⭐⭐⭐⭐☆ 4.6/5 · “Muy limpio y cómodo. ¡Excelente atención!”
        </div>
      </div>
    </div>

    {{-- Columna derecha: resumen y acciones --}}
    <div class="bg-white border rounded-2xl shadow-sm p-6 h-fit">
      <h3 class="text-lg font-semibold mb-4">Detalles de la reserva</h3>
      <dl class="text-sm space-y-2">
        <div class="flex justify-between">
          <dt>Check-in</dt>
          <dd>{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}</dd>
        </div>
        <div class="flex justify-between">
          <dt>Check-out</dt>
          <dd>{{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}</dd>
        </div>
        <div class="flex justify-between">
          <dt>Huéspedes</dt><dd>{{ $reserva->numero_adultos }}</dd>
        </div>
        <div class="flex justify-between">
          <dt>Noches</dt>
          <dd>{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->diffInDays($reserva->fecha_salida) }}</dd>
        </div>
        <div class="flex justify-between">
          <dt>Estado</dt>
          <dd class="{{ $reserva->payment_status === 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
            {{ ucfirst($reserva->estado) }}
            @if($reserva->payment_status === 'paid') · Pagada @endif
          </dd>
        </div>
        <div class="flex justify-between font-semibold text-lg">
          <dt>Total</dt><dd>${{ number_format($reserva->total_reserva, 0) }}</dd>
        </div>
      </dl>

      {{-- Countdown si está en pre_reserva y sin pagar --}}
      @if($reserva->estado === 'pre_reserva' && $reserva->payment_status === 'unpaid' && $reserva->expires_at)
        <div class="mt-4 p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-sm">
          ⏳ Tienes <span id="countdown" class="font-bold"></span> para completar el pago antes de que expire tu reserva.
        </div>

        {{-- Pasamos expire_at como timestamp UTC en ms --}}
        <div id="expire-data" data-expire-ts="{{ $reserva->expires_at->utc()->timestamp * 1000 }}"></div>

        <script>
          document.addEventListener("DOMContentLoaded", function () {
            const container = document.getElementById("expire-data");
            const countdownEl = document.getElementById("countdown");
            const expireAt = Number(container.dataset.expireTs);

            function updateCountdown() {
              const now = Date.now();
              const diff = expireAt - now;

              if (diff <= 0) {
                countdownEl.textContent = "expirada";
                setTimeout(() => window.location.reload(), 1500);
                return;
              }

              const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
              const seconds = Math.floor((diff % (1000 * 60)) / 1000);

              countdownEl.textContent = `${minutes}m ${seconds}s`;
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
          });
        </script>
      @endif

      {{-- Acciones según estado --}}
      @if($reserva->payment_status !== 'paid' && !($reserva->expirada ?? false))
        <a href="{{ route('reservas.pago.show', $reserva) }}"
           class="mt-6 inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white py-3 font-semibold">
          Ir al pago
        </a>
        <a href="{{ route('home') }}"
           class="mt-2 inline-flex w-full items-center justify-center rounded-lg border text-slate-700 py-2">
          Elegir otra habitación
        </a>
      @elseif($reserva->payment_status === 'paid')
        <a href="{{ route('dashboard') }}"
           class="mt-6 inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 text-white py-3 font-semibold">
          Volver al dashboard
        </a>
      @else
        <div class="mt-6 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
          ❌ Esta reserva ha expirado. Por favor selecciona otra habitación.
        </div>
        <a href="{{ route('home') }}"
           class="mt-3 inline-flex w-full items-center justify-center rounded-lg border text-slate-700 py-2">
          Hacer nueva reserva
        </a>
      @endif
    </div>
  </div>
</div>
@endsection
