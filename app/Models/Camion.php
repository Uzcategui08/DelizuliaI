<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Camion extends Model
{
  protected $table = 'camiones';

  protected $fillable = [
    'nombre',
    'placa',
    'ultimo_cambio_aceite_km',
    'activo',
  ];

  protected $casts = [
    'activo' => 'boolean',
    'ultimo_cambio_aceite_km' => 'integer',
  ];

  public function kilometrajes(): HasMany
  {
    return $this->hasMany(CamionKilometraje::class);
  }

  public function ultimoKilometraje(): HasOne
  {
    return $this->hasOne(CamionKilometraje::class)->latestOfMany('fecha');
  }

  public function kmDesdeCambioAceite(): ?int
  {
    $ultimo = $this->ultimoKilometraje?->kilometraje;
    $base = $this->ultimo_cambio_aceite_km;

    if ($ultimo === null || $base === null) {
      return null;
    }

    return (int) ($ultimo - $base);
  }

  public function requiereCambioAceite(int $umbralKm = 5000): bool
  {
    $km = $this->kmDesdeCambioAceite();

    return $km !== null && $km >= $umbralKm;
  }
}
