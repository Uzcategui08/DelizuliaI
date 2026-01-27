<?php

namespace App\Http\Controllers;

use App\Models\Almacene;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AlmaceneRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AlmaceneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $almacenes = Almacene::with(['inventarios.producto'])->paginate();

        return view('almacene.index', compact('almacenes'))
            ->with('i', ($request->input('page', 1) - 1) * $almacenes->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $almacene = new Almacene();

        return view('almacene.create', compact('almacene'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AlmaceneRequest $request): RedirectResponse
    {
        Almacene::create($request->validated());

        return Redirect::route('almacenes.index')
            ->with('success', 'Almacen creado satisfactoriamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $almacene = Almacene::find($id);

        return view('almacene.show', compact('almacene'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $almacene = Almacene::find($id);

        return view('almacene.edit', compact('almacene'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AlmaceneRequest $request, Almacene $almacene): RedirectResponse
    {
        $almacene->update($request->validated());

        return Redirect::route('almacenes.index')
            ->with('success', 'Almacen actualizado satisfactoriamente.');
    }

    public function destroy($id): RedirectResponse
    {
        Almacene::find($id)->delete();

        return Redirect::route('almacenes.index')
            ->with('success', 'Almacen eliminado satifactoriamente.');
    }
}
