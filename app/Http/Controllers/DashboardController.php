<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Inventario;
use App\Models\AjusteInventario;

class DashboardController extends Controller
{
    public function index()
    {
        // Métricas básicas de inventario para el panel
        $productos = Producto::count();
        $stockTotal = (int) Inventario::sum('cantidad');
        $stockBajo = (int) Inventario::where('cantidad', '>', 0)->where('cantidad', '<=', 3)->count();
        $sinStock = (int) Inventario::where('cantidad', 0)->count();

        $ajustesMes = AjusteInventario::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('dashboard', [
            'productos' => $productos,
            'stockTotal' => $stockTotal,
            'stockBajo' => $stockBajo,
            'sinStock' => $sinStock,
            'ajustesMes' => $ajustesMes,
        ]);
    }
}
