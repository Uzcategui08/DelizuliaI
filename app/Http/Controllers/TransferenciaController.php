<?php

namespace App\Http\Controllers;

use App\Models\Transferencia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TransferenciaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Producto;
use App\Models\Almacene;
use App\Models\Inventario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transferencias = Transferencia::with(['producto', 'almacenOrigen', 'almacenDestino', 'usuario'])->get();
        return view('transferencia.index', compact('transferencias'));
    }

    public function create()
    {
        $productos = Producto::all();
        $almacenes = Almacene::all();
        $transferencia = new Transferencia();
        return view('transferencia.create', compact('productos', 'almacenes', 'transferencia'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:productos,id_producto',
            'id_almacen_origen' => 'required|exists:almacenes,id_almacen',
            'id_almacen_destino' => 'required|exists:almacenes,id_almacen|different:id_almacen_origen',
            'cantidad' => 'required|integer|min:1',
            'observaciones' => 'nullable|string'
        ]);
    
        DB::transaction(function () use ($request) {
            $inventarioOrigen = Inventario::where('id_producto', $request->id_producto)
                ->where('id_almacen', $request->id_almacen_origen)
                ->firstOrFail();
    
            if ($inventarioOrigen->cantidad < $request->cantidad) {
                throw new \Exception('No hay suficiente stock en el almacén de origen');
            }
    
            $transferencia = Transferencia::create([
                'id_producto' => $request->id_producto,
                'id_almacen_origen' => $request->id_almacen_origen,
                'id_almacen_destino' => $request->id_almacen_destino,
                'cantidad' => $request->cantidad,
                'user_id' => Auth::id(),
                'observaciones' => $request->observaciones
            ]);
    
            $inventarioOrigen->decrement('cantidad', $request->cantidad);
    
// In your store() method, replace the updateOrCreate with:
$inventarioDestino = Inventario::where('id_producto', $request->id_producto)
    ->where('id_almacen', $request->id_almacen_destino)
    ->first();

if ($inventarioDestino) {
    $inventarioDestino->increment('cantidad', $request->cantidad);
} else {
    Inventario::create([
        'id_producto' => $request->id_producto,
        'id_almacen' => $request->id_almacen_destino,
        'cantidad' => $request->cantidad
    ]);
}
        });
    
        return redirect()->route('transferencias.index')->with('success', 'Transferencia realizada satisfactoriamente');
    }
    
    public function verificarStock(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id_producto',
            'almacen_id' => 'required|exists:almacenes,id_almacen',
            'cantidad' => 'nullable|integer|min:0'
        ]);
    
        $inventario = Inventario::where('id_producto', $request->producto_id)
                        ->where('id_almacen', $request->almacen_id)
                        ->first();
    
        $stockDisponible = $inventario ? $inventario->cantidad : 0;
    
        return response()->json([
            'suficiente' => $stockDisponible >= ($request->cantidad ?? 0),
            'stock' => $stockDisponible
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $transferencia = Transferencia::find($id);

        return view('transferencia.show', compact('transferencia'));
    }

    public function edit($id)
    {
        $transferencia = Transferencia::with(['producto', 'almacenOrigen', 'almacenDestino', 'usuario'])
                        ->findOrFail($id);
                        
        $productos = Producto::all();
        $almacenes = Almacene::all();
        
        $inventarioOrigen = Inventario::where('id_producto', $transferencia->id_producto)
                            ->where('id_almacen', $transferencia->id_almacen_origen)
                            ->first();
        
        if (!$inventarioOrigen) {
            return redirect()->back()->with('error', 'El producto no existe en el almacén de origen');
        }
        
        return view('transferencia.edit', compact('transferencia', 'productos', 'almacenes', 'inventarioOrigen'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_producto' => 'required|exists:productos,id_producto',
            'id_almacen_origen' => 'required|exists:almacenes,id_almacen',
            'id_almacen_destino' => 'required|exists:almacenes,id_almacen|different:id_almacen_origen',
            'cantidad' => 'required|integer|min:1',
            'observaciones' => 'nullable|string'
        ]);
    
        $transferencia = Transferencia::findOrFail($id);
        
        DB::transaction(function () use ($request, $transferencia) {
            $inventarioOrigen = Inventario::where('id_producto', $request->id_producto)
                ->where('id_almacen', $request->id_almacen_origen)
                ->firstOrFail();

            $diferencia = $request->cantidad - $transferencia->cantidad;
            
            if ($inventarioOrigen->cantidad < $diferencia) {
                throw new \Exception('No hay suficiente stock en el almacén de origen para esta modificación');
            }

            $this->revertirTransferencia($transferencia);

            $transferencia->update([
                'id_producto' => $request->id_producto,
                'id_almacen_origen' => $request->id_almacen_origen,
                'id_almacen_destino' => $request->id_almacen_destino,
                'cantidad' => $request->cantidad,
                'observaciones' => $request->observaciones,
                'user_id' => Auth::id()
            ]);

            $inventarioOrigen->decrement('cantidad', $request->cantidad);
            
            Inventario::updateOrCreate(
                [
                    'id_producto' => $request->id_producto,
                    'id_almacen' => $request->id_almacen_destino
                ],
                [
                    'cantidad' => DB::raw("cantidad + {$request->cantidad}")
                ]
            );
        });
    
        return redirect()->route('transferencias.index')->with('success', 'Transferencia actualizada con éxito');
    }
    
    protected function revertirTransferencia($transferencia)
    {
        Inventario::where('id_producto', $transferencia->id_producto)
            ->where('id_almacen', $transferencia->id_almacen_origen)
            ->increment('cantidad', $transferencia->cantidad);
        
        Inventario::where('id_producto', $transferencia->id_producto)
            ->where('id_almacen', $transferencia->id_almacen_destino)
            ->decrement('cantidad', $transferencia->cantidad);
    }

    public function destroy($id): RedirectResponse
    {
        DB::transaction(function () use ($id) {
            $transferencia = Transferencia::findOrFail($id);
            
            Inventario::where('id_producto', $transferencia->id_producto)
                ->where('id_almacen', $transferencia->id_almacen_origen)
                ->increment('cantidad', $transferencia->cantidad);

            Inventario::where('id_producto', $transferencia->id_producto)
                ->where('id_almacen', $transferencia->id_almacen_destino)
                ->decrement('cantidad', $transferencia->cantidad);

            $transferencia->delete();
        });
    
        return Redirect::route('transferencias.index')
            ->with('success', 'Transferencia revertida y eliminada satisfactoriamente.');
    }
}
