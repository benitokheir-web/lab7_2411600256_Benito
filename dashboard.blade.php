@extends('layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h2 class="page-title">Dashboard</h2>
        <p class="text-muted mb-0">Real-time hardware inventory overview.</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Add Product
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div><span>Total Products</span><h3 id="statTotalProducts">{{ $totalProducts }}</h3></div>
            <i class="bi bi-box-seam stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card warning">
            <div><span>Low Stock</span><h3 id="statLowStock">{{ $lowStock }}</h3></div>
            <i class="bi bi-exclamation-triangle stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card danger">
            <div><span>Out of Stock</span><h3 id="statOutOfStock">{{ $outOfStock }}</h3></div>
            <i class="bi bi-x-circle stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div><span>Inventory Value</span><h3 id="statInventoryValue">₱{{ number_format($inventoryValue, 2) }}</h3></div>
            <i class="bi bi-cash-stack stat-icon"></i>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <section class="card dashboard-chart-card h-100">
            <div class="card-header">
                <h3><i class="bi bi-bar-chart-line-fill"></i> Stock by Category</h3>
            </div>
            <div class="card-body">
                <div class="dashboard-chart-wrap">
                    <canvas id="categoryStockChart" aria-label="Stock quantity by product category"></canvas>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-5">
        <section class="card dashboard-chart-card h-100">
            <div class="card-header">
                <h3><i class="bi bi-pie-chart-fill"></i> Inventory Health</h3>
            </div>
            <div class="card-body">
                <div class="dashboard-chart-wrap">
                    <canvas id="inventoryHealthChart" aria-label="Products grouped by stock status"></canvas>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-7">
        <section class="card dashboard-chart-card h-100">
            <div class="card-header">
                <h3><i class="bi bi-graph-up-arrow"></i> Stock Movement (Last 7 Days)</h3>
            </div>
            <div class="card-body">
                <div class="dashboard-chart-wrap">
                    <canvas id="stockMovementChart" aria-label="Daily stock in and stock out quantities"></canvas>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-5">
        <section class="card dashboard-chart-card h-100">
            <div class="card-header">
                <h3><i class="bi bi-trophy-fill"></i> Top Stocked Products</h3>
            </div>
            <div class="card-body">
                <div class="dashboard-chart-wrap">
                    <canvas id="topProductsChart" aria-label="Products with the highest available stock"></canvas>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>Recent Products</strong>
        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Product</th><th>SKU</th><th>Category</th><th>Qty</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($recentProducts as $product)
                <tr>
                    <td><strong>{{ $product->name }}</strong></td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>
                        @if($product->isOutOfStock())
                            <span class="badge bg-danger">Out of Stock</span>
                        @elseif($product->isLowStock())
                            <span class="badge bg-warning text-dark">Low Stock</span>
                        @else
                            <span class="badge bg-success">In Stock</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4">No products found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="row g-4 mt-1">
 <div class="col-lg-5"><div class="card shadow-sm border-0 h-100"><div class="card-header bg-white fw-bold">Low Stock Alerts <span class="badge bg-warning text-dark" id="lowStockCount">{{ $lowStock }}</span></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Product</th><th>Stock</th><th>Reorder</th></tr></thead><tbody id="lowStockRows">@forelse(\App\Models\Product::lowStock()->orderBy('quantity')->limit(10)->get() as $p)<tr><td><a href="{{ route('products.show',$p) }}">{{ $p->name }}</a></td><td>{{ $p->quantity }}</td><td>{{ $p->reorder_level }}</td></tr>@empty<tr><td colspan="3">No low-stock products.</td></tr>@endforelse</tbody></table></div></div></div>
 <div class="col-lg-7"><div class="card shadow-sm border-0 h-100"><div class="card-header bg-white fw-bold">Recent Transactions</div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Date</th><th>Product</th><th>Type</th><th>Qty</th><th>User</th></tr></thead><tbody id="recentTransactionRows">@forelse(\App\Models\InventoryTransaction::with(['product','user'])->latest()->limit(8)->get() as $t)<tr><td>{{ $t->created_at->format('M d, h:i A') }}</td><td>{{ $t->product->name ?? 'Deleted product' }}</td><td>{{ strtoupper($t->type) }}</td><td>{{ $t->quantity }}</td><td>{{ $t->user->name ?? 'System' }}</td></tr>@empty<tr><td colspan="5">No transactions yet.</td></tr>@endforelse</tbody></table></div></div></div>
</div>
<div class="text-muted small mt-3">Last Updated: <span id="lastUpdated">—</span> · Dashboard refreshes every 30 seconds.</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script>
    const chartColors = {
        blue: '#42a5f5',
        darkBlue: '#0d47a1',
        green: '#43a047',
        orange: '#ff9f43',
        red: '#ff5c7a',
        grid: 'rgba(23, 32, 51, 0.1)',
        muted: '#6c757d',
    };

    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    usePointStyle: true,
                    color: chartColors.muted,
                    padding: 18,
                },
            },
        },
    };

    new Chart(document.getElementById('categoryStockChart'), {
        type: 'bar',
        data: {
            labels: {{ Illuminate\Support\Js::from($categoryLabels) }},
            datasets: [{
                label: 'Units in stock',
                data: {{ Illuminate\Support\Js::from($categoryQuantities) }},
                backgroundColor: 'rgba(66, 165, 245, 0.55)',
                borderColor: chartColors.blue,
                borderWidth: 1,
                borderRadius: 5,
            }],
        },
        options: {
            ...commonOptions,
            plugins: { ...commonOptions.plugins, legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: chartColors.grid },
                },
                x: { grid: { display: false } },
            },
        },
    });

    new Chart(document.getElementById('inventoryHealthChart'), {
        type: 'doughnut',
        data: {
            labels: ['In Stock', 'Low Stock', 'Out of Stock'],
            datasets: [{
                data: {{ Illuminate\Support\Js::from($inventoryHealth) }},
                backgroundColor: [chartColors.blue, chartColors.orange, chartColors.red],
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 8,
            }],
        },
        options: {
            ...commonOptions,
            cutout: '58%',
        },
    });

    new Chart(document.getElementById('stockMovementChart'), {
        type: 'line',
        data: {
            labels: {{ Illuminate\Support\Js::from($movementLabels) }},
            datasets: [
                {
                    label: 'Stock In',
                    data: {{ Illuminate\Support\Js::from(array_values($stockInTrend)) }},
                    borderColor: chartColors.blue,
                    backgroundColor: 'rgba(66, 165, 245, 0.12)',
                    pointBackgroundColor: chartColors.blue,
                    fill: true,
                    tension: 0.35,
                },
                {
                    label: 'Stock Out',
                    data: {{ Illuminate\Support\Js::from(array_values($stockOutTrend)) }},
                    borderColor: chartColors.orange,
                    backgroundColor: 'rgba(255, 159, 67, 0.08)',
                    pointBackgroundColor: chartColors.orange,
                    fill: true,
                    tension: 0.35,
                },
            ],
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: chartColors.grid },
                },
                x: { grid: { display: false } },
            },
        },
    });

    new Chart(document.getElementById('topProductsChart'), {
        type: 'bar',
        data: {
            labels: {{ Illuminate\Support\Js::from($topProductLabels) }},
            datasets: [{
                label: 'Available units',
                data: {{ Illuminate\Support\Js::from($topProductQuantities) }},
                backgroundColor: 'rgba(66, 165, 245, 0.55)',
                borderColor: chartColors.blue,
                borderWidth: 1,
                borderRadius: 5,
            }],
        },
        options: {
            ...commonOptions,
            indexAxis: 'y',
            plugins: { ...commonOptions.plugins, legend: { display: false } },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: chartColors.grid },
                },
                y: { grid: { display: false } },
            },
        },
    });
