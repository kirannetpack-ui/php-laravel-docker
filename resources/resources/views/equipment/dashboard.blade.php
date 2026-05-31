<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Equipment Owner Dashboard</h2>
    </x-slot>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <h1 class="text-2xl font-bold text-gray-800">🙏 Namaste, {{ Auth::user()->name }}!</h1>
    <p class="text-gray-600">Welcome back to your equipment dashboard.</p>
</div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6 border-l-8 border-blue-500">
                    <div class="flex items-center">
                        <i class="fas fa-wrench text-4xl text-blue-500 mr-3"></i>
                        <div>
                            <p class="text-gray-500 text-sm">Equipment Registered</p>
                            <p class="text-2xl font-bold">{{ \App\Models\Equipment::where('owner_id', auth()->id())->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-8 border-green-500">
                    <div class="flex items-center">
                        <i class="fas fa-briefcase text-4xl text-green-500 mr-3"></i>
                        <div>
                            <p class="text-gray-500 text-sm">Jobs Completed</p>
                            <p class="text-2xl font-bold">{{ \App\Models\EquipmentJob::whereHas('equipment', fn($q)=>$q->where('owner_id', auth()->id()))->where('status', 'completed')->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-8 border-yellow-500">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-4xl text-yellow-500 mr-3"></i>
                        <div>
                            <p class="text-gray-500 text-sm">In Progress</p>
                            <p class="text-2xl font-bold">{{ \App\Models\EquipmentJob::whereHas('equipment', fn($q)=>$q->where('owner_id', auth()->id()))->where('status', 'accepted')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-2">Quick Links</h3>
                <a href="{{ route('equipment.register') }}" class="inline-block bg-blue-500 text-white px-4 py-2 rounded">+ Register New Equipment</a>
                <a href="{{ route('equipment.jobs') }}" class="inline-block bg-green-500 text-white px-4 py-2 rounded ml-2">View Available Jobs</a>
            </div>
        </div>
    </div>
</x-app-layout>