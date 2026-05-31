<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pickup Jobs</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('error'))
                    <div class="bg-red-100 text-red-700 p-3 mb-4">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 mb-4">{{ session('success') }}</div>
                @endif

                @if($pickups->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border p-2">Pickup Address</th>
                                    <th class="border p-2">Destination Warehouse</th>
                                    <th class="border p-2">Boxes</th>
                                    <th class="border p-2">Contact</th>
                                    <th class="border p-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pickups as $p)
                                @php
                                    $destWarehouse = $p->destinationWarehouse;
                                @endphp
                                <tr>
                                    <td class="border p-2">{{ $p->pickup_address }}</td>
                                    <td class="border p-2">
                                        @if($destWarehouse)
                                            <strong>{{ $destWarehouse->name }}</strong><br>
                                            {{ $destWarehouse->address ?? 'No address' }}<br>
                                            <span class="text-xs">Contact: {{ $destWarehouse->contact_person ?? 'N/A' }} ({{ $destWarehouse->contact_phone ?? 'N/A' }})</span>
                                        @else
                                            Not specified
                                        @endif
                                    </td>
                                    <td class="border p-2">{{ $p->estimated_boxes }}</td>
                                    <td class="border p-2">
                                        {{ $p->contact_person }}<br>
                                        {{ $p->contact_phone }}
                                    </td>
                                    <td class="border p-2">
                                        <form method="POST" action="{{ route('driver.pickups.complete', $p->id) }}">
                                            @csrf
                                            <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded">Mark Completed</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No pickup jobs assigned to you.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout><x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pickup Jobs</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('error'))
                    <div class="bg-red-100 text-red-700 p-3 mb-4">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 mb-4">{{ session('success') }}</div>
                @endif

                @if($pickups->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border p-2">Pickup Address</th>
                                    <th class="border p-2">Destination Warehouse</th>
                                    <th class="border p-2">Boxes</th>
                                    <th class="border p-2">Contact</th>
                                    <th class="border p-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pickups as $p)
                                @php
                                    $destWarehouse = $p->destinationWarehouse;
                                @endphp
                                <tr>
                                    <td class="border p-2">{{ $p->pickup_address }}</td>
                                    <td class="border p-2">
                                        @if($destWarehouse)
                                            <strong>{{ $destWarehouse->name }}</strong><br>
                                            {{ $destWarehouse->address ?? 'No address' }}<br>
                                            <span class="text-xs">Contact: {{ $destWarehouse->contact_person ?? 'N/A' }} ({{ $destWarehouse->contact_phone ?? 'N/A' }})</span>
                                        @else
                                            Not specified
                                        @endif
                                    </td>
                                    <td class="border p-2">{{ $p->estimated_boxes }}</td>
                                    <td class="border p-2">
                                        {{ $p->contact_person }}<br>
                                        {{ $p->contact_phone }}
                                    </td>
                                    <td class="border p-2">
                                        <form method="POST" action="{{ route('driver.pickups.complete', $p->id) }}">
                                            @csrf
                                            <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded">Mark Completed</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No pickup jobs assigned to you.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>