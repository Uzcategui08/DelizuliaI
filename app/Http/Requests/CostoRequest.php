<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CostoRequest extends FormRequest
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
			'f_costos' => 'required',
			'id_tecnico' => 'required',
			'descripcion' => 'required|string',
			'subcategoria' => 'required',
			'valor' => 'required',
			'estatus' => 'required',
        ];
    }
}
