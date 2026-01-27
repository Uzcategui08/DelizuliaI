<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\InventarioRequest;
use App\Models\Almacene;
use App\Models\Producto;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Exports\InventariosExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AjusteInventario;
use Illuminate\Support\Facades\Log;


class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function getData(Request $request)
    {
        $query = Inventario::with(['producto', 'almacene']);

        if ($request->has('almacen_id') && $request->almacen_id) {
            $query->where('id_almacen', $request->almacen_id);
        }

        // Ordenar de menor a mayor, los ceros al final
        $query->orderByRaw('CASE WHEN cantidad = 0 THEN 1 ELSE 0 END ASC')
            ->orderBy('cantidad', 'ASC');

        $inventarios = $query->get();

        return response()->json([
            'data' => $inventarios
        ]);
    }

    public function index(Request $request): View
    {
        // Ordenar primero por cantidad = 0 al final, luego por cantidad ascendente
        $inventarios1 = Inventario::with(['producto', 'almacene'])
            ->orderByRaw('CASE WHEN cantidad::integer = 0 THEN 1 ELSE 0 END ASC, cantidad::integer ASC')
            ->get();

        $almacenes = Almacene::all();

        return view('inventario.index', compact('inventarios1', 'almacenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Obtén todos los productos y almacenes
        $productos = Producto::all();
        $almacenes = Almacene::all();
        $inventario = new Inventario();

        // Pasa los datos a la vista
        return view('inventario.create', compact('inventario', 'productos', 'almacenes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InventarioRequest $request): RedirectResponse
    {
        Inventario::create($request->validated());

        return Redirect::route('inventarios.index')
            ->with('success', 'Inventario creado satisfactoriamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        // Carga el inventario con sus relaciones y maneja el caso de no encontrado
        $inventario = Inventario::with(['producto', 'almacene'])
            ->findOrFail($id);

        return view('inventario.show', compact('inventario'));
    }

    public function cargas(Request $request)
    {
        $query = AjusteInventario::with(['producto', 'almacene', 'user'])
            ->orderBy('created_at', 'desc');

        // Si el usuario está autenticado y es limited_user, filtrar solo sus cargas
        $user = auth()->user();
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('limited')) {
            $query->where('user_id', $user->id);
        }

        // Obtener todos los registros sin paginación
        $cargas = $query->get();

        return view('inventario.cargas', [
            'cargas' => $cargas,
            'i' => 0 // Como no hay paginación, el índice comienza en 0
        ]);
    }
    public function edit($id_inventario): View
    {
        $inventario = Inventario::with(['producto', 'almacene'])->findOrFail($id_inventario);
        return view('inventario.edit', compact('inventario'));
    }

    /**
     * Actualización básica
     */
    public function update(Request $request, $id_inventario)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:0',
        ]);

        $inventario = Inventario::findOrFail($id_inventario);
        $inventario->update(['cantidad' => $request->cantidad]);

        return redirect()->route('inventarios.index')
            ->with('success', 'Inventario actualizado correctamente');
    }

    /**
     * Edición con sistema de ajustes (nuevo formulario avanzado)
     */
    public function editarConAjustes($id_inventario): View
    {
        $inventario = Inventario::with(['producto', 'almacene'])->findOrFail($id_inventario);
        $productos = Producto::select('id_producto', 'item',)->get();
        $almacenes = Almacene::select('id_almacen', 'nombre')->get();

        return view('inventario.ajustar', compact('inventario', 'productos', 'almacenes'));
    }

    /**
     * Actualización con sistema de ajustes
     */
    public function actualizarConAjustes(Request $request, $id_inventario)
    {
        $request->validate([
            'tipo_ajuste' => 'required|in:compra,resta,ajuste,ajuste2',
            'cantidad_ajuste' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:500',
            'precio_llave' => 'required_if:tipo_ajuste,ajuste2|numeric|min:0',
            'cierre' => 'required_if:tipo_ajuste,ajuste2|boolean',
            'fecha_ajuste' => 'required|date',
        ]);

        $inventario = Inventario::findOrFail($id_inventario);
        $cantidadAnterior = $inventario->cantidad;
        $cantidadAjuste = $request->cantidad_ajuste;


        switch ($request->tipo_ajuste) {
            case 'compra':
            case 'ajuste':
                $nuevaCantidad = $cantidadAnterior + $cantidadAjuste;
                break;
            case 'resta':
            case 'ajuste2':
                $nuevaCantidad = $cantidadAnterior - $cantidadAjuste;
                if ($nuevaCantidad < 0) {
                    return back()->withErrors(['cantidad_ajuste' => 'La cantidad resultante no puede ser negativa']);
                }
                break;
        }

        // Actualizar inventario
        $inventario->update(['cantidad' => $nuevaCantidad]);

        // Registrar el ajuste
        AjusteInventario::create([
            'id_producto' => $inventario->id_producto,
            'id_almacen' => $inventario->id_almacen,
            'tipo_ajuste' => $request->tipo_ajuste,
            'cantidad_anterior' => $cantidadAnterior,
            'cantidad_nueva' => $nuevaCantidad,
            'descripcion' => $request->descripcion,
            'precio_llave' => $request->precio_llave,
            'cierre' => $request->tipo_ajuste === 'ajuste2' ? (bool)$request->cierre : false,
            'fecha_ajuste' => $request->fecha_ajuste,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('inventarios.index')
            ->with('success', 'Inventario ajustado correctamente. Se registró el movimiento.');
    }


    public function destroy($id): RedirectResponse
    {
        Inventario::find($id)->delete();

        return Redirect::route('inventarios.index')
            ->with('success', 'Inventario eliminado satifactoriamente.');
    }

    public function export()
    {
        return Excel::download(new InventariosExport, 'inventario.xlsx');
    }

    /**
     * Elimina un ajuste de inventario (carga/descarga) por id y revierte el cambio en el inventario
     */
    public function destroyAjuste($id)
    {
        $ajuste = \App\Models\AjusteInventario::findOrFail($id);
        $inventario = \App\Models\Inventario::where('id_producto', $ajuste->id_producto)
            ->where('id_almacen', $ajuste->id_almacen)
            ->first();

        if ($inventario) {
            // Revertir el ajuste usando la diferencia calculada
            $inventario->cantidad -= $ajuste->diferencia;
            if ($inventario->cantidad < 0) {
                $inventario->cantidad = 0;
            }
            $inventario->save();
        }

        $ajuste->delete();
        return redirect()->route('inventario.cargas')
            ->with('success', 'Ajuste de inventario eliminado correctamente y cantidad revertida.');
    }
}
