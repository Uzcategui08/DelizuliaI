<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoteMerma extends Model
{
  protected $table = 'lote_mermas';

  protected $fillable = [
    'lote_dia_id',
    'id_producto',
    'cantidad_merma',
    'kilos_merma',
  ];

  protected $casts = [
    'kilos_merma' => 'float',
  ];

  public function dia(): BelongsTo
  {
    return $this->belongsTo(LoteDia::class, 'lote_dia_id');
  }

  public function producto(): BelongsTo
  {
    return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
  }
}
