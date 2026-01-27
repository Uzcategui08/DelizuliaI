<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CamionController extends Controller
{
  public function index(): View
  {
    $camiones = Camion::query()->orderBy('nombre')->get();

    return view('camiones.index', compact('camiones'));
  }

  public function create(): View
  {
    $camion = new Camion();

    return view('camiones.create', compact('camion'));
  }

  public function store(Request $request): RedirectResponse
  {
    $data = $this->validatedData($request);

    Camion::create($data);

    return redirect()
      ->route('camiones.index')
      ->with('success', 'Camión creado correctamente.');
  }

  public function edit(Camion $camion): View
  {
    return view('camiones.edit', compact('camion'));
  }

  public function update(Request $request, Camion $camion): RedirectResponse
  {
    $data = $this->validatedData($request);

    $camion->update($data);

    return redirect()
      ->route('camiones.index')
      ->with('success', 'Camión actualizado correctamente.');
  }

  public function destroy(Camion $camion): RedirectResponse
  {
    $camion->delete();

    return redirect()
      ->route('camiones.index')
      ->with('success', 'Camión eliminado.');
  }

  protected function validatedData(Request $request): array
  {
    return $request->validate([
      'nombre' => ['required', 'string', 'max:255'],
      'placa' => ['nullable', 'string', 'max:255'],
      'ultimo_cambio_aceite_km' => ['nullable', 'integer', 'min:0'],
      'activo' => ['sometimes', 'boolean'],
    ]);
  }
}
