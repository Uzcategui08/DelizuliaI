<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Descuento
 *
 * @property $id_descuentos
 * @property $id_empleado
 * @property $concepto
 * @property $valor
 * @property $d_fecha
 * @property $created_at
 * @property $updated_at
 *
 * @property Empleado $empleado
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Descuento extends Model
{
    
    protected $perPage = 20;
    protected $primaryKey = 'id_descuentos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_empleado', 'concepto', 'valor', 'd_fecha', 'fecha_pago', 'status'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empleado()
    {
        return $this->belongsTo(\App\Models\Empleado::class, 'id_empleado', 'id_empleado');
    }
    
}
