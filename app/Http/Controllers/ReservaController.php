<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReservaController extends Controller
{
    /**
     * Paso 1: Búsqueda + listado de disponibles (JSON para el sheet)
     */
    public function buscar(Request $request)
    {
        try {
            $check_in  = $request->query('check_in');
            $check_out = $request->query('check_out');
            $huespedes = (int) $request->query('huespedes', 1);

            $habitaciones = collect();
            $noches = 0;

            if ($check_in && $check_out) {
                $request->validate([
                    'check_in'  => ['required','date','after_or_equal:today'],
                    'check_out' => ['required','date','after:check_in'],
                    'huespedes' => ['nullable','integer','min:1'],
                ]);

                $noches = Carbon::parse($check_in)->diffInDays(Carbon::parse($check_out));

                $habitaciones = Habitacion::query()
                    ->when($huespedes, fn($q) => $q->where('capacidad','>=',$huespedes))
                    ->disponibles($check_in, $check_out) // usa tu scope (fecha_entrada/fecha_salida)
                    ->orderBy('precio_noche')
                    ->get();
            }

            // Forzamos JSON (el sheet siempre hace fetch)
            return response()->json([
                'habitaciones' => $habitaciones->map(function ($h) use ($noches) {
                    $precioNoche = (float) $h->precio_noche;
                    $precioTotal = $noches > 0 ? $precioNoche * $noches : 0.0;

                    return [
                        'id'            => (int) $h->id,
                        'numero'        => $h->numero, // en tu esquema no hay "codigo"
                        'tipo'          => $h->tipo,
                        'capacidad'     => (int) $h->capacidad,
                        'precio_noche'  => $precioNoche,
                        'precio_total'  => round($precioTotal, 2),
                        'descripcion'   => $h->descripcion,
                        'imagen'        => $h->imagen,
                    ];
                })->values(),
                'check_in'   => $check_in,
                'check_out'  => $check_out,
                'huespedes'  => $huespedes,
                'noches'     => $noches,
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
    return response()->json([
        'message' => 'Parámetros inválidos.',
        'errors'  => $ve->errors(),
    ], 422);
} catch (\Throwable $e) {
    \Log::error('Error en reservas.buscar: '.$e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);

    if (app()->environment('local')) {
        return response()->json([
            'message' => $e->getMessage(),
            'file'    => basename($e->getFile()),
            'line'    => $e->getLine(),
        ], 500);
    }

    return response()->json(['message' => 'Error interno.'], 500);
}
    }

    /**
     * Paso 2: Crear reserva al elegir una habitación
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'habitacion_id' => ['required','exists:habitaciones,id'],
            'check_in'      => ['required','date','after_or_equal:today'],
            'check_out'     => ['required','date','after:check_in'],
            'huespedes'     => ['required','integer','min:1'],
        ]);

        return DB::transaction(function () use ($request, $data) {
            // Revalidar disponibilidad por seguridad
            $disponible = Habitacion::where('id', $data['habitacion_id'])
                ->disponibles($data['check_in'], $data['check_out'])
                ->exists();

            abort_if(!$disponible, 422, 'La habitación ya no está disponible en ese rango.');

            // Mapear a los nombres reales de la tabla "reservas"
            $checkIn  = Carbon::parse($data['check_in'])->toDateString();
            $checkOut = Carbon::parse($data['check_out'])->toDateString();
            $noches   = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));

            $reserva = new Reserva([
                'usuario_id'     => $request->user()->id, // FK correcta
                'habitacion_id'  => $data['habitacion_id'],
                'fecha_entrada'  => $checkIn,
                'fecha_salida'   => $checkOut,
                'numero_adultos' => (int) $data['huespedes'], // ajusta si separas adultos/niños
                'numero_ninos'   => 0,
                'estado'         => 'pendiente',
                'origen'         => 'web',
            ]);

            // Calcular total simple (precio base * noches).
            // Si activas precios por rango, reemplaza por lógica por-día.
            $habitacion = Habitacion::findOrFail($data['habitacion_id']);
            $reserva->total_reserva = round($noches * (float)$habitacion->precio_noche, 2);

            // Si tu modelo Reserva genera codigo_reserva en boot(), no hace falta setearlo aquí
            $reserva->save();

            return redirect()->route('reservas.show', $reserva)->with('ok','Reserva creada.');
        });
    }

    /**
     * Paso 3: Pantalla de confirmación
     */
    public function show(Reserva $reserva)
    {
        // relación correcta: 'usuario', no 'user'
        $reserva->load('habitacion','usuario');
        return view('reservas.show', compact('reserva'));
    }
}
