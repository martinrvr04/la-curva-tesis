<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    // Tabla: pagos (coincide con convención)
    protected $table = 'pagos';

    protected $fillable = [
        'reserva_id',
        'metodo',       // stripe, efectivo, etc.
        'estado',       // pendiente|aprobado|fallido|reembolsado
        'moneda',       // ISO-3, ej: CLP
        'monto',
        'referencia',
        'descripcion',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}
