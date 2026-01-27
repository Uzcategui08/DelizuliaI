<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use App\Models\Presupuesto;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PresupuestoRequest;
use App\Models\Almacene;
use App\Models\Inventario;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PresupuestoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index(Request $request): View
    {
        $query = Presupuesto::with('cliente');
    
            if (!auth()->user()->hasAnyRole('admin')) {
                $query->where('user_id', auth()->id())
                      ->whereIn('estado', ['aprobado', 'rechazado']);
            }

        $presupuestos = $query->paginate(20);
            return view('presupuesto.index', compact('presupuestos'))
                ->with('i', ($request->input('page', 1) - 1) * $presupuestos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $clientes = Cliente::all();

        $inventario = Inventario::with('producto')->get();

        $almacenes = Almacene::all();

        $presupuesto = new Presupuesto();

        return view('presupuesto.create', compact('presupuesto', 'clientes', 'inventario', 'almacenes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PresupuestoRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
    
        // Agregar el ID del usuario autenticado automáticamente
        $validatedData['user_id'] = auth()->id();
    
        $items = [];
        foreach ($request->input('items', []) as $item) {
            if (!empty($item['descripcion'])) {
                $items[] = [
                    'descripcion' => $item['descripcion'],
                    'precio' => (float)($item['precio'] ?? 0),
                ];
            }
        }    
    
        $validatedData['items'] = json_encode($items);
    
        Presupuesto::create($validatedData);
    
        return Redirect::route('presupuestos.index')
            ->with('success', 'Presupuesto creado satisfactoriamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $presupuesto = Presupuesto::findOrFail($id);

        $items = is_string($presupuesto->items) ? json_decode($presupuesto->items, true) : ($presupuesto->items ?? []);

        $presupuesto->items = is_array($items) ? array_map(function($item) {
            return [
                'descripcion' => $item['descripcion'] ?? 'Descripción no disponible',
                'precio' => (float)($item['precio'] ?? 0)
            ];
        }, $items) : [];
    
        return view('presupuesto.show', compact('presupuesto'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $presupuesto = Presupuesto::findOrFail($id);
        $clientes = Cliente::all();
        $items = json_decode($presupuesto->items, true) ?? [];

        $itemsNormalizados = [];
        foreach ($items as $item) {
            $itemsNormalizados[] = [
                'descripcion' => $item['descripcion'] ?? '',
                'precio' => $item['precio'] ?? 0
            ];
        }
        
        $presupuesto->items = $itemsNormalizados;
        
        return view('presupuesto.edit', compact('presupuesto', 'clientes'));
    }
    

    /**
     * Update the specified resource in storage.
     */

    public function update(PresupuestoRequest $request, Presupuesto $presupuesto): RedirectResponse
    {
        $validatedData = $request->validated();

        $items = [];
        foreach ($request->input('items', []) as $item) {
            if (!empty($item['descripcion'])) {
                $items[] = [
                    'descripcion' => $item['descripcion'],
                    'precio' => (float)($item['precio'] ?? 0),
                ];
            }
        }

        $presupuesto->update([
            'id_cliente' => $validatedData['id_cliente'],
            'f_presupuesto' => $validatedData['f_presupuesto'],
            'validez' => $validatedData['validez'],
            'descuento' => $validatedData['descuento'],
            'iva' => $validatedData['iva'],
            'estado' => $validatedData['estado'],
            'items' => $items
        ]);
    
        return Redirect::route('presupuestos.index')
            ->with('success', 'Presupuesto actualizado satisfactoriamente.');
    }
    
    

    public function destroy($id): RedirectResponse
    {
        Presupuesto::find($id)->delete();

        return Redirect::route('presupuestos.index')
            ->with('success', 'Presupuesto eliminado satisfactoriamente.');
    }

    public function obtenerProductos(Request $request)
    {
        $idAlmacen = $request->input('id_almacen');

        $productos = Inventario::with('producto')
            ->where('id_almacen', $idAlmacen)
            ->get()
            ->map(function ($item) {
                return [
                    'id_producto' => $item->id_producto,
                    'item' => $item->producto->item,
                    'cantidad' => $item->cantidad,
                    'precio' => $item->producto ? $item->producto->precio : 0,
                ];
            });

        return response()->json($productos);
    }

    public function generarPdf($id)
    {
        $presupuesto = Presupuesto::findOrFail($id);

        $items = is_string($presupuesto->items) ? json_decode($presupuesto->items, true) : ($presupuesto->items ?? []);
        
        $presupuesto->items = is_array($items) ? array_map(function($item) {
            return [
                'descripcion' => $item['descripcion'] ?? 'Descripción no disponible',
                'precio' => (float)($item['precio'] ?? 0)
            ];
        }, $items) : [];
        
        $pdf = Pdf::loadView('presupuesto.pdf', compact('presupuesto'));
        
        return $pdf->stream('presupuesto_' . $presupuesto->id_presupuesto . '.pdf');
    }

    public function generatePdf($id)
    {
        $presupuesto = Presupuesto::findOrFail($id);

        $items = is_string($presupuesto->items) ? json_decode($presupuesto->items, true) : ($presupuesto->items ?? []);
        
        $presupuesto->items = is_array($items) ? array_map(function($item) {
            return [
                'descripcion' => $item['descripcion'] ?? 'Descripción no disponible',
                'precio' => (float)($item['precio'] ?? 0)
            ];
        }, $items) : [];
        
        $pdf = Pdf::loadView('presupuesto.eng', compact('presupuesto'));
        
        return $pdf->stream('budget_' . $presupuesto->id_presupuesto . '.pdf');
    }
}
