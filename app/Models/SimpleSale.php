<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimpleSale extends Model
{
  protected $table = 'simple_sales';
  public $timestamps = false;

  protected $fillable = [
    'fecha_h',
    'id_cliente',
    'zona',
    'items',
    'total_bruto',
    'total_neto',
    'descuento',
    'id_empleado'
  ];

  protected $casts = [
    'fecha_h' => 'datetime',
    'items' => 'array',
    'total_bruto' => 'float',
    'total_neto' => 'float',
    'descuento' => 'float',
  ];

  public function cliente()
  {
    return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
  }

  public function empleado()
  {
    return $this->belongsTo(Empleado::class, 'id_empleado', 'id_empleado');
  }
}
