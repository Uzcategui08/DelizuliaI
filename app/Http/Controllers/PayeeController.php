<?php

namespace App\Http\Controllers;

use App\Models\Payee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $payees = Payee::query()->orderBy('name')->get();

        return view('payees.index', compact('payees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $payee = new Payee();

        return view('payees.create', compact('payee'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        Payee::create($data);

        return redirect()
            ->route('payees.index')
            ->with('success', 'Destinatario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payee $payee): RedirectResponse
    {
        return redirect()->route('payees.edit', $payee);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payee $payee): View
    {
        return view('payees.edit', compact('payee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payee $payee): RedirectResponse
    {
        $data = $this->validatedData($request);

        $payee->update($data);

        return redirect()
            ->route('payees.index')
            ->with('success', 'Destinatario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payee $payee): RedirectResponse
    {
        $payee->delete();

        return redirect()
            ->route('payees.index')
            ->with('success', 'Destinatario eliminado.');
    }

    /**
     * Validate the incoming request data.
     */
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'alias' => ['nullable', 'string', 'max:255'],
            'contact_info' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
