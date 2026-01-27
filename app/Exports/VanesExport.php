<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VanesExport implements FromView
{
  protected $data;
  protected $startDate;
  protected $endDate;

  public function __construct($data, $startDate, $endDate)
  {
    $this->data = $data;
    $this->startDate = $startDate;
    $this->endDate = $endDate;
  }

  public function view(): View
  {
    return view('estadisticas.vanes_export', [
      'vanGrande' => $this->data['vanGrande'],
      'vanPequena' => $this->data['vanPequena'],
      'ventasVanGrande' => $this->data['ventasVanGrande'],
      'ventasVanPequena' => $this->data['ventasVanPequena'],
      'gastosVanGrande' => $this->data['gastosVanGrande'],
      'gastosVanPequena' => $this->data['gastosVanPequena'],
      'costosVanGrande' => $this->data['costosVanGrande'],
      'costosVanPequena' => $this->data['costosVanPequena'],
      'llavesPorTecnico' => $this->data['llavesPorTecnico'],
      'porcentajeCerrajeroGrande' => $this->data['porcentajeCerrajeroGrande'],
      'porcentajeCerrajeroPequena' => $this->data['porcentajeCerrajeroPequena'],
      'totales' => $this->data['totales'],
      'startDate' => $this->startDate,
      'endDate' => $this->endDate,
    ]);
  }
}
