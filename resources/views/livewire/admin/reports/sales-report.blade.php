<div class="max-w-7xl">
  <div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">Sales Report</h1>
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Analyze your sales performance, revenue trends, and customer insights.</p>
      </div>
      {{-- <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <flux:button wire:click="exportReport" variant="primary" color="blue" class="cursor-pointer">
          Export Report
        </flux:button>
      </div> --}}
    </div>

    {{-- @dd($topProducts) --}}

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <div class="bg-white overflow-hidden shadow rounded-lg dark:bg-gray-800">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                  </path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Sales</dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                  ₱{{ number_format($salesData['total_sales'], 2) }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg dark:bg-gray-800">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Average Order Value</dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                  ₱{{ number_format($salesData['average_order_value'], 2) }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg dark:bg-gray-800">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                  </path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Orders</dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                  {{ number_format($salesData['total_orders']) }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg dark:bg-gray-800">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Items Sold</dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                  {{ number_format($salesData['total_items']) }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800">
      <div class="px-4 py-6 sm:px-8">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
          <div>
            <flux:select label="Period" wire:model.live="period">
              <flux:select.option value="today">Today</flux:select.option>
              <flux:select.option value="week">This Week</flux:select.option>
              <flux:select.option value="month">This Month</flux:select.option>
              <flux:select.option value="quarter">This Quarter</flux:select.option>
              <flux:select.option value="year">This Year</flux:select.option>
              <flux:select.option value="custom">Custom Range</flux:select.option>
            </flux:select>
          </div>

          @if ($period === 'custom')
            <div>
              <flux:input label="From Date" type="date" wire:model.live="dateFrom" />
            </div>

            <div>
              <flux:input label="To Date" type="date" wire:model.live="dateTo" />
            </div>
          @endif

          {{-- <div>
            <flux:select label="Chart View" wire:model.live="chartType">
              <flux:select.option value="sales">Sales Revenue</flux:select.option>
              <flux:select.option value="orders">Order Count</flux:select.option>
              <flux:select.option value="products">Product Performance</flux:select.option>
            </flux:select>
          </div> --}}
        </div>
      </div>
    </div>

    <!-- Charts and Analytics -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <!-- Daily Sales Chart -->
      <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800">
        <div class="px-4 py-6 sm:px-8">
          <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Sales Trend</h3>
          <div class="mt-6">
            @if ($dailySales->count() > 0)
              <div x-data="{
                  chart: null,
                  init() {
                      let salesData = @js($dailySales);

                      this.chart = new ApexCharts(this.$refs.chart, {
                          chart: {
                              type: 'area',
                              height: 350,
                              zoom: {
                                  enabled: false
                              }
                          },
                          series: [{
                              name: 'Sales',
                              data: salesData.map(item => item.total_sales)
                          }],
                          xaxis: {
                              categories: salesData.map(item => item.label)
                          },
                          dataLabels: {
                              enabled: false
                          },
                          stroke: {
                              curve: 'smooth'
                          },
                          theme: {
                              mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                          },
                      });

                      this.chart.render();

                      // Listen for Livewire updates
                      Livewire.on('updateSalesChart', (newData) => {
                          this.chart.updateOptions({
                              series: [{
                                  name: 'Sales',
                                  data: newData.map(item => item.total_sales)
                              }],
                              xaxis: {
                                  categories: newData.map(item => item.label)
                              }
                          });
                      });
                  }
              }">
                <div x-ref="chart"></div>
              </div>
            @else
              <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                  </path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No sales data</h3>
                <p class="mt-1 text-sm text-gray-500">No sales found for the selected period.</p>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Order Status Breakdown -->
      <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800">
        <div class="px-4 py-6 sm:px-8">
          <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Order Status Breakdown</h3>
          <div class="mt-6">
            @if ($orderStatusBreakdown->count() > 0)
              <div class="space-y-4">
                @foreach ($orderStatusBreakdown as $status)
                  <div class="flex items-center justify-between">
                    <div class="flex items-center justify-between space-x-3">
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $status->count }}
                        {{ Str::plural('order', $status->count) }}
                      </div>

                      <flux:badge size="sm" color="{{ $this->getStatusBadgeColor($status->status) }}">
                        {{ $status->status }}
                      </flux:badge>
                    </div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      ₱{{ number_format($status->total_amount, 2) }}
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                  </path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No orders found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No orders found for the selected period.</p>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Top Selling Products -->
      <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800">
        <div class="px-4 py-6 sm:px-8">
          <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Top Selling Products</h3>
          <div class="mt-6 flow-root">
            <div class="overflow-x-auto">
              <div class="inline-block min-w-full py-2 align-middle">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                  <thead>
                    <tr>
                      <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Product</th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Qty Sold</th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Revenue</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($topProducts as $item)
                      <tr>
                        <td class="py-4 pl-4 pr-3 text-sm">
                          <div class="flex items-center">
                            <div class="h-8 w-8 flex-shrink-0">
                              @if ($item->product)
                                <img class="h-8 w-8 rounded-lg object-cover"
                                  src='{{ $item->product->primaryImage ? asset("storage/{$item->product->primaryImage->url}") : asset("images/no-image.svg") }}'
                                  alt="{{ $item->product->name }}">
                              @else
                                <img class="h-8 w-8 rounded-lg object-cover"
                                  src='{{ asset("images/no-image.svg") }}'
                                  alt="Product image">
                              @endif
                            </div>
                            <div class="ml-3">
                              <div class="font-medium text-gray-900 dark:text-white">{{ $item->product->name ?? 'Unknown Product' }}</div>
                              <div class="text-gray-500 dark:text-gray-400">{{ $item->productVariant->variant_sku ?? 'N/A' }}</div>
                              <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->productVariant->variant_name ?? 'N/A' }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-white">
                          {{ number_format($item->total_quantity) }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900 dark:text-white">
                          ₱{{ number_format($item->total_revenue, 2) }}
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="3" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-white">No product sales found.
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Clients -->
      <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800">
        <div class="px-4 py-6 sm:px-8">
          <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Top Clients by Revenue</h3>
          <div class="mt-6 flow-root">
            <div class="overflow-x-auto">
              <div class="inline-block min-w-full py-2 align-middle">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                  <thead>
                    <tr>
                      <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Client</th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Orders</th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Total Spent</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-800">
                    @forelse($topClients as $clientOrder)
                      <tr>
                        <td class="py-4 pl-4 pr-3 text-sm">
                          <div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $clientOrder->client->name }}</div>
                            <div class="text-gray-500 dark:text-gray-400">{{ $clientOrder->client->code }}</div>
                          </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-white">
                          {{ number_format($clientOrder->total_orders) }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900 dark:text-white">
                          ₱{{ number_format($clientOrder->total_spent, 2) }}
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="3" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-white">No client data found.
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
