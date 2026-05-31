<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Warehouses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Add New Warehouse Button (forced visible) -->
                <a href="{{ route('warehouses.create') }}" style="display:inline-block; margin-bottom:1rem; background-color:#22c55e; color:#ffffff; padding:8px 16px; border-radius:6px; text-decoration:none; font-weight:bold;">
                    + Add New Warehouse
                </a>

                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 my-4">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="min-w-full border mt-4">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2">Name</th>
                            <th class="border p-2">Dimensions (L×W×H)</th>
                            <th class="border p-2">Status</th>
                            <th class="border p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouses as $wh)
                            <tr class="border-b">
                                <td class="p-2">{{ $wh->name }}</td>
                                <td class="p-2">{{ $wh->length }} × {{ $wh->width }} × {{ $wh->height }} m</td>
                                <td class="p-2">
                                    @if($wh->status == 'pending')
                                        <span style="background-color:#fef08a; color:#1e293b; padding:4px 8px; border-radius:4px; font-size:0.75rem; font-weight:bold;">
                                            Pending
                                        </span>
                                    @elseif($wh->status == 'approved')
                                        <span style="background-color:#bbf7d0; color:#1e293b; padding:4px 8px; border-radius:4px; font-size:0.75rem; font-weight:bold;">
                                            Approved
                                        </span>
                                    @else
                                        <span style="background-color:#fecaca; color:#1e293b; padding:4px 8px; border-radius:4px; font-size:0.75rem; font-weight:bold;">
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="p-2">
                                    <a href="{{ route('warehouses.show', $wh->id) }}" class="text-blue-600 hover:underline">View Details</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500">No warehouses added yet. Click the button above to list your first warehouse.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>