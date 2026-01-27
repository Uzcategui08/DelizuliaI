<?php

namespace App\Observers;

use App\Models\Inventario;
use App\Traits\ChecksLowStock;

class InventarioObserver
{
    use ChecksLowStock;

    /**
     * Handle the Inventario "updated" event.
     *
     * @param  \App\Models\Inventario  $inventario
     * @return void
     */
    public function updated(Inventario $inventario)
    {
        if ($inventario->isDirty('cantidad')) {
            $this->checkAndNotifyLowStock(
                $inventario->producto,
                $inventario->cantidad,
                [
                    'almacen' => $inventario->almacene ? $inventario->almacene->nombre : null,
                    'inventario_id' => $inventario->id_inventario,
                ]
            );
        }
    }
}
