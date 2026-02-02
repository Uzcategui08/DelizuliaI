<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceListItem extends Model
{
  protected $table = 'price_list_items';
  public $timestamps = false;

  protected $fillable = [
    'price_list_id',
    'id_producto',
    'price_per_kg',
    'has_iva',
  ];

  protected $casts = [
    'price_per_kg' => 'float',
    'has_iva' => 'boolean',
  ];

  public function priceList()
  {
    return $this->belongsTo(PriceList::class, 'price_list_id');
  }

  public function producto()
  {
    return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
  }
}
