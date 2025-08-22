{{-- Badge para estados de reservas y pagos --}}
@props(['status' => 'pendiente'])

@php
    $map = [
        'pendiente'   => 'bg-amber-100 text-amber-800 border-amber-200',
        'confirmada'  => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'cancelada'   => 'bg-gray-100 text-gray-700 border-gray-200',
        'completada'  => 'bg-blue-100 text-blue-800 border-blue-200',
        'aprobado'    => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'fallido'     => 'bg-rose-100 text-rose-800 border-rose-200',
        'reembolsado' => 'bg-purple-100 text-purple-800 border-purple-200',
    ];
    $classes = $map[strtolower($status)] ?? 'bg-gray-100 text-gray-700 border-gray-200';
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full border '.$classes
]) }}>
  {{ ucfirst($status) }}
</span>
