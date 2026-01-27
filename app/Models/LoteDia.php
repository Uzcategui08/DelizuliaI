<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoteDia extends Model
{
  protected $table = 'lote_dias';

  protected $fillable = [
    'lote_id',
    'dia_numero',
  ];

  public function lote(): BelongsTo
  {
    return $this->belongsTo(Lote::class);
  }

  public function mermas(): HasMany
  {
    return $this->hasMany(LoteMerma::class, 'lote_dia_id');
  }
}
