<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TrabajoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class TrabajoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $trabajos = Trabajo::all();

        return view('trabajo.index', compact('trabajos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $trabajo = new Trabajo();

        return view('trabajo.create', compact('trabajo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrabajoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['traducciones'] = is_array($data['traducciones'] ?? null) ? $data['traducciones'] : [];

        $data['traducciones']['es'] = $data['nombre'];

        $data['traducciones'] = json_encode($data['traducciones']);

        Trabajo::create($data);

        return Redirect::route('trabajos.index')
            ->with('success', 'Trabajo creado satisfactoriamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $trabajo = Trabajo::find($id);

        return view('trabajo.show', compact('trabajo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $trabajo = Trabajo::find($id);

        return view('trabajo.edit', compact('trabajo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TrabajoRequest $request, Trabajo $trabajo): RedirectResponse
    {
        $data = $request->validated();

        $data['traducciones'] = is_array($data['traducciones'] ?? null) ? $data['traducciones'] : [];

        $data['traducciones']['es'] = $data['nombre'];

        $data['traducciones'] = json_encode($data['traducciones']);

        $trabajo->update($data);

        return Redirect::route('trabajos.index')
            ->with('success', 'Trabajo actualizado satisfactoriamente.');
    }

    public function destroy($id): RedirectResponse
    {
        Trabajo::find($id)->delete();

        return Redirect::route('trabajos.index')
            ->with('success', 'Trabajo eliminado satisfactoriamente.');
    }

    public function quickStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        $nombre = trim($validated['nombre']);

        $trabajo = Trabajo::create([
            'nombre' => $nombre,
            'traducciones' => json_encode(['es' => $nombre]),
        ]);

        return response()->json([
            'id' => $trabajo->id_trabajo,
            'nombre' => $trabajo->nombre,
        ]);
    }
}
