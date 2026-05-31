<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Client Dashboard</h2>
    </x-slot>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <h1 class="text-2xl font-bold text-gray-800">🙏 Namaste, {{ Auth::user()->name }}!</h1>
    <p class="text-gray-600">Welcome back to your KTM-WDC dashboard.</p>
</div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6 border-l-8 border-blue-500">
                    <div class="flex items-center">
                        <i class="fas fa-warehouse text-4xl text-blue-500 mr-3"></i>
                        <div>
                            <p class="text-gray-500 text-sm">Warehouses</p>
                            <p class="text-2xl font-bold">{{ \App\Models\WarehouseRequest::where('client_id', auth()->id())->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-8 border-green-500">
                    <div class="flex items-center">
                        <i class="fas fa-truck text-4xl text-green-500 mr-3"></i>
                        <div>
                            <p class="text-gray-500 text-sm">Dispatch Orders</p>
                            <p class="text-2xl font-bold">{{ \App\Models\DispatchOrder::whereHas('warehouseRequest', fn($q)=>$q->where('client_id', auth()->id()))->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-8 border-yellow-500">
                    <div class="flex items-center">
                        <i class="fas fa-hand-holding-heart text-4xl text-yellow-500 mr-3"></i>
                        <div>
                            <p class="text-gray-500 text-sm">Pickups</p>
                            <p class="text-2xl font-bold">{{ \App\Models\PickupRequest::where('client_id', auth()->id())->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-8 border-purple-500">
                    <div class="flex items-center">
                        <i class="fas fa-boxes text-4xl text-purple-500 mr-3"></i>
                        <div>
                            <p class="text-gray-500 text-sm">Total Stock (boxes)</p>
                            <p class="text-2xl font-bold">{{ \App\Models\Stock::whereHas('warehouseRequest', fn($q)=>$q->where('client_id', auth()->id()))->sum('quantity') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-2">Recent Activity</h3>
                <div class="space-y-2">
                    <p class="text-gray-600">✔️ You have {{ \App\Models\WarehouseRequest::where('client_id', auth()->id())->where('status', 'assigned')->count() }} active warehouse requests.</p>
                    <p class="text-gray-600">🚚 {{ \App\Models\DispatchOrder::whereHas('warehouseRequest', fn($q)=>$q->where('client_id', auth()->id()))->where('status', 'pending')->count() }} dispatch orders pending driver assignment.</p>
                    <p class="text-gray-600">📦 {{ \App\Models\Stock::whereHas('warehouseRequest', fn($q)=>$q->where('client_id', auth()->id()))->where('status', 'pending')->count() }} stock items awaiting verification.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>