<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferenciaRequest extends FormRequest
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
			'id_transferencia' => 'required',
			'id_producto' => 'required',
			'id_almacen_origen' => 'required',
			'id_almacen_destino' => 'required',
			'cantidad' => 'required',
			'user_id' => 'required',
			'observaciones' => 'string',
        ];
    }
}
