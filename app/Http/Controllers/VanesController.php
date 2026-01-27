<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gasto;
use App\Models\Costo;
use App\Models\RegistroV;
use App\Models\Empleado;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Exports\VanesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class VanesController extends Controller
{

    public function index(Request $request)
    {
        $vanGrande = 'Van Grande-Pulga';
        $vanPequena = 'Van Pequeña-Pulga';

        // Obtener fechas de filtro
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Si no hay fechas, usar mes/año actual como fallback
        if (!$startDate || !$endDate) {
            $monthSelected = $request->input('month', Carbon::now()->month);
            $yearSelected = $request->input('year', Carbon::now()->year);
            $startDate = Carbon::create($yearSelected, $monthSelected, 1)->format('Y-m-d');
            $endDate = Carbon::create($yearSelected, $monthSelected, 1)->endOfMonth()->format('Y-m-d');
        }

        // Procesar ventas por van con filtro de fechas
        $ventasVanGrande = $this->procesarVentasPorVan($vanGrande, $startDate, $endDate);
        $ventasVanPequena = $this->procesarVentasPorVan($vanPequena, $startDate, $endDate);

        // Obtener IDs de gastos y costos asociados a cada van
        $gastoIdsGrande = $ventasVanGrande['ventas']->flatMap(function ($venta) {
            $gastos = $venta->gastos;
            if (is_string($gastos)) {
                $gastos = json_decode($gastos, true);
            }
            return collect($gastos ?: []);
        })->unique()->values();

        $gastoIdsPequena = $ventasVanPequena['ventas']->flatMap(function ($venta) {
            $gastos = $venta->gastos;
            if (is_string($gastos)) {
                $gastos = json_decode($gastos, true);
            }
            return collect($gastos ?: []);
        })->unique()->values();

        $costoIdsGrande = $ventasVanGrande['ventas']->flatMap(function ($venta) {
            $costos = $venta->costos;
            if (is_string($costos)) {
                $costos = json_decode($costos, true);
            }
            return collect($costos ?: []);
        })->unique()->values();

        $costoIdsPequena = $ventasVanPequena['ventas']->flatMap(function ($venta) {
            $costos = $venta->costos;
            if (is_string($costos)) {
                $costos = json_decode($costos, true);
            }
            return collect($costos ?: []);
        })->unique()->values();

        // Consultar solo los gastos y costos asociados
        $gastosVanGrande = Gasto::whereIn('id_gastos', $gastoIdsGrande)->get();
        $gastosVanPequena = Gasto::whereIn('id_gastos', $gastoIdsPequena)->get();
        $costosVanGrande = Costo::whereIn('id_costos', $costoIdsGrande)->get();
        $costosVanPequena = Costo::whereIn('id_costos', $costoIdsPequena)->get();

        // Obtener porcentajes de cerrajero
        $porcentajeCerrajeroGrande = $ventasVanGrande['ventas']->sum('porcentaje_c');
        $porcentajeCerrajeroPequena = $ventasVanPequena['ventas']->sum('porcentaje_c');

        $llavesPorTecnico = $this->procesarLlavesPorTecnico(
            $startDate,
            $endDate,
            [$vanGrande, $vanPequena]
        );

        // Calcular totales
        $totales = [
            'porcentajeCerrajeroGrande' => $porcentajeCerrajeroGrande,
            'porcentajeCerrajeroPequena' => $porcentajeCerrajeroPequena,
            'ventasGrande' => $ventasVanGrande['ventas']->sum('valor_v'),
            'ventasPequena' => $ventasVanPequena['ventas']->sum('valor_v'),
            'gastosGrande' => $gastosVanGrande->sum('valor'),
            'gastosPequena' => $gastosVanPequena->sum('valor'),
            'costosGrande' => $costosVanGrande->sum('valor'),
            'costosPequena' => $costosVanPequena->sum('valor'),
            'itemsGrande' => $ventasVanGrande['items']->sum('total_cantidad'),
            'itemsPequena' => $ventasVanPequena['items']->sum('total_cantidad'),
            'valorItemsGrande' => $ventasVanGrande['items']->sum('total_valor'),
            'valorItemsPequena' => $ventasVanPequena['items']->sum('total_valor'),
            'totalLlaves' => $llavesPorTecnico->sum('total_llaves'),
            'totalValorLlaves' => $llavesPorTecnico->sum('total_valor'),

            'utilidadGrande' =>
            $ventasVanGrande['ventas']->sum('valor_v')
                - $costosVanGrande->sum('valor')
                - $porcentajeCerrajeroGrande
                - $gastosVanGrande->sum('valor')
                - $ventasVanGrande['items']->sum('total_valor'),

            'utilidadPequena' =>
            $ventasVanPequena['ventas']->sum('valor_v')
                - $costosVanPequena->sum('valor')
                - $porcentajeCerrajeroPequena
                - $gastosVanPequena->sum('valor')
                - $ventasVanPequena['items']->sum('total_valor'),
        ];

        return view('estadisticas.vanes', compact(
            'vanGrande',
            'vanPequena',
            'ventasVanGrande',
            'ventasVanPequena',
            'gastosVanGrande',
            'gastosVanPequena',
            'costosVanGrande',
            'costosVanPequena',
            'llavesPorTecnico',
            'porcentajeCerrajeroGrande',
            'porcentajeCerrajeroPequena',
            'totales',
            'startDate',
            'endDate'
        ));
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getReporteData($request);
        return Excel::download(new VanesExport($data, $data['startDate'], $data['endDate']), 'reporte_vanes.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReporteData($request);
        $pdf = Pdf::loadView('estadisticas.vanes_export', [
            'vanGrande' => $data['vanGrande'],
            'vanPequena' => $data['vanPequena'],
            'ventasVanGrande' => $data['ventasVanGrande'],
            'ventasVanPequena' => $data['ventasVanPequena'],
            'gastosVanGrande' => $data['gastosVanGrande'],
            'gastosVanPequena' => $data['gastosVanPequena'],
            'costosVanGrande' => $data['costosVanGrande'],
            'costosVanPequena' => $data['costosVanPequena'],
            'llavesPorTecnico' => $data['llavesPorTecnico'],
            'porcentajeCerrajeroGrande' => $data['porcentajeCerrajeroGrande'],
            'porcentajeCerrajeroPequena' => $data['porcentajeCerrajeroPequena'],
            'totales' => $data['totales'],
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
        ]);
        return $pdf->download('reporte_vanes.pdf');
    }

    private function getReporteData(Request $request)
    {
        $vanGrande = 'Van Grande-Pulga';
        $vanPequena = 'Van Pequeña-Pulga';
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if (!$startDate || !$endDate) {
            $monthSelected = $request->input('month', Carbon::now()->month);
            $yearSelected = $request->input('year', Carbon::now()->year);
            $startDate = Carbon::create($yearSelected, $monthSelected, 1)->format('Y-m-d');
            $endDate = Carbon::create($yearSelected, $monthSelected, 1)->endOfMonth()->format('Y-m-d');
        }
        $ventasVanGrande = $this->procesarVentasPorVan($vanGrande, $startDate, $endDate);
        $ventasVanPequena = $this->procesarVentasPorVan($vanPequena, $startDate, $endDate);
        $gastoIdsGrande = $ventasVanGrande['ventas']->flatMap(function ($venta) {
            $gastos = $venta->gastos;
            if (is_string($gastos)) {
                $gastos = json_decode($gastos, true);
            }
            return collect($gastos ?: []);
        })->unique()->values();
        $gastoIdsPequena = $ventasVanPequena['ventas']->flatMap(function ($venta) {
            $gastos = $venta->gastos;
            if (is_string($gastos)) {
                $gastos = json_decode($gastos, true);
            }
            return collect($gastos ?: []);
        })->unique()->values();
        $costoIdsGrande = $ventasVanGrande['ventas']->flatMap(function ($venta) {
            $costos = $venta->costos;
            if (is_string($costos)) {
                $costos = json_decode($costos, true);
            }
            return collect($costos ?: []);
        })->unique()->values();
        $costoIdsPequena = $ventasVanPequena['ventas']->flatMap(function ($venta) {
            $costos = $venta->costos;
            if (is_string($costos)) {
                $costos = json_decode($costos, true);
            }
            return collect($costos ?: []);
        })->unique()->values();
        $gastosVanGrande = Gasto::whereIn('id_gastos', $gastoIdsGrande)->get();
        $gastosVanPequena = Gasto::whereIn('id_gastos', $gastoIdsPequena)->get();
        $costosVanGrande = Costo::whereIn('id_costos', $costoIdsGrande)->get();
        $costosVanPequena = Costo::whereIn('id_costos', $costoIdsPequena)->get();
        $porcentajeCerrajeroGrande = $ventasVanGrande['ventas']->sum('porcentaje_c');
        $porcentajeCerrajeroPequena = $ventasVanPequena['ventas']->sum('porcentaje_c');
        $llavesPorTecnico = $this->procesarLlavesPorTecnico($startDate, $endDate, [$vanGrande, $vanPequena]);
        $totales = [
            'porcentajeCerrajeroGrande' => $porcentajeCerrajeroGrande,
            'porcentajeCerrajeroPequena' => $porcentajeCerrajeroPequena,
            'ventasGrande' => $ventasVanGrande['ventas']->sum('valor_v'),
            'ventasPequena' => $ventasVanPequena['ventas']->sum('valor_v'),
            'gastosGrande' => $gastosVanGrande->sum('valor'),
            'gastosPequena' => $gastosVanPequena->sum('valor'),
            'costosGrande' => $costosVanGrande->sum('valor'),
            'costosPequena' => $costosVanPequena->sum('valor'),
            'itemsGrande' => $ventasVanGrande['items']->sum('total_cantidad'),
            'itemsPequena' => $ventasVanPequena['items']->sum('total_cantidad'),
            'valorItemsGrande' => $ventasVanGrande['items']->sum('total_valor'),
            'valorItemsPequena' => $ventasVanPequena['items']->sum('total_valor'),
            'totalLlaves' => $llavesPorTecnico->sum('total_llaves'),
            'totalValorLlaves' => $llavesPorTecnico->sum('total_valor'),
            'utilidadGrande' => $ventasVanGrande['ventas']->sum('valor_v') - $costosVanGrande->sum('valor') - $porcentajeCerrajeroGrande - $gastosVanGrande->sum('valor') - $ventasVanGrande['items']->sum('total_valor'),
            'utilidadPequena' => $ventasVanPequena['ventas']->sum('valor_v') - $costosVanPequena->sum('valor') - $porcentajeCerrajeroPequena - $gastosVanPequena->sum('valor') - $ventasVanPequena['items']->sum('total_valor'),
        ];
        return [
            'vanGrande' => $vanGrande,
            'vanPequena' => $vanPequena,
            'ventasVanGrande' => $ventasVanGrande,
            'ventasVanPequena' => $ventasVanPequena,
            'gastosVanGrande' => $gastosVanGrande,
            'gastosVanPequena' => $gastosVanPequena,
            'costosVanGrande' => $costosVanGrande,
            'costosVanPequena' => $costosVanPequena,
            'llavesPorTecnico' => $llavesPorTecnico,
            'porcentajeCerrajeroGrande' => $porcentajeCerrajeroGrande,
            'porcentajeCerrajeroPequena' => $porcentajeCerrajeroPequena,
            'totales' => $totales,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }

    private function procesarVentasPorVan($van, $startDate = null, $endDate = null)
    {
        $query = RegistroV::with('empleado')
            ->where('lugarventa', $van);

        if ($startDate && $endDate) {
            $query->whereDate('fecha_h', '>=', $startDate)
                ->whereDate('fecha_h', '<=', $endDate);
        }

        $ventas = $query->orderBy('fecha_h', 'desc')->get();

        $itemsInfo = collect();
        $totalItems = 0;
        $totalValor = 0;

        foreach ($ventas as $venta) {
            $items = json_decode($venta->items, true) ?? [];

            foreach ($items as $item) {
                if (isset($item['productos']) && is_array($item['productos'])) {
                    foreach ($item['productos'] as $producto) {
                        if (isset($producto['nombre_producto'], $producto['cantidad'], $producto['precio'])) {
                            $itemNombre = $producto['nombre_producto'] ?? 'Producto sin nombre';
                            $cantidad = (int)$producto['cantidad'];
                            $precio = (float)$producto['precio'];

                            if (!$itemsInfo->has($itemNombre)) {
                                $itemsInfo->put($itemNombre, [
                                    'nombre' => $itemNombre,
                                    'total_cantidad' => 0,
                                    'total_valor' => 0,
                                    'ventas' => collect()
                                ]);
                            }

                            $itemData = $itemsInfo->get($itemNombre);
                            $itemData['total_cantidad'] += $cantidad;
                            $itemData['total_valor'] += ($cantidad * $precio);

                            // Agregar información de la venta específica
                            $itemData['ventas']->push([
                                'fecha' => Carbon::parse($venta->fecha_h)->format('d/m/Y'),
                                'cliente' => $venta->cliente,
                                'cantidad' => $cantidad,
                                'precio_unitario' => $precio,
                                'total' => $cantidad * $precio,
                                'tecnico' => $venta->empleado->nombre ?? 'Sin técnico'
                            ]);

                            $itemsInfo->put($itemNombre, $itemData);

                            $totalItems += $cantidad;
                            $totalValor += ($cantidad * $precio);
                        }
                    }
                }
            }
        }

        return [
            'ventas' => $ventas,
            'items' => $itemsInfo->values(),
            'total_items' => $totalItems,
            'total_valor_items' => $totalValor
        ];
    }

    private function getGastosPorFechas($fechas, $lugaresVenta = [], $month = null, $year = null)
    {
        $query = Gasto::where('subcategoria', 'gasto_extra')
            ->whereIn(DB::raw('DATE(f_gastos)'), $fechas);

        if ($month && $year) {
            $query->whereMonth('f_gastos', $month)
                ->whereYear('f_gastos', $year);
        }

        return $query->orderBy('f_gastos', 'desc')->get();
    }

    private function getCostosPorFechas($fechas, $lugaresVenta = [], $month = null, $year = null)
    {
        $query = Costo::where('subcategoria', 'costo_extra')
            ->whereIn(DB::raw('DATE(f_costos)'), $fechas);

        if ($month && $year) {
            $query->whereMonth('f_costos', $month)
                ->whereYear('f_costos', $year);
        }

        return $query->orderBy('f_costos', 'desc')->get();
    }

    private function procesarLlavesPorTecnico($startDate, $endDate, $lugaresVenta = [])
    {
        return Empleado::with(['ventas' => function ($query) use ($startDate, $endDate, $lugaresVenta) {
            if ($startDate && $endDate) {
                $query->whereDate('fecha_h', '>=', $startDate)
                    ->whereDate('fecha_h', '<=', $endDate);
            }
            if (!empty($lugaresVenta)) {
                $query->whereIn('lugarventa', $lugaresVenta);
            }
        }])
            ->whereHas('ventas', function ($query) use ($startDate, $endDate, $lugaresVenta) {
                if ($startDate && $endDate) {
                    $query->whereDate('fecha_h', '>=', $startDate)
                        ->whereDate('fecha_h', '<=', $endDate);
                }
                if (!empty($lugaresVenta)) {
                    $query->whereIn('lugarventa', $lugaresVenta);
                }
            })
            ->get()
            ->map(function ($tecnico) {
                $llavesInfo = collect();
                $totalLlaves = 0;
                $totalValor = 0;

                foreach ($tecnico->ventas as $venta) {
                    $items = json_decode($venta->items, true) ?? [];

                    foreach ($items as $item) {
                        if (isset($item['productos']) && is_array($item['productos'])) {
                            foreach ($item['productos'] as $producto) {
                                if (isset($producto['almacen'], $producto['cantidad'], $producto['precio'])) {
                                    $almacenId = $producto['almacen'];
                                    $llaveNombre = $producto['nombre_producto'] ?? 'Llave sin nombre';
                                    $cantidad = (int)$producto['cantidad'];
                                    $precio = (float)$producto['precio'];

                                    if (!$llavesInfo->has($llaveNombre)) {
                                        $llavesInfo->put($llaveNombre, [
                                            'nombre' => $llaveNombre,
                                            'almacenes' => collect(),
                                            'total_cantidad' => 0,
                                            'total_valor' => 0
                                        ]);
                                    }

                                    $llave = $llavesInfo->get($llaveNombre);

                                    if (!$llave['almacenes']->has($almacenId)) {
                                        $llave['almacenes']->put($almacenId, [
                                            'cantidad' => 0,
                                            'total' => 0
                                        ]);
                                    }

                                    $almacen = $llave['almacenes']->get($almacenId);
                                    $almacen['cantidad'] += $cantidad;
                                    $almacen['total'] += ($cantidad * $precio);
                                    $llave['almacenes']->put($almacenId, $almacen);

                                    $llave['total_cantidad'] += $cantidad;
                                    $llave['total_valor'] += ($cantidad * $precio);
                                    $llavesInfo->put($llaveNombre, $llave);

                                    $totalLlaves += $cantidad;
                                    $totalValor += ($cantidad * $precio);
                                }
                            }
                        }
                    }
                }

                return $totalLlaves > 0 ? [
                    'tecnico' => $tecnico->nombre,
                    'llaves' => $llavesInfo->values(),
                    'total_llaves' => $totalLlaves,
                    'total_valor' => $totalValor
                ] : null;
            })
            ->filter();
    }
}
