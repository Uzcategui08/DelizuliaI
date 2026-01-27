<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Tnomina;
use App\Models\Costo;
use App\Models\Gasto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\EmpleadoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $empleados = Empleado::paginate();

        return view('empleado.index', compact('empleados'))
            ->with('i', ($request->input('page', 1) - 1) * $empleados->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $empleado = new Empleado();

        return view('empleado.create', compact('empleado'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmpleadoRequest $request): RedirectResponse
    {
        Empleado::create($request->validated());

        return Redirect::route('empleados.index')
            ->with('success', 'Empleado creado satisfactoriamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $empleado = Empleado::find($id);

        return view('empleado.show', compact('empleado'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $empleado = Empleado::find($id);

        return view('empleado.edit', compact('empleado'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmpleadoRequest $request, Empleado $empleado): RedirectResponse
    {
        $validated = $request->validated();
        
        if ($empleado->tipo_pago !== $validated['tipo_pago']) {
            $validated['salario_base'] = 0;
        } else {
            $validated['salario_base'] = $request->input('salario_base', 0);
        }
        
        $empleado->update($validated);

        return Redirect::route('empleados.index')
            ->with('success', 'Empleado actualizado satisfactoriamente.');
    }

    public function destroy($id): RedirectResponse
    {
        Empleado::find($id)->delete();

        return Redirect::route('empleados.index')
            ->with('success', 'Empleado eliminado satisfactoriamente.');
    }

    public function getDatosPago($id)
    {
        $empleado = Empleado::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'tipo_pago' => $empleado->tipo_pago,
            'sueldo_base' => $empleado->salario_base
        ]);
    }
}
