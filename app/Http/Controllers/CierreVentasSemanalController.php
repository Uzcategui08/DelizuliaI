<?php

namespace App\Http\Controllers;

use App\Models\RegistroV;
use App\Models\Empleado;
use App\Models\TiposDePago;
use App\Models\Costo;
use App\Models\Gasto;
use App\Models\Almacene;  // Add this import at the top with other use statements
use App\Models\Producto;
use App\Models\AjusteInventario;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CierreSemanalExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Nempleado;
use Illuminate\Support\Facades\Log;
use App\Models\Trabajo as TrabajoModel;

class CierreVentasSemanalController extends Controller
{
    private $formatosTrabajo = [
        'duplicado' => 'Duplicado',
        'perdida' => 'Pérdida',
        'programacion' => 'Programación',
        'alarma' => 'Alarma',
        'airbag' => 'Airbag',
        'rekey' => 'Rekey',
        'lishi' => 'Lishi',
        'remote_start' => 'Remote Start',
        'control' => 'Control',
        'venta' => 'Venta',
        'apertura' => 'Apertura',
        'cambio_chip' => 'Cambio de Chip',
        'revision' => 'Revisión',
        'suiche' => 'Suiche',
        'llave_puerta' => 'Llave de Puerta',
        'cinturon' => 'Cinturón',
        'diag' => 'Diagnóstico',
        'emuladores' => 'Emuladores',
        'clonacion' => 'Clonación'
    ];

    private function getNombreTrabajoPorId($trabajoId)
    {
        $trabajo = TrabajoModel::find($trabajoId);
        return $trabajo ? $trabajo->nombre : 'Trabajo no encontrado';
    }

    private function decodeUnicode($texto)
    {
        if (is_string($texto)) {
            return json_decode('"' . str_replace('\\u', '\u', $texto) . '"');
        }
        return $texto;
    }

    public function index(Request $request)
    {
        $yearSelected = $request->input('year', now()->year);
        $weekSelected = $request->input('week', now()->weekOfYear);

        if ($request->has('start_date') && $request->has('end_date')) {
            try {
                $startOfWeek = Carbon::parse($request->input('start_date'))->startOfDay();
                $endOfWeek = Carbon::parse($request->input('end_date'))->endOfDay();

                if ($startOfWeek->diffInDays($endOfWeek) > 31) {
                    throw new \Exception("Rango máximo excedido");
                }

                $weekSelected = $startOfWeek->weekOfYear;
                $yearSelected = $startOfWeek->year;
            } catch (\Exception $e) {
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
            }
        } else {
            $startOfWeek = Carbon::now()->setISODate($yearSelected, $weekSelected)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
        }

        $availableYears = $this->getAvailableYears();
        $metodosPago = TiposDePago::pluck('name', 'id');
        $tiposDePago = TiposDePago::all();
        $empleados = Empleado::all()->pluck('nombre', 'id_empleado');

        $reporteVentas = $this->getVentasPorTecnico($startOfWeek, $endOfWeek);
        $reporteCostosGastos = $this->getCostosGastosPorTecnico($startOfWeek, $endOfWeek, $metodosPago);
        $ingresosRecibidos = $this->getIngresosRecibidos($startOfWeek, $endOfWeek, $metodosPago);
        $ventasDetalladasPorTecnico = $this->getVentasDetalladasPorTecnico($startOfWeek, $endOfWeek);

        $llavesPorTecnico = $this->getLlavesPorTecnico($startOfWeek, $endOfWeek);

        $descargasManuales = $this->getCargasDescargas($startOfWeek, $endOfWeek);

        $descargasManualesFormato = [
            'tecnico' => 'Manual',
            'llaves' => $descargasManuales->map(function ($item) {
                $idProducto = $item['id_producto'] ?? null;
                $almacenId = $item['id_almacen'] ?? 'manual';
                $cantidadAnterior = $item['cantidad_anterior'] ?? 0;
                $cantidadNueva = $item['cantidad_nueva'] ?? 0;
                $diferencia = abs($cantidadNueva - $cantidadAnterior);
                $precio = $item['precio'] ?? 0;

                return [
                    'nombre' => $item['producto'] ?? 'Producto no encontrado',
                    'id_producto' => $idProducto,
                    'almacenes' => [
                        $almacenId => [
                            'cantidad' => $diferencia,
                            'total' => $diferencia * $precio,
                            'id_producto' => $idProducto
                        ]
                    ],
                    'total_cantidad' => $diferencia,
                    'total_valor' => $diferencia * $precio,
                    'id_producto' => $idProducto
                ];
            }),
            'total_llaves' => $descargasManuales->map(function ($item) {
                $cantidadAnterior = $item['cantidad_anterior'] ?? 0;
                $cantidadNueva = $item['cantidad_nueva'] ?? 0;
                return abs($cantidadNueva - $cantidadAnterior);
            })->sum(),
            'total_valor' => $descargasManuales->map(function ($item) {
                $cantidadAnterior = $item['cantidad_anterior'] ?? 0;
                $cantidadNueva = $item['cantidad_nueva'] ?? 0;
                $diferencia = abs($cantidadNueva - $cantidadAnterior);
                $precio = $item['precio'] ?? 0;
                return $diferencia * $precio;
            })->sum()
        ];

        $llavesPorTecnico = collect($llavesPorTecnico);
        $llavesPorTecnico->push($descargasManualesFormato);

        $idsAlmacenes = collect([]);
        foreach ($llavesPorTecnico as $tecnico) {
            foreach ($tecnico['llaves'] as $llave) {
                foreach ($llave['almacenes'] as $almacenId => $datos) {
                    if (is_numeric($almacenId)) {
                        $idsAlmacenes->push((int)$almacenId);
                    }
                }
            }
        }
        $idsAlmacenes = $idsAlmacenes->unique()->values();

        Log::info('Consultando almacenes disponibles');
        $almacenesDisponibles = Almacene::all();
        Log::info('Almacenes encontrados:', ['count' => $almacenesDisponibles->count()]);

        $almacenesDisponibles = $almacenesDisponibles
            ->map(function ($almacen) {
                return (object)[
                    'id' => $almacen->id_almacen,
                    'nombre' => $almacen->nombre
                ];
            });

        $ventasPorCliente = $this->getVentasPorCliente($startOfWeek, $endOfWeek);
        $ventasPorTrabajo = $this->getVentasPorTrabajo($startOfWeek, $endOfWeek, $metodosPago);
        $resumenTrabajos = $this->getResumenTrabajos($startOfWeek, $endOfWeek);
        $ventasPorLugarVenta = $this->getVentasPorLugarVenta($startOfWeek, $endOfWeek);
        $cargasDescargas = $this->getCargasDescargas($startOfWeek, $endOfWeek);

        $totales = $this->calcularTotales(
            $reporteVentas,
            $reporteCostosGastos,
            $ingresosRecibidos,
            $llavesPorTecnico
        );

        $totalCostosLlaves = $llavesPorTecnico->sum('total_valor');

        $ganancia = ($totales['totalVentas'] ?? 0)
            - ($totales['totalCostos'] ?? 0)
            - ($totalCostosLlaves ?? 0)
            - ($totales['totalGastos'] ?? 0);

        $retiroDueño = Nempleado::whereHas('empleado', function ($query) {
            $query->where('cargo', 5);
        })
            ->whereBetween('fecha_pago', [$startOfWeek, $endOfWeek])
            ->sum('total_pagado');

        $gananciaFinal = $ganancia - $retiroDueño;

        $cargasDescargas = $descargasManuales;

        return view('estadisticas.cierre-semanal', array_merge(
            [
                'weekSelected' => $weekSelected,
                'yearSelected' => $yearSelected,
                'startOfWeek' => $startOfWeek,
                'endOfWeek' => $endOfWeek,
                'availableYears' => $availableYears,
                'metodosPago' => $metodosPago,
                'reporteVentas' => $reporteVentas,
                'reporteCostosGastos' => $reporteCostosGastos,
                'ingresosRecibidos' => $ingresosRecibidos,
                'llavesPorTecnico' => $llavesPorTecnico,
                'almacenesDisponibles' => $almacenesDisponibles,
                'ventasPorCliente' => $ventasPorCliente,
                'totalVentasClientes' => $ventasPorCliente->sum('total_ventas'),
                'ventasPorTrabajo' => $ventasPorTrabajo,
                'totalCostosLlaves' => $totalCostosLlaves,
                'ganancia' => $ganancia,
                'retiroDueño' => $retiroDueño,
                'gananciaFinal' => $gananciaFinal,
                'resumenTrabajos' => $resumenTrabajos,
                'totalTrabajos' => $resumenTrabajos->sum('cantidad'),
                'ventasPorLugarVenta' => $ventasPorLugarVenta,
                'totalGeneralLlaves' => $llavesPorTecnico->sum('total_llaves'),
                'totalGeneralValorLlaves' => $llavesPorTecnico->sum('total_valor'),
                'ventasDetalladasPorTecnico' => $ventasDetalladasPorTecnico,
                'tiposDePago' => $tiposDePago,
                'empleados' => $empleados,
                'cargasDescargas' => $cargasDescargas,
                'totalCargas' => $cargasDescargas->where('es_carga', true)->sum('cantidad'),
                'totalDescargas' => $cargasDescargas->where('tipo', 'ajuste2')->sum('cantidad'),
            ],
            $totales
        ));
    }

