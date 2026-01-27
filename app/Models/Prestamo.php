<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Prestamo
 *
 * @property $id_prestamos
 * @property $id_empleado
 * @property $valor
 * @property $cuotas
 * @property $cuota_actual
 * @property $activo
 * @property $created_at
 * @property $updated_at
 *
 * @property Empleado $empleado
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Prestamo extends Model
{

    protected $perPage = 20;
    protected $primaryKey = 'id_prestamo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'f_prestamo',
        'id_empleado',
        'descripcion',
        'subcategoria',
        'valor',
        'estatus',
        'pagos'
    ];

    protected $casts = [
        'pagos' => 'array',
        'fecha' => 'date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empleado()
    {
        return $this->belongsTo(\App\Models\Empleado::class, 'id_empleado', 'id_empleado');
    }


    public function categoria()
    {
        return $this->belongsTo(\App\Models\Categoria::class, 'subcategoria', 'id_categoria');
    }

    public function generarCuotas()
    {
        $valorPorCuota = $this->valor / $this->cuotas;

        for ($i = 1; $i <= $this->cuotas; $i++) {
            $this->cuotas()->create([
                'valor' => $valorPorCuota,
                'pagada' => false
            ]);
        }
    }

    public function cuotaSiguiente()
    {
        return $this->valor / $this->cuotas;
    }

    public function saldoActual()
    {
        return $this->valor - $this->cuotas()->where('pagada', true)->sum('valor');
    }

    public function actualizarCuotaActual()
    {
        $this->cuota_actual = min($this->cuota_actual + 1, $this->cuotas);
        if (
            $this->cuota_actual >= $this->cuotas &&
            $this->cuotas()->where('pagada', false)->doesntExist()
        ) {
            $this->activo = 0;
        }
        $this->save();
    }
}
