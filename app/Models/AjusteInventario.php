<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AjusteInventario extends Model
{

    protected $table = 'ajuste_inventarios';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'id_producto',
        'id_almacen',
        'tipo_ajuste',
        'cantidad_anterior',
        'cantidad_nueva',
        'descripcion',
        'precio_llave',
        'user_id',
        'cierre',
        'fecha_ajuste'
    ];

    // Campos calculados o accesores
    protected $appends = ['diferencia'];

    // No se debe asignar directamente porque es calculado en la base
    protected $casts = [
        'cantidad_anterior' => 'integer',
        'cantidad_nueva' => 'integer',
        'diferencia' => 'integer',
        'precio_llave' => 'float',
    ];

    /**
     * Obtener la diferencia calculada (cantidad_nueva - cantidad_anterior).
     * Si el campo está almacenado en la base, puedes omitir este accesor.
     */
    public function getDiferenciaAttribute()
    {
        return $this->cantidad_nueva - $this->cantidad_anterior;
    }

    /**
     * Relación con Producto.
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    /**
     * Relación con Almacen.
     */
    public function almacene(): BelongsTo
    {
        return $this->belongsTo(Almacene::class, 'id_almacen', 'id_almacen');
    }

    /**
     * Relación con Usuario responsable.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
