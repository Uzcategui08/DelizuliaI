<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Abono
 *
 * @property $id_abonos
 * @property $id_empleado
 * @property $concepto
 * @property $valor
 * @property $a_fecha
 * @property $created_at
 * @property $updated_at
 *
 * @property Empleado $empleado
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Abono extends Model
{
    
    protected $perPage = 20;
    protected $primaryKey = 'id_abonos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_abonos', 'id_empleado', 'concepto', 'valor', 'a_fecha', 'fecha_pago', 'status'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empleado()
    {
        return $this->belongsTo(\App\Models\Empleado::class, 'id_empleado', 'id_empleado');
    }

    public function registroV()
    {
        return $this->hasMany(RegistroV::class);
    }
    
}
