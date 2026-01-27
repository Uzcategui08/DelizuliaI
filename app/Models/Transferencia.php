<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Transferencia
 *
 * @property $id_transferencia
 * @property $id_producto
 * @property $id_almacen_origen
 * @property $id_almacen_destino
 * @property $cantidad
 * @property $user_id
 * @property $observaciones
 * @property $created_at
 * @property $updated_at
 *
 * @property Almacene $almacene
 * @property Almacene $almacene
 * @property Producto $producto
 * @property User $user
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Transferencia extends Model
{
    
    protected $perPage = 20;
    protected $primaryKey = 'id_transferencia';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_transferencia', 'id_producto', 'id_almacen_origen', 'id_almacen_destino', 'cantidad', 'user_id', 'observaciones'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }

    public function almacenOrigen()
    {
        return $this->belongsTo(Almacene::class, 'id_almacen_origen');
    }

    public function almacenDestino()
    {
        return $this->belongsTo(Almacene::class, 'id_almacen_destino');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
