<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Product;
use App\Models\Apartado;

class StatisticsController extends Controller
{
    public function index()
    {
        $ventasHoy = Venta::whereDate('created_at', today())->sum('total');

        $ventasMes = Venta::whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->sum('total');

        $egresos = 500.00;

        $productosBajoStock = Product::where('stock', '<=', 10)->get();

        $apartadosVigentes = Apartado::where('status', 'vigente')->get();

        return view('statistics', compact(
            'ventasHoy',
            'ventasMes',
            'egresos',
            'productosBajoStock',
            'apartadosVigentes'
        ));
    }
}