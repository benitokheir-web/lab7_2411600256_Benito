<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Carbon\CarbonImmutable;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $lowStock = Product::where('quantity', '>', 0)
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->count();
        $outOfStock = Product::where('quantity', '<=', 0)->count();

        $inventoryValue = Product::selectRaw(
            'COALESCE(SUM(quantity * unit_price), 0) as total'
        )->value('total');

        $recentProducts = Product::latest()->take(8)->get();

        $categoryStock = Product::query()
            ->select('category')
            ->selectRaw('SUM(quantity) as quantity')
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        $inventoryHealth = [
            Product::whereColumn('quantity', '>', 'reorder_level')->count(),
            $lowStock,
            $outOfStock,
        ];

        $startDate = CarbonImmutable::today()->subDays(6);
        $dateKeys = [];
        $movementLabels = [];

        for ($day = 0; $day < 7; $day++) {
            $date = $startDate->addDays($day);
            $dateKeys[] = $date->format('Y-m-d');
            $movementLabels[] = $date->format('M j');
        }

        $stockInTrend = array_fill_keys($dateKeys, 0);
        $stockOutTrend = array_fill_keys($dateKeys, 0);

        $transactions = InventoryTransaction::query()
            ->where('created_at', '>=', $startDate->startOfDay())
            ->where('created_at', '<', $startDate->addDays(7)->startOfDay())
            ->get(['type', 'quantity', 'created_at']);

        foreach ($transactions as $transaction) {
            $date = $transaction->created_at->format('Y-m-d');

            if ($transaction->type === 'in') {
                $stockInTrend[$date] += $transaction->quantity;
            } else {
                $stockOutTrend[$date] += $transaction->quantity;
            }
        }

        $topProducts = Product::query()
            ->orderByDesc('quantity')
            ->orderBy('name')
            ->take(5)
            ->get(['name', 'quantity']);

        $categoryLabels = $categoryStock->pluck('category');
        $categoryQuantities = $categoryStock->pluck('quantity');
        $topProductLabels = $topProducts->pluck('name');
        $topProductQuantities = $topProducts->pluck('quantity');

        return view('dashboard', compact(
            'totalProducts',
            'lowStock',
            'outOfStock',
            'inventoryValue',
            'recentProducts',
            'inventoryHealth',
            'movementLabels',
            'stockInTrend',
            'stockOutTrend',
            'categoryLabels',
            'categoryQuantities',
            'topProductLabels',
            'topProductQuantities'
        ));
    }

    public function stats()
    {
        return response()->json([
            'totalProducts' => Product::count(),
            'lowStock' => Product::lowStock()->where('quantity', '>', 0)->count(),
            'outOfStock' => Product::outOfStock()->count(),
            'inventoryValue' => (float) Product::selectRaw('COALESCE(SUM(quantity * unit_price), 0) as total')->value('total'),
            'updatedAt' => now()->toDateTimeString(),
        ]);
    }

    public function lowStockProducts()
    {
        return response()->json(Product::lowStock()->orderBy('quantity')->limit(10)
            ->get(['id', 'name', 'sku', 'quantity', 'reorder_level']));
    }

    public function recentTransactions()
    {
        return response()->json(InventoryTransaction::with(['product:id,name', 'user:id,name'])
            ->latest()->limit(10)->get()->map(fn ($t) => [
                'id' => $t->id,
                'date' => $t->created_at?->format('M d, Y h:i A'),
                'product' => $t->product?->name ?? 'Deleted product',
                'type' => $t->type,
                'quantity' => $t->quantity,
                'user' => $t->user?->name ?? 'System',
            ]));
    }
}
