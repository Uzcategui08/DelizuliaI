@component('mail::message')
# Alerta: Bajo Stock de Producto

El producto **{{ $producto->item }}** ({{ $producto->marca }}) tiene un stock bajo.

**Cantidad actual:** {{ $cantidad }} unidades

**Código SKU:** {{ $producto->sku }}

@component('mail::button', ['url' => url('/productos/' . $producto->id_producto)])
Ver Producto
@endcomponent

Por favor, considere realizar un nuevo pedido para reponer el inventario.

Gracias,<br>
{{ config('app.name') }}
@endcomponent