    public function convertToProperArray($data)
    {
        if (is_array($data)) {
            return array_map(function ($item) {
                if (is_object($item)) {
                    try {
                        $array = (array) $item;
                        if (array_values($array) === $array) {

                            return $array;
                        }

                        if (is_object($item) && property_exists($item, 'metodo_pago')) {
                            return $item;
                        }
                        return array_map(function ($value) {
                            return is_object($value) ? (array) $value : $value;
                        }, $array);
                    } catch (\Exception $e) {

                        return $item;
                    }
                }
                return $item;
            }, $data);
        }
        return $data;
    }

    public function exportPdf(Request $request)
    {
        $trabajos = TrabajoModel::select('id_trabajo', 'nombre')
            ->get()
            ->pluck('nombre', 'id_trabajo')
            ->toArray();

        $formatosTrabajo = [
            'duplicado' => 'Duplicado',
            'perdida' => 'Pérdida',
            'programacion' => 'Programación',
            'alarma' => 'Alarma',
            'airbag' => 'Airbag',
            'rekey' => 'Rekey',
            'lishi' => 'Lishi',
            'remote_start' => 'Remote Start',
            'control' => 'Control',
            'venta' => 'Venta',
            'apertura' => 'Apertura',
            'cambio_chip' => 'Cambio de Chip',
            'revision' => 'Revisión',
            'suiche' => 'Suiche',
            'llave_puerta' => 'Llave de Puerta',
            'cinturon' => 'Cinturón',
            'diag' => 'Diagnóstico',
            'emuladores' => 'Emuladores',
            'clonacion' => 'Clonación'
        ];

        $trabajos = array_merge($trabajos, $formatosTrabajo);

        Log::info('Trabajos disponibles:', ['count' => count($trabajos), 'sample' => array_slice($trabajos, 0, 3)]);
        try {
            Log::info('Inicio de exportPdf');
            Log::info('Datos del request:', [
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'year' => $request->input('year'),
                'week' => $request->input('week')
            ]);

            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            if (!$startDate || !$endDate) {
                Log::info('Usando fechas por semana');
                $startDate = Carbon::parse($request->input('year', now()->year) . 'W' . $request->input('week', now()->weekOfYear))->startOfWeek();
                $endDate = $startDate->copy()->endOfWeek();
            } else {
                Log::info('Usando fechas seleccionadas');
                $startDate = Carbon::parse($startDate);
                $endDate = Carbon::parse($endDate);
            }

            Log::info('Fechas calculadas:', [
                'startDate' => $startDate->format('Y-m-d'),
                'endDate' => $endDate->format('Y-m-d')
            ]);

            Log::info('Consultando métodos de pago');
            $metodosPago = TiposDePago::all();
            $metodosPagoArray = $metodosPago->pluck('name', 'id')->toArray();
            Log::info('Métodos de pago encontrados:', [
                'count' => $metodosPago->count(),
                'data' => $metodosPagoArray
            ]);

            Log::info('Consultando reporte de ventas');
            $reporteVentas = $this->getVentasPorTecnico($startDate, $endDate);
            Log::info('Ventas encontradas:', [
                'count' => count($reporteVentas),
                'first' => $reporteVentas->first() ?? null
            ]);

            Log::info('Consultando costos y gastos');
            $reporteCostosGastos = $this->getCostosGastosPorTecnico($startDate, $endDate, $metodosPago);

            Log::info('Estructura detallada de costos y gastos:', [
                'total_tecnicos' => $reporteCostosGastos->count(),
                'sample_tecnico' => $reporteCostosGastos->first() ? [
                    'tecnico' => $reporteCostosGastos->first()['tecnico'] ?? null,
                    'total_costos' => count($reporteCostosGastos->first()['costos'] ?? []),
                    'total_gastos' => count($reporteCostosGastos->first()['gastos'] ?? []),
                    'sample_costo' => $reporteCostosGastos->first()['costos'][0] ?? null,
                    'sample_gasto' => $reporteCostosGastos->first()['gastos'][0] ?? null,
                ] : null,
                'metodos_pago_format' => 'Array [id => nombre]',
                'metodos_pago_sample' => array_slice($metodosPagoArray, 0, 3, true) + ['...' => '...']
            ]);

            if ($reporteCostosGastos->isNotEmpty() && !empty($reporteCostosGastos->first()['costos'])) {
                $sampleCostos = collect($reporteCostosGastos->first()['costos'])->take(2)->map(function ($costo) {
                    return [
                        'descripcion' => $costo['descripcion'] ?? null,
                        'metodo_pago' => $costo['metodo_pago'] ?? null,
                        'total' => $costo['total'] ?? null,
                        'fecha_pago' => $costo['fecha_pago'] ?? null,
                        'keys' => array_keys($costo)
                    ];
                });
                Log::info('Ejemplo de costos:', ['costos' => $sampleCostos]);
            }

            if ($reporteCostosGastos->isNotEmpty() && !empty($reporteCostosGastos->first()['gastos'])) {
                $sampleGastos = collect($reporteCostosGastos->first()['gastos'])->take(2)->map(function ($gasto) {
                    return [
                        'descripcion' => $gasto['descripcion'] ?? null,
                        'metodo_pago' => $gasto['metodo_pago'] ?? null,
                        'total' => $gasto['total'] ?? null,
                        'fecha_pago' => $gasto['fecha_pago'] ?? null,
                        'keys' => array_keys($gasto)
                    ];
                });
                Log::info('Ejemplo de gastos:', ['gastos' => $sampleGastos]);
            }

            Log::info('Costos y gastos encontrados (antes de convertir):', [
                'count' => count($reporteCostosGastos),
                'first' => $reporteCostosGastos->first() ?? null,
                'sample_costos' => $reporteCostosGastos->first()?->costos ?? [],
                'sample_gastos' => $reporteCostosGastos->first()?->gastos ?? []
            ]);

            $reporteCostosGastosArray = $reporteCostosGastos->toArray();
            Log::info('Costos y gastos después de convertir a array:', [
                'count' => count($reporteCostosGastosArray),
                'first' => $reporteCostosGastosArray[0] ?? null,
                'sample_costos' => $reporteCostosGastosArray[0]['costos'] ?? [],
                'sample_gastos' => $reporteCostosGastosArray[0]['gastos'] ?? []
            ]);

            Log::info('Consultando ingresos recibidos');
            $ingresosRecibidos = $this->getIngresosRecibidos($startDate, $endDate, $metodosPago);
            Log::info('Ingresos encontrados:', [
                'count' => count($ingresosRecibidos),
                'first' => $ingresosRecibidos->first() ?? null
            ]);

            Log::info('Consultando llaves por técnico');
            $llavesPorTecnico = $this->getLlavesPorTecnico($startDate, $endDate);
            Log::info('Llaves por técnico (antes de convertir):', [
                'raw_value' => $llavesPorTecnico,
                'is_array' => is_array($llavesPorTecnico),
                'is_object' => is_object($llavesPorTecnico),
                'is_collection' => $llavesPorTecnico instanceof \Illuminate\Support\Collection,
                'is_null' => is_null($llavesPorTecnico),
                'is_false' => $llavesPorTecnico === false
            ]);

            $llavesPorTecnico = $llavesPorTecnico
                ? collect($llavesPorTecnico)->filter(function ($tecnico) {
                    return !is_null($tecnico) && is_array($tecnico) && isset($tecnico['llaves']);
                })->values()
                : collect([]);

            Log::info('Llaves por técnico (después de filtrar nulls):', [
                'count' => $llavesPorTecnico->count(),
                'first' => $llavesPorTecnico->first() ?? null,
                'sample_data' => $llavesPorTecnico->take(2)->toArray()
            ]);

            if ($llavesPorTecnico->isNotEmpty()) {
                Log::info('Estructura de datos de llaves por técnico:', [
                    'first_tecnico' => $llavesPorTecnico->first(),
                    'llaves_structure' => $llavesPorTecnico->first()['llaves'] ?? null,
                    'almacenes_structure' => ($llavesPorTecnico->first()['llaves'] ?? [])->first()['almacenes'] ?? null
                ]);
            }

            Log::info('Consultando descargas manuales');
            $descargasManuales = $this->getCargasDescargas($startDate, $endDate);
            Log::info('Descargas encontradas:', [
                'count' => count($descargasManuales),
                'first' => $descargasManuales->first() ?? null
            ]);

            $descargasManuales = collect($descargasManuales);

            if ($descargasManuales->isNotEmpty()) {
                $descargasManuales->groupBy('producto')->each(function ($grupo, $producto) use (&$llavesPorTecnico) {
                    $primerRegistro = $grupo->first();
                    $idProducto = $primerRegistro['id_producto'] ?? null;
                    $nombreProducto = Producto::where('id_producto', $primerRegistro['id_producto'])->value('item') ?? 'Producto no encontrado';

                    $llavesPorTecnico->push([
                        'tecnico' => 'Manual',
                        'llaves' => collect([$grupo])->map(function ($grupo) use ($primerRegistro, $idProducto, $nombreProducto) {
                            return [
                                'nombre' => $nombreProducto,
                                'id_producto' => $idProducto,
                                'total_cantidad' => $grupo->map(function ($item) {
                                    return abs($item['cantidad_nueva'] - $item['cantidad_anterior']);
                                })->sum(),
                                'total_valor' => $grupo->map(function ($item) {
                                    $diferencia = abs($item['cantidad_nueva'] - $item['cantidad_anterior']);
                                    $precio = Producto::where('id_producto', $item['id_producto'])->value('precio') ?? 0;
                                    return $diferencia * $precio;
                                })->sum(),
                                'almacenes' => collect($grupo)->groupBy('id_almacen')->map(function ($almacenGrupo) use ($idProducto) {
                                    $diferencias = $almacenGrupo->map(function ($item) {
                                        return abs($item['cantidad_nueva'] - $item['cantidad_anterior']);
                                    });

                                    $cantidad = $diferencias->sum();
                                    $precio = Producto::where('id_producto', $almacenGrupo->first()['id_producto'])->value('precio') ?? 0;
                                    return [
                                        'cantidad' => $cantidad,
                                        'total' => $cantidad * $precio,
                                        'id_producto' => $idProducto
                                    ];
                                })->toArray()
                            ];
                        })->toArray()
                    ]);
                });
            }

            Log::info('Calculando totales');
            $totales = $this->calcularTotales(
                $reporteVentas,
                $reporteCostosGastos,
                $ingresosRecibidos,
                $llavesPorTecnico
            );
            Log::info('Totales calculados:', $totales);

            Log::info('Calculando costos de llaves');
            $totalCostosLlaves = $llavesPorTecnico->sum('total_valor');
            Log::info('Total costos llaves:', ['valor' => $totalCostosLlaves]);

            Log::info('Calculando ganancia');
            $ganancia = ($totales['totalVentas'] ?? 0)
                - ($totales['totalCostos'] ?? 0)
                - ($totalCostosLlaves ?? 0)
                - ($totales['totalGastos'] ?? 0);
            Log::info('Ganancia calculada:', ['valor' => $ganancia]);

            Log::info('Consultando retiro del dueño');
            $retiroDueño = Nempleado::whereHas('empleado', function ($query) {
                $query->where('cargo', 5);
            })
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('fecha_pago', [$startDate, $endDate])
                        ->orWhereBetween('fecha_desde', [$startDate, $endDate])
                        ->orWhereBetween('fecha_hasta', [$startDate, $endDate]);
                })
                ->sum('total_pagado');

