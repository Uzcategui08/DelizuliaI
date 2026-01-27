<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProductoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ProductoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:crear_', only: ['create', 'store','edit']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $productos = Producto::all();

        return view('producto.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $producto = new Producto();

        return view('producto.create', compact('producto'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductoRequest $request): RedirectResponse
    {
        Producto::create($request->validated());

        return Redirect::route('productos.index')
            ->with('success', 'Producto creado satisfactoriamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $producto = Producto::find($id);

        return view('producto.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id_producto): View
    {
        $producto = Producto::find($id_producto);

        return view('producto.edit', compact('producto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated());

        return Redirect::route('productos.index')
            ->with('success', 'Producto actualizado satisfactoriamente.');
    }

    public function destroy($id): RedirectResponse
    {
        Producto::find($id)->delete();

        return Redirect::route('productos.index')
            ->with('success', 'Producto eliminado satisfactoriamente.');
    }
}
