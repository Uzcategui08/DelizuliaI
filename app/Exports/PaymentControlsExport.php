<?php

namespace App\Exports;

use App\Models\PaymentControl;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentControlsExport implements FromCollection, WithHeadings, WithMapping
{
    private ?string $q;
    private ?string $desde;
    private ?string $hasta;
    private bool $defaultWeek;

    public function __construct(?string $q = null, ?string $desde = null, ?string $hasta = null, bool $defaultWeek = true)
    {
        $this->q = $q;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->defaultWeek = $defaultWeek;
    }

    public function collection()
    {
        return PaymentControl::query()
            ->when($this->q, function ($q) {
                $term = '%' . $this->q . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('nombre', 'like', $term)->orWhere('descripcion', 'like', $term);
                });
            })
            ->when($this->desde, function ($q) {
                $q->whereDate('fecha', '>=', $this->desde);
            })
            ->when($this->hasta, function ($q) {
                $q->whereDate('fecha', '<=', $this->hasta);
            })
            ->when($this->defaultWeek && !$this->desde && !$this->hasta, function ($q) {
                $q->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()]);
            })
            ->orderByDesc('fecha')->orderByDesc('id')
            ->get();
    }

    public function headings(): array
    {
        return ['ID', 'Nombre', 'Descripcion', 'Monto', 'Fecha', 'Aprobado', 'Pagado', 'Creado', 'Actualizado'];
    }

    public function map($pago): array
    {
        return [
            $pago->id,
            $pago->nombre,
            $pago->descripcion,
            $pago->monto,
            optional($pago->fecha)->format('Y-m-d'),
            $pago->aprobado ? 'Si' : 'No',
            $pago->pagado ? 'Si' : 'No',
            optional($pago->created_at)->format('Y-m-d H:i'),
            optional($pago->updated_at)->format('Y-m-d H:i'),
        ];
    }
}
