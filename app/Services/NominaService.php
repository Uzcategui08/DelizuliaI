<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Nempleado;

class NominaService
{
    public function calcularRetiroDueño($startDate, $endDate)
    {
        $start = Carbon::createFromFormat('d/m/Y', $startDate);
        $end = Carbon::createFromFormat('d/m/Y', $endDate);
        
        return Nempleado::whereHas('empleado', function($query) {
            $query->where('cargo', 5); 
        })
        ->whereBetween('fecha_pago', [$start, $end])
        ->sum('total_pagado');
    }
}
