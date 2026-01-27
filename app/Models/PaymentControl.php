<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentControl extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'monto',
        'fecha',
        'largo_plazo',
        'aprobado',
        'pagado',
    ];

    protected $casts = [
        'monto' => 'float',
        'fecha' => 'date',
        'largo_plazo' => 'boolean',
        'aprobado' => 'boolean',
        'pagado' => 'boolean',
    ];
}
