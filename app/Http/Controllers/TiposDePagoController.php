<?php

namespace App\Http\Controllers;

use App\Models\TiposDePago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TiposDePagoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TiposDePagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $tiposDePagos = TiposDePago::paginate();

        return view('tipos-de-pago.index', compact('tiposDePagos'))
            ->with('i', ($request->input('page', 1) - 1) * $tiposDePagos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $tiposDePago = new TiposDePago();

        return view('tipos-de-pago.create', compact('tiposDePago'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TiposDePagoRequest $request): RedirectResponse
    {
        TiposDePago::create($request->validated());

        return Redirect::route('tipos-de-pagos.index')
            ->with('success', 'Tipo creado satisfactoriamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $tiposDePago = TiposDePago::find($id);

        return view('tipos-de-pago.show', compact('tiposDePago'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $tiposDePago = TiposDePago::find($id);

        return view('tipos-de-pago.edit', compact('tiposDePago'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TiposDePagoRequest $request, TiposDePago $tiposDePago): RedirectResponse
    {
        $tiposDePago->update($request->validated());

        return Redirect::route('tipos-de-pagos.index')
            ->with('success', 'Tipo editado satifactoriamente.');
    }

    public function destroy($id): RedirectResponse
    {
        TiposDePago::find($id)->delete();

        return Redirect::route('tipos-de-pagos.index')
            ->with('success', 'Tipo eliminado satifactoriamente.');
    }
}