            Log::info('Retiro del dueño encontrado:', [
                'valor' => $retiroDueño,
                'fecha_inicio' => $startDate->format('Y-m-d'),
                'fecha_fin' => $endDate->format('Y-m-d')
            ]);

            Log::info('Calculando ganancia final');
            $gananciaFinal = $ganancia - $retiroDueño;
            Log::info('Ganancia final calculada:', [
                'ganancia' => $ganancia,
                'retiroDueño' => $retiroDueño,
                'gananciaFinal' => $gananciaFinal
            ]);

            Log::info('Generando PDF');
            Log::info('Consultando ventas detalladas por técnico');
            $ventasDetalladasPorTecnico = $this->getVentasDetalladasPorTecnico($startDate, $endDate);
            Log::info('Ventas detalladas encontradas:', [
                'count' => count($ventasDetalladasPorTecnico),
                'first' => $ventasDetalladasPorTecnico->first() ?? null
            ]);

            Log::info('Consultando ventas por trabajo');
            $metodosPagoArray = $metodosPago->pluck('name', 'id')->toArray();
            Log::info('Métodos de pago para ventas por trabajo:', ['metodos' => $metodosPagoArray]);

            $ventasPorTrabajo = $this->getVentasPorTrabajo($startDate, $endDate, $metodosPagoArray);
            Log::info('Ventas por trabajo obtenidas:', [
                'contado_count' => count($ventasPorTrabajo['contado'] ?? []),
                'credito_count' => count($ventasPorTrabajo['credito'] ?? [])
            ]);

            Log::info('Procesando ventas por trabajo');
            Log::info('Estructura de ventasPorTrabajo:', ['sample' => $ventasPorTrabajo['contado']->first() ?? null]);

            $ventasPorTrabajo['contado'] = collect($ventasPorTrabajo['contado'])->map(function ($trabajoData) use ($metodosPagoArray) {
                $metodos = collect($trabajoData['metodos'])->map(function ($metodoData, $metodoKey) use ($metodosPagoArray) {
                    if (is_numeric($metodoKey)) {
                        $metodoId = (string)$metodoKey;
                        return [
                            'nombre' => $metodosPagoArray[$metodoId] ?? "Método $metodoId",
                            'total' => $metodoData['total'],
                            'count' => $metodoData['count']
                        ];
                    }
                    return [
                        'nombre' => $metodoKey,
                        'total' => $metodoData['total'],
                        'count' => $metodoData['count']
                    ];
                })->values();

                return [
                    'total' => $trabajoData['total'],
                    'metodos' => $metodos
                ];
            });

            $ventasPorTrabajo['credito'] = collect($ventasPorTrabajo['credito'])->map(function ($trabajoData) use ($metodosPagoArray) {
                $metodos = collect($trabajoData['metodos'])->map(function ($metodoData, $metodoKey) use ($metodosPagoArray) {
                    if (is_numeric($metodoKey)) {
                        $metodoId = (string)$metodoKey;
                        return [
                            'nombre' => $metodosPagoArray[$metodoId] ?? "Método $metodoId",
                            'total' => $metodoData['total'],
                            'count' => $metodoData['count']
                        ];
                    }
                    return [
                        'nombre' => $metodoKey,
                        'total' => $metodoData['total'],
                        'count' => $metodoData['count']
                    ];
                })->values();

                return [
                    'total' => $trabajoData['total'],
                    'metodos' => $metodos
                ];
            });

            Log::info('Ventas por trabajo procesadas:', [
                'contado_first' => $ventasPorTrabajo['contado']->first(),
                'credito_first' => $ventasPorTrabajo['credito']->first()
            ]);

            Log::info('Consultando resumen de trabajos');
            Log::info('Datos de entrada:', ['startDate' => $startDate->format('Y-m-d'), 'endDate' => $endDate->format('Y-m-d')]);
            $resumenTrabajos = $this->getResumenTrabajos($startDate, $endDate);
            Log::info('Resumen de trabajos obtenido:', [
                'count' => count($resumenTrabajos),
                'first' => $resumenTrabajos->first() ?? null,
                'total_trabajos' => $resumenTrabajos->sum('cantidad')
            ]);

            $trabajosInvalidos = $resumenTrabajos->filter(function ($trabajo) {
                return !is_string($trabajo['nombre']) || empty(trim($trabajo['nombre']));
            });
            if ($trabajosInvalidos->count() > 0) {
                Log::warning('Trabajos inválidos encontrados:', [
                    'count' => $trabajosInvalidos->count(),
                    'invalid_jobs' => $trabajosInvalidos->toArray()
                ]);
            }

            Log::info('Consultando ventas por lugar de venta');
            Log::info('Datos de entrada:', ['startDate' => $startDate->format('Y-m-d'), 'endDate' => $endDate->format('Y-m-d')]);
            $ventasPorLugarVenta = $this->getVentasPorLugarVenta($startDate, $endDate);
            Log::info('Ventas por lugar de venta obtenidas:', [
                'count' => count($ventasPorLugarVenta),
                'first' => $ventasPorLugarVenta->first() ?? null,
                'total_ventas' => $ventasPorLugarVenta->sum('total_ventas')
            ]);

            $lugaresInvalidos = $ventasPorLugarVenta->filter(function ($lugar) {
                return !is_string($lugar['nombre']) || empty(trim($lugar['nombre']));
            });
            if ($lugaresInvalidos->count() > 0) {
                Log::warning('Lugares de venta inválidos encontrados:', [
                    'count' => $lugaresInvalidos->count(),
                    'invalid_places' => $lugaresInvalidos->toArray()
                ]);
            }

            Log::info('Consultando ventas por cliente');
            Log::info('Datos de entrada:', ['startDate' => $startDate->format('Y-m-d'), 'endDate' => $endDate->format('Y-m-d')]);
            $ventasPorCliente = $this->getVentasPorCliente($startDate, $endDate);
            Log::info('Ventas por cliente obtenidas:', [
                'count' => count($ventasPorCliente),
                'first' => $ventasPorCliente->first() ?? null,
                'total_ventas' => $ventasPorCliente->sum('total_ventas')
            ]);

            $clientesInvalidos = $ventasPorCliente->filter(function ($cliente) {
                return !is_string($cliente['cliente']) || empty(trim($cliente['cliente']));
            });
            if ($clientesInvalidos->count() > 0) {
                Log::warning('Clientes con nombres inválidos encontrados:', [
                    'count' => $clientesInvalidos->count(),
                    'invalid_clients' => $clientesInvalidos->toArray()
                ]);
            }

            Log::info('Calculando total de descargas');
            $totalDescargas = $descargasManuales->where('tipo', 'ajuste2')->sum('cantidad');
            Log::info('Total de descargas:', ['valor' => $totalDescargas]);

            Log::info('Generando vista PDF');