</script>

<script>
(() => {
 const money = n => '₱' + Number(n || 0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});
 async function refreshInventory(){
   try {
     const [statsRes, lowRes, txRes] = await Promise.all([
       fetch(@json(route('api.dashboard.stats')),{headers:{Accept:'application/json'}}),
       fetch(@json(route('api.products.low-stock')),{headers:{Accept:'application/json'}}),
       fetch(@json(route('api.transactions.recent')),{headers:{Accept:'application/json'}})
     ]);
     if(!statsRes.ok||!lowRes.ok||!txRes.ok) throw new Error('Could not refresh inventory data');
     const stats=await statsRes.json(), low=await lowRes.json(), tx=await txRes.json();
     document.getElementById('statTotalProducts').textContent=stats.totalProducts;
     document.getElementById('statLowStock').textContent=stats.lowStock;
     document.getElementById('statOutOfStock').textContent=stats.outOfStock;
     document.getElementById('statInventoryValue').textContent=money(stats.inventoryValue);
     document.getElementById('lowStockCount').textContent=low.length;
     document.getElementById('lowStockRows').innerHTML=low.length?low.map(p=>`<tr><td><a href="/products/${p.id}">${escapeText(p.name)}</a></td><td>${p.quantity}</td><td>${p.reorder_level}</td></tr>`).join(''):'<tr><td colspan="3">No low-stock products.</td></tr>';
     document.getElementById('recentTransactionRows').innerHTML=tx.length?tx.map(t=>`<tr><td>${escapeText(t.date||'')}</td><td>${escapeText(t.product)}</td><td>${escapeText(String(t.type).toUpperCase())}</td><td>${t.type==='out'?'−':(t.type==='in'?'+':'=')}${t.quantity}</td><td>${escapeText(t.user)}</td></tr>`).join(''):'<tr><td colspan="5">No transactions yet.</td></tr>';
     document.getElementById('lastUpdated').textContent=new Date().toLocaleTimeString();
   } catch(error) { document.getElementById('lastUpdated').textContent='Refresh failed; retrying soon'; console.error(error); }
 }
 function escapeText(value){const el=document.createElement('span');el.textContent=value??'';return el.innerHTML;}
 refreshInventory();setInterval(refreshInventory,30000);
})();
</script>

@endpush
