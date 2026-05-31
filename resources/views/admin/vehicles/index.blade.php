<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Vehicles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Add Vehicle Form -->
                <form method="POST" action="{{ route('admin.vehicles.store') }}" enctype="multipart/form-data" class="mb-8 border-b pb-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <input type="text" name="type" required class="w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g., Cycle, Bike, Van, Truck, 				Crane, etc.">
				</select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Registration Number</label>
                            <input type="text" name="registration_number" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Driver Name</label>
                            <input type="text" name="driver_name" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Driver Phone</label>
                            <input type="text" name="driver_phone" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">License Photo (optional)</label>
                            <input type="file" name="license_photo" accept="image/*" class="w-full">
                        </div>
<div>
    <label class="block text-sm font-medium mb-1">Capacity (boxes)</label>
    <input type="number" name="capacity_boxes" class="w-full border-gray-300 rounded-md shadow-sm">
</div>
                        <div class="flex items-end">
                            <button type="submit" style="background-color:#3b82f6; color:white; font-weight:bold; padding:8px 16px; border-radius:6px; border:none; cursor:pointer;">
    Add Vehicle
</button>
                                Add Vehicle
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Vehicles List -->
                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2">Type</th>
                                <th class="border p-2">Reg No</th>
                                <th class="border p-2">Driver</th>
                                <th class="border p-2">Phone</th>
                                <th class="border p-2">License</th>
                                <th class="border p-2">Status</th>
                                <th class="border p-2">Actions</th>
				<th class="border p-2">Capacity (boxes)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicles as $vehicle)
                            <tr>
                                <td class="border p-2">{{ ucfirst($vehicle->type) }}</td>
                                <td class="border p-2">{{ $vehicle->registration_number }}</td>
                                <td class="border p-2">{{ $vehicle->driver_name }}</td>
                                <td class="border p-2">{{ $vehicle->driver_phone }}</td>
				<td class="border p-2">{{ $vehicle->capacity_boxes ?? '-' }}</td>
				<th>Current Load</th>
				<td>{{ $vehicle->current_load }}</td>
                                <td class="border p-2">
                                    @if($vehicle->driver_license_photo)
                                        <a href="{{ asset('storage/'.$vehicle->driver_license_photo) }}" target="_blank" class="text-blue-600">View</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="border p-2">
                                    <span class="px-2 py-1 rounded text-xs {{ $vehicle->is_available ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                        {{ $vehicle->is_available ? 'Available' : 'Busy' }}
                                    </span>
                                </td>
                                <td class="border p-2">
                                    <form method="POST" action="{{ route('admin.vehicles.delete', $vehicle->id) }}" onsubmit="return confirm('Delete this vehicle?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-4 text-center text-gray-500">No vehicles added yet. Use the form above to add.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>