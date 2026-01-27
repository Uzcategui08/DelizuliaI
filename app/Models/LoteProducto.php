<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoteProducto extends Model
{
  protected $table = 'lote_productos';

  protected $fillable = [
    'lote_id',
    'id_producto',
    'cantidad_inicial',
    'kilos_por_unidad',
  ];

  protected $casts = [
    'kilos_por_unidad' => 'float',
  ];

  public function lote(): BelongsTo
  {
    return $this->belongsTo(Lote::class);
  }

  public function producto(): BelongsTo
  {
    return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
  }
}
