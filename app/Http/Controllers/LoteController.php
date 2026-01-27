<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Models\LoteDia;
use App\Models\LoteMerma;
use App\Models\LoteProducto;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoteController extends Controller
{
  public function index(): View
  {
    $lotes = Lote::query()
      ->withCount(['dias', 'productos'])
      ->orderByDesc('id')
      ->paginate(20);

    return view('lotes.index', compact('lotes'));
  }

  public function create(): View
  {
    $productos = Producto::query()
      ->orderBy('item')
      ->get(['id_producto', 'item', 'marca', 'kilos_promedio']);

    return view('lotes.create', compact('productos'));
  }

  public function store(Request $request): RedirectResponse
  {
    $validated = $request->validate([
      'nombre' => ['required', 'string', 'max:255'],
      'fecha_inicio' => ['nullable', 'date'],
      'dias' => ['nullable', 'array'],
      'dias.*' => ['integer', 'min:2', 'max:365'],
      'productos' => ['required', 'array', 'min:1'],
      'productos.*.id_producto' => ['required', 'integer', 'exists:productos,id_producto'],
      'productos.*.cantidad' => ['required', 'integer', 'min:0'],
      'productos.*.kilos_por_unidad' => ['required', 'numeric', 'min:0.001'],
    ]);

    $lote = null;

    $dias = collect($validated['dias'] ?? [])
      ->map(fn($d) => (int) $d)
      ->unique()
      ->sort()
      ->values();

    $productosInput = collect($validated['productos'])
      ->map(fn($p) => [
        'id_producto' => (int) $p['id_producto'],
        'cantidad' => (int) $p['cantidad'],
        'kilos_por_unidad' => (float) $p['kilos_por_unidad'],
      ]);

    // Evitar productos duplicados (si el usuario repite el producto, sumamos cantidades).
    // En caso de duplicados, se conserva el último kilos_por_unidad enviado para ese producto.
    $productosAgrupados = $productosInput
      ->groupBy('id_producto')
      ->map(fn($rows) => [
        'cantidad' => (int) $rows->sum('cantidad'),
        'kilos_por_unidad' => (float) ($rows->last()['kilos_por_unidad'] ?? 1),
      ]);

    DB::transaction(function () use ($validated, $dias, $productosAgrupados, &$lote) {
      $lote = Lote::create([
        'nombre' => $validated['nombre'],
        'fecha_inicio' => $validated['fecha_inicio'] ?? null,
      ]);

      foreach ($productosAgrupados as $idProducto => $row) {
        LoteProducto::create([
          'lote_id' => $lote->id,
          'id_producto' => $idProducto,
          'cantidad_inicial' => (int) $row['cantidad'],
          'kilos_por_unidad' => (float) ($row['kilos_por_unidad'] ?? 1),
        ]);
      }

      foreach ($dias as $diaNumero) {
        $dia = LoteDia::create([
          'lote_id' => $lote->id,
          'dia_numero' => $diaNumero,
        ]);

        // Inicializa la merma en 0 para cada producto del lote.
        foreach ($productosAgrupados as $idProducto => $row) {
          LoteMerma::create([
            'lote_dia_id' => $dia->id,
            'id_producto' => $idProducto,
            'cantidad_merma' => 0,
            'kilos_merma' => 0,
          ]);
        }
      }
    });

    return redirect()
      ->route('lotes.show', $lote)
      ->with('success', 'Lote creado correctamente.');
  }

  public function show(Lote $lote): View
  {
    $lote->load([
      'productos.producto',
      'dias.mermas.producto',
    ]);

    // Construye mapas para el render.
    $productos = $lote->productos->map(function (LoteProducto $lp) {
      return [
        'id_producto' => $lp->id_producto,
        'item' => $lp->producto?->item ?? ('Producto #' . $lp->id_producto),
        'marca' => $lp->producto?->marca ?? null,
        'cantidad_inicial' => $lp->cantidad_inicial,
        'kilos_por_unidad' => $lp->kilos_por_unidad ?? ($lp->producto?->kilos_promedio ?? 1),
      ];
    })->values();

    $kpuPorProducto = $productos
      ->keyBy('id_producto')
      ->map(fn($p) => (float) ($p['kilos_por_unidad'] ?? 1))
      ->all();

    // mermas[dayNum][productId] = kilos
    $mermas = [];
    foreach ($lote->dias as $dia) {
      foreach ($dia->mermas as $merma) {
        $kpu = (float) ($kpuPorProducto[$merma->id_producto] ?? 1);
        $kg = (float) ($merma->kilos_merma ?? 0);
        if ($kg <= 0 && (int) $merma->cantidad_merma > 0) {
          $kg = ((float) $merma->cantidad_merma) * $kpu;
        }
        $mermas[$dia->dia_numero][$merma->id_producto] = $kg;
      }
    }

    return view('lotes.show', compact('lote', 'productos', 'mermas'));
  }

  public function edit(Lote $lote): View
  {
    $lote->load(['productos', 'dias']);

    $productos = Producto::query()
      ->orderBy('item')
      ->get(['id_producto', 'item', 'marca', 'kilos_promedio']);

    $selectedDays = $lote->dias->pluck('dia_numero')->sort()->values();
    $initialProducts = $lote->productos
      ->map(fn(LoteProducto $lp) => [
        'id_producto' => $lp->id_producto,
        'cantidad' => $lp->cantidad_inicial,
        'kilos_por_unidad' => $lp->kilos_por_unidad,
      ])
      ->values();

    return view('lotes.edit', compact('lote', 'productos', 'selectedDays', 'initialProducts'));
  }

  public function update(Request $request, Lote $lote): RedirectResponse
  {
    $validated = $request->validate([
      'nombre' => ['required', 'string', 'max:255'],
      'fecha_inicio' => ['nullable', 'date'],
      'dias' => ['nullable', 'array'],
      'dias.*' => ['integer', 'min:2', 'max:365'],
      'productos' => ['required', 'array', 'min:1'],
      'productos.*.id_producto' => ['required', 'integer', 'exists:productos,id_producto'],
      'productos.*.cantidad' => ['required', 'integer', 'min:0'],
      'productos.*.kilos_por_unidad' => ['required', 'numeric', 'min:0.001'],
    ]);

    $diasSeleccionados = collect($validated['dias'] ?? [])
      ->map(fn($d) => (int) $d)
      ->unique()
      ->sort()
      ->values();

    $productosInput = collect($validated['productos'])
      ->map(fn($p) => [
        'id_producto' => (int) $p['id_producto'],
        'cantidad' => (int) $p['cantidad'],
        'kilos_por_unidad' => (float) $p['kilos_por_unidad'],
      ]);

    $productosAgrupados = $productosInput
      ->groupBy('id_producto')
      ->map(fn($rows) => [
        'cantidad' => (int) $rows->sum('cantidad'),
        'kilos_por_unidad' => (float) ($rows->last()['kilos_por_unidad'] ?? 1),
      ]);

    DB::transaction(function () use ($lote, $validated, $diasSeleccionados, $productosAgrupados) {
      $lote->update([
        'nombre' => $validated['nombre'],
        'fecha_inicio' => $validated['fecha_inicio'] ?? null,
      ]);

      $lote->load(['productos', 'dias']);

      $existingProducts = $lote->productos->keyBy('id_producto');
      $existingProductIds = $existingProducts->keys()->map(fn($v) => (int) $v)->all();
      $newProductIds = $productosAgrupados->keys()->map(fn($v) => (int) $v)->all();

      $toDelete = array_values(array_diff($existingProductIds, $newProductIds));
      $toAdd = array_values(array_diff($newProductIds, $existingProductIds));
      $toUpdate = array_values(array_intersect($existingProductIds, $newProductIds));

      if (!empty($toDelete)) {
        LoteProducto::query()
          ->where('lote_id', $lote->id)
          ->whereIn('id_producto', $toDelete)
          ->delete();

        $dayIds = $lote->dias->pluck('id')->all();
        if (!empty($dayIds)) {
          LoteMerma::query()
            ->whereIn('lote_dia_id', $dayIds)
            ->whereIn('id_producto', $toDelete)
            ->delete();
        }
      }

      foreach ($toUpdate as $idProducto) {
        /** @var \App\Models\LoteProducto $lp */
        $lp = $existingProducts->get($idProducto);
        $lp->update([
          'cantidad_inicial' => (int) ($productosAgrupados[$idProducto]['cantidad'] ?? 0),
          'kilos_por_unidad' => (float) ($productosAgrupados[$idProducto]['kilos_por_unidad'] ?? 1),
        ]);
      }

      foreach ($toAdd as $idProducto) {
        LoteProducto::create([
          'lote_id' => $lote->id,
          'id_producto' => $idProducto,
          'cantidad_inicial' => (int) ($productosAgrupados[$idProducto]['cantidad'] ?? 0),
          'kilos_por_unidad' => (float) ($productosAgrupados[$idProducto]['kilos_por_unidad'] ?? 1),
        ]);
      }

      // Días: solo agregamos los nuevos (no borramos para evitar perder historial).
      $existingDaysByNumber = $lote->dias->keyBy('dia_numero');
      $existingDayNumbers = $existingDaysByNumber->keys()->map(fn($v) => (int) $v)->all();
      $toAddDays = array_values(array_diff($diasSeleccionados->all(), $existingDayNumbers));

      // Reload productos actualizados para crear mermas
      $lote->load(['productos', 'dias']);
      $currentProductIds = $lote->productos->pluck('id_producto')->map(fn($v) => (int) $v)->all();

      foreach ($toAddDays as $diaNumero) {
        $dia = LoteDia::create([
          'lote_id' => $lote->id,
          'dia_numero' => (int) $diaNumero,
        ]);

        foreach ($currentProductIds as $idProducto) {
          LoteMerma::updateOrCreate(
            ['lote_dia_id' => $dia->id, 'id_producto' => $idProducto],
            ['cantidad_merma' => 0, 'kilos_merma' => 0]
          );
        }
      }

      // Si se agregaron productos, crear mermas (0) en los días existentes.
      if (!empty($toAdd)) {
        $dayIds = $lote->dias->pluck('id')->all();
        foreach ($dayIds as $dayId) {
          foreach ($toAdd as $idProducto) {
            LoteMerma::updateOrCreate(
              ['lote_dia_id' => $dayId, 'id_producto' => (int) $idProducto],
              ['cantidad_merma' => 0, 'kilos_merma' => 0]
            );
          }
        }
      }
    });

    return redirect()
      ->route('lotes.show', $lote)
      ->with('success', 'Lote actualizado correctamente.');
  }

  public function updateMermas(Request $request, Lote $lote): RedirectResponse
  {
    $validated = $request->validate([
      'mermas' => ['required', 'array'],
      'mermas.*' => ['array'],
    ]);

    $lote->load(['dias', 'productos']);

    $diasPorNumero = $lote->dias->keyBy('dia_numero');
    $idsProductos = $lote->productos->pluck('id_producto')->map(fn($v) => (int) $v)->all();
    $kpuPorProducto = $lote->productos
      ->keyBy('id_producto')
      ->map(fn(LoteProducto $lp) => (float) ($lp->kilos_por_unidad ?? 1))
      ->all();

    DB::transaction(function () use ($validated, $diasPorNumero, $idsProductos, $kpuPorProducto) {
      foreach ($validated['mermas'] as $diaNumero => $porProducto) {
        $diaNumero = (int) $diaNumero;
        $dia = $diasPorNumero->get($diaNumero);
        if (!$dia) {
          continue;
        }

        foreach ($porProducto as $idProducto => $cantidadMerma) {
          $idProducto = (int) $idProducto;
          if (!in_array($idProducto, $idsProductos, true)) {
            continue;
          }

          $kilosMerma = (float) $cantidadMerma;
          if ($kilosMerma < 0) {
            $kilosMerma = 0;
          }

          $kpu = (float) ($kpuPorProducto[$idProducto] ?? 1);
          $cantidadDerivada = 0;
          if ($kpu > 0) {
            $cantidadDerivada = (int) round($kilosMerma / $kpu);
            if ($cantidadDerivada < 0) {
              $cantidadDerivada = 0;
            }
          }

          LoteMerma::updateOrCreate(
            ['lote_dia_id' => $dia->id, 'id_producto' => $idProducto],
            ['cantidad_merma' => $cantidadDerivada, 'kilos_merma' => $kilosMerma]
          );
        }
      }
    });

    return redirect()
      ->route('lotes.show', $lote)
      ->with('success', 'Mermas actualizadas correctamente.');
  }
}
