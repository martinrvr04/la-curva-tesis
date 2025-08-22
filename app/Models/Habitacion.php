<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'tipo',
        'capacidad',
        'precio_noche',
        'descripcion',
        'imagen',
    ];

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    public function scopeDisponibles($query, $checkIn, $checkOut)
    {
        return $query->whereDoesntHave('reservas', function ($q) use ($checkIn, $checkOut) {
            $q->where('estado', '!=', 'cancelada')
              ->where('fecha_entrada', '<', $checkOut)
              ->where('fecha_salida', '>', $checkIn);
        });
    }
}
