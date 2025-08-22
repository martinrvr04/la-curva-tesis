<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    /**
     * Mostrar pantalla de pago
     */
    public function show(Reserva $reserva)
    {
        // Si ya está confirmada, lo mandamos al resumen normal
        if ($reserva->payment_status === 'paid') {
            return redirect()->route('reservas.show', $reserva);
        }

        $reserva->load('habitacion');
        return view('reservas.pago', compact('reserva'));
    }

    /**
     * Procesar pago (demo)
     */
    public function pay(Request $request, Reserva $reserva)
    {
        if ($reserva->payment_status === 'paid') {
            return redirect()->route('reservas.show', $reserva);
        }

        // Aquí debería integrarse Nequi, PSE, PayPal, etc.
        // Por ahora simulamos un pago exitoso
        $reserva->update([
            'payment_status' => 'paid',
            'estado' => 'confirmada',
        ]);

        return redirect()
            ->route('reservas.show', $reserva)
            ->with('ok', '¡Pago realizado con éxito! Tu reserva está confirmada.');
    }
}
