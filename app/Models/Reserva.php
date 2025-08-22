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

    // Estados válidos
    public const ESTADOS = ['pre_reserva','confirmada','cancelada'];
    public const PAGOS   = ['unpaid','paid','failed'];

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
        'payment_status',
        'expires_at',
        'payment_intent_id',
    ];

    protected $casts = [
        'fecha_entrada'     => 'date',
        'fecha_salida'      => 'date',
        'total_reserva'     => 'decimal:2',
        'expires_at'        => 'datetime',
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
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'reserva_id');
    }

    /* ==========================
     | Scopes útiles
     ==========================*/

    // Activas = bloquean disponibilidad
    public function scopeActivas($q)
    {
        return $q->where(function($q) {
            $q->where('estado', 'confirmada')
              ->orWhere(function($q2) {
                  $q2->where('estado', 'pre_reserva')
                     ->where('expires_at', '>', now()); // ⏳ solo las no vencidas
              });
        });
    }

    // Que se solapen con un rango
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

    public function getExpiradaAttribute(): bool
    {
        return $this->estado === 'pre_reserva'
            && $this->expires_at
            && $this->expires_at->isPast();
    }

    /* ==========================
     | Lógica de negocio
     ==========================*/
    public function solapaCon(string $desde, string $hasta): bool
    {
        return ($this->fecha_entrada < $hasta) && ($this->fecha_salida > $desde);
    }

    public function calcularTotal(): float
    {
        $hab = $this->habitacion()->firstOrFail();
        $noches = $this->noches;
        if ($noches <= 0) return 0.0;

        $rangos = DB::table('precios_habitaciones')
            ->where('habitacion_id', $hab->id)
            ->where(function ($q) {
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
    protected static function booted()
    {
        static::creating(function (self $reserva) {
            if (empty($reserva->codigo_reserva)) {
                $reserva->codigo_reserva = strtoupper('LC-' . substr(uniqid('', true), -6));
            }

            // Defaults de seguridad
            if (empty($reserva->estado)) {
                $reserva->estado = 'pre_reserva';
            }
            if (empty($reserva->payment_status)) {
                $reserva->payment_status = 'unpaid';
            }
            if (empty($reserva->expires_at)) {
                $reserva->expires_at = now()->addMinutes(15);
            }
        });
    }
}
