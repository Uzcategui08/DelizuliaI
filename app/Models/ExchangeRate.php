<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
  protected $table = 'exchange_rates';

  protected $fillable = [
    'date',
    'rate',
  ];

  protected $casts = [
    'date' => 'date',
    'rate' => 'float',
  ];
}
