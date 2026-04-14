<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Movement;
use App\Models\Product;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with KPIs and important information.
     *
     * Muestra el panel de control con indicadores clave y alertas recientes.
     */
    public function index(): View
    {
        // Conteo de alertas criticas no leidas
        $criticalAlertsCount = Alert::where('level', 'critical')
            ->where('is_read', false)
            ->count();

        // Conteo de productos con stock bajo
        $lowStockCount = Product::where('is_active', true)
            ->where('current_stock', '<', DB::raw('min_stock'))
            ->where('min_stock', '>', 0)
            ->count();

        // Conteo de recepciones del dia actual
        $receiptsToday = Receipt::whereDate('created_at', Carbon::today())->count();

        // Ultimos 10 movimientos con relaciones
        $recentMovements = Movement::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Ultimas 5 alertas no leidas ordenadas por nivel
        $recentAlerts = Alert::with(['product', 'lot'])
            ->where('is_read', false)
            ->orderByRaw("
                CASE
                    WHEN level = 'critical' THEN 1
                    WHEN level = 'warning' THEN 2
                    WHEN level = 'info' THEN 3
                    ELSE 4
                END
            ")
            ->limit(5)
            ->get();

        // Total de productos activos
        $activeProductsCount = Product::where('is_active', true)->count();

        return view('dashboard', [
            'criticalAlertsCount' => $criticalAlertsCount,
            'lowStockCount' => $lowStockCount,
            'activeProductsCount' => $activeProductsCount,
            'receiptsToday' => $receiptsToday,
            'recentMovements' => $recentMovements,
            'recentAlerts' => $recentAlerts,
        ]);
    }
}
