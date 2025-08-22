<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    protected $table = 'habitaciones';

    protected $fillable = [
        'numero', 'tipo', 'capacidad', 'precio_noche', 'descripcion', 'imagen',
        // agrega tus campos reales…
    ];

    /* Relaciones */
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'habitacion_id');
    }

    /**
     * Scope: habitaciones disponibles entre $desde y $hasta
     * Regla: NO debe existir una reserva que:
     *  - se solape con el rango, y
     *  - (estado = confirmada) o (estado = pre_reserva y NO vencida)
     */
    public function scopeDisponibles($q, string $desde, string $hasta)
    {
        return $q->whereDoesntHave('reservas', function ($r) use ($desde, $hasta) {
            $r->where(function ($w) use ($desde, $hasta) {
                // solape: A < B2 && B > A2
                $w->where('fecha_entrada', '<', $hasta)
                  ->where('fecha_salida',  '>', $desde);
            })
            ->where(function ($w2) {
                $w2->where('estado', 'confirmada')
                   ->orWhere(function ($w3) {
                       $w3->where('estado', 'pre_reserva')
                          ->where('expires_at', '>', now()); // ⏳ solo las NO vencidas bloquean
                   });
            });
        });
    }
}
