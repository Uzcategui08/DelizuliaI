<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\Inventario;
use App\Models\Almacene;
use App\Models\Cliente;
use App\Models\Empleado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\OrdenRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use App\Models\Producto;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Orden::with('cliente', 'empleado');
        
        // Si el usuario es limited, filtrar solo sus órdenes
        if (auth()->user()->hasRole('limited')) {
            $query->where('id_tecnico', auth()->id());
        }
        
        $ordens = $query->get();

        return view('orden.index', compact('ordens'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $inventario = Inventario::with('producto')->get();

        $almacenes = Almacene::all();

        $clientes = Cliente::all();

        $orden = new Orden();

        $empleado = Empleado::where('cargo', '1')->get();

        return view('orden.create', compact('orden', 'inventario', 'almacenes', 'clientes', 'empleado'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrdenRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
    
        $items = [];
        if ($request->has('items')) {
            foreach ($request->input('items') as $item) {
                if (!empty($item['descripcion']) && !empty($item['cantidad'])) {
                    $items[] = [
                        'descripcion' => $item['descripcion'],
                        'cantidad' => $item['cantidad'],
                    ];
                }
            }
        }
    
        $validatedData['items'] = !empty($items) ? json_encode($items) : null;
    
        Orden::create($validatedData);
    
        return Redirect::route('ordens.index')
            ->with('success', 'Órden creada satisfactoriamente.');
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $orden = Orden::findOrFail($id);

        $items = is_string($orden->items) ? json_decode($orden->items, true) : ($orden->items ?? []);

        $orden->items = array_map(function($item) {
            return [
                'descripcion' => $item['descripcion'] ?? 'Descripción no disponible',
                'cantidad' => $item['cantidad'] ?? 1
            ];
        }, $items);
    
        return view('orden.show', compact('orden'));
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $orden = Orden::findOrFail($id);
        $clientes = Cliente::all();
        $empleado = Empleado::where('cargo', '1')->get();

        $items = is_string($orden->items) ? json_decode($orden->items, true) : ($orden->items ?? []);

        $orden->items = array_map(function($item) {
            return [
                'descripcion' => $item['descripcion'] ?? '',
                'cantidad' => $item['cantidad'] ?? '0.00'
            ];
        }, $items);
    
        return view('orden.edit', [
            'orden' => $orden,
            'clientes' => $clientes,
            'empleado' => $empleado,
        ]);
    }
    

    public function update(OrdenRequest $request, Orden $orden): RedirectResponse
    {
        $validatedData = $request->validated();

        $items = array_filter($request->input('items', []), function($item) {
            return !empty($item['descripcion']) && !empty($item['cantidad']);
        });
    
        $items = array_map(function($item) {
            return [
                'descripcion' => $item['descripcion'] ?? '',
                'cantidad' => str_replace(',', '.', $item['cantidad'] ?? '0.00')
            ];
        }, $items);

        $validatedData['items'] = !empty($items) ? json_encode(array_values($items)) : null;

        $orden->update($validatedData);
    
        return redirect()->route('ordens.index')
               ->with('success', __('Órden actualizada satisfactoriamente.'));
    }

    public function destroy($id): RedirectResponse
    {
        Orden::find($id)->delete();

        return Redirect::route('ordens.index')
            ->with('success', 'Órden eliminada satisfactoriamente.');
    }

    public function obtenerProductos(Request $request)
    {
        $idAlmacen = $request->input('id_almacen');

        $productos = Inventario::where('id_almacen', $idAlmacen)
                               ->with('producto')
                               ->get()
                               ->map(function ($item) {
                                   return [
                                    'id_producto' => $item->id_producto,
                                    'item' => $item->producto->item,
                                    'cantidad' => $item->cantidad,
                                    'stock' => $item->cantidad,
                                    'precio_venta' => $item->producto ? $item->producto->precio : 0,
                                   ];
                               });

        return response()->json($productos);
    }

    public function generarPdf($id)
    {
        $orden = Orden::with(['cliente', 'empleado'])->findOrFail($id);

        $items = is_string($orden->items) ? json_decode($orden->items, true) : ($orden->items ?? []);

        $orden->items = is_array($items) ? array_map(function($item) {
            return [
                'descripcion' => $item['descripcion'] ?? 'Descripción no disponible',
                'cantidad' => (int)($item['cantidad'] ?? 1)
            ];
        }, $items) : [];

        $empresa = [
            'nombre' => config('app.name', 'Mi Empresa'),
            'direccion' => 'Dirección de la empresa',
            'telefono' => '123-456-7890',
            'email' => 'contacto@empresa.com',
            'cuit' => 'XX-XXXXXXXX-X'
        ];
        
        $pdf = Pdf::loadView('orden.pdf', compact('orden', 'empresa'));
        
        return $pdf->stream('orden_' . $orden->id_orden . '.pdf');
    }
}