            Log::info('Tipos de datos antes de la conversión:', [
                'ventasPorCliente' => gettype($ventasPorCliente),
                'resumenTrabajos' => gettype($resumenTrabajos),
                'ventasPorLugarVenta' => gettype($ventasPorLugarVenta),
                'reporteVentas' => gettype($reporteVentas),
                'reporteCostosGastos' => gettype($reporteCostosGastos),
                'ingresosRecibidos' => gettype($ingresosRecibidos),
                'llavesPorTecnico' => gettype($llavesPorTecnico),
                'metodosPago' => gettype($metodosPago),
                'ventasDetalladasPorTecnico' => gettype($ventasDetalladasPorTecnico),
                'tiposDePago' => gettype(TiposDePago::all()),
                'almacenesDisponibles' => gettype(Almacene::all()),
                'cargasDescargas' => gettype($descargasManuales),
                'ventasPorTrabajo_contado' => gettype($ventasPorTrabajo['contado']),
                'ventasPorTrabajo_credito' => gettype($ventasPorTrabajo['credito'])
            ]);

            Log::info('Estructura de ventasPorTrabajo:', [
                'contado_sample' => $ventasPorTrabajo['contado']->first() ?? null,
                'credito_sample' => $ventasPorTrabajo['credito']->first() ?? null
            ]);

            $reporteCostosGastosResolved = $reporteCostosGastos->map(function ($item) use ($metodosPago) {
                $item['costos'] = collect($item['costos'])->map(function ($costo) use ($metodosPago) {
                    $costo['metodo_pago'] = $metodosPago->where('id', $costo['metodo_pago'])->first()?->name ?? 'Desconocido';
                    return $costo;
                })->toArray();

                $item['gastos'] = collect($item['gastos'])->map(function ($gasto) use ($metodosPago) {
                    $gasto['metodo_pago'] = $metodosPago->where('id', $gasto['metodo_pago'])->first()?->name ?? 'Desconocido';
                    return $gasto;
                })->toArray();

                return $item;
            })->toArray();

            $data = [
                'ventasPorCliente' => $this->convertToProperArray($ventasPorCliente->toArray()),
                'resumenTrabajos' => $this->convertToProperArray($resumenTrabajos->toArray()),
                'ventasPorLugarVenta' => $this->convertToProperArray($ventasPorLugarVenta->toArray()),
                'startDate' => $startDate->format('d/m/Y'),
                'endDate' => $endDate->format('d/m/Y'),
                'reporteVentas' => $this->convertToProperArray($reporteVentas->toArray()),
                'reporteCostosGastos' => $reporteCostosGastosResolved,
                'ingresosRecibidos' => $this->convertToProperArray($ingresosRecibidos->toArray()),

                'llavesPorTecnico' => $llavesPorTecnico->map(function ($tecnico) {
                    return [
                        'tecnico' => $tecnico['tecnico'],
                        'llaves' => collect($tecnico['llaves'])->map(function ($llave) {
                            return [
                                'nombre' => $llave['nombre'],
                                'id_producto' => $llave['id_producto'],
                                'total_cantidad' => $llave['total_cantidad'],
                                'total_valor' => $llave['total_valor'],
                                'almacenes' => collect($llave['almacenes'])->map(function ($almacen, $id) {
                                    return [
                                        'cantidad' => $almacen['cantidad'],
                                        'total' => $almacen['total']
                                    ];
                                })->toArray()
                            ];
                        })->toArray()
                    ];
                })->toArray(),
                'totales' => $totales,
                'totalCostosLlaves' => $totalCostosLlaves,
                'ganancia' => $ganancia,
                'retiroDueño' => $retiroDueño,
                'gananciaFinal' => $gananciaFinal,
                'metodosPago' => $metodosPagoArray,
                'trabajos' => $trabajos,
                'ventasDetalladasPorTecnico' => $ventasDetalladasPorTecnico,
                'tiposDePago' => $metodosPagoArray,
                'almacenesDisponibles' => $this->convertToProperArray(Almacene::all()->toArray()),
                'cargasDescargas' => $this->convertToProperArray($descargasManuales->toArray()),
                'totalDescargas' => $totalDescargas,
                'ventasPorTrabajo' => [
                    'contado' => $this->convertToProperArray($ventasPorTrabajo['contado']->toArray()),
                    'credito' => $this->convertToProperArray($ventasPorTrabajo['credito']->toArray())
                ]
            ];


            $datosInvalidos = collect($data)->filter(function ($value, $key) {
                if (is_array($value)) {
                    return empty($value);
                } elseif ($value instanceof \Illuminate\Support\Collection) {
                    return $value->isEmpty();
                }
                return $value === null || (is_string($value) && trim($value) === '');
            });

            if ($datosInvalidos->count() > 0) {
                Log::warning('Datos inválidos encontrados en la vista PDF:', [
                    'invalid_data' => $datosInvalidos->toArray(),
                    'total_invalid' => $datosInvalidos->count()
                ]);
            }

            Log::info('Datos para vista PDF:', [
                'count' => count($data),
                'keys' => array_keys($data),
                'sample_data' => collect($data)->map(function ($value, $key) {
                    if (is_array($value)) {
                        return reset($value) ?? null;
                    } elseif ($value instanceof \Illuminate\Support\Collection) {
                        return $value->first() ?? null;
                    }
                    return $value;
                })->toArray()
            ]);

            Log::info('Tipo de datos después de la conversión:', [
                'ventasPorCliente' => gettype($data['ventasPorCliente']),
                'resumenTrabajos' => gettype($data['resumenTrabajos']),
                'ventasPorLugarVenta' => gettype($data['ventasPorLugarVenta']),
                'reporteVentas' => gettype($data['reporteVentas']),
                'reporteCostosGastos' => gettype($data['reporteCostosGastos']),
                'ingresosRecibidos' => gettype($data['ingresosRecibidos']),
                'llavesPorTecnico' => gettype($data['llavesPorTecnico']),
                'metodosPago' => gettype($data['metodosPago']),
                'ventasDetalladasPorTecnico' => gettype($data['ventasDetalladasPorTecnico']),
                'tiposDePago' => gettype($data['tiposDePago']),
                'almacenesDisponibles' => gettype($data['almacenesDisponibles']),
                'cargasDescargas' => gettype($data['cargasDescargas']),
                'ventasPorTrabajo_contado' => gettype($data['ventasPorTrabajo']['contado']),
                'ventasPorTrabajo_credito' => gettype($data['ventasPorTrabajo']['credito'])
            ]);

            Log::info('Cargando vista PDF');
            $pdf = PDF::loadView('estadisticas.cierre-semanal-pdf', $data);
            Log::info('Vista PDF cargada');

            Log::info('Generando nombre de archivo');
            $fileName = 'cierre-semanal-' . $startDate->format('Y') . '-' . $startDate->format('W') . '.pdf';
            Log::info('Nombre de archivo generado:', ['filename' => $fileName]);

