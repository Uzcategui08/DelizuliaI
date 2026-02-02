<?php

namespace App\Http\Controllers;

use App\Models\PriceInvoice;
use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\Producto;
use App\Services\ExchangeRateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PriceInvoiceController extends Controller
{
  private function ensureDefaults(): void
  {
    PriceList::firstOrCreate(['code' => 'A'], ['name' => 'Lista A']);
    PriceList::firstOrCreate(['code' => 'B'], ['name' => 'Lista B']);
    PriceList::firstOrCreate(['code' => 'C'], ['name' => 'Lista C']);
  }

  public function index(): View
  {
    $this->ensureDefaults();

    $invoices = PriceInvoice::with(['priceList'])
      ->orderByDesc('fecha')
      ->orderByDesc('id')
      ->paginate(20);

    return view('price-invoices.index', compact('invoices'));
  }

  public function create(): View
  {
    $this->ensureDefaults();

    $listas = PriceList::orderBy('code')->get();
    $productos = Producto::orderBy('item')->get(['id_producto', 'item']);

    $fecha = now()->toDateString();
    $exchangeRateService = new ExchangeRateService();
    $exchangeRate = $exchangeRateService->getOrCreateFromInvoices($fecha) ?? $exchangeRateService->getForDate($fecha);
    $tasaDefault = $exchangeRate?->rate ?? 0;
    $tasaLocked = $exchangeRate !== null;

    return view('price-invoices.create', [
      'listas' => $listas,
      'productos' => $productos,
      'tasaDefault' => $tasaDefault,
      'tasaLocked' => $tasaLocked,
      'tasaDate' => $fecha,
      'ivaRate' => 0.16,
    ]);
  }

  public function store(Request $request): RedirectResponse
  {
    $data = $request->validate([
      'fecha' => 'required|date',
      'price_list_id' => 'required|integer|exists:price_lists,id',
      'tasa' => 'nullable',
      'items' => 'required|string',
    ]);

    $exchangeRateService = new ExchangeRateService();

    // Determina la tasa a usar para los cálculos.
    // Si ya existe tasa guardada para esa fecha, se usa esa (y se ignora lo que venga del formulario).
    // Si no existe, se usa la tasa del formulario solo para cálculo (se persistirá al guardar la factura).
    $exchangeRate = $exchangeRateService->getOrCreateFromInvoices($data['fecha']) ?? $exchangeRateService->getForDate($data['fecha']);
    if ($exchangeRate) {
      $tasa = (float) $exchangeRate->rate;
    } else {
      $tasaRaw = is_string($data['tasa'] ?? null) ? str_replace(',', '.', (string) $data['tasa']) : ($data['tasa'] ?? null);
      $tasa = (float) $tasaRaw;
      if ($tasa <= 0) {
        return back()->withInput()->with('error', 'La tasa debe ser mayor a 0.');
      }
    }

    $ivaRate = 0.16;

    $itemsIn = json_decode($data['items'], true) ?: [];
    if (!is_array($itemsIn)) {
      $itemsIn = [];
    }

    $priceItems = PriceListItem::where('price_list_id', (int) $data['price_list_id'])->get()->keyBy('id_producto');

    $lineas = [];
    $baseTotal = 0.0;
    $ivaTotal = 0.0;

    foreach ($itemsIn as $it) {
      $productoId = (int) ($it['producto'] ?? 0);
      $kgRaw = $it['kg'] ?? 0;
      $kgRaw = is_string($kgRaw) ? str_replace(',', '.', $kgRaw) : $kgRaw;
      $kg = (float) $kgRaw;
      if ($productoId <= 0 || $kg <= 0) {
        continue;
      }

      $pli = $priceItems->get($productoId);
      $pricePerKg = (float) ($pli?->price_per_kg ?? 0);
      $hasIva = (bool) ($pli?->has_iva ?? false);

      $unitBs = $pricePerKg * $tasa;
      $base = $kg * $unitBs;
      $iva = $hasIva ? ($base * $ivaRate) : 0.0;

      $baseTotal += $base;
      $ivaTotal += $iva;

      $lineas[] = [
        'producto' => $productoId,
        'kg' => $kg,
        'price_per_kg' => $pricePerKg,
        'has_iva' => $hasIva,
        'unit_bs' => $unitBs,
        'base_bs' => $base,
        'iva_bs' => $iva,
        'total_bs' => $base + $iva,
      ];
    }

    if (count($lineas) === 0) {
      return back()->withInput()->with('error', 'Agrega al menos un producto con Kg mayor a 0.');
    }

    $total = $baseTotal + $ivaTotal;

    DB::beginTransaction();
    try {
      // Solo persistimos la tasa del día si la factura se va a guardar.
      // Si otro usuario la creó en el ínterin, se respeta la ya guardada.
      try {
        $tasa = $exchangeRateService->resolveRateForDate($data['fecha'], $data['tasa'] ?? (string) $tasa);
      } catch (\InvalidArgumentException $e) {
        return back()->withInput()->with('error', $e->getMessage());
      }

      $invoice = PriceInvoice::create([
        'fecha' => $data['fecha'],
        'price_list_id' => (int) $data['price_list_id'],
        'tasa' => $tasa,
        'iva_rate' => $ivaRate,
        'items' => json_encode($lineas),
        'base_total' => $baseTotal,
        'iva_total' => $ivaTotal,
        'total' => $total,
      ]);

      DB::commit();
      return redirect()->route('price-invoices.show', $invoice)->with('success', 'Factura guardada (#' . $invoice->id . ')');
    } catch (\Throwable $e) {
      DB::rollBack();
      Log::error('Error guardando factura por Kg', ['error' => $e->getMessage()]);
      return back()->withInput()->with('error', $e->getMessage());
    }
  }

  public function show(PriceInvoice $priceInvoice): View
  {
    $priceInvoice->load(['priceList']);

    $lineas = is_array($priceInvoice->items) ? $priceInvoice->items : (json_decode($priceInvoice->items, true) ?: []);

    $productos = Producto::whereIn('id_producto', collect($lineas)->pluck('producto')->filter()->unique()->values()->all())
      ->get(['id_producto', 'item'])
      ->keyBy('id_producto');

    return view('price-invoices.show', [
      'invoice' => $priceInvoice,
      'lineas' => $lineas,
      'productos' => $productos,
    ]);
  }
}
