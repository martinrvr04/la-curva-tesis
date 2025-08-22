@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6 space-y-8">
  <h1 class="text-2xl font-bold">Nueva reserva</h1>

  <form method="GET" action="{{ route('reservas.buscar') }}" class="bg-white/70 rounded-2xl p-4 shadow-sm space-y-3">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div>
        <label class="text-sm font-medium">Check-in</label>
        <input type="date" name="check_in" value="{{ $check_in }}" required class="w-full rounded-xl border p-2">
        @error('check_in')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="text-sm font-medium">Check-out</label>
        <input type="date" name="check_out" value="{{ $check_out }}" required class="w-full rounded-xl border p-2">
        @error('check_out')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="text-sm font-medium">Huéspedes</label>
        <input type="number" name="huespedes" min="1" value="{{ $huespedes }}" class="w-full rounded-xl border p-2">
      </div>
    </div>
    <button class="px-4 py-2 rounded-xl bg-amber-700 text-white">Buscar disponibilidad</button>
  </form>

  @if($check_in && $check_out)
    <div class="space-y-4">
      <h2 class="text-xl font-semibold">Habitaciones disponibles</h2>

      @if($habitaciones->isEmpty())
        <div class="rounded-2xl border p-6 bg-white/60">No hay disponibilidad para ese rango.</div>
      @else
        <div class="grid gap-4 sm:grid-cols-2">
          @foreach($habitaciones as $hab)
            @php
              $total  = $noches * $hab->precio_noche;
            @endphp
            <div class="rounded-2xl border bg-white/70 p-4 shadow-sm">
              <div class="flex items-start justify-between">
                <div>
                  <h3 class="font-semibold text-lg">Hab. {{ $hab->codigo }}</h3>
                  <p class="text-sm text-gray-600 capitalize">{{ $hab->tipo }} · Capacidad {{ $hab->capacidad }}</p>
                </div>
                <div class="text-right">
                  <div class="text-amber-800 font-bold">${{ number_format($hab->precio_noche,0,',','.') }} / noche</div>
                  <div class="text-sm text-gray-600">{{ $noches }} noche(s)</div>
                </div>
              </div>

              @if($hab->descripcion)
                <p class="text-sm text-gray-700 mt-2">{{ Str::limit($hab->descripcion, 120) }}</p>
              @endif

              <form method="POST" action="{{ route('reservas.store') }}" class="mt-4">
                @csrf
                <input type="hidden" name="habitacion_id" value="{{ $hab->id }}">
                <input type="hidden" name="check_in" value="{{ $check_in }}">
                <input type="hidden" name="check_out" value="{{ $check_out }}">
                <input type="hidden" name="huespedes" value="{{ $huespedes }}">
                <button class="w-full rounded-xl bg-emerald-600 text-white py-2 font-medium">
                  Reservar por ${{ number_format($total,0,',','.') }}
                </button>
              </form>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  @endif
</div>
@endsection
