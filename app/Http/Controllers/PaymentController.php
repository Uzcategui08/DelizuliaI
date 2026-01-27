<?php

namespace App\Http\Controllers;

use App\Models\Payee;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $pendingPayments = Payment::query()
            ->with('payee')
            ->where('is_paid', false)
            ->orderByRaw('COALESCE(scheduled_for, created_at) ASC')
            ->get();

        $completedPayments = Payment::query()
            ->with('payee')
            ->where('is_paid', true)
            ->orderByDesc('paid_at')
            ->get();

        return view('payments.index', compact('pendingPayments', 'completedPayments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $payment = new Payment();
        $payees = Payee::query()->orderBy('name')->pluck('name', 'id');

        return view('payments.create', compact('payment', 'payees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['is_paid'] = $request->boolean('is_paid');
        $data['paid_at'] = $data['is_paid'] ? now() : null;

        Payment::create($data);

        return redirect()
            ->route('payments.index')
            ->with('success', 'Pago registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): RedirectResponse
    {
        return redirect()->route('payments.edit', $payment);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment): View
    {
        $payees = Payee::query()->orderBy('name')->pluck('name', 'id');

        return view('payments.edit', compact('payment', 'payees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['is_paid'] = $request->boolean('is_paid');
        $data['paid_at'] = $data['is_paid']
            ? ($payment->paid_at ?? now())
            : null;

        $payment->update($data);

        return redirect()
            ->route('payments.index')
            ->with('success', 'Pago actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->delete();

        return redirect()
            ->route('payments.index')
            ->with('success', 'Pago eliminado.');
    }

    /**
     * Toggle the paid status for a payment.
     */
    public function toggle(Payment $payment): RedirectResponse
    {
        $payment->toggleStatus(! $payment->is_paid);

        return back()->with('success', 'Estado del pago actualizado.');
    }

    /**
     * Validate the incoming request.
     */
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'payee_id' => ['required', 'exists:payees,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'scheduled_for' => ['nullable', 'date'],
            'reminder_at' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ]);
    }
}
