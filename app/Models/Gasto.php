<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Gasto
 *
 * @property $id_gastos
 * @property $f_gastos
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
class Gasto extends Model
{
    
    protected $perPage = 20;
    protected $primaryKey = 'id_gastos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'f_gastos',
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
    

    public function agregarPago($monto, $metodoPago, $fechaPago)
    {
        $pagos = $this->pagos ?? [];
        
        $pagos[] = [
            'monto' => $monto,
            'metodo_pago' => $metodoPago,
            'fecha' => $fechaPago,
            'registrado_el' => now()->toDateTimeString()
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

    public function empleado() {
        return $this->belongsTo(Empleado::class, 'id_tecnico', 'id_empleado');
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