            Log::info('Iniciando descarga de PDF');
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('Error en exportPdf:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'context' => [
                    'startDate' => $startDate ? $startDate->format('Y-m-d') : null,
                    'endDate' => $endDate ? $endDate->format('Y-m-d') : null
                ]
            ]);
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$startDate || !$endDate) {
            $startDate = Carbon::parse($request->input('year', now()->year) . 'W' . $request->input('week', now()->weekOfYear))->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();
        } else {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        }

        $metodosPago = TiposDePago::pluck('name', 'id')->toArray();

        $reporteVentas = $this->getVentasPorTecnico($startDate, $endDate);
        $reporteCostosGastos = $this->getCostosGastosPorTecnico($startDate, $endDate, $metodosPago);
        $ingresosRecibidos = $this->getIngresosRecibidos($startDate, $endDate, $metodosPago);
        $llavesPorTecnico = $this->getLlavesPorTecnico($startDate, $endDate);
        $descargasManuales = $this->getCargasDescargas($startDate, $endDate);
        $ventasDetalladasPorTecnico = $this->getVentasDetalladasPorTecnico($startDate, $endDate);

        $descargasManualesFormato = [
            'tecnico' => 'Manual',
            'llaves' => collect($descargasManuales)->groupBy('id_producto')->map(function ($grupo) {
                $primerRegistro = $grupo->first();
                $idProducto = $primerRegistro['id_producto'] ?? null;
                $nombreProducto = $primerRegistro['producto'] ?? 'Producto no encontrado';

                $almacenes = $grupo->groupBy('id_almacen')->map(function ($almacenGrupo, $almacenId) use ($idProducto) {
                    $cantidad = $almacenGrupo->map(function ($item) {
                        $cantidadAnterior = $item['cantidad_anterior'] ?? 0;
                        $cantidadNueva = $item['cantidad_nueva'] ?? 0;
                        return abs($cantidadNueva - $cantidadAnterior);
                    })->sum();

                    $precio = $almacenGrupo->first()['precio'] ?? 0;

                    return [
                        'cantidad' => $cantidad,
                        'total' => $cantidad * $precio,
                        'id_producto' => $idProducto,
                        'id_almacen' => $almacenId
                    ];
                })->toArray();

                $totalCantidad = $grupo->map(function ($item) {
                    $cantidadAnterior = $item['cantidad_anterior'] ?? 0;
                    $cantidadNueva = $item['cantidad_nueva'] ?? 0;
                    return abs($cantidadNueva - $cantidadAnterior);
                })->sum();

                $totalValor = $grupo->map(function ($item) {
                    $cantidadAnterior = $item['cantidad_anterior'] ?? 0;
                    $cantidadNueva = $item['cantidad_nueva'] ?? 0;
                    $precio = $item['precio'] ?? 0;
                    return abs($cantidadNueva - $cantidadAnterior) * $precio;
                })->sum();

                return [
                    'nombre' => $nombreProducto,
                    'id_producto' => $idProducto,
                    'almacenes' => $almacenes,
                    'total_cantidad' => $totalCantidad,
                    'total_valor' => $totalValor,
                ];
            })->values()->toArray(),
            'total_llaves' => collect($descargasManuales)->map(function ($item) {
                $cantidadAnterior = $item['cantidad_anterior'] ?? 0;
                $cantidadNueva = $item['cantidad_nueva'] ?? 0;
                return abs($cantidadNueva - $cantidadAnterior);
            })->sum(),
            'total_valor' => collect($descargasManuales)->map(function ($item) {
                $cantidadAnterior = $item['cantidad_anterior'] ?? 0;
                $cantidadNueva = $item['cantidad_nueva'] ?? 0;
                $precio = $item['precio'] ?? 0;
                return abs($cantidadNueva - $cantidadAnterior) * $precio;
            })->sum()
        ];

        $llavesPorTecnico = collect($llavesPorTecnico);
        $llavesPorTecnico->push($descargasManualesFormato);

        $totales = $this->calcularTotales(
            $reporteVentas,
            $reporteCostosGastos,
            $ingresosRecibidos,
            $llavesPorTecnico
        );

        $totalCostosLlaves = $llavesPorTecnico->sum('total_valor');

        $ganancia = ($totales['totalVentas'] ?? 0)
            - ($totales['totalCostos'] ?? 0)
            - ($totalCostosLlaves ?? 0)
            - ($totales['totalGastos'] ?? 0);

        $retiroDueño = Nempleado::whereHas('empleado', function ($query) {
            $query->where('cargo', 5);
        })
            ->whereBetween('fecha_pago', [$startDate, $endDate])
            ->sum('total_pagado');

        $gananciaFinal = $ganancia - $retiroDueño;


        $ventasPorLugarVenta = $this->getVentasPorLugarVenta($startDate, $endDate);


        $ventasPorTrabajo = $this->getVentasPorTrabajo($startDate, $endDate, $metodosPago);


        $ventasPorCliente = $this->getVentasPorCliente($startDate, $endDate);


        $resumenTrabajos = $this->getResumenTrabajos($startDate, $endDate);

        $almacenesDisponibles = Almacene::pluck('nombre', 'id_almacen')->toArray();


        $data = [
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            'reporteVentas' => $reporteVentas,
            'reporteCostosGastos' => $reporteCostosGastos,
            'ingresosRecibidos' => $ingresosRecibidos,
            'llavesPorTecnico' => $llavesPorTecnico,
            'totales' => $totales,
            'totalCostosLlaves' => $totalCostosLlaves,
            'ganancia' => $ganancia,
            'retiroDueño' => $retiroDueño,
            'gananciaFinal' => $gananciaFinal,
            'ventasDetalladasPorTecnico' => $ventasDetalladasPorTecnico,
            'ventasPorLugarVenta' => $ventasPorLugarVenta,
            'ventasPorTrabajo' => $ventasPorTrabajo,
            'resumenTrabajos' => $resumenTrabajos,
            'ventasPorCliente' => $ventasPorCliente,
            'almacenesDisponibles' => $almacenesDisponibles
        ];

        return Excel::download(new CierreSemanalExport($data), 'cierre-semanal-' . $startDate->format('Y') . '-' . $startDate->format('W') . '.xlsx');
    }

    private function getLlavesPorTecnico($startDate, $endDate)
    {
        return Empleado::with([
            'ventas' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha_h', [$startDate, $endDate]);
            }
        ])
            ->whereHas('ventas', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha_h', [$startDate, $endDate]);
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
                                    $llaveNombre = $producto['nombre_producto'] ?? 'Llave sin nombre';
                                    $almacenId = $producto['almacen'];
                                    $cantidad = $producto['cantidad'];
                                    $precio = $producto['precio'] ?? 0;

                                    if (!$llavesInfo->has($llaveNombre)) {
                                        $llavesInfo->put($llaveNombre, [
                                            'nombre' => $llaveNombre,
                                            'id_producto' => $producto['producto'] ?? null,
                                            'almacenes' => collect(),
                                            'total_cantidad' => 0,
                                            'total_valor' => 0
                                        ]);
                                    }

                                    $llaveData = $llavesInfo->get($llaveNombre);

                                    if (!$llaveData['almacenes']->has($almacenId)) {
                                        $llaveData['almacenes']->put($almacenId, [
                                            'cantidad' => 0,
                                            'total' => 0
                                        ]);
                                    }

                                    $almacenData = $llaveData['almacenes'][$almacenId];
                                    $almacenData['cantidad'] += $cantidad;
                                    $almacenData['total'] += ($cantidad * $precio);

                                    $llaveData['almacenes'][$almacenId] = $almacenData;
                                    $llaveData['total_cantidad'] += $cantidad;
                                    $llaveData['total_valor'] += ($cantidad * $precio);

                                    $llavesInfo->put($llaveNombre, $llaveData);

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

    private function getWeeksWithDates($year)
    {
        $weeks = [];
        $date = Carbon::create($year, 1, 1)->startOfWeek();

        if ($date->weekOfYear > 1) {
            $date->subWeek();
        }

        for ($i = 1; $i <= 52; $i++) {
            $start = $date->copy();
            $end = $date->copy()->endOfWeek();

            $weeks[$i] = [
                'number' => $i,
                'start' => $start->format('d M'),
                'end' => $end->format('d M'),
                'full' => $start->format('d M') . ' - ' . $end->format('d M Y')
            ];

            $date->addWeek();
        }

        return $weeks;
    }

    private function getAvailableYears()
    {
        return RegistroV::selectRaw('EXTRACT(YEAR FROM fecha_h) as year')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->pluck('year');
    }

    private function getVentasPorTecnico($startDate, $endDate)
    {
        return Empleado::with(['ventas' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('fecha_h', [$startDate, $endDate]);
        }])
            ->whereHas('ventas', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha_h', [$startDate, $endDate]);
            })
            ->get()
            ->map(function ($tecnico) {
                $ventasContado = $tecnico->ventas->where('tipo_venta', 'contado')->sum('valor_v');
                $ventasCredito = $tecnico->ventas->where('tipo_venta', 'credito')->sum('valor_v');

                return [
                    'tecnico' => $tecnico->nombre,
                    'ventas_contado' => $ventasContado,
                    'ventas_credito' => $ventasCredito,
                    'total_ventas' => $ventasContado + $ventasCredito
                ];
            });
    }

    private function getCostosGastosPorTecnico($startDate, $endDate, $metodosPago)
    {
        return Empleado::with([
            'costos' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('f_costos', [$startDate, $endDate]);
            },
            'gastos' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('f_gastos', [$startDate, $endDate]);
            },
            'pagosEmpleados' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha_pago', [$startDate, $endDate]);
            }
        ])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('cargo', '!=', 5)
                    ->where(function ($q) use ($startDate, $endDate) {
                        $q->whereHas('costos', function ($q) use ($startDate, $endDate) {
                            $q->whereBetween('f_costos', [$startDate, $endDate]);
                        })
                            ->orWhereHas('gastos', function ($q) use ($startDate, $endDate) {
                                $q->whereBetween('f_gastos', [$startDate, $endDate]);
                            })
                            ->orWhereHas('pagosEmpleados', function ($q) use ($startDate, $endDate) {
                                $q->whereBetween('fecha_pago', [$startDate, $endDate]);
                            });
                    });
            })
            ->get()
            ->map(function ($tecnico) use ($metodosPago) {
                $pagosEmpleado = $tecnico->pagosEmpleados->map(function ($pago) use ($tecnico) {
                    $metodoPagoOrigen = $pago->metodo_pago;
                    $metodoPagoNombre = 'Desconocido';

                    if (is_string($metodoPagoOrigen)) {
                        $metodoDecodificado = json_decode($metodoPagoOrigen, true);
                        if (is_array($metodoDecodificado) && isset($metodoDecodificado[0]['nombre'])) {
                            $metodoPagoNombre = $metodoDecodificado[0]['nombre'];
                        } elseif (!empty($metodoPagoOrigen)) {
                            $metodoPagoNombre = $metodoPagoOrigen;
                        }
                    } elseif (is_array($metodoPagoOrigen) && isset($metodoPagoOrigen[0]['nombre'])) {
                        $metodoPagoNombre = $metodoPagoOrigen[0]['nombre'];
                    }

                    $tipoEmpleado = (int) ($tecnico->tipo ?? 1);
                    $tipoClasificado = $tipoEmpleado === 1 ? 1 : 2;

                    return [
                        'valor' => (float) $pago->total_pagado,
                        'metodo_pago' => ucfirst(strtolower($metodoPagoNombre)),
                        'fecha' => $pago->fecha_pago,
                        'tipo' => $tipoClasificado
                    ];
                })->toArray();

                $costosEmpleado = array_filter($pagosEmpleado, fn($pago) => ($pago['tipo'] ?? 1) === 1);
                $gastosEmpleado = array_filter($pagosEmpleado, fn($pago) => ($pago['tipo'] ?? 1) === 2);

                $costosCombinados = $tecnico->costos->toArray();
                $gastosCombinados = $tecnico->gastos->toArray();

                if (!empty($costosEmpleado)) {
                    $costosCombinados = array_merge($costosCombinados, $costosEmpleado);
                }

                if (!empty($gastosEmpleado)) {
                    $gastosCombinados = array_merge($gastosCombinados, $gastosEmpleado);
                }

                return [
                    'tecnico' => $tecnico->nombre,
                    'costos' => $this->procesarTransacciones($costosCombinados, $metodosPago),
                    'gastos' => $this->procesarTransacciones($gastosCombinados, $metodosPago)
                ];
            });
    }

    private function getVentasDetalladasPorTecnico($startDate, $endDate)
    {
        return Empleado::with(['ventas' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('fecha_h', [$startDate, $endDate])
                ->with(['costosAsociados', 'gastosAsociados']);
        }])
            ->whereHas('ventas', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha_h', [$startDate, $endDate]);
            })
            ->get()
            ->map(function ($tecnico) {
                return [
                    'tecnico' => $tecnico->nombre,
                    'ventas' => $tecnico->ventas->map(function ($venta) {
                        $items = json_decode($venta->items, true) ?? [];
                        $pagos = $this->parsePagos($venta->pagos ?? '[]');

                        $trabajos = collect($items)->map(function ($item) {
                            // Obtener el nombre del trabajo usando el trabajo_id si está presente
                            $nombreTrabajo = $item['trabajo_id']
                                ? $this->getNombreTrabajoPorId($item['trabajo_id'])
                                : ($this->formatosTrabajo[$item['trabajo'] ?? 'Sin especificar'] ?? ($item['trabajo'] ?? 'Sin especificar'));

                            return [
                                'trabajo' => $nombreTrabajo,
                                'precio_trabajo' => $item['precio_trabajo'] ?? 0,
                                'descripcion' => $item['descripcion'] ? $this->decodeUnicode($item['descripcion']) : null,
                                'productos' => isset($item['productos']) ? array_map(function ($producto) {
                                    return [
                                        'producto' => $producto['producto'] ?? null,
                                        'nombre' => $producto['nombre_producto'] ?? 'Producto sin nombre',
                                        'cantidad' => $producto['cantidad'] ?? 0,
                                        'precio' => $producto['precio'] ?? 0,
                                        'almacen' => $producto['almacen'] ?? null
                                    ];
                                }, $item['productos']) : []
                            ];
                        });

                        $costos = $venta->costosAsociados->map(function ($costo) {
                            $pagosCosto = $this->parsePagos($costo->pagos ?? '[]');
                            return [
                                'id' => $costo->id_costos,
                                'descripcion' => $costo->descripcion,
                                'subcategoria' => $this->formatearSubcategoria($costo->subcategoria),
                                'valor' => $costo->valor,
                                'metodo_pago_id' => $pagosCosto[0]['metodo_pago'] ?? null,
                                'metodos_pago' => collect($pagosCosto)->pluck('metodo_pago')->unique()->implode(', '),
                                'fecha' => $costo->f_costos
                            ];
                        });

                        $gastos = $venta->gastosAsociados->map(function ($gasto) {
                            $pagosGasto = $this->parsePagos($gasto->pagos ?? '[]');
                            return [
                                'id' => $gasto->id_gastos,
                                'descripcion' => $gasto->descripcion,
                                'subcategoria' => $this->formatearSubcategoria($gasto->subcategoria),
                                'valor' => $gasto->valor,
                                'metodo_pago_id' => $pagosGasto[0]['metodo_pago'] ?? null,
                                'metodos_pago' => collect($pagosGasto)->pluck('metodo_pago')->unique()->implode(', '),
                                'fecha' => $gasto->f_gastos
                            ];
                        });

                        $metodosPago = collect($pagos)->pluck('metodo_pago')->unique()->implode(', ');
                        $totalPagado = collect($pagos)->sum('monto');

                        return [
                            'id' => $venta->id,
                            'fecha' => $venta->fecha_h,
                            'cliente' => $venta->cliente ? $venta->cliente->nombre : $venta->id_cliente,
                            'valor_total' => $venta->valor_v,
                            'tipo_venta' => $venta->tipo_venta,
                            'estatus' => $venta->estatus,
                            'pagos' => $pagos,
                            'metodos_pago' => $metodosPago,
                            'total_pagado' => $totalPagado,
                            'trabajos' => $trabajos->map(function ($trabajo) use ($trabajos) {
                                // Si el trabajo no está en la base de datos, mantener el nombre original
                                if (!isset($trabajos[$trabajo['trabajo']])) {
                                    return [
                                        'trabajo' => $trabajo['trabajo'],
                                        'nombre' => $trabajo['trabajo'],
                                        'precio_trabajo' => $trabajo['precio_trabajo'],
                                        'descripcion' => $trabajo['descripcion'],
                                        'productos' => $trabajo['productos'] ?? []
                                    ];
                                }

                                return [
                                    'trabajo' => $trabajo['trabajo'],
                                    'nombre' => $trabajos[$trabajo['trabajo']],
                                    'precio_trabajo' => $trabajo['precio_trabajo'],
                                    'descripcion' => $trabajo['descripcion'],
                                    'productos' => $trabajo['productos'] ?? []
                                ];
                            }),
                            'costos' => $costos,
                            'total_costos' => $costos->sum('valor'),
                            'gastos' => $gastos,
                            'total_gastos' => $gastos->sum('valor'),
                            'ganancia_bruta' => $venta->valor_v - $costos->sum('valor') - $gastos->sum('valor')
                        ];
                    }),
                    'total_ventas' => $tecnico->ventas->sum('valor_v'),
                    'total_costos' => $tecnico->ventas->sum(function ($venta) {
                        return $venta->costosAsociados->sum('valor');
                    }),
                    'total_gastos' => $tecnico->ventas->sum(function ($venta) {
                        return $venta->gastosAsociados->sum('valor');
                    }),
                    'ganancia_total' => $tecnico->ventas->sum('valor_v') -
                        $tecnico->ventas->sum(function ($venta) {
                            return $venta->costosAsociados->sum('valor') +
                                $venta->gastosAsociados->sum('valor');
                        })
                ];
            });
    }

    private function getIngresosRecibidos($startDate, $endDate, $metodosPago)
    {
        Log::info('Iniciando getIngresosRecibidos', [
            'startDate' => $startDate->format('Y-m-d H:i:s'),
            'endDate' => $endDate->format('Y-m-d H:i:s'),
            'metodosPago_count' => is_countable($metodosPago) ? count($metodosPago) : 0,
            'metodosPago_sample' => is_array($metodosPago)
                ? array_slice($metodosPago, 0, 3, true) + ['...' => '...']
                : []
        ]);

        $query = Empleado::with(['ventas' => function ($query) use ($startDate, $endDate) {
            $query->whereNotNull('pagos')
                ->whereRaw("json_array_length(pagos) > 0");
        }])
            ->whereHas('ventas', function ($query) {
                $query->whereNotNull('pagos')
                    ->whereRaw("json_array_length(pagos) > 0");
            });

        Log::info('Consulta SQL generada', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        $empleados = $query->get();

        Log::info('Empleados encontrados', [
            'count' => $empleados->count(),
            'empleados' => $empleados->pluck('nombre', 'id')
        ]);

        $result = $empleados->map(function ($tecnico) use ($startDate, $endDate, $metodosPago) {
            Log::info('Procesando técnico', [
                'tecnico_id' => $tecnico->id,
                'tecnico_nombre' => $tecnico->nombre,
                'ventas_count' => $tecnico->ventas->count()
            ]);

            $pagosRecibidos = collect();

            foreach ($tecnico->ventas as $venta) {
                $pagos = $this->parsePagos($venta->pagos ?? '[]');

                Log::debug('Venta procesada', [
                    'venta_id' => $venta->id,
                    'fecha_venta' => $venta->fecha_h,
                    'pagos_count' => count($pagos),
                    'pagos' => $pagos
                ]);

                foreach ($pagos as $pago) {
                    if (!isset($pago['fecha'], $pago['metodo_pago'], $pago['monto'])) {
                        Log::warning('Pago con formato incorrecto', ['pago' => $pago]);
                        continue;
                    }

                    $fechaPago = Carbon::parse($pago['fecha']);
                    $fechaVenta = $venta->fecha_h ? Carbon::parse($venta->fecha_h) : null;

                    Log::debug('Verificando pago', [
                        'fecha_pago' => $fechaPago->format('Y-m-d H:i:s'),
                        'rango_inicio' => $startDate->format('Y-m-d H:i:s'),
                        'rango_fin' => $endDate->format('Y-m-d H:i:s'),
                        'dentro_rango' => $fechaPago->between($startDate, $endDate) ? 'Sí' : 'No'
                    ]);

                    if ($fechaPago->between($startDate, $endDate)) {
                        // Solo mostramos pagos de créditos antiguas: excluir ventas al contado
                        // y pagos correspondientes al mismo mes del trabajo.
                        if ($venta->tipo_venta === 'contado') {
                            Log::debug('Pago descartado por ser venta al contado', ['venta_id' => $venta->id]);
                            continue;
                        }

                        if ($fechaVenta && $fechaVenta->format('Ym') >= $fechaPago->format('Ym')) {
                            Log::debug('Pago descartado por ser del mismo mes que la venta', [
                                'venta_id' => $venta->id,
                                'fecha_venta' => $fechaVenta->format('Y-m-d'),
                                'fecha_pago' => $fechaPago->format('Y-m-d')
                            ]);
                            continue;
                        }

                        $infoPago = [
                            'metodo_pago' => $metodosPago[$pago['metodo_pago']] ?? 'Desconocido',
                            'monto' => $pago['monto'],
                            'fecha_venta' => $venta->fecha_h,
                            'fecha_pago' => $pago['fecha']
                        ];

                        Log::debug('Pago válido agregado', $infoPago);
                        $pagosRecibidos->push($infoPago);
                    }
                }
            }

            $resultado = [
                'tecnico' => $tecnico->nombre,
                'pagos' => $pagosRecibidos,
                'total' => $pagosRecibidos->sum('monto')
            ];

            Log::info('Resultado del técnico', [
                'tecnico' => $tecnico->nombre,
                'pagos_count' => $pagosRecibidos->count(),
                'total' => $resultado['total']
            ]);

            return $resultado;
        });

        $totalPagos = $result->sum(function ($item) {
            return $item['pagos']->count();
        });
        $montoTotal = $result->sum('total');

        Log::info('Resultado final de getIngresosRecibidos', [
            'total_empleados' => $result->count(),
            'total_pagos' => $totalPagos,
            'monto_total' => $montoTotal,
            'empleados_con_pagos' => $result->filter(function ($item) {
                return $item['pagos']->isNotEmpty();
            })->map(function ($item) {
                return [
                    'tecnico' => $item['tecnico'],
                    'total_pagos' => $item['pagos']->count(),
                    'monto_total' => $item['total']
                ];
            })
        ]);

        return $result;
    }

    private function getVentasPorCliente($startDate, $endDate)
    {
        return RegistroV::whereBetween('fecha_h', [$startDate, $endDate])
            ->with('cliente')
            ->get()
            ->groupBy('id_cliente')
            ->map(function ($ventas, $idCliente) {
                $cliente = $ventas->first()->cliente;
                return [
                    'id_cliente' => $idCliente,
                    'cliente' => $cliente ? $cliente->nombre : $idCliente,
                    'ventas_contado' => $ventas->where('tipo_venta', 'contado')->sum('valor_v'),
                    'ventas_credito' => $ventas->where('tipo_venta', 'credito')->sum('valor_v'),
                    'total_ventas' => $ventas->sum('valor_v')
                ];
            })
            ->values()
            ->sortByDesc('total_ventas');
    }

    private function getVentasPorTrabajo($startDate, $endDate, $metodosPago)
    {
        $ventas = RegistroV::whereBetween('fecha_h', [$startDate, $endDate])
            ->get(['id', 'tipo_venta', 'valor_v', 'pagos', 'items']);

        $contado = collect();
        $credito = collect();

        foreach ($ventas as $venta) {
            $items = json_decode($venta->items, true) ?? [];
            $pagosVenta = $this->parsePagos($venta->pagos);

            $totalItems = count($items);
            $valorPorItem = $totalItems > 0 ? $venta->valor_v / $totalItems : $venta->valor_v;

            foreach ($items as $item) {
                // Obtener el nombre del trabajo usando el trabajo_id si existe
                $trabajoNombre = $item['trabajo_id']
                    ? $this->getNombreTrabajoPorId($item['trabajo_id'])
                    : ($this->formatosTrabajo[$item['trabajo'] ?? 'Sin especificar'] ?? ($item['trabajo'] ?? 'Sin especificar'));
                $trabajoKey = $trabajoNombre;

                if ($venta->tipo_venta === 'contado') {
                    if (!$contado->has($trabajoNombre)) {
                        $contado->put($trabajoNombre, [
                            'metodos' => collect(),
                            'total' => 0,
                            'trabajo_id' => $item['trabajo_id'] ?? null
                        ]);
                    }

                    $trabajoData = $contado->get($trabajoNombre);
                    $trabajoData['total'] += $valorPorItem;

                    foreach ($pagosVenta as $pago) {
                        $metodoNombre = $metodosPago[$pago['metodo_pago']] ?? 'Método ' . $pago['metodo_pago'];
                        $montoProporcional = $pago['monto'] / $totalItems;

                        if (!$trabajoData['metodos']->has($metodoNombre)) {
                            $trabajoData['metodos']->put($metodoNombre, [
                                'total' => 0,
                                'count' => 0,
                                'metodo_id' => $pago['metodo_pago'] ?? null
                            ]);
                        }

                        $metodoData = $trabajoData['metodos']->get($metodoNombre);
                        $metodoData['total'] += $montoProporcional;
                        $metodoData['count'] += 1;
                        $trabajoData['metodos']->put($metodoNombre, $metodoData);
                    }

                    $contado->put($trabajoNombre, $trabajoData);
                } else {
                    if (!$credito->has($trabajoNombre)) {
                        $credito->put($trabajoNombre, [
                            'metodos' => collect(),
                            'total' => 0,
                            'trabajo_id' => $item['trabajo_id'] ?? null
                        ]);
                    }

                    $trabajoData = $credito->get($trabajoNombre);
                    $trabajoData['total'] += $valorPorItem;

                    foreach ($pagosVenta as $pago) {
                        $metodoNombre = $metodosPago[$pago['metodo_pago']] ?? 'Método ' . $pago['metodo_pago'];
                        $montoProporcional = $pago['monto'] / $totalItems;

                        if (!$trabajoData['metodos']->has($metodoNombre)) {
                            $trabajoData['metodos']->put($metodoNombre, [
                                'total' => 0,
                                'count' => 0,
                                'metodo_id' => $pago['metodo_pago'] ?? null
                            ]);
                        }

                        $metodoData = $trabajoData['metodos']->get($metodoNombre);
                        $metodoData['total'] += $montoProporcional;
                        $metodoData['count'] += 1;
                        $trabajoData['metodos']->put($metodoNombre, $metodoData);
                    }

                    $credito->put($trabajoNombre, $trabajoData);
                }
            }
        }

        return [
            'contado' => $contado->sortByDesc('total'),
            'credito' => $credito->sortByDesc('total'),
            'total_contado' => $contado->sum('total'),
            'total_credito' => $credito->sum('total')
        ];
    }

    private function getResumenTrabajos($startDate, $endDate)
    {
        $ventas = RegistroV::whereBetween('fecha_h', [$startDate, $endDate])
            ->get(['items']);

        $trabajos = collect();

        foreach ($ventas as $venta) {
            $items = json_decode($venta->items, true) ?? [];

            foreach ($items as $item) {
                // Obtener el nombre del trabajo usando el trabajo_id si existe
                $trabajoNombre = $item['trabajo_id']
                    ? $this->getNombreTrabajoPorId($item['trabajo_id'])
                    : ($this->formatosTrabajo[$item['trabajo'] ?? 'Sin especificar'] ?? ($item['trabajo'] ?? 'Sin especificar'));
                $trabajoKey = $trabajoNombre;
                $trabajoId = $item['trabajo_id'] ?? null;

                if (!$trabajos->has($trabajoKey)) {
                    $trabajos->put($trabajoKey, [
                        'cantidad' => 0,
                        'trabajo_id' => $trabajoId
                    ]);
                }

                $trabajoData = $trabajos->get($trabajoKey);
                $trabajoData['cantidad']++;
                $trabajos->put($trabajoKey, $trabajoData);
            }
        }

        return $trabajos->map(function ($data, $trabajoKey) {
            return [
                'cantidad' => $data['cantidad'],
                'nombre' => $trabajoKey,
                'trabajo_id' => $data['trabajo_id']
            ];
        })->sortByDesc('cantidad')->values();
    }

    private function getVentasPorLugarVenta($startDate, $endDate)
    {
        return RegistroV::whereBetween('fecha_h', [$startDate, $endDate])
            ->get(['id', 'lugarventa', 'valor_v'])
            ->groupBy('lugarventa')
            ->map(function ($ventas, $lugarVenta) {
                return [
                    'nombre' => $lugarVenta ?? 'Sin especificar',
                    'cantidad' => $ventas->count(),
                    'monto' => $ventas->sum('valor_v')
                ];
            });
    }

    private function parsePagos($pagosData)
    {
        if (is_string($pagosData)) {
            return json_decode($pagosData, true) ?? [];
        }

        if (is_array($pagosData)) {
            return $pagosData;
        }

        return [];
    }

    private function procesarTransacciones($transacciones, $metodosPago)
    {
        $metodosSample = is_array($metodosPago)
            ? array_slice($metodosPago, 0, 3, true) + ['...' => '...']
            : ($metodosPago instanceof \Illuminate\Support\Collection
                ? $metodosPago->take(3)->toArray() + ['...' => '...']
                : []);

        Log::info('Iniciando procesarTransacciones', [
            'transacciones_count' => is_countable($transacciones) ? count($transacciones) : 'N/A',
            'metodos_pago_count' => is_countable($metodosPago) ? count($metodosPago) : 0,
            'metodos_pago_sample' => $metodosSample
        ]);

        if (!is_array($transacciones)) {
            $transacciones = $transacciones->toArray();
        }

        $resultados = [];

        foreach ($transacciones as $transaccion) {
            if (!is_array($transaccion)) {
                Log::warning('Transacción no es un array', ['transaccion' => $transaccion]);
                continue;
            }

            Log::debug('Procesando transacción', ['transaccion' => $transaccion]);

            if (isset($transaccion['tipo'])) {
                $metodoPagoData = json_decode($transaccion['metodo_pago'] ?? '[]', true);
                $metodoPago = is_array($metodoPagoData) && isset($metodoPagoData[0]['nombre'])
                    ? $metodoPagoData[0]['nombre']
                    : (is_string($transaccion['metodo_pago'] ?? null)
                        ? $transaccion['metodo_pago']
                        : 'Desconocido');

                $metodoPago = ucfirst($metodoPago);

                $resultados[] = [
                    'subcategoria' => $transaccion['tipo'] == 1 ? 'Salario Cerrajero' : 'Gastos Personal',
                    'descripcion' => 'Pago a empleado (Nómina)',
                    'metodo_pago' => $metodoPago,
                    'total' => $transaccion['valor'] ?? $transaccion['total'] ?? 0,
                    'fecha_pago' => $transaccion['fecha'] ?? $transaccion['fecha_pago'] ?? null
                ];

                continue;
            } else {
                $pagos = [];

                if (isset($transaccion['pagos'])) {
                    if (is_string($transaccion['pagos'])) {
                        $pagos = json_decode($transaccion['pagos'], true) ?? [];
                    } elseif (is_array($transaccion['pagos'])) {
                        $pagos = $transaccion['pagos'];
                    }
                } elseif (isset($transaccion['metodo_pago'])) {
                    $pagos = [[
                        'metodo_pago' => $transaccion['metodo_pago'],
                        'monto' => $transaccion['valor'] ?? $transaccion['total'] ?? 0,
                        'fecha' => $transaccion['fecha'] ?? $transaccion['fecha_pago'] ?? null
                    ]];
                }

                foreach ($pagos as $pago) {
                    $metodoPagoKey = $pago['metodo_pago'] ?? null;
                    $metodoPagoNombre = 'Desconocido';

                    if (is_numeric($metodoPagoKey) && isset($metodosPago[$metodoPagoKey])) {
                        $metodoPagoNombre = $metodosPago[$metodoPagoKey];
                    } elseif (is_string($metodoPagoKey)) {
                        $metodoData = json_decode($metodoPagoKey, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($metodoData)) {
                            $metodoPagoNombre = $metodoData[0]['nombre'] ?? 'Desconocido';
                        } else {
                            $metodoPagoNombre = $metodoPagoKey;
                        }
                    }

                    $resultados[] = [
                        'subcategoria' => $this->formatearSubcategoria($transaccion['subcategoria'] ?? ''),
                        'descripcion' => $transaccion['descripcion'] ?? ($transaccion['concepto'] ?? ''),
                        'metodo_pago' => $metodoPagoNombre,
                        'total' => $pago['monto'] ?? $pago['valor'] ?? 0,
                        'fecha_pago' => $pago['fecha'] ?? $transaccion['fecha'] ?? null
                    ];
                }

                if (empty($pagos) && (isset($transaccion['valor']) || isset($transaccion['total']))) {
                    $resultados[] = [
                        'subcategoria' => $this->formatearSubcategoria($transaccion['subcategoria'] ?? ''),
                        'descripcion' => $transaccion['descripcion'] ?? ($transaccion['concepto'] ?? ''),
                        'metodo_pago' => 'Desconocido',
                        'total' => $transaccion['valor'] ?? $transaccion['total'] ?? 0,
                        'fecha_pago' => $transaccion['fecha'] ?? $transaccion['fecha_pago'] ?? null
                    ];
                }
            }
        }

        $agrupados = [];
        foreach ($resultados as $item) {
            $key = $item['subcategoria'] . '|' . $item['descripcion'] . '|' . $item['metodo_pago'];

            if (!isset($agrupados[$key])) {
                $agrupados[$key] = [
                    'subcategoria' => $item['subcategoria'],
                    'descripcion' => $item['descripcion'],
                    'metodo_pago' => $item['metodo_pago'],
                    'total' => 0,
                    'metodos_pago' => []
                ];
            }

            if (!in_array($item['metodo_pago'], $agrupados[$key]['metodos_pago'])) {
                $agrupados[$key]['metodos_pago'][] = $item['metodo_pago'];
            }

            $agrupados[$key]['total'] += $item['total'];
        }

        $resultados = array_values($agrupados);

        Log::info('Transacciones procesadas', [
            'transacciones_procesadas' => count($resultados),
            'ejemplo_resultado' => !empty($resultados) ? $resultados[0] : 'No hay resultados'
        ]);

        return $resultados;
    }

    private function calcularTotales($reporteVentas, $reporteCostosGastos, $ingresosRecibidos, $llavesData)
    {
        $costosLlaves = $llavesData->sum('total_valor');

        return [
            'totalVentasContado' => collect($reporteVentas)->sum('ventas_contado'),
            'totalVentasCredito' => collect($reporteVentas)->sum('ventas_credito'),
            'totalVentas' => collect($reporteVentas)->sum('total_ventas'),
            'totalCostos' => collect($reporteCostosGastos)->sum(fn($item) => collect($item['costos'])->sum('total')),
            'totalGastos' => collect($reporteCostosGastos)->sum(fn($item) => collect($item['gastos'])->sum('total')),
            'totalIngresosRecibidos' => $ingresosRecibidos->sum('total'),
            'totalGeneralLlaves' => $llavesData->sum('total_llaves'),
            'totalGeneralValorLlaves' => $costosLlaves
        ];
    }

    private function getCargasDescargas($startDate, $endDate)
    {
        return AjusteInventario::with(['producto', 'almacene', 'user'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('fecha_ajuste', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->whereNull('fecha_ajuste')
                            ->whereBetween('created_at', [$startDate, $endDate]);
                    });
            })
            ->where('tipo_ajuste', 'ajuste2')
            ->where('cierre', true)
            ->get()
            ->map(function ($ajuste) {
                $fecha = $ajuste->fecha_ajuste ?
                    \Carbon\Carbon::parse($ajuste->fecha_ajuste) :
                    \Carbon\Carbon::parse($ajuste->created_at);

                $producto = $ajuste->producto;
                $almacen = $ajuste->almacene;
                $usuario = $ajuste->user;

                $productoNombre = 'Producto no encontrado';
                if ($producto) {
                    $productoNombre = !empty($producto->item)
                        ? $producto->item
                        : (!empty($producto->nombre) ? $producto->nombre : $productoNombre);
                }

                $precioProducto = 0;
                if ($producto && isset($producto->precio)) {
                    $precioProducto = $producto->precio;
                }

                $precio = $ajuste->precio_llave ?? $precioProducto;

                return [
                    'usuario' => $usuario->name ?? 'Sistema',
                    'producto' => $productoNombre,
                    'id_producto' => $producto->id_producto ?? null,
                    'almacen' => $almacen->nombre ?? 'Sin almacén',
                    'id_almacen' => $almacen->id_almacen ?? null,
                    'tipo' => $ajuste->tipo_ajuste,
                    'cantidad' => abs($ajuste->diferencia),
                    'cantidad_anterior' => $ajuste->cantidad_anterior,
                    'cantidad_nueva' => $ajuste->cantidad_nueva,
                    'motivo' => $ajuste->descripcion,
                    'fecha' => $fecha->format('d/m/Y'),
                    'es_carga' => false,
                    'precio' => (float) $precio,
                    'precio_llave' => $ajuste->precio_llave
                ];
            });
    }

    private function getVentasAlContado($startDate, $endDate)
    {
        return RegistroV::whereBetween('fecha_h', [$startDate, $endDate])
            ->where('metodo_pce', 'contado')
            ->get(['id', 'lugarventa', 'valor_v'])
            ->groupBy('lugarventa')
            ->map(function ($ventas, $lugarVenta) {
                return [
                    'nombre' => $lugarVenta ?? 'Sin especificar',
                    'cantidad' => $ventas->count(),
                    'monto' => $ventas->sum('valor_v')
                ];
            });
    }

    private function getVentasCredito($startDate, $endDate)
    {
        return RegistroV::whereBetween('fecha_h', [$startDate, $endDate])
            ->where('metodo_pce', 'credito')
            ->get(['id', 'lugarventa', 'valor_v'])
            ->groupBy('lugarventa')
            ->map(function ($ventas, $lugarVenta) {
                return [
                    'nombre' => $lugarVenta ?? 'Sin especificar',
                    'cantidad' => $ventas->count(),
                    'monto' => $ventas->sum('valor_v')
                ];
            });
    }

    private function formatearSubcategoria($subcategoria)
    {
        $formateadas = [
            'compras_insumos' => 'Compras Insumos',
            'gasolina' => 'Gasolina',
            'mantenimiento_vanes' => 'Mantenimiento Vanes',
            'salario_cerrajero' => 'Salario Cerrajero',
            'depreciacion_maquinas' => 'Depreciación Máquinas',
            'seguros_vehiculos' => 'Seguros Vehículos',
            'alquiler_pulga' => 'Alquiler Pulga',
            'codigos' => 'Códigos',
            'servicios_subcontratados' => 'Servicios Subcontratados'
        ];

        return $formateadas[$subcategoria] ?? ucfirst(str_replace('_', ' ', $subcategoria));
    }
}
