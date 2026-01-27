<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Nempleado;
use App\Models\Pnomina;
use App\Models\Empleado;
use App\Models\Cuota;
use App\Models\Abono;
use App\Models\TiposDePago;
use App\Models\Descuento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\NempleadoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class NempleadoController extends Controller
{
    public function index(Request $request): View
    {
        $nempleados = Nempleado::all();
        return view('nempleado.index', compact('nempleados'));
    }

    public function create(): View
    {
        $nempleado = new Nempleado();
        $empleados = Empleado::all();
        $metodosPago = TiposDePago::all();
        return view('nempleado.create', compact('nempleado', 'empleados', 'metodosPago'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            Log::info('Inicio de store Nempleado');
            Log::info('Datos del request:', [
                'all' => $request->all(),
                'empleado_id' => $request->id_empleado,
                'tipo_pago' => $request->tipo_pago ?? 'no especificado'
            ]);
            $empleado = Empleado::find($request->id_empleado);
            Log::info('Empleado encontrado:', [
                'id' => $empleado->id_empleado,
                'nombre' => $empleado->nombre,
                'tipo_pago' => $empleado->tipo_pago
            ]);
            
            if (!$empleado) {
                Log::error('Empleado no encontrado con ID: ' . $request->id_empleado);
                throw new \Exception("Empleado no encontrado");
            }

            if ($empleado->tipo_pago === 'retiro' && $request->has('sueldo_base_retiro')) {
                $request->merge(['sueldo_base' => $request->sueldo_base_retiro]);
            }

            $reglasValidacion = [
                'id_empleado' => 'required|exists:empleados,id_empleado',
                'fecha_desde' => 'required|date',
                'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
                'total_pagado' => 'required|numeric|min:0',
                'id_abonos_json' => $empleado->tipo_pago === 'comision' ? 'required|json' : 'nullable|json',
                'id_descuentos_json' => 'nullable|json',
                'metodo_pago_json' => 'required|json',
                'horas_trabajadas' => $empleado->tipo_pago === 'horas' ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
                'sueldo_base' => $empleado->tipo_pago === 'comision' ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
                'precio_hora_normal' => $empleado->tipo_pago === 'horas' ? 'required|numeric|min:0' : 'nullable',
                'precio_hora_extra' => $empleado->tipo_pago === 'horas' ? 'required|numeric|min:0' : 'nullable',
                'fecha_pago' => 'required|date',
            ];

            $validator = Validator::make($request->all(), $reglasValidacion, [
                'id_abonos_json.required' => 'Para pago por comisión debe seleccionar al menos un concepto',
                'metodo_pago_json.required' => 'Debe seleccionar al menos un método de pago'
            ]);

            if ($validator->fails()) {
                Log::error('Validación fallida:', [
                    'errors' => $validator->errors()->toArray(),
                    'request' => $request->all()
                ]);
                return back()->withErrors($validator)->withInput();
            }

            $abonosIds = $this->procesarIds($request->id_abonos_json ?? '[]', 'abono');
            $descuentosIds = $this->procesarIds($request->id_descuentos_json ?? '[]', 'descuento');
            $metodosPago = $this->procesarMetodosPago($request->metodo_pago_json);

            Log::info('Procesamiento de datos:', [
                'abonos_ids' => $abonosIds,
                'descuentos_ids' => $descuentosIds,
                'metodos_pago' => $metodosPago
            ]);

            if (empty($metodosPago)) {
                throw new \Exception("Debe seleccionar al menos un método de pago válido");
            }

            $this->validarPertenenciaEmpleado($request->id_empleado, $abonosIds, $descuentosIds);

            $totalAbonos = $abonosIds ? Abono::whereIn('id_abonos', $abonosIds)->sum('valor') : 0;
            $totalDescuentos = $descuentosIds ? Descuento::whereIn('id_descuentos', $descuentosIds)->sum('valor') : 0;

            $sueldoCalculado = 0;
            $detallePago = null;

            if ($empleado->tipo_pago === 'horas') {
                $horasTrabajadas = $request->horas_trabajadas ?? 0;
                $precioNormal = $request->precio_hora_normal ?? 0;
                $precioExtra = $request->precio_hora_extra ?? 0;

                $horasNormales = min($horasTrabajadas, 40);
                $horasExtras = max($horasTrabajadas - 40, 0);

                $sueldoCalculado = ($horasNormales * $precioNormal) + ($horasExtras * $precioExtra);
                $detallePago = "Horas normales: $horasNormales × $precioNormal = " . ($horasNormales * $precioNormal) . " | " .
                    "Horas extras: $horasExtras × $precioExtra = " . ($horasExtras * $precioExtra);
            } else {
                $sueldoCalculado = $request->sueldo_base ?? 0;
                $detallePago = "Monto: $sueldoCalculado";
            }

            $totalCalculado = $sueldoCalculado + $totalAbonos - $totalDescuentos;

            if (abs($totalCalculado - $request->total_pagado) > 0.01) {
                throw new \Exception("El total a pagar no coincide con los cálculos. Calculado: $totalCalculado, Recibido: {$request->total_pagado}");
            }

            if ($request->has('sueldo_base_retiro')) {
                $request->merge(['sueldo_base' => $request->sueldo_base_retiro]);
            }

            $nempleadoData = [
                'id_empleado' => $request->id_empleado,
                'id_abonos' => $abonosIds,
                'id_descuentos' => $descuentosIds,
                'sueldo_base' => $request->sueldo_base ?? 0,
                'total_descuentos' => $totalDescuentos,
                'total_abonos' => $totalAbonos,
                'total_pagado' => $request->total_pagado,
                'metodo_pago' => $metodosPago,
                'fecha_desde' => $request->fecha_desde,
                'fecha_hasta' => $request->fecha_hasta,
                'horas_trabajadas' => ($empleado->tipo_pago === 'horas') ? $request->horas_trabajadas : null,
                'detalle_pago' => $detallePago,
                'tipo_pago_empleado' => $empleado->tipo_pago,
                'fecha_pago' => $request->fecha_pago
            ];

            Log::info('Datos para crear Nempleado:', $nempleadoData);

            $nempleado = Nempleado::create($nempleadoData);
            Log::info('Nempleado creado:', [
                'id' => $nempleado->id_nempleado,
                'data' => $nempleado->toArray()
            ]);

            $this->marcarComoProcesados($abonosIds, $descuentosIds);

            DB::commit();
            Log::info('Transacción completada exitosamente');

            return redirect()->route('nempleados.index')
                ->with('success', 'Registro creado satisfactoriamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en store Nempleado:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return back()->withInput()
                ->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    protected function procesarIds($jsonString, $tipo)
    {
        $ids = json_decode($jsonString, true);

        if (!is_array($ids)) {
            throw new \Exception("Formato de $tipo inválido");
        }

        return collect($ids)
            ->filter(fn($id) => is_numeric($id) && $id > 0)
            ->map(fn($id) => (int)$id)
            ->unique()
            ->values()
            ->all();
    }

    protected function procesarMetodosPago($metodoPago)
    {
        if (empty($metodoPago)) {
            return [];
        }

        if (is_array($metodoPago) && isset($metodoPago[0]['nombre'])) {
            return $metodoPago;
        }

        if (is_array($metodoPago) && isset($metodoPago[0]['metodo_id'])) {
            return array_map(function ($metodo) {
                return [
                    'nombre' => $this->getNombreMetodoPago($metodo['metodo_id']),
                    'monto' => $metodo['monto'] ?? 0
                ];
            }, $metodoPago);
        }

        if (is_string($metodoPago)) {
            $metodos = json_decode($metodoPago, true);
            return is_array($metodos) ? $this->procesarMetodosPago($metodos) : [];
        }

        return [];
    }

    protected function validarPertenenciaEmpleado($empleadoId, $abonosIds, $descuentosIds)
    {
        if ($abonosIds) {
            $abonosInvalidos = Abono::whereIn('id_abonos', $abonosIds)
                ->where('id_empleado', '!=', $empleadoId)
                ->count();

            if ($abonosInvalidos > 0) {
                throw new \Exception("Algunos abonos no pertenecen al empleado");
            }
        }

        if ($descuentosIds) {
            $descuentosInvalidos = Descuento::whereIn('id_descuentos', $descuentosIds)
                ->where('id_empleado', '!=', $empleadoId)
                ->count();

            if ($descuentosInvalidos > 0) {
                throw new \Exception("Algunos descuentos no pertenecen al empleado");
            }
        }
    }

    protected function marcarComoProcesados($abonosIds, $descuentosIds)
    {
        if ($abonosIds) {
            Abono::whereIn('id_abonos', $abonosIds)
                ->update([
                    'status' => 1,
                    'fecha_pago' => now(),
                ]);
        }

        if ($descuentosIds) {
            Descuento::whereIn('id_descuentos', $descuentosIds)
                ->update([
                    'status' => 1,
                    'fecha_pago' => now(),
                ]);
        }
    }

    public function show($id): View
    {
        $nempleado = Nempleado::find($id);
        return view('nempleado.show', compact('nempleado'));
    }

    public function edit($id): View
    {
        $nempleado = Nempleado::find($id);
        $metodosPago = TiposDePago::all();
        return view('nempleado.edit', compact('nempleado', 'metodosPago'));
    }

    public function update(NempleadoRequest $request, Nempleado $nempleado): RedirectResponse
    {
        $nempleado->update($request->validated());
        return Redirect::route('nempleados.index')
            ->with('success', 'Registro actualizado satisfactoriamente.');
    }

    public function destroy($id): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $nempleado = Nempleado::findOrFail($id);

            if (!empty($nempleado->id_abonos)) {
                $abonosIds = is_string($nempleado->id_abonos)
                    ? json_decode($nempleado->id_abonos, true)
                    : $nempleado->id_abonos;

                if (is_array($abonosIds) && !empty($abonosIds)) {
                    Abono::whereIn('id_abonos', $abonosIds)
                        ->update([
                            'status' => 0,
                            'fecha_pago' => null
                        ]);
                }
            }

            if (!empty($nempleado->id_descuentos)) {
                $descuentosIds = is_string($nempleado->id_descuentos)
                    ? json_decode($nempleado->id_descuentos, true)
                    : $nempleado->id_descuentos;

                if (is_array($descuentosIds) && !empty($descuentosIds)) {
                    Descuento::whereIn('id_descuentos', $descuentosIds)
                        ->update([
                            'status' => 0,
                            'fecha_pago' => null
                        ]);
                }
            }

            $nempleado->delete();

            DB::commit();

            return Redirect::route('nempleados.index')
                ->with('success', 'Registro eliminado satisfactoriamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route('nempleados.index')
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function generarReciboIndividual($idNempleado)
    {
        try {
            $nominaEmpleado = Nempleado::with(['empleado'])
                ->findOrFail($idNempleado);
            
            $abonos = $this->procesarRelacion($nominaEmpleado->id_abonos, Abono::class, 'id_abonos', 'a_fecha');
            $descuentos = $this->procesarRelacion($nominaEmpleado->id_descuentos, Descuento::class, 'id_descuentos', 'd_fecha');
            $metodosPago = $this->procesarMetodosPago($nominaEmpleado->metodo_pago);

            $data = [
                'nomina' => $nominaEmpleado,
                'empleado' => $nominaEmpleado->empleado,
                'fecha_desde' => $nominaEmpleado->fecha_desde,
                'fecha_hasta' => $nominaEmpleado->fecha_hasta,
                'fecha_pago' => $nominaEmpleado->fecha_pago,
                'sueldo_base' => $nominaEmpleado->sueldo_base,
                'total_descuentos' => $nominaEmpleado->total_descuentos,
                'total_abonos' => $nominaEmpleado->total_abonos,
                'total_costos' => $nominaEmpleado->total_costos,
                'total_prestamos' => $nominaEmpleado->total_prestamos,
                'neto_pagado' => $nominaEmpleado->total_pagado,
                'descuentos' => $descuentos,
                'abonos' => $abonos,
                'metodos_pago' => $metodosPago,
                'tipo_pago_empleado' => $nominaEmpleado->tipo_pago_empleado,
                'horas_trabajadas' => $nominaEmpleado->horas_trabajadas,
                'detalle_pago' => $nominaEmpleado->detalle_pago
            ];

            return PDF::loadView('nempleado.pdf', $data)
                ->stream('recibo-' . $nominaEmpleado->empleado->cedula . '-' . $nominaEmpleado->created_at->format('Ymd') . '.pdf');
        } catch (\Exception $e) {
            abort(500, "Error al generar el recibo de pago: " . $e->getMessage());
        }
    }

    private function procesarRelacion($data, $model, $idField, $fechaField)
    {
        if (empty($data)) {
            return [];
        }

        $ids = [];

        if (is_string($data)) {
            $ids = json_decode($data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $ids = [];
            }
        } elseif (is_array($data)) {
            $ids = $data;
        }

        if (!empty($ids)) {
            return $model::whereIn($idField, $ids)
                ->orderBy($fechaField, 'desc')
                ->get();
        }

        return [];
    }

    public function generarReciboGeneral($fechaDesde, $fechaHasta)
    {
        try {
            $nominasEmpleados = Nempleado::with('empleado')
                ->whereBetween('fecha_pago', [
                    \Carbon\Carbon::parse($fechaDesde)->startOfDay(),
                    \Carbon\Carbon::parse($fechaHasta)->endOfDay()
                ])
                ->orderBy('fecha_pago', 'desc')
                ->get();

            $totales = [
                'totalPagado' => 0,
                'totalDescuentos' => 0,
                'totalCostos' => 0,
                'totalAbonos' => 0,
                'netoPagado' => 0,
                'totalSalarioBase' => 0
            ];

            $empleadosData = $nominasEmpleados->map(function ($nominaEmpleado) use (&$totales) {
                $empleado = $nominaEmpleado->empleado;

                $empleadoData = [
                    'id' => $empleado->id_empleado,
                    'nombre' => $empleado->nombre,
                    'cedula' => $empleado->cedula,
                    'cargo' => $empleado->cargo,
                    'salario_base' => $nominaEmpleado->sueldo_base,
                    'totalPagado' => $nominaEmpleado->sueldo_base + $nominaEmpleado->total_abonos,
                    'totalDescuentos' => $nominaEmpleado->total_descuentos,
                    'totalCostos' => $nominaEmpleado->total_costos,
                    'totalAbonos' => $nominaEmpleado->total_abonos,
                    'netoPagado' => $nominaEmpleado->total_pagado,
                    'metodos_pago' => $this->procesarMetodosPago($nominaEmpleado->metodo_pago)
                ];

                $totales['totalPagado'] += $empleadoData['totalPagado'];
                $totales['totalDescuentos'] += $empleadoData['totalDescuentos'];
                $totales['totalCostos'] += $empleadoData['totalCostos'];
                $totales['totalAbonos'] += $empleadoData['totalAbonos'];
                $totales['netoPagado'] += $empleadoData['netoPagado'];
                $totales['totalSalarioBase'] += $empleadoData['salario_base'];

                return $empleadoData;
            });

            $metodosPagoGlobales = $this->consolidarMetodosPago($nominasEmpleados);

            $data = [
                'fechaDesde' => $fechaDesde,
                'fechaHasta' => $fechaHasta,
                'empleados' => $empleadosData,
                'totales' => $totales,
                'metodosPagoGlobales' => $metodosPagoGlobales,
                'fechaGeneracion' => now()->format('d/m/Y H:i:s')
            ];

            return PDF::loadView('nempleado.pdfgeneral', $data)
                ->setPaper('a4', 'landscape')
                ->stream('nomina_general_' . $fechaDesde . '_' . $fechaHasta . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el reporte general');
        }
    }

    private function consolidarMetodosPago($nominasEmpleados)
    {
        $metodosConsolidados = [];

        foreach ($nominasEmpleados as $nomina) {
            $metodos = $this->procesarMetodosPago($nomina->metodo_pago);

            foreach ($metodos as $metodo) {
                if (!isset($metodosConsolidados[$metodo['nombre']])) {
                    $metodosConsolidados[$metodo['nombre']] = 0;
                }
                $metodosConsolidados[$metodo['nombre']] += $metodo['monto'];
            }
        }

        return $metodosConsolidados;
    }

    public function reporte(Request $request)
    {
        try {
            $empleados = Empleado::orderBy('nombre')->get();

            $pagosIndividuales = collect();
            $resumenGeneral = null;

            if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
                $fechaDesde = $request->fecha_desde;
                $fechaHasta = $request->fecha_hasta;

                if ($fechaDesde > $fechaHasta) {
                    throw new \Exception("La fecha desde no puede ser mayor que la fecha hasta");
                }

                if ($request->filled('tipo') && $request->tipo == 'individual') {
                    $query = Nempleado::with(['empleado'])
                        ->whereDate('fecha_pago', '>=', $fechaDesde)
                        ->whereDate('fecha_pago', '<=', $fechaHasta);

                    if ($request->filled('empleado_id')) {
                        $query->where('id_empleado', $request->empleado_id);
                    }

                    $pagosIndividuales = $query->orderBy('fecha_pago', 'desc')->get()
                        ->map(function ($pago) {

                            $pago->descripcion_pago = $this->getDescripcionPago($pago);
                            return $pago;
                        });
                } elseif ($request->filled('tipo') && $request->tipo == 'general') {
                    $pagos = Nempleado::with(['empleado'])
                        ->whereDate('fecha_pago', '>=', $fechaDesde)
                        ->whereDate('fecha_pago', '<=', $fechaHasta)
                        ->get();

                    $metodosPago = [];
                    foreach ($pagos as $pago) {
                        $metodos = $pago->metodo_pago;

                        if (is_string($metodos)) {
                            $metodos = json_decode($metodos, true);
                        }

                        if (is_array($metodos)) {
                            foreach ($metodos as $metodo) {
                                if (isset($metodo['metodo_id'])) {
                                    $nombreMetodo = $this->getNombreMetodoPago($metodo['metodo_id']);
                                    $monto = $metodo['monto'] ?? 0;
                                } elseif (isset($metodo['nombre'])) {
                                    $nombreMetodo = strtolower($metodo['nombre']);
                                    $monto = $metodo['monto'] ?? 0;
                                } else {
                                    continue;
                                }

                                if (!isset($metodosPago[$nombreMetodo])) {
                                    $metodosPago[$nombreMetodo] = 0;
                                }
                                $metodosPago[$nombreMetodo] += $monto;
                            }
                        }
                    }

                    $resumenGeneral = [
                        'total_empleados' => $pagos->unique('id_empleado')->count(),
                        'total_pagos' => $pagos->count(),
                        'total_pagado' => $pagos->reduce(function ($carry, $item) {
                            return $carry + (float)($item->total_pagado ?? 0);
                        }, 0),
                        'total_sueldo_base' => $pagos->reduce(function ($carry, $item) {
                            return $carry + (float)($item->sueldo_base ?? 0);
                        }, 0),
                        'total_abonos' => $pagos->reduce(function ($carry, $item) {
                            return $carry + (float)($item->total_abonos ?? 0);
                        }, 0),
                        'total_descuentos' => $pagos->reduce(function ($carry, $item) {
                            return $carry + (float)($item->total_descuentos ?? 0);
                        }, 0),
                        'total_costos' => $pagos->reduce(function ($carry, $item) {
                            return $carry + (float)($item->total_costos ?? 0);
                        }, 0),
                        'metodos_pago' => $metodosPago,
                        'fecha_desde' => $fechaDesde,
                        'fecha_hasta' => $fechaHasta,
                        'tipos_pago' => $pagos->groupBy('tipo_pago_empleado')->map->count()
                    ];
                }
            }

            if ($request->ajax()) {
                return view('nempleado.reporte', compact('empleados', 'pagosIndividuales', 'resumenGeneral'));
            }

            return view('nempleado.reporte', compact('empleados', 'pagosIndividuales', 'resumenGeneral'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->withError($e->getMessage());
        }
    }

    protected function getDescripcionPago($pago)
    {
        switch ($pago->tipo_pago_empleado) {
            case 'horas':
                return "Horas trabajadas: {$pago->horas_trabajadas} - {$pago->detalle_pago}";
            case 'comision':
                return "Pago por comisión - {$pago->detalle_pago}";
            default:
                return "Salario base";
        }
    }

    private function getNombreMetodoPago($id)
    {

        if (!is_numeric($id)) {
            return strtolower($id);
        }

        $metodo = TiposDePago::find($id);

        return $metodo ? strtolower($metodo->name) : 'otro';
    }

    public function getRegistros(Request $request)
    {
        $request->validate([
            'id_empleado' => 'required|exists:empleados,id_empleado',
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde'
        ]);

        $abonos = Abono::where('id_empleado', $request->id_empleado)
            ->where('status', 0)
            ->whereBetween('a_fecha', [$request->fecha_desde, $request->fecha_hasta])
            ->get();

        $descuentos = Descuento::where('id_empleado', $request->id_empleado)
            ->where('status', 0)
            ->whereBetween('d_fecha', [$request->fecha_desde, $request->fecha_hasta])
            ->get();

        return response()->json([
            'abonos' => $abonos,
            'descuentos' => $descuentos
        ]);
    }
}
