<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PrestamoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Categoria;

class PrestamoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $prestamos = Prestamo::with('empleado', 'categoria')->get();
        $categorias = Categoria::all();

        return view('prestamo.index', compact('prestamos', 'categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $prestamo = new Prestamo();
        $empleado = \App\Models\Empleado::all();
        $metodos = \App\Models\TiposDePago::all();
        $categorias = \App\Models\Categoria::all();

        return view('prestamo.create', compact('prestamo', 'empleado', 'metodos', 'categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'f_prestamo' => 'required|date',
                'id_empleado' => 'required|integer|min:1',
                'descripcion' => 'required|string|max:500',
                'subcategoria' => 'required',
                'valor' => 'required|numeric|min:0',
                'estatus' => 'required|in:pendiente,parcialmente pagado,pagado',
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

            $prestamo = new Prestamo([
                'f_prestamo' => $validated['f_prestamo'],
                'id_empleado' => $validated['id_empleado'],
                'descripcion' => $validated['descripcion'],
                'subcategoria' => $validated['subcategoria'],
                'valor' => $validated['valor'],
                'estatus' => $validated['estatus'],
                'pagos' => $pagosData
            ]);

            if (!$prestamo->save()) {
                throw new \Exception("No se pudo guardar el registro en la base de datos");
            }

            return Redirect::route('prestamos.index')
                ->with('success', 'Prestamo creado satisfactoriamente.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al guardar el prestamo: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $prestamo = Prestamo::with('empleado')->findOrFail($id);
        $categorias = \App\Models\Categoria::all();

        $pagos = is_string($prestamo->pagos) ? json_decode($prestamo->pagos, true) : ($prestamo->pagos ?? []);

        $metodos = \App\Models\TiposDePago::all()->pluck('name', 'id');

        return view('prestamo.show', [
            'prestamo' => $prestamo,
            'metodos' => $metodos,
            'total_pagado' => $this->calcularTotalPagado($pagos),
            'saldo_pendiente' => $prestamo->valor - $this->calcularTotalPagado($pagos),
            'categorias' => $categorias
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $prestamo = Prestamo::find($id);
        $empleado = \App\Models\Empleado::all();
        $metodos = \App\Models\TiposDePago::all();
        $categorias = \App\Models\Categoria::all();
        return view('prestamo.edit', compact('prestamo', 'empleado', 'metodos', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'f_prestamo' => 'required|date',
                'id_empleado' => 'required|integer|min:1',
                'descripcion' => 'required|string|max:500',
                'subcategoria' => 'required|string',
                'valor' => 'required|numeric|min:0',
                'pagos' => 'required|json'
            ]);

            $pagosJson = trim($validated['pagos'], '"\'');
            $pagos = json_decode($pagosJson, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($pagos)) {
                throw new \Exception("Formato de pagos inválido: " . json_last_error_msg());
            }

            $prestamo = Prestamo::findOrFail($id);
            $totalPagado = $this->calcularTotalPagado($pagos);
            $estatus = $this->determinarEstatus($validated['valor'], $pagos);

            $prestamo->update([
                'f_prestamo' => $validated['f_prestamo'],
                'id_empleado' => $validated['id_empleado'],
                'descripcion' => $validated['descripcion'],
                'subcategoria' => $validated['subcategoria'],
                'valor' => $validated['valor'],
                'pagos' => $pagos,
                'estatus' => $estatus
            ]);

            return Redirect::route('prestamos.index')
                ->with('success', 'Prestamo actualizado satisfactoriamente.');
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
            return 'parcialmente pagado';
        }
        return 'pendiente';
    }

    public function destroy($id): RedirectResponse
    {
        Prestamo::find($id)->delete();

        return Redirect::route('prestamos.index')
            ->with('success', 'Prestamo deleted successfully');
    }
}
