<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['Cement 40kg', 'CEM-001', 'General construction cement.', 'Hardware', 50, 10, 285.00, 'BuildPro Supply'],
            ['Steel Bar 10mm', 'STB-001', '10mm reinforcing steel bar.', 'Hardware', 8, 10, 185.00, 'SteelWorks'],
            ['Plywood 1/2 inch', 'PLY-001', 'Half-inch construction plywood.', 'Lumber', 20, 5, 850.00, 'Woodline Depot'],
            ['2x4 Lumber', 'LUM-001', 'Standard 2x4 construction lumber.', 'Lumber', 30, 10, 320.00, 'Woodline Depot'],
            ['Claw Hammer', 'HAM-001', 'Heavy-duty claw hammer.', 'Tools', 12, 5, 450.00, 'ToolMaster'],
            ['Cordless Drill', 'DRL-001', 'Cordless power drill.', 'Tools', 4, 5, 3200.00, 'ToolMaster'],
            ['Screwdriver Set', 'SCR-001', 'Multi-size screwdriver set.', 'Tools', 15, 5, 650.00, 'ToolMaster'],
            ['Electrical Wire 2.0mm', 'ELW-001', 'Copper electrical wire.', 'Electrical', 100, 20, 1250.00, 'ElectroSupply'],
            ['LED Bulb 12W', 'LED-001', '12-watt LED bulb.', 'Electrical', 6, 10, 120.00, 'ElectroSupply'],
            ['PVC Pipe 1/2 inch', 'PVC-001', 'Half-inch PVC plumbing pipe.', 'Plumbing', 25, 10, 95.00, 'PlumbPro'],
            ['Faucet', 'FAU-001', 'Standard water faucet.', 'Plumbing', 7, 8, 780.00, 'PlumbPro'],
            ['White Paint 1L', 'PNT-001', 'White interior/exterior paint.', 'Paint', 18, 5, 450.00, 'ColorHouse'],
            ['Blue Paint 1L', 'PNT-002', 'Blue interior/exterior paint.', 'Paint', 3, 5, 450.00, 'ColorHouse'],
            ['Nails 2 inch', 'NAL-001', 'Two-inch common nails.', 'Hardware', 60, 20, 85.00, 'BuildPro Supply'],
            ['Adjustable Wrench', 'WRN-001', 'Adjustable steel wrench.', 'Tools', 2, 5, 550.00, 'ToolMaster'],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['sku' => $p[1]],
                [
                    'name' => $p[0],
                    'description' => $p[2],
                    'category' => $p[3],
                    'quantity' => $p[4],
                    'reorder_level' => $p[5],
                    'unit_price' => $p[6],
                    'supplier' => $p[7],
                ]
            );
        }
    }
}
