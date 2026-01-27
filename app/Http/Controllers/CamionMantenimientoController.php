<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CamionMantenimientoController extends Controller
{
  public function index(Request $request): View
  {
    $umbralKm = 5000;

    $camiones = Camion::query()
      ->with('ultimoKilometraje')
      ->orderBy('nombre')
      ->get();

    $camionesActivos = $camiones->where('activo', true);

    return view('camiones.mantenimiento', [
      'camiones' => $camionesActivos,
      'umbralKm' => $umbralKm,
    ]);
  }

  public function marcarCambioAceite(Request $request, Camion $camion): RedirectResponse
  {
    $request->validate([
      'kilometraje_base' => ['nullable', 'integer', 'min:0'],
    ]);

    $ultimoKm = $camion->ultimoKilometraje?->kilometraje;
    $base = $request->input('kilometraje_base');

    if ($base === null) {
      $base = $ultimoKm;
    }

    if ($base === null) {
      return redirect()
        ->route('camiones.mantenimiento')
        ->with('error', 'No hay kilometraje registrado para fijar el cambio de aceite.');
    }

    $camion->update([
      'ultimo_cambio_aceite_km' => (int) $base,
    ]);

    return redirect()
      ->route('camiones.mantenimiento')
      ->with('success', 'Cambio de aceite marcado.');
  }
}
