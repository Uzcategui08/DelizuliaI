<?php

namespace App\Traits;

use App\Models\Producto;
use App\Notifications\LowStockNotification;

/**
 * Trait ChecksLowStock
 * 
 * This trait provides functionality to check for low stock levels
 * and create notifications when stock reaches a certain threshold.
 */
trait ChecksLowStock
{
    /**
     * 
     *
     * @param Producto $producto
     * @param int $currentStock
     * @param int $threshold
     * @return void
     */
    /**
     * 
     *
     * @param Producto $producto
     * @param int $currentStock
     * @param int $threshold
     * @return void
     */
    protected function checkAndNotifyLowStock(Producto $producto, int $currentStock, array $metadata = [], int $threshold = 3): void
    {
        if ($currentStock <= $threshold && $currentStock > 0) {
            $users = \App\Models\User::role('admin')->get();

            foreach ($users as $user) {
                $user->notify(new LowStockNotification(
                    $producto,
                    $currentStock,
                    $metadata['almacen'] ?? null,
                    $metadata['inventario_id'] ?? null
                ));
            }
        }
    }
}
