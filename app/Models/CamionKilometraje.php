<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CamionKilometraje extends Model
{
  protected $table = 'camion_kilometrajes';

  protected $fillable = [
    'camion_id',
    'fecha',
    'kilometraje',
    'nota',
    'user_id',
  ];

  protected $casts = [
    'fecha' => 'date',
    'kilometraje' => 'integer',
  ];

  public function camion(): BelongsTo
  {
    return $this->belongsTo(Camion::class);
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
}
