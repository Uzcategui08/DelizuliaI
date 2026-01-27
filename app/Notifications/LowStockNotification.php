<?php

namespace App\Notifications;

use App\Models\Producto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public $producto;
    public $cantidad;
    public $almacenNombre;
    public $inventarioId;

    /**
     * Create a new notification instance.
     */
    public function __construct(Producto $producto, $cantidad, ?string $almacenNombre = null, ?int $inventarioId = null)
    {
        $this->producto = $producto;
        $this->cantidad = $cantidad;
        $this->almacenNombre = $almacenNombre;
        $this->inventarioId = $inventarioId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'El producto ' . $this->producto->item . ' tiene un stock bajo (' . $this->cantidad . ' unidades).',
            'producto_id' => $this->producto->id_producto,
            'cantidad' => $this->cantidad,
            'url' => '/productos/' . $this->producto->id_producto,
            'almacen' => $this->almacenNombre,
            'inventario_id' => $this->inventarioId,
            'id_llave' => $this->producto->id_producto,
        ];
    }
}
