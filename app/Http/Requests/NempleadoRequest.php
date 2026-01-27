<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NempleadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'id_empleado' => 'required',
			'total_descuentos' => 'required',
			'total_abonos' => 'required',
			'total_prestamos' => 'required',
			'total_pagado' => 'required',
            'metodo_pago' => 'required|array',
            'id_abonos' => 'nullable|array',
            'id_abonos.*' => 'integer|exists:abonos,id_abono',
            'id_descuentos' => 'nullable|array',
            'id_descuentos.*' => 'integer|exists:descuentos,id_descuento',
            'id_costos' => 'nullable|array',
            'id_costos.*' => 'integer|exists:costos,id_costo',
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            'sueldo_base' => 'required|numeric',
            'horas_trabajadas' => 'nullable',
            'tipo_pago_empleado' => 'nullable',
            'detalle_pago' => 'nullable'
        ];
    }
}
