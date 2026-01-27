<?php

namespace App\Exports;

use App\Models\Inventario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InventariosExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Inventario::with(['producto', 'almacene'])->get();
    }

    public function headings(): array
    {
        return [
            'ID Inventario',
            'ID Producto',
            'Producto',
            'Almacén',
            'Cantidad',
            'Total $'
        ];
    }

    public function map($inventario): array
    {
        return [
            $inventario->id_inventario,
            $inventario->producto->id_producto,
            $inventario->producto->item,
            $inventario->almacene->nombre,
            $inventario->cantidad,
            number_format($inventario->cantidad * $inventario->producto->precio, 2)
        ];
    }
}