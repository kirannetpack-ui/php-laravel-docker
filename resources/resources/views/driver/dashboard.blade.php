<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Driver Dashboard</h2>
    </x-slot>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <h1 class="text-2xl font-bold text-gray-800">🙏 Namaste, {{ Auth::user()->name }}!</h1>
    <p class="text-gray-600">Welcome back to your driver dashboard.</p>
</div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6 border-l-8 border-green-500">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-4xl text-green-500 mr-3"></i>
                        <div>
                            <p class="text-gray-500 text-sm">Completed Jobs</p>
                            <p class="text-2xl font-bold">{{ \App\Models\DispatchOrder::where('driver_id', auth()->id())->where('status', 'delivered')->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-8 border-yellow-500">
                    <div class="flex items-center">
                        <i class="fas fa-spinner text-4xl text-yellow-500 mr-3"></i>
                        <div>
                            <p class="text-gray-500 text-sm">In Progress</p>
                            <p class="text-2xl font-bold">{{ \App\Models\DispatchOrder::where('driver_id', auth()->id())->where('status', 'accepted')->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-8 border-blue-500">
                    <div class="flex items-center">
                        <i class="fas fa-truck text-4xl text-blue-500 mr-3"></i>
                        <div>
                            <p class="text-gray-500 text-sm">My Vehicles</p>
                            <p class="text-2xl font-bold">{{ \App\Models\Vehicle::where('driver_user_id', auth()->id())->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-2">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('driver.available-jobs') }}" class="bg-blue-500 text-white text-center py-2 rounded">Browse Available Jobs</a>
                    <a href="{{ route('driver.vehicles.index') }}" class="bg-green-500 text-white text-center py-2 rounded">Manage Vehicles</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>