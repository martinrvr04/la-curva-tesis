<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Pago;
use App\Models\Resena;
use App\Models\Evento;
use App\Models\ReservaEvento;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Hero por defecto (ajusta si guardaste otra)
        $heroUrl = asset('storage/hostal/fachada/IMG-20250820-WA0079.jpg');

        $nextBooking = Reserva::with(['habitacion','pagos'])
            ->where('usuario_id', $userId)
            ->whereNot('estado', 'cancelada')
            ->whereDate('fecha_entrada', '>=', now()->toDateString())
            ->orderBy('fecha_entrada')
            ->first();

        $bookings = Reserva::with('habitacion')
            ->where('usuario_id', $userId)
            ->latest('fecha_entrada')
            ->paginate(6);

        $paymentsRecent = Pago::with('reserva')
            ->whereHas('reserva', fn($q) => $q->where('usuario_id', $userId))
            ->latest()
            ->take(6)
            ->get();

        $reviews = Resena::with(['reserva','reserva.habitacion'])
            ->where('usuario_id', $userId)
            ->latest()
            ->take(3)
            ->get();

        $events = Evento::select('id','nombre','fecha','hora','lugar','capacidad_maxima')
            ->whereDate('fecha','>=', now()->toDateString())
            ->orderBy('fecha')
            ->take(4)
            ->get()
            ->map(function($ev){
                $confirmados = ReservaEvento::where('evento_id',$ev->id)
                    ->where('estado','confirmada')
                    ->sum('numero_invitados');
                $ev->cupos_restantes = max(0, (int)$ev->capacidad_maxima - (int)$confirmados);
                return $ev;
            });

        return view('dashboard', compact('heroUrl','nextBooking','bookings','paymentsRecent','reviews','events'));
    }
}
