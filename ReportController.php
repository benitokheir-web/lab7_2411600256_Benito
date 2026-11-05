<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryTransaction;

class ReportController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('category')->orderBy('name')->get();

        $totalStock = Product::sum('quantity');
        $inventoryValue = Product::selectRaw(
            'COALESCE(SUM(quantity * unit_price), 0) as total'
        )->value('total');

        $stockIn = InventoryTransaction::where('type', 'in')->sum('quantity');
        $stockOut = InventoryTransaction::where('type', 'out')->sum('quantity');

        return view('reports.index', compact(
            'products',
            'totalStock',
            'inventoryValue',
            'stockIn',
            'stockOut'
        ));
    }
}
