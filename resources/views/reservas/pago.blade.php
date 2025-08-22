@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-4xl py-10">

  <h1 class="text-2xl font-bold mb-6">Pago de la Reserva #{{ $reserva->id }}</h1>

  <div class="bg-white border rounded-2xl p-6 shadow-sm">
    {{-- Información de la habitación --}}
    <div class="flex flex-col md:flex-row gap-6">
      <img src="{{ $reserva->habitacion->imagen ?? asset('img/demo-room.jpg') }}"
           alt="Habitación"
           class="w-full md:w-1/2 h-56 object-cover rounded-lg border">

      <div class="flex-1">
        <h2 class="text-xl font-semibold">
          Habitación {{ $reserva->habitacion->numero }}
          <span class="text-slate-500">({{ $reserva->habitacion->tipo }})</span>
        </h2>
        <p class="text-slate-600 mt-2">
          {{ $reserva->habitacion->descripcion ?? 'Acogedora habitación con baño privado.' }}
        </p>

        <dl class="mt-4 text-sm space-y-1">
          <div class="flex justify-between">
            <dt>Check-in</dt><dd>{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}</dd>
          </div>
          <div class="flex justify-between">
            <dt>Check-out</dt><dd>{{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}</dd>
          </div>
          <div class="flex justify-between">
            <dt>Huéspedes</dt><dd>{{ $reserva->numero_adultos }}</dd>
          </div>
          <div class="flex justify-between font-semibold text-lg">
            <dt>Total</dt><dd>${{ number_format($reserva->total_reserva, 0) }}</dd>
          </div>
        </dl>
      </div>
    </div>

    {{-- Métodos de pago --}}
    <div class="mt-8">
      <h3 class="text-lg font-semibold mb-4">Elige un método de pago</h3>
      <div class="grid sm:grid-cols-3 gap-4">

        <button class="border rounded-lg py-3 bg-slate-50 text-slate-600 cursor-not-allowed">
          Nequi (Próx.)
        </button>

        <button class="border rounded-lg py-3 bg-slate-50 text-slate-600 cursor-not-allowed">
          PSE (Próx.)
        </button>

        {{-- Botón de pago simulado --}}
        <form method="POST" action="{{ route('reservas.pago.do', $reserva) }}">
          @csrf
          <button class="w-full rounded-lg py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold">
            Pagar ahora (demo)
          </button>
        </form>
      </div>
    </div>

    <div class="mt-6">
      <a href="{{ route('reservas.show', $reserva) }}"
         class="text-slate-600 underline text-sm">← Volver al resumen</a>
    </div>
  </div>
</div>
@endsection
