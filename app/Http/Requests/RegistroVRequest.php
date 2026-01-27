<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Cliente;
use App\Models\Categoria;
use App\Models\Trabajo;

class RegistroVRequest extends FormRequest
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
            'fecha_h' => 'required',
            'id_empleado' => 'required|exists:empleados,id_empleado',
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'valor_v' => 'required',
            'estatus' => 'required|string',
            'titular_c' => 'required|string',
            'cobro' => 'nullable|string',
            'descripcion_ce' => 'nullable|string',
            'monto_ce' => 'nullable',
            'porcentaje_c' => 'required|string',
            'lugarventa' => 'required|string',
            'marca' => 'nullable|string',
            'modelo' => 'nullable|string',
            'año' => 'nullable|integer',
            'items' => 'required',
            'metodo_pce' => 'nullable|string',
            'tipo_venta' => 'required|string',
            'costos_extras.*.f_costos' => 'nullable|date',
            'gastos.*.f_gastos' => 'nullable|date'
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = $this->all();

        if (!empty($data['id_cliente']) && !ctype_digit((string) $data['id_cliente'])) {
            $nombreCliente = trim((string) $data['id_cliente']);
            if ($nombreCliente !== '') {
                $cliente = Cliente::firstOrCreate(
                    ['nombre' => $nombreCliente],
                    ['telefono' => '', 'direccion' => '']
                );
                $data['id_cliente'] = $cliente->id_cliente;
            }
        }

        $data['costos_extras'] = $this->normalizarCategorias($data['costos_extras'] ?? []);
        $data['gastos'] = $this->normalizarCategorias($data['gastos'] ?? []);
        $data['items'] = $this->normalizarTrabajos($data['items'] ?? []);

        $this->replace($data);
    }

    private function normalizarCategorias(array $registros): array
    {
        foreach ($registros as $index => $registro) {
            if (isset($registro['subcategoria']) && !ctype_digit((string) $registro['subcategoria'])) {
                $nombreCategoria = trim((string) $registro['subcategoria']);

                if ($nombreCategoria === '') {
                    continue;
                }

                $categoria = Categoria::firstOrCreate(['nombre' => $nombreCategoria]);
                $registros[$index]['subcategoria'] = $categoria->id_categoria;
            }
        }

        return $registros;
    }

    private function normalizarTrabajos(array $items): array
    {
        foreach ($items as $index => $item) {
            $trabajoId = $item['trabajo_id'] ?? null;
            $trabajoNombre = $item['trabajo_nombre'] ?? $item['trabajo'] ?? null;

            $requiresCreation = empty($trabajoId) || !ctype_digit((string) $trabajoId);

            if ($requiresCreation && $trabajoNombre) {
                $nombre = trim((string) $trabajoNombre);

                if ($nombre === '') {
                    continue;
                }

                $trabajo = Trabajo::firstOrCreate(
                    ['nombre' => $nombre],
                    ['traducciones' => json_encode(['es' => $nombre])]
                );

                $items[$index]['trabajo_id'] = (string) $trabajo->id_trabajo;
                $items[$index]['trabajo_nombre'] = $trabajo->nombre;
                $items[$index]['trabajo'] = (string) $trabajo->id_trabajo;
            }
        }

        return $items;
    }
}
