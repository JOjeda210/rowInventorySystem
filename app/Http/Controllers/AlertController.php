<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AlertController extends Controller
{
    public function index(): View
    {
        $level = request('level');

        $activeAlerts = Alert::with('product', 'lot')
            ->where('is_read', false)
            ->when($level && $level !== 'all', fn($q) => $q->where('level', $level))
            ->orderByRaw("CASE WHEN level = 'critical' THEN 1 WHEN level = 'warning' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->get();

        $criticalAlerts = Alert::with('product', 'lot')
            ->where('is_read', false)
            ->where('level', 'critical')
            ->orderBy('created_at', 'desc')
            ->get();

        $warningAlerts = Alert::with('product', 'lot')
            ->where('is_read', false)
            ->where('level', 'warning')
            ->orderBy('created_at', 'desc')
            ->get();

        $allAlerts = Alert::with('product', 'lot')
            ->when($level && $level !== 'all', fn($q) => $q->where('level', $level))
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('alerts.index', compact('activeAlerts', 'criticalAlerts', 'warningAlerts', 'allAlerts', 'level'));
    }

    public function markAsRead(Alert $alert): RedirectResponse
    {
        $alert->update([
            'is_read' => true,
            'read_by' => Auth::id(),
            'read_at' => now(),
        ]);

        return back()->with('success', 'Alerta marcada como leida.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        Alert::where('is_read', false)->update([
            'is_read' => true,
            'read_by' => Auth::id(),
            'read_at' => now(),
        ]);

        return back()->with('success', 'Todas las alertas marcadas como leidas.');
    }
}
