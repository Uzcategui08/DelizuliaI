<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoRequest extends FormRequest
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
            'id_producto' => 'required|integer',
            'item' => 'required|string',
            'marca' => 'required|string',
            't_llave' => 'required|string',
            'sku' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'kilos_promedio' => 'nullable|numeric|min:0',
        ];
    }
}
