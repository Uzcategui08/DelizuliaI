<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Nempleado
 *
 * @property $id_nempleado
 * @property $id_pnomina
 * @property $id_empleado
 * @property $total_descuentos
 * @property $total_abonos
 * @property $total_prestamos
 * @property $total_pagado
 * @property $created_at
 * @property $updated_at
 *
 * @property Empleado $empleado
 * @property Pnomina $pnomina
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Nempleado extends Model
{
    
    protected $perPage = 20;
    protected $primaryKey = 'id_nempleado';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_nempleado', 'id_empleado', 'total_descuentos', 'total_abonos', 'total_prestamos', 'total_costos', 'total_pagado', 'metodo_pago', 'id_abonos', 'id_descuentos', 'id_costos', 'fecha_desde', 'fecha_hasta', 'sueldo_base', 'horas_trabajadas', 'tipo_pago_empleado', 'detalle_pago', 'fecha_pago'];

    protected $casts = [
        'id_abonos' => 'array',
        'id_descuentos' => 'array',
        'id_costos' => 'array',
        'metodo_pago' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empleado()
    {
        return $this->belongsTo(\App\Models\Empleado::class, 'id_empleado', 'id_empleado');
    }
    
    public function abonos()
    {

        $abonosIds = $this->id_abonos ? json_decode($this->id_abonos, true) : [];
 
        return $this->hasMany(Abono::class, 'id_empleado', 'id_empleado')
                   ->whereIn('id_abonos', $abonosIds);
    }
    
    public function descuentos()
    {
        $descuentosIds = $this->id_descuentos ? json_decode($this->id_descuentos, true) : [];
        
        return $this->hasMany(Descuento::class, 'id_empleado', 'id_empleado')
                   ->whereIn('id_descuentos', $descuentosIds);
    }
    
}
