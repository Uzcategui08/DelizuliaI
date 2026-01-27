<?php

namespace App\Http\Controllers;

use App\Models\Almacene;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\SimpleSale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SimpleSaleController extends Controller
{
  public function index(): View
  {
    $ventas = SimpleSale::with(['cliente', 'empleado'])->orderByDesc('fecha_h')->orderByDesc('id')->paginate(20);
    return view('simple-sales.index', compact('ventas'));
  }
  public function create(): View
  {
    return view('simple-sales.create', [
      'clientes' => Cliente::orderBy('nombre')->get(),
      // Mostrar todos los empleados para seleccionar vendedor (sin filtrar por cargo)
      'empleados' => Empleado::orderBy('nombre')->get(),
      'almacenes' => Almacene::orderBy('nombre')->get(),
      'productos' => Producto::orderBy('item')->get(['id_producto', 'item', 'precio', 'kilos_promedio'])
    ]);
  }

  public function store(Request $request): RedirectResponse
  {
    $data = $request->validate([
      'fecha_h' => 'required|date',
      'id_cliente' => 'nullable|integer|exists:clientes,id_cliente',
      'id_empleado' => 'nullable|integer|exists:empleados,id_empleado',
      'zona' => 'nullable|string|max:255',
      'descuento' => 'nullable|numeric|min:0',
      'items' => 'required|string',
    ]);

    $items = json_decode($data['items'], true) ?: [];
    if (!is_array($items)) {
      $items = [];
    }

    DB::beginTransaction();
    try {
      $lineas = [];
      $totalBruto = 0.0;

      foreach ($items as $i => $it) {
        $productoId = (int)($it['producto'] ?? 0);
        $almacenId = (int)($it['almacen'] ?? 0);
        $cantidad = (float)($it['cantidad'] ?? 0);
        $precio = (float)($it['precio'] ?? 0);
        $kilos = (float)($it['kilos'] ?? 1);
        $incluye = (bool)($it['precio_incluye_kilos'] ?? false);
        if ($productoId <= 0 || $almacenId <= 0 || $cantidad <= 0) {
          continue;
        }

        $inventario = Inventario::where('id_producto', $productoId)
          ->where('id_almacen', $almacenId)
          ->lockForUpdate()
          ->first();
        if (!$inventario) {
          throw new \Exception("Producto $productoId no existe en almacén $almacenId");
        }
        if ($inventario->cantidad < $cantidad) {
          throw new \Exception("Stock insuficiente para producto $productoId en almacén $almacenId");
        }

        $inventario->cantidad -= $cantidad;
        $inventario->save();

        $totalBruto += $incluye ? ($cantidad * $precio) : ($cantidad * $precio * $kilos);

        $lineas[] = [
          'producto' => $productoId,
          'almacen' => $almacenId,
          'cantidad' => $cantidad,
          'precio' => $precio,
          'kilos' => $kilos,
          'precio_incluye_kilos' => $incluye,
        ];
      }

      $descuento = (float)($data['descuento'] ?? 0);
      $totalNeto = max($totalBruto - $descuento, 0);

      $venta = SimpleSale::create([
        'fecha_h' => $data['fecha_h'],
        'id_cliente' => $data['id_cliente'] ?? null,
        'id_empleado' => $data['id_empleado'] ?? null,
        'zona' => $data['zona'] ?? null,
        'items' => json_encode($lineas),
        'total_bruto' => $totalBruto,
        'descuento' => $descuento,
        'total_neto' => $totalNeto,
      ]);

      DB::commit();
      return redirect()->route('simple-sales.create')->with('success', 'Venta guardada (#' . $venta->id . ')');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Error en SimpleSale', ['error' => $e->getMessage()]);
      return back()->withInput()->with('error', $e->getMessage());
    }
  }

  public function show(SimpleSale $simpleSale): View
  {
    $lineas = is_array($simpleSale->items) ? $simpleSale->items : (json_decode($simpleSale->items, true) ?: []);
    return view('simple-sales.show', [
      'venta' => $simpleSale,
      'lineas' => $lineas,
    ]);
  }

  public function edit(SimpleSale $simpleSale): View
  {
    return view('simple-sales.edit', [
      'venta' => $simpleSale,
      'clientes' => Cliente::orderBy('nombre')->get(),
      // Mostrar todos los empleados para seleccionar vendedor (sin filtrar por cargo)
      'empleados' => Empleado::orderBy('nombre')->get(),
      'almacenes' => Almacene::orderBy('nombre')->get(),
      'productos' => Producto::orderBy('item')->get(['id_producto', 'item', 'precio'])
    ]);
  }

  public function update(Request $request, SimpleSale $simpleSale): RedirectResponse
  {
    $data = $request->validate([
      'fecha_h' => 'required|date',
      'id_cliente' => 'nullable|integer|exists:clientes,id_cliente',
      'id_empleado' => 'nullable|integer|exists:empleados,id_empleado',
      'zona' => 'nullable|string|max:255',
      'descuento' => 'nullable|numeric|min:0',
      'items' => 'required|string',
    ]);

    $nuevos = json_decode($data['items'], true) ?: [];
    if (!is_array($nuevos)) {
      $nuevos = [];
    }

    DB::beginTransaction();
    try {
      // 1) Reponer inventario de líneas antiguas
      $antiguos = is_array($simpleSale->items) ? $simpleSale->items : (json_decode($simpleSale->items, true) ?: []);
      foreach ($antiguos as $it) {
        $productoId = (int)($it['producto'] ?? 0);
        $almacenId = (int)($it['almacen'] ?? 0);
        $cantidad = (float)($it['cantidad'] ?? 0);
        if ($productoId > 0 && $almacenId > 0 && $cantidad > 0) {
          $inv = Inventario::where('id_producto', $productoId)->where('id_almacen', $almacenId)->lockForUpdate()->first();
          if ($inv) {
            $inv->cantidad += $cantidad;
            $inv->save();
          }
        }
      }

      // 2) Validar y descontar nuevos
      $lineas = [];
      $totalBruto = 0.0;
      foreach ($nuevos as $it) {
        $productoId = (int)($it['producto'] ?? 0);
        $almacenId = (int)($it['almacen'] ?? 0);
        $cantidad = (float)($it['cantidad'] ?? 0);
        $precio = (float)($it['precio'] ?? 0);
        $kilos = (float)($it['kilos'] ?? 1);
        $incluye = (bool)($it['precio_incluye_kilos'] ?? false);
        if ($productoId <= 0 || $almacenId <= 0 || $cantidad <= 0) {
          continue;
        }

        $inv = Inventario::where('id_producto', $productoId)->where('id_almacen', $almacenId)->lockForUpdate()->first();
        if (!$inv) {
          throw new \Exception("Producto $productoId no existe en almacén $almacenId");
        }
        if ($inv->cantidad < $cantidad) {
          throw new \Exception("Stock insuficiente para producto $productoId en almacén $almacenId");
        }
        $inv->cantidad -= $cantidad;
        $inv->save();

        $totalBruto += $incluye ? ($cantidad * $precio) : ($cantidad * $precio * $kilos);
        $lineas[] = compact('productoId', 'almacenId', 'cantidad', 'precio', 'kilos', 'incluye');
        $lineas[count($lineas) - 1]['producto'] = $productoId;
        $lineas[count($lineas) - 1]['almacen'] = $almacenId;
        $lineas[count($lineas) - 1]['precio_incluye_kilos'] = $incluye;
      }

      $descuento = (float)($data['descuento'] ?? 0);
      $totalNeto = max($totalBruto - $descuento, 0);

      $simpleSale->update([
        'fecha_h' => $data['fecha_h'],
        'id_cliente' => $data['id_cliente'] ?? null,
        'id_empleado' => $data['id_empleado'] ?? null,
        'zona' => $data['zona'] ?? null,
        'items' => json_encode($lineas),
        'total_bruto' => $totalBruto,
        'descuento' => $descuento,
        'total_neto' => $totalNeto,
      ]);

      DB::commit();
      return redirect()->route('simple-sales.index')->with('success', 'Venta actualizada');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Error actualizando SimpleSale', ['error' => $e->getMessage()]);
      return back()->withInput()->with('error', $e->getMessage());
    }
  }
}
