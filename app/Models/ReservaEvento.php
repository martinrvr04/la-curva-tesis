<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservaEvento extends Model
{
    // OJO: el nombre de la tabla es 'reservas_eventos' (no estándar para Eloquent)
    protected $table = 'reservas_eventos';

    protected $fillable = [
        'usuario_id',
        'evento_id',
        'numero_invitados',
        'estado', // pendiente|confirmada|cancelada
    ];

    protected $casts = [
        'numero_invitados' => 'integer',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}
