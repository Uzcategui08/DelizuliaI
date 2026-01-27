<?php

namespace App\Http\Controllers;

use App\Models\PaymentControl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentControlsExport;

class PaymentControlController extends Controller
{
    public function index(Request $request): View
    {
        $useDefaultWeek = !$request->filled('desde') && !$request->filled('hasta');

        $pagos = PaymentControl::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('nombre', 'like', $term)->orWhere('descripcion', 'like', $term);
                });
            })
            ->when($request->filled('desde'), function ($q) use ($request) {
                $q->whereDate('fecha', '>=', $request->desde);
            })
            ->when($request->filled('hasta'), function ($q) use ($request) {
                $q->whereDate('fecha', '<=', $request->hasta);
            })
            ->when($useDefaultWeek, function ($q) {
                $q->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()]);
            })
            ->orderByDesc('fecha')->orderByDesc('id')
            ->get();
        $aprobados = $pagos->where('aprobado', true)->where('largo_plazo', false);
        $noAprobados = $pagos->where('aprobado', false)->where('largo_plazo', false);
        $largoPlazo = $pagos->where('largo_plazo', true);
        return view('payment-controls.index', [
            'aprobados' => $aprobados,
            'noAprobados' => $noAprobados,
            'largoPlazo' => $largoPlazo,
            'filtro' => $request->q,
            'desde' => $request->desde,
            'hasta' => $request->hasta,
            'usa_semana_default' => $useDefaultWeek,
        ]);
    }

    public function create(): View
    {
        $pago = new PaymentControl([
            'fecha' => now()->format('Y-m-d'),
            'aprobado' => false,
            'pagado' => false,
        ]);
        return view('payment-controls.create', compact('pago'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        PaymentControl::create($data);
        return Redirect::route('payment-controls.index')->with('success', 'Pago creado');
    }

    public function edit(PaymentControl $paymentControl): View
    {
        return view('payment-controls.edit', ['pago' => $paymentControl]);
    }

    public function update(Request $request, PaymentControl $paymentControl): RedirectResponse
    {
        $data = $this->validateData($request);
        $paymentControl->update($data);
        return Redirect::route('payment-controls.index')->with('success', 'Pago actualizado');
    }

    public function toggleAprobado(PaymentControl $paymentControl): RedirectResponse
    {
        $paymentControl->update(['aprobado' => !$paymentControl->aprobado]);
        return back()->with('success', 'Estado de aprobado actualizado');
    }

    public function togglePagado(PaymentControl $paymentControl): RedirectResponse
    {
        $paymentControl->update(['pagado' => !$paymentControl->pagado]);
        return back()->with('success', 'Estado de pagado actualizado');
    }

    public function export(Request $request)
    {
        $useDefaultWeek = !$request->filled('desde') && !$request->filled('hasta');
        return Excel::download(new PaymentControlsExport(
            $request->q,
            $request->desde,
            $request->hasta,
            $useDefaultWeek
        ), 'pagos.xlsx');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'largo_plazo' => 'sometimes|boolean',
            'aprobado' => 'sometimes|boolean',
            'pagado' => 'sometimes|boolean',
        ], [], [
            'nombre' => 'nombre',
            'monto' => 'monto',
            'fecha' => 'fecha',
        ]);
    }
}
