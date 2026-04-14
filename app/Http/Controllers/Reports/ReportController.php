<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Movement;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function inventory(): View
    {
        $categoryId = request('category_id');
        $stockStatus = request('stock_status', 'all');

        $query = Product::with('category', 'unit', 'location')
            ->where('is_active', true);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($stockStatus === 'low') {
            $query->where('current_stock', '>', 0)
                ->whereRaw('current_stock < min_stock');
        } elseif ($stockStatus === 'out') {
            $query->where('current_stock', 0);
        }

        $products = $query->orderBy('name')->get();
        $categories = DB::table('categories')->orderBy('name')->get();

        return view('reports.inventory', compact('products', 'categories', 'categoryId', 'stockStatus'));
    }

    public function movements(): View
    {
        $productId = request('product_id');
        $type = request('type');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $query = Movement::with('product', 'lot', 'user');

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(50);

        $products = DB::table('products')->where('is_active', true)->orderBy('name')->get();

        return view('reports.movements', compact('movements', 'products', 'productId', 'type', 'dateFrom', 'dateTo'));
    }

    public function alertsReport(): View
    {
        $level = request('level');
        $isRead = request('is_read');

        $query = Alert::with('product', 'lot', 'readBy');

        if ($level && $level !== 'all') {
            $query->where('level', $level);
        }

        if ($isRead === 'read') {
            $query->where('is_read', true);
        } elseif ($isRead === 'unread') {
            $query->where('is_read', false);
        }

        $alerts = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('reports.alerts', compact('alerts', 'level', 'isRead'));
    }
}
