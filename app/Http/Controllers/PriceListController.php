<?php

namespace App\Http\Controllers;

use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PriceListController extends Controller
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

    $lists = PriceList::orderBy('code')->get();
    return view('price-lists.index', compact('lists'));
  }

  public function edit(PriceList $priceList): View
  {
    $this->ensureDefaults();

    $productos = Producto::orderBy('item')->get(['id_producto', 'item', 'marca']);
    $items = PriceListItem::where('price_list_id', $priceList->id)->get()->keyBy('id_producto');

    return view('price-lists.edit', compact('priceList', 'productos', 'items'));
  }

  public function update(Request $request, PriceList $priceList): RedirectResponse
  {
    $data = $request->validate([
      'items' => 'array',
      'items.*.price_per_kg' => 'nullable',
      'items.*.has_iva' => 'nullable',
    ]);

    $rows = $data['items'] ?? [];

    DB::transaction(function () use ($rows, $priceList) {
      foreach ($rows as $productId => $row) {
        $productId = (int) $productId;
        if ($productId <= 0) {
          continue;
        }

        $rawPrice = $row['price_per_kg'] ?? null;
        $rawPrice = is_string($rawPrice) ? str_replace(',', '.', $rawPrice) : $rawPrice;
        $price = (float) ($rawPrice ?? 0);
        $hasIva = !empty($row['has_iva']);

        PriceListItem::updateOrCreate(
          ['price_list_id' => $priceList->id, 'id_producto' => $productId],
          ['price_per_kg' => $price, 'has_iva' => $hasIva]
        );
      }
    });

    return redirect()->route('price-lists.edit', $priceList)
      ->with('success', 'Lista actualizada.');
  }

  public function itemsJson(PriceList $priceList)
  {
    $items = PriceListItem::where('price_list_id', $priceList->id)
      ->get(['id_producto', 'price_per_kg', 'has_iva']);

    return response()->json($items);
  }
}
