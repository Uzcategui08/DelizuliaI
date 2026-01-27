<?php

namespace App\Http\Controllers;

use App\Models\RegistroV;
use App\Models\Gasto;
use App\Models\Costo;
use App\Models\Categoria;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class EstadisticasVentasController extends Controller
{
    protected $month;
    protected $year;
    protected $availableYears;

    public function __construct()
    {
        // Obtener años disponibles para el filtro
        $this->availableYears = $this->getAvailableYears();
    }

    public function index(Request $request)
    {
        $this->month = $request->input('month', date('m'));
        $this->year = $request->input('year', date('Y'));

        // Verificar si hay datos de ventas para el mes seleccionado
        $hasData = RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->exists();

        $stats = $hasData ? $this->getAllStats() : null;

        return view('estadisticas.ventas', [
            'stats' => $stats,
            'monthSelected' => $this->month,
            'yearSelected' => $this->year,
            'availableYears' => $this->availableYears,
            'noData' => !$hasData // Pasamos explícitamente si no hay datos
        ]);
    }

    protected function getAvailableYears()
    {
        // Obtener años únicos de todas las tablas relevantes
        $yearsRegistroV = RegistroV::selectRaw('EXTRACT(YEAR FROM fecha_h) as year')
            ->distinct()
            ->pluck('year');

        $yearsGastos = Gasto::selectRaw('EXTRACT(YEAR FROM f_gastos) as year')
            ->distinct()
            ->pluck('year');

        $yearsCostos = Costo::selectRaw('EXTRACT(YEAR FROM f_costos) as year')
            ->distinct()
            ->pluck('year');

        // Combinar y obtener años únicos
        $allYears = $yearsRegistroV->merge($yearsGastos)->merge($yearsCostos)->unique()->sortDesc();

        return $allYears->values()->all();
    }

    // Métodos para estadísticas de ventas
    protected function cobradoDelMes()
    {
        $total = 0;
        $registros = RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->get();

        foreach ($registros as $registro) {
            $pagos = is_array($registro->pagos) ? $registro->pagos : (json_decode($registro->pagos, true) ?? []);
            foreach ($pagos as $pago) {
                if (isset($pago['fecha_pago'])) {
                    $fechaPago = Carbon::parse($pago['fecha_pago']);
                    if ($fechaPago->year == $this->year && $fechaPago->month == $this->month) {
                        $total += $pago['monto'] ?? 0;
                    }
                } else {
                    // Si no hay fecha de pago, solo sumar si la venta es de este mes
                    if (Carbon::parse($registro->fecha_h)->year == $this->year && Carbon::parse($registro->fecha_h)->month == $this->month) {
                        $total += $pago['monto'] ?? 0;
                    }
                }
            }
        }
        return $total;
    }

    protected function facturacionDelMes()
    {
        return RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->sum('valor_v');
    }

    protected function evolucionFacturacion()
    {
        // Mes actual: suma de trabajos (items)
        $registros_mes_actual = RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->get()
            ->sum(function ($registro) {
                $items = is_array($registro->items) ? $registro->items : (json_decode($registro->items, true) ?? []);
                return is_array($items) ? count($items) : 0;
            });
        // Mes anterior: conteo de registros
        $lastMonth = Carbon::create($this->year, $this->month, 1)->subMonth();
        $registros_mes_anterior = RegistroV::whereYear('fecha_h', $lastMonth->year)
            ->whereMonth('fecha_h', $lastMonth->month)
            ->count();
        // Diferencia porcentual
        $diferencia = 0;
        if ($registros_mes_anterior > 0) {
            $diferencia = ($registros_mes_actual / $registros_mes_anterior);
        } elseif ($registros_mes_actual > 0) {
            $diferencia = 100;
        }
        return round($diferencia, 2);
    }

    protected function numeroTransacciones()
    {
        return RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->count();
    }

    protected function ticketPromedio()
    {
        // Solo considerar ventas con items válidos
        $ventas = RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->get(['items', 'valor_v']);
        $totalTrabajos = 0;
        $totalFacturacion = 0;
        foreach ($ventas as $venta) {
            $items = is_array($venta->items) ? $venta->items : (json_decode($venta->items, true) ?? []);
            $numItems = is_array($items) ? count($items) : 0;
            if ($numItems > 0) {
                $totalTrabajos += $numItems;
                $totalFacturacion += $venta->valor_v;
            }
        }
        return $totalTrabajos == 0 ? 0 : $totalFacturacion / $totalTrabajos;
    }

    // Métodos para costos y gastos
    protected function totalCostosDelMes()
    {
        return Costo::whereYear('f_costos', $this->year)
            ->whereMonth('f_costos', $this->month)
            ->sum('valor');
    }
    protected function totalCostoVenta()
    {
        return Costo::whereYear('f_costos', $this->year)
            ->whereMonth('f_costos', $this->month)
            ->sum('valor');
    }

    protected function totalGastoPersonal()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->where('subcategoria', 'personal')
            ->sum('valor');
    }

    protected function totalGastosOperativos()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->where('subcategoria', 'operativos')
            ->sum('valor');
    }

    protected function totalOtrosGastos()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->where('subcategoria', 'otros')
            ->sum('valor');
    }

    protected function totalFinancierosImpuestos()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->where('subcategoria', 'financieros_impuestos')
            ->sum('valor');
    }

    protected function totalGastos()
    {
        return Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->sum('valor');
    }

    // Métodos auxiliares
    protected function calcularPorcentaje($valor, $facturacion)
    {
        return $facturacion == 0 ? 0 : ($valor / $facturacion) * 100;
    }

    protected function calcularUtilidadBruta()
    {
        return $this->facturacionDelMes() - $this->totalCostoVenta();
    }

    protected function calcularUtilidadNeta()
    {
        return $this->calcularUtilidadBruta() - $this->totalGastos();
    }

    // Método principal que obtiene todas las estadísticas
    protected function getAllStats()
    {
        $facturacion = $this->facturacionDelMes();
        $utilidadBruta = $this->calcularUtilidadBruta();
        $utilidadNeta = $this->calcularUtilidadNeta();
        // Obtener subcategorías únicas (son strings, no IDs)
        $subcategorias = Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->pluck('subcategoria')
            ->unique()
            ->filter(); // Elimina valores nulos

        $gastosPorSubcategoria = [];

        foreach ($subcategorias as $subcategoria) {
            $total = Gasto::whereYear('f_gastos', $this->year)
                ->whereMonth('f_gastos', $this->month)
                ->where('subcategoria', $subcategoria)
                ->sum('valor');
            // Buscar el nombre real de la subcategoría
            $nombreSubcategoria = \App\Models\Categoria::find($subcategoria)?->nombre ?? $subcategoria;
            $gastosPorSubcategoria[] = [
                'nombre' => $nombreSubcategoria,
                'total' => $total,
                'porcentaje' => $this->calcularPorcentaje($total, $facturacion)
            ];
        }

        // Calcular total de trabajos como en getResumenTrabajos
        $ventas = RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->get(['items']);
        $trabajos = collect();
        foreach ($ventas as $venta) {
            $items = is_array($venta->items) ? $venta->items : (json_decode($venta->items, true) ?? []);
            foreach ($items as $item) {
                $trabajoKey = $item['trabajo'] ?? 'Sin especificar';
                if (!$trabajos->has($trabajoKey)) {
                    $trabajos->put($trabajoKey, 0);
                }
                $trabajos->put($trabajoKey, $trabajos->get($trabajoKey) + 1);
            }
        }
        $totalTrabajos = $trabajos->sum();

        // Obtener registros detallados con sus pagos
        $registros = RegistroV::whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->get();

        // Procesar ventas por trabajo y métodos de pago
        $ventasPorTrabajo = [
            'contado' => [],
            'credito' => []
        ];

        foreach ($registros as $registro) {
            $tipoPago = $registro->tipo_pago ?? 'contado'; // fallback por si no existe
            $trabajos = is_array($registro->items) ? $registro->items : (json_decode($registro->items, true) ?? []);

            foreach ($trabajos as $item) {
                $trabajoKey = is_array($item) ? ($item['trabajo'] ?? (is_string($item) ? $item : 'Sin especificar')) : (is_string($item) ? $item : 'Sin especificar');
                if (!isset($ventasPorTrabajo[$tipoPago][$trabajoKey])) {
                    $ventasPorTrabajo[$tipoPago][$trabajoKey] = [
                        'total' => 0,
                        'metodos' => []
                    ];
                }

                // Procesar métodos de pago
                $pagos = is_array($registro->pagos) ? $registro->pagos : (json_decode($registro->pagos, true) ?? []);
                foreach ($pagos as $pago) {
                    $metodo = $pago['metodo'] ?? ($pago['metodo_pago'] ?? 'N/A'); // Compatibilidad
                    if (!isset($ventasPorTrabajo[$tipoPago][$trabajoKey]['metodos'][$metodo])) {
                        $ventasPorTrabajo[$tipoPago][$trabajoKey]['metodos'][$metodo] = [
                            'total' => 0
                        ];
                    }
                    $ventasPorTrabajo[$tipoPago][$trabajoKey]['metodos'][$metodo]['total'] += $pago['monto'] ?? 0;
                    $ventasPorTrabajo[$tipoPago][$trabajoKey]['total'] += $pago['monto'] ?? 0;
                }
            }
        }

        return [
            // Datos básicos
            'month' => $this->month,
            'year' => $this->year,

            // Estadísticas de ventas
            'ventas' => [
                'cobrado_mes' => $this->cobradoDelMes(),
                'facturacion' => $facturacion,
                'evolucion_facturacion' => $this->evolucionFacturacion(),
                'num_transacciones' => $totalTrabajos, // Mostrar la suma de trabajos
                'ticket_promedio' => $this->ticketPromedio(),
            ],
            'ventas_por_trabajo' => $ventasPorTrabajo,

            // Costos y utilidad
            'costos' => [
                'total_costo_venta' => $this->totalCostoVenta(),
                'porcentaje_costo_venta' => $this->calcularPorcentaje($this->totalCostoVenta(), $facturacion),
                'utilidad_bruta' => $utilidadBruta,
                'porcentaje_utilidad_bruta' => $this->calcularPorcentaje($utilidadBruta, $facturacion),
                'total_costos_mes' => $this->totalCostosDelMes(),
                'porcentaje_total_costos' => $this->calcularPorcentaje($this->totalCostosDelMes(), $facturacion)
            ],
            'gastos' => [
                'por_subcategoria' => $gastosPorSubcategoria,
                'total_gastos' => $this->totalGastos(),
                'porcentaje_gastos' => $this->calcularPorcentaje($this->totalGastos(), $facturacion)
            ],
            // Resultados finales
            'resultados' => [
                'utilidad_neta' => $utilidadNeta,
                'porcentaje_utilidad_neta' => $this->calcularPorcentaje($utilidadNeta, $facturacion)
            ]
        ];
    }
    //PDF   Que muestra todo
    public function showReportForm()
    {
        // Obtener fechas del mes actual
        $fechaInicio = Carbon::now()->firstOfMonth()->format('Y-m-d');
        $fechaFin = Carbon::now()->lastOfMonth()->format('Y-m-d');

        return view('registrosV.report-form', [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ]);
    }

    public function generatePdfTotal(Request $request)
    {
        $request->validate([
            'month' => 'required|numeric|between:1,12',
            'year' => 'required|numeric|min:2020|max:' . (date('Y') + 1)
        ]);

        $this->month = $request->input('month');
        $this->year = $request->input('year');

        // Obtener todas las estadísticas
        $stats = $this->getAllStats();

        // Obtener registros detallados
        $registros = RegistroV::with('empleado')
            ->whereYear('fecha_h', $this->year)
            ->whereMonth('fecha_h', $this->month)
            ->orderBy('fecha_h', 'desc')
            ->get();

        // Obtener gastos detallados
        $gastos = Gasto::whereYear('f_gastos', $this->year)
            ->whereMonth('f_gastos', $this->month)
            ->orderBy('f_gastos', 'desc')
            ->get();

        // Obtener costos detallados
        $costos = Costo::whereYear('f_costos', $this->year)
            ->whereMonth('f_costos', $this->month)
            ->orderBy('f_costos', 'desc')
            ->get();

        // Preparar datos para el PDF
        $data = [
            'title' => 'Reporte Estadístico Mensual',
            'date' => now()->format('d/m/Y H:i'),
            'mes' => Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y'),
            'stats' => $stats,
            'registros' => $registros,
            'gastos' => $gastos,
            'costos' => $costos,
            'totalTrabajos' => $registros->sum(fn($r) => count(json_decode($r->items, true) ?? [])),
            'totalPagos' => $registros->sum(fn($r) => $r->pagos ? array_sum(array_column($r->pagos, 'monto')) : 0)
        ];

        $pdf = PDF::loadView('estadisticas.stats-pdf', [
            'title' => 'Reporte Financiero',
            'mes' => $this->month . '/' . $this->year,
            'date' => now()->format('d/m/Y H:i'),
            'stats' => $stats,
            'registros' => $registros,
            'gastos' => $gastos,
            'costos' => $costos,
        ])->setPaper('a4', 'landscape'); // <-- Esto lo pone horizontal

        return $pdf->stream('reporte_financiero.pdf');
    }
}
