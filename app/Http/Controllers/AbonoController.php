<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Empleado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AbonoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AbonoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $abonos = Abono::all();

        return view('abono.index', compact('abonos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $abono = new Abono();
        $empleados = Empleado::all();

        return view('abono.create', compact('abono', 'empleados'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AbonoRequest $request): RedirectResponse
    {
        Abono::create($request->validated());

        return Redirect::route('abonos.index')
            ->with('success', 'Abono creado satisfactoriamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $abono = Abono::find($id);

        return view('abono.show', compact('abono'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $abono = Abono::find($id);
        $empleados = Empleado::all();

        return view('abono.edit', compact('abono', 'empleados'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AbonoRequest $request, Abono $abono): RedirectResponse
    {
        $abono->update($request->validated());

        return Redirect::route('abonos.index')
            ->with('success', 'Abono actualizado satifactoriamente.');
    }

    public function destroy($id): RedirectResponse
    {
        $abono = Abono::findOrFail($id);
        $abono->delete();

        return Redirect::route('abonos.index')
            ->with('success', 'Abono eliminado satisfactoriamente.');
    }
}
