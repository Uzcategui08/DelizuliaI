<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Producto
 *
 * @property int $id_producto
 * @property string $item
 * @property string $marca
 * @property string $t_llave
 * @property string $sku
 * @property float $precio
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Producto extends Model
{
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'id_producto';

    protected $fillable = ['id_producto', 'item', 'marca', 't_llave', 'sku', 'precio', 'kilos_promedio'];

    protected $casts = [
        'precio' => 'float',
        'kilos_promedio' => 'float',
    ];

    /**
     * Get the inventory records associated with the product.
     */
    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_producto', 'id_producto');
    }

    public function registroVs()
    {
        return $this->belongsTo(RegistroV::class);
    }
}
