<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RegistroV
 *
 * @property $id
 * @property $fecha_h
 * @property $id_empleado
 * @property $trabajo
 * @property $cliente
// * @property $telefono
 * @property $valor_v
 * @property $estatus
 * @property $metodo_p
 * @property $titular_c
 * @property $pagos
 * @property $descripcion_ce
 * @property $monto_ce
 * @property $cobro
 * @property $porcentaje_c
 * @property $marca
 * @property $modelo
 * @property $año
 * @property $items
 * @property $lugarventa
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class RegistroV extends Model
{

    protected $perPage = 20;

    protected $table = 'registroV'; // or whatever your actual table name is
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['fecha_h', 'trabajo', 'id_empleado', 'cliente', 'valor_v', 'estatus', 'metodo_p', 'titular_c', 'pagos', 'descripcion_ce', 'monto_ce', 'cobro', 'porcentaje_c', 'marca', 'modelo', 'año', 'items', 'lugarventa', 'id_cliente', 'metodo_pce', 'costos', 'gastos', 'id_abono', 'tipo_venta', 'cargado'];

    protected $casts = [
        'monto_ce' => 'float',
        'valor_v' => 'float',
        'porcentaje_c' => 'float',
        'pagos' => 'array',
        'fecha_h' => 'datetime',
        'costos' => 'array',
        'gastos' => 'array',
        'cargado' => 'integer', // casteo forzado
    ];

    public function setPagosAttribute($value)
    {
        $this->attributes['pagos'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getPagosAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_producto', 'id_producto');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
    }

    public function registroVs()
    {
        return $this->belongsTo(RegistroV::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function abono()
    {
        return $this->hasMany(Abono::class);
    }

    public function costosRelacionados()
    {
        return $this->hasMany(Costo::class, 'id_costos');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'subcategoria', 'id_categoria');
    }

    public function costosAsociados()
    {
        return $this->hasMany(Costo::class, 'id_costos');
    }

    public function gastosAsociados()
    {
        return $this->hasMany(Gasto::class, 'id_gastos');
    }
}
