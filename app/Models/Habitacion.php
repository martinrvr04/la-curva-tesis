<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use HasFactory;

    // Nombre real de la tabla (evita 'habitacions')
    protected $table = 'habitaciones';

    protected $fillable = [
        'numero',
        'tipo',
        'capacidad',
        'precio_noche',
        'descripcion',
        'imagen',
    ];

    /**
     * Relación: una habitación tiene muchas reservas
     * FK correcta en la tabla 'reservas': habitacion_id
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'habitacion_id');
    }

    /**
     * Scope: habitaciones disponibles entre dos fechas
     * No disponible si hay reserva no cancelada que se solape:
     *  fecha_entrada < checkOut  &&  fecha_salida > checkIn
     */
    public function scopeDisponibles($query, $checkIn, $checkOut)
    {
        return $query->whereDoesntHave('reservas', function ($q) use ($checkIn, $checkOut) {
            $q->where('estado', '!=', 'cancelada')
              ->where('fecha_entrada', '<', $checkOut)
              ->where('fecha_salida',  '>', $checkIn);
        });
    }
}
