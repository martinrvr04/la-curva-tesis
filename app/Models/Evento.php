<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    // Tabla: eventos
    protected $table = 'eventos';

    protected $fillable = [
        'nombre',
        'fecha',
        'hora',
        'descripcion',
        'lugar',
        'capacidad_maxima',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'string',
        'capacidad_maxima' => 'integer',
    ];

    // 🔗 Relaciones
    public function reservasEventos()
    {
        return $this->hasMany(ReservaEvento::class);
    }
}
