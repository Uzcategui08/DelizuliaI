<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use App\Models\CamionKilometraje;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CamionKilometrajeController extends Controller
{
  public function store(Request $request): RedirectResponse
  {
    $data = $request->validate([
      'camion_id' => ['required', 'integer', Rule::exists('camiones', 'id')],
      'fecha' => ['required', 'date'],
      'kilometraje' => ['required', 'integer', 'min:0'],
      'nota' => ['nullable', 'string'],
    ]);

    $fecha = Carbon::parse($data['fecha']);
    if (! $fecha->isFriday()) {
      return back()
        ->withInput()
        ->withErrors(['fecha' => 'Este registro se llena los viernes (selecciona un viernes).']);
    }

    $camion = Camion::query()->with('ultimoKilometraje')->findOrFail($data['camion_id']);

    $ultimoKm = $camion->ultimoKilometraje?->kilometraje;
    if ($ultimoKm !== null && (int) $data['kilometraje'] < (int) $ultimoKm) {
      return back()
        ->withInput()
        ->withErrors(['kilometraje' => 'El kilometraje no puede ser menor al último registrado (' . $ultimoKm . ').']);
    }

    $yaExiste = CamionKilometraje::query()
      ->where('camion_id', $camion->id)
      ->whereDate('fecha', $fecha->toDateString())
      ->exists();

    if ($yaExiste) {
      return back()
        ->withInput()
        ->withErrors(['fecha' => 'Ya existe un registro para ese camión en esa fecha.']);
    }

    CamionKilometraje::create([
      'camion_id' => $camion->id,
      'fecha' => $fecha->toDateString(),
      'kilometraje' => (int) $data['kilometraje'],
      'nota' => $data['nota'] ?? null,
      'user_id' => $request->user()?->id,
    ]);

    return redirect()
      ->route('camiones.mantenimiento')
      ->with('success', 'Kilometraje registrado correctamente.');
  }
}
