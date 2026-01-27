<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Orden
 *
 * @property $id_orden
 * @property $f_orden
 * @property $direccion
 * @property $id_tecnico
 * @property $trabajo
 * @property $items
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Orden extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'id_orden';
    protected $fillable = ['id_orden', 'f_orden', 'direccion', 'id_tecnico', 'estado', 'items', 'id_cliente'];


    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function empleado()
    {
        return $this->belongsTo(\App\Models\Empleado::class, 'id_tecnico', 'id_empleado');
    }



}
