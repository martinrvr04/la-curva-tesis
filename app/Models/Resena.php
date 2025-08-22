<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    // Tabla: resenas (coincide con convención)
    protected $table = 'resenas';

    protected $fillable = [
        'usuario_id',
        'reserva_id',
        'calificacion', // 1..5
        'comentario',
    ];

    protected $casts = [
        'calificacion' => 'integer',
    ];

    public function usuario()
    {
        // FK personalizada
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}
