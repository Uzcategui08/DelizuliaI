<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbonoRequest extends FormRequest
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
			'concepto' => 'nullable|string',
			'valor' => 'required',
			'a_fecha' => 'required',
            'fecha_pago' => 'nullable|date',
            'status' => 'nullable|integer',
        ];
    }
}
