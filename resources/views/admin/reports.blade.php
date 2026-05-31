<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports & Analytics</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-blue-100 p-4 rounded text-center">
                        <h3 class="font-bold text-lg">Warehouses</h3>
                        <p class="text-2xl">{{ $totalWarehouses }}</p>
                        <p class="text-sm">Approved: {{ $approvedWarehouses }}</p>
                    </div>
                    <div class="bg-yellow-100 p-4 rounded text-center">
                        <h3 class="font-bold text-lg">Requests</h3>
                        <p class="text-2xl">{{ $pendingRequests + $assignedRequests }}</p>
                        <p class="text-sm">Pending: {{ $pendingRequests }} | Assigned: {{ $assignedRequests }}</p>
                    </div>
                    <div class="bg-green-100 p-4 rounded text-center">
                        <h3 class="font-bold text-lg">Dispatch Orders</h3>
                        <p class="text-2xl">{{ $pendingOrders + $deliveredOrders }}</p>
                        <p class="text-sm">Pending: {{ $pendingOrders }} | Delivered: {{ $deliveredOrders }}</p>
                    </div>
                </div>

                <!-- Chart -->
                <canvas id="statsChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('statsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Approved Warehouses', 'Pending Requests', 'Assigned Requests', 'Pending Orders', 'Delivered Orders'],
                datasets: [{
                    label: 'Count',
                    data: [{{ $approvedWarehouses }}, {{ $pendingRequests }}, {{ $assignedRequests }}, {{ $pendingOrders }}, {{ $deliveredOrders }}],
                    backgroundColor: ['#22c55e', '#eab308', '#3b82f6', '#ef4444', '#10b981']
                }]
            }
        });
    </script>
</x-app-layout>