<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Report</title>
    <style>
        @page {
            margin: 25px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 3px solid #2563eb;
        }
        .header h1 {
            color: #2563eb;
            font-size: 22px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .header p {
            color: #666;
            font-size: 11px;
        }
        .meta-info {
            margin-bottom: 20px;
            padding: 12px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
        }
        .meta-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-info td {
            padding: 4px 0;
            font-size: 10px;
        }
        .meta-info td:first-child {
            font-weight: bold;
            width: 140px;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e5e7eb;
        }

        /* Stats Grid */
        .stats-container {
            width: 100%;
            margin-bottom: 20px;
        }
        .stats-row {
            width: 100%;
            display: table;
            table-layout: fixed;
        }
        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            vertical-align: top;
        }
        .stat-label {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 6px;
            display: block;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            display: block;
        }

        /* Data Tables */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table thead {
            background-color: #2563eb;
            color: white;
        }
        table.data-table th {
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #1e40af;
        }
        table.data-table td {
            padding: 7px 6px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }
        .page-break {
            page-break-after: always;
        }
        .block {
            display: block;
        }
        .text-sm {
          font-size: 9px;
          color: #6b7280; /* Gray-600 */
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inventory Report</h1>
        <p>Comprehensive inventory analysis and stock overview</p>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td>Report Generated:</td>
                <td>{{ now()->format('F d, Y - h:i A') }}</td>
            </tr>
            <tr>
                <td>Report Period:</td>
                <td>{{ $dateFrom }} to {{ $dateTo }}</td>
            </tr>
            @if($warehouseFilter)
            <tr>
                <td>Warehouse:</td>
                <td>{{ $warehouses->find($warehouseFilter)->name ?? 'All' }}</td>
            </tr>
            @endif
            @if($categoryFilter)
            <tr>
                <td>Category:</td>
                <td>{{ $categories->find($categoryFilter)->name ?? 'All' }}</td>
            </tr>
            @endif
            @if($brandFilter)
            <tr>
                <td>Brand:</td>
                <td>{{ $brands->find($brandFilter)->name ?? 'All' }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Summary Statistics -->
    <div class="section">
        <div class="section-title">Summary Statistics</div>
        <div class="stats-container">
            <div class="stats-row">
                <div class="stat-card">
                    <span class="stat-label">Total Products</span>
                    <span class="stat-value">{{ number_format($inventoryStats['total_products']) }}</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Total Stock Value</span>
                    <span class="stat-value">₱{{ number_format($inventoryStats['total_stock_value'], 2) }}</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Low Stock Items</span>
                    <span class="stat-value">{{ number_format($inventoryStats['low_stock_count']) }}</span>
                </div>
                <div class="stat-card">
                    <span class="stat-label">Out of Stock</span>
                    <span class="stat-value">{{ number_format($inventoryStats['out_of_stock_count']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Warehouse Stock Overview -->
    @if($warehouseStock->count() > 0)
    <div class="section">
        <div class="section-title">Warehouse Stock Overview</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Warehouse</th>
                    <th class="text-center">Total Items</th>
                    <th class="text-center">Total Stock</th>
                    <th class="text-right">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($warehouseStock as $stock)
                <tr>
                    <td>{{ $stock['warehouse']->name }}</td>
                    <td class="text-center">{{ number_format($stock['total_products']) }}</td>
                    <td class="text-center">{{ number_format($stock['total_stock']) }}</td>
                    <td class="text-right">₱{{ number_format($stock['total_value'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Top Categories -->
    @if($topCategories->count() > 0)
    <div class="section">
        <div class="section-title">Top Categories by Product Count</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-center">Product Count</th>
                    <th class="text-right">Total Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topCategories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td class="text-center">{{ number_format($category->products_count) }}</td>
                    <td class="text-right">{{ number_format($category->total_stock ?? 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Top Brands -->
    @if($topBrands->count() > 0)
    <div class="section">
        <div class="section-title">Top Brands by Product Count</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Brand</th>
                    <th class="text-center">Product Count</th>
                    <th class="text-right">Total Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topBrands as $brand)
                <tr>
                    <td>{{ $brand->name }}</td>
                    <td class="text-center">{{ number_format($brand->products_count) }}</td>
                    <td class="text-right">{{ number_format($brand->total_stock ?? 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="page-break"></div>

    <!-- Low Stock Products -->
    @if($lowStockProducts->count() > 0)
    <div class="section">
        <div class="section-title">Low Stock Products</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th class="text-center">Current Stock</th>
                    <th class="text-center">Threshold</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockProducts as $product)
                <tr>
                    <td>{{ $product->variant_sku }}</td>
                    <td>
                      <div>
                        <span class="block">{{ $product->product->name }}</span>
                        <span class="block text-sm">{{ $product->variant_name }}</span>
                      </div>
                    </td>
                    <td>{{ $product->product->category->name ?? 'N/A' }}</td>
                    <td>{{ $product->product->brand->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ number_format($product->cached_stock) }}</td>
                    <td class="text-center">{{ number_format($product->low_stock_threshold) }}</td>
                    <td class="text-center">
                        <span class="badge badge-warning">Low Stock</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Out of Stock Products -->
    @if($outOfStockProducts->count() > 0)
    <div class="section">
        <div class="section-title">Out of Stock Products</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($outOfStockProducts as $product)
                <tr>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                    <td>{{ $product->brand->name ?? 'N/A' }}</td>
                    <td class="text-center">
                        <span class="badge badge-danger">Out of Stock</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>BikeTrack Inventory Management System - Generated on {{ now()->format('F d, Y') }}</p>
        <p>This report is confidential and intended for internal use only.</p>
    </div>
</body>
</html>
