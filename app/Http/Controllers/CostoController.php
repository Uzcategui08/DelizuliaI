<?php

namespace App\Http\Controllers;

use App\Models\Costo;
use App\Models\Empleado;
use App\Models\TiposDePago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Categoria;


class CostoController extends Controller
{
    public function index(Request $request): View
    {
        $costos = Costo::with(['empleado', 'categoria'])
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return view('costo.index', compact('costos'));
    }

    public function create(): View
    {
        $costo = new Costo();
        $empleado = Empleado::where('cargo', '1')->get();
        $costo->f_costos = now()->format('Y-m-d');
        $costo->estatus = 'pendiente';
        $metodos = TiposDePago::all();
        $categorias = Categoria::all();
        return view('costo.create', compact('costo','empleado', 'metodos', 'categorias'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'f_costos' => 'required|date',
                'id_tecnico' => 'required|integer|min:1',
                'descripcion' => 'required|string|max:500',
                'subcategoria' => 'required',
                'valor' => 'required|numeric|min:0',
                'estatus' => 'required|in:pendiente,parcialmente_pagado,pagado',
            ]);

            $pagosData = [];
            if ($request->has('pagos')) {
                $pagosInput = $request->input('pagos');
                if (is_string($pagosInput)) {
                    $pagosData = json_decode(trim($pagosInput, '"\' '), true) ?? [];
                } elseif (is_array($pagosInput)) {
                    $pagosData = $pagosInput;
                }
            }

            $costo = new Costo([
                'f_costos' => $validated['f_costos'],
                'id_tecnico' => $validated['id_tecnico'],
                'descripcion' => $validated['descripcion'],
                'subcategoria' => $validated['subcategoria'],
                'valor' => $validated['valor'],
                'estatus' => $validated['estatus'],
                'pagos' => $pagosData
            ]);

            if (!$costo->save()) {
                throw new \Exception("No se pudo guardar el registro en la base de datos");
            }

            return Redirect::route('costos.index')
                ->with('success', 'Costo creado satisfactoriamente.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al guardar el costo: ' . $e->getMessage()]);
        }
    }

    public function show($id): View
    {
        $costo = Costo::with('empleado')->findOrFail($id);
        $metodos = TiposDePago::all()->pluck('name', 'id');
        $categorias = Categoria::all();
        
        return view('costo.show', [
            'costo' => $costo,
            'metodos' => $metodos,
            'total_pagado' => $this->calcularTotalPagado($costo->pagos),
            'saldo_pendiente' => $costo->valor - $this->calcularTotalPagado($costo->pagos),
            'categorias' => $categorias
        ]);
    }

    public function edit($id): View
    {
        $costo = Costo::findOrFail($id);
        $empleado = Empleado::where('cargo', '1')->get();
        $metodos = TiposDePago::all();
        $categorias = Categoria::all();
        return view('costo.edit', compact('costo', 'empleado', 'metodos', 'categorias'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'f_costos' => 'required|date',
                'id_tecnico' => 'required|integer|min:1',
                'descripcion' => 'required|string|max:500',
                'subcategoria' => 'required',
                'valor' => 'required|numeric|min:0',
                'pagos' => 'required|json'
            ]);

            $pagosJson = trim($validated['pagos'], '"\'');
            $pagos = json_decode($pagosJson, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($pagos)) {
                throw new \Exception("Formato de pagos inválido: " . json_last_error_msg());
            }

            $costo = Costo::findOrFail($id);
            $totalPagado = $this->calcularTotalPagado($pagos);
            $estatus = $this->determinarEstatus($validated['valor'], $pagos);

            $costo->update([
                'f_costos' => $validated['f_costos'],
                'id_tecnico' => $validated['id_tecnico'],
                'descripcion' => $validated['descripcion'],
                'subcategoria' => $validated['subcategoria'],
                'valor' => $validated['valor'],
                'pagos' => $pagos,
                'estatus' => $estatus
            ]);

            return Redirect::route('costos.index')
                ->with('success', 'Costo actualizado satisfactoriamente.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function calcularTotalPagado(array $pagos): float
    {
        if (empty($pagos)) return 0;
        return array_sum(array_column($pagos, 'monto'));
    }

    private function determinarEstatus(float $valor, array $pagos): string
    {
        $totalPagado = $this->calcularTotalPagado($pagos);
        
        if (abs($totalPagado - $valor) < 0.01) {
            return 'pagado';
        } elseif ($totalPagado > 0) {
            return 'parcialmente_pagado';
        }
        return 'pendiente';
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $costo = Costo::findOrFail($id);
            $costo->delete();
            return Redirect::route('costos.index')
                ->with('success', 'Costo eliminado satisfactoriamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Ocurrió un error al eliminar el costo']);
        }
    }
}