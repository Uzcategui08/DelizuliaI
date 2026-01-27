<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Costo
 *
 * @property $id_costos
 * @property $f_costos
 * @property $id_tecnico
 * @property $descripcion
 * @property $subcategoria
 * @property $valor
 * @property $estatus
 * @property $pagos
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Costo extends Model
{
    
    protected $perPage = 20;
    protected $primaryKey = 'id_costos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'f_costos',
        'id_tecnico',
        'descripcion',
        'subcategoria',
        'valor',
        'estatus',
        'pagos',
        'metodo_pago',
        'id_categoria'
    ];
    
    protected $casts = [
        'pagos' => 'array',
        'fecha' => 'date',
        'metodo_pago' => 'array'
    ];

    public function empleado()
    {
        return $this->belongsTo(\App\Models\Empleado::class, 'id_tecnico', 'id_empleado');
    }
    

    public function agregarPago($monto, $metodoPago, $fechaPago)
    {
        $pagos = $this->pagos ?? [];
        
        $pagos[] = [
            'monto' => $monto,
            'metodo_pago' => $metodoPago,
            'fecha' => $fechaPago,
        ];
        
        $this->pagos = $pagos;

        $totalPagado = collect($pagos)->sum('monto');

        if ($totalPagado >= $this->valor) {
            $this->estatus = 'pagado';
        } elseif ($totalPagado > 0) {
            $this->estatus = 'parcialmente_pagado';
        } else {
            $this->estatus = 'pendiente';
        }
        
        $this->save();
    }

    public function totalPagado()
    {
        if (empty($this->pagos)) {
            return 0;
        }
        
        return collect($this->pagos)->sum('monto');
    }
    
    public function saldoPendiente()
    {
        return $this->valor - $this->totalPagado();
    }

    public function tecnico()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_tecnico');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'subcategoria', 'id_categoria');
    }

}
