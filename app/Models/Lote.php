<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lote extends Model
{
  protected $fillable = [
    'nombre',
    'fecha_inicio',
  ];

  protected $casts = [
    'fecha_inicio' => 'date',
  ];

  public function productos(): HasMany
  {
    return $this->hasMany(LoteProducto::class);
  }

  public function dias(): HasMany
  {
    return $this->hasMany(LoteDia::class);
  }
}
