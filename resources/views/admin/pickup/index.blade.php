<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pickup Requests</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 mb-4">{{ session('success') }}</div>
                @endif

                @if($pickups->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border p-2">Client</th>
                                    <th class="border p-2">Pickup Address</th>
                                    <th class="border p-2">Boxes</th>
                                    <th class="border p-2">Contact</th>
                                    <th class="border p-2">Status</th>
                                    <th class="border p-2">Vehicle</th>
                                    <th class="border p-2">Partner Payment</th>
                                    <th class="border p-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pickups as $p)
                                <tr>
                                    <td class="border p-2">{{ $p->client->name ?? 'N/A' }}<br>{{ $p->client->email ?? '' }}</td>
                                    <td class="border p-2">{{ $p->pickup_address }}</td>
                                    <td class="border p-2">{{ $p->estimated_boxes }}</td>
                                    <td class="border p-2">{{ $p->contact_person }}<br>{{ $p->contact_phone }}</td>
                                    <td class="border p-2">{{ ucfirst($p->status) }}</td>
                                    <td class="border p-2">{{ $p->vehicle->registration_number ?? 'Not assigned' }}</td>
                                    <td class="border p-2">
                                        @if($p->status == 'completed')
                                            @if($p->partner_paid)
                                                <span class="text-green-600 font-semibold">Paid</span>
                                            @else
                                                <form method="POST" action="{{ route('admin.jobs.mark-paid', ['type' => 'pickup', 'id' => $p->id]) }}">
                                                    @csrf
                                                    <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Mark Paid</button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="border p-2">
                                        @if($p->status == 'pending')
                                            <form method="POST" action="{{ route('admin.pickup.assign', $p->id) }}">
                                                @csrf
                                                <select name="vehicle_id" required>
                                                    <option value="">Select vehicle</option>
                                                    @foreach($vehicles as $v)
                                                        <option value="{{ $v->id }}">{{ $v->registration_number }} ({{ $v->type }})</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Assign</button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No pickup requests.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>