@php
$color = match($status) {
    'available' => '#198754',
    'sold'      => '#dc3545',
    'reserved'  => '#ffc107',
    default     => '#0dcaf0'
};
$textColor = ($status === 'reserved') ? 'black' : 'white';
@endphp
<span class="badge" style="background-color: {{ $color }}; color: {{ $textColor }}; padding: 0.5em 0.8em; text-transform: uppercase;">
    {{ $status }}
</span>
