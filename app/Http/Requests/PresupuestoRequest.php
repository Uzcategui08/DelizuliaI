<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresupuestoRequest extends FormRequest
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
			'id_cliente' => 'required',
            'f_presupuesto' => 'required|date',
            'validez' => 'required|date',
            'estado' => 'sometimes|in:pendiente,aprobado,rechazado',
            'iva' => 'nullable|numeric',
            'descuento' => 'nullable|numeric',
            'items' => 'required|array',
        ];
    }
    protected function prepareForValidation()
{
    $this->merge([
        'estado' => $this->estado ?? 'pendiente'
    ]);
}
}
