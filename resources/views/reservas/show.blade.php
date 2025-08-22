@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 space-y-6">
  <h1 class="text-2xl font-bold">Reserva #{{ $reserva->id }}</h1>

  <div class="rounded-2xl border bg-white/70 p-6 space-y-2">
    <p>
      <span class="font-medium">Habitación:</span>
      {{ $reserva->habitacion->numero }} ({{ $reserva->habitacion->tipo }})
    </p>
    <p>
      <span class="font-medium">Fechas:</span>
      {{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}
      →
      {{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}
    </p>
    <p>
      <span class="font-medium">Huéspedes:</span>
      {{ $reserva->numero_adultos }} adulto(s)
      @if($reserva->numero_ninos > 0)
        + {{ $reserva->numero_ninos }} niño(s)
      @endif
    </p>
    <p>
      <span class="font-medium">Estado:</span>
      {{ ucfirst($reserva->estado) }}
    </p>
    <p class="text-xl">
      <span class="font-bold">Total:</span>
      ${{ number_format($reserva->total_reserva, 0, ',', '.') }}
    </p>
  </div>

  <a href="{{ route('reservas.buscar') }}"
     class="inline-block rounded-xl bg-amber-700 text-white px-4 py-2">
    Hacer otra reserva
  </a>
</div>
@endsection
