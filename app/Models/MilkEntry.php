<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payee;

class MilkEntry extends Model
{
  use HasFactory;

  protected $fillable = [
    'date',
    'payee_id',
    'payee_name',
    'liters',
    'amount',
    'week_end',
    'closed_at',
  ];

  protected $casts = [
    'date' => 'date',
    'week_end' => 'date',
    'closed_at' => 'datetime',
    'liters' => 'decimal:2',
    'amount' => 'decimal:2',
  ];

  public function payee()
  {
    return $this->belongsTo(Payee::class);
  }
}
