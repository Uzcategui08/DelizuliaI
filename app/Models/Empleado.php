<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Empleado
 *
 * @property $id_empleado
 * @property $nombre
 * @property $cedula
 * @property $cargo
 * @property $salario_base
 * @property $metodo_pago
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Empleado extends Model
{
    
    protected $perPage = 20;
    protected $primaryKey = 'id_empleado';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = ['id_empleado', 'nombre', 'cedula', 'cargo', 'salario_base', 'tipo', 'tipo_pago' ];

    /** 
    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'id_prestamos', 'id_prestamos'); 
    }
    */

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'id_empleado');
    }

    public function descuentos()
    {
        return $this->hasMany(Descuento::class, 'id_descuentos');
    }

    public function abonos()
    {
        return $this->hasMany(Abono::class, 'id_abonos');
    }

    public function costos()
    {
        return $this->hasMany(Costo::class, 'id_tecnico', 'id_empleado');
    }

    public function registrosV()
    {
        return $this->hasMany(RegistroV::class, 'id_empleado', 'id_empleado');
    }

    public function ventas()
    {
        return $this->hasMany(RegistroV::class, 'id_empleado');
    }

    public function gastos()
    {
        return $this->hasMany(Gasto::class, 'id_tecnico', 'id_empleado');
    }

    public function pagosEmpleados()
    {
        return $this->hasMany(Nempleado::class, 'id_empleado');
    }
}
