<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceInvoice extends Model
{
  protected $table = 'price_invoices';
  public $timestamps = false;

  protected $fillable = [
    'fecha',
    'price_list_id',
    'tasa',
    'iva_rate',
    'items',
    'base_total',
    'iva_total',
    'total',
  ];

  protected $casts = [
    'fecha' => 'date',
    'tasa' => 'float',
    'iva_rate' => 'float',
    'items' => 'array',
    'base_total' => 'float',
    'iva_total' => 'float',
    'total' => 'float',
  ];

  public function priceList()
  {
    return $this->belongsTo(PriceList::class, 'price_list_id');
  }
}
