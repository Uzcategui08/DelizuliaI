<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
  protected $table = 'price_lists';
  public $timestamps = false;

  protected $fillable = [
    'code',
    'name',
  ];

  public function items()
  {
    return $this->hasMany(PriceListItem::class, 'price_list_id');
  }
}
