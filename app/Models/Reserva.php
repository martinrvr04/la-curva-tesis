<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    // Estados válidos según tu CHECK
    public const ESTADOS = ['pendiente','confirmada','cancelada','completada'];

    protected $fillable = [
        'codigo_reserva',
        'usuario_id',
        'habitacion_id',
        'fecha_entrada',
        'fecha_salida',
        'numero_adultos',
        'numero_ninos',
        'estado',
        'total_reserva',
        'nota_cliente',
        'origen',
    ];

    protected $casts = [
        'fecha_entrada' => 'date',
        'fecha_salida'  => 'date',
        'total_reserva' => 'decimal:2',
    ];

    /* ==========================
     | Relaciones
     ==========================*/

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_id');
    }

    public function usuario()
    {
        // tu FK es usuario_id → users.id
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /* ==========================
     | Scopes útiles
     ==========================*/

    // Activas = las que bloquean disponibilidad
    public function scopeActivas($q)
    {
        return $q->whereIn('estado', ['pendiente','confirmada','completada']);
    }

    // Que se solapen con un rango (A<C2 && B>C1)
    public function scopeSolapan($q, $checkIn, $checkOut)
    {
        return $q->where('fecha_entrada', '<', $checkOut)
                 ->where('fecha_salida',  '>', $checkIn);
    }

    /* ==========================
     | Atributos calculados
     ==========================*/
    public function getNochesAttribute(): int
    {
        if (!$this->fecha_entrada || !$this->fecha_salida) return 0;
        return Carbon::parse($this->fecha_entrada)->diffInDays(Carbon::parse($this->fecha_salida));
    }

    /* ==========================
     | Lógica de negocio
     ==========================*/

    // ¿Esta reserva se solapa con otro rango?
    public function solapaCon(string $desde, string $hasta): bool
    {
        return ($this->fecha_entrada < $hasta) && ($this->fecha_salida > $desde);
    }

    /**
     * Calcula el total considerando precios por día.
     * - Usa precios_habitaciones si hay rangos para cada fecha,
     * - si no, usa habitaciones.precio_noche.
     */
    public function calcularTotal(): float
    {
        $hab = $this->habitacion()->firstOrFail();
        $noches = $this->noches;
        if ($noches <= 0) return 0.0;

        // Trae rangos que cubran o toquen el periodo
        $rangos = DB::table('precios_habitaciones')
            ->where('habitacion_id', $hab->id)
            ->where(function ($q) {
                // Se completa con el rango de esta reserva
                $q->whereBetween('fecha_inicio', [$this->fecha_entrada, $this->fecha_salida])
                  ->orWhereBetween('fecha_fin',   [$this->fecha_entrada, $this->fecha_salida])
                  ->orWhere(function ($q2) {
                      $q2->where('fecha_inicio', '<=', $this->fecha_entrada)
                         ->where('fecha_fin',    '>=', $this->fecha_salida);
                  });
            })
            ->orderBy('fecha_inicio')
            ->get();

        $total = 0.0;
        $cursor = Carbon::parse($this->fecha_entrada)->copy();

        for ($i = 0; $i < $noches; $i++) {
            $dia = $cursor->toDateString();
            $precioDia = (float)$hab->precio_noche;

            foreach ($rangos as $r) {
                // Nota: asumimos fecha_fin es exclusiva (como en el controlador)
                if ($dia >= $r->fecha_inicio && $dia < $r->fecha_fin) {
                    $precioDia = (float)$r->precio_noche;
                    break;
                }
            }

            $total += $precioDia;
            $cursor->addDay();
        }

        return round($total, 2);
    }

    /* ==========================
     | Hooks
     ==========================*/

    // Genera un código si no viene
    protected static function booted()
    {
        static::creating(function (self $reserva) {
            if (empty($reserva->codigo_reserva)) {
                $reserva->codigo_reserva = strtoupper('LC-' . substr(uniqid('', true), -6));
            }
        });
    }
}
