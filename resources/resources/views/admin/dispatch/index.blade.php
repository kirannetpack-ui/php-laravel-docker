<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dispatch Orders') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 mb-4">{{ session('success') }}</div>
                @endif

                @if($orders->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border p-2">Client</th>
                                    <th class="border p-2">Destination</th>
                                    <th class="border p-2">PAN/VAT</th>
                                    <th class="border p-2">Status</th>
                                    <th class="border p-2">Vehicle</th>
                                    <th class="border p-2">Partner Payment</th>
                                    <th class="border p-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr class="border-b">
                                    <td class="border p-2">{{ $order->warehouseRequest->client->name ?? 'N/A' }}<br>
                                        <span class="text-xs text-gray-500">{{ $order->warehouseRequest->client->email ?? '' }}</span>
                                    </td>
                                    <td class="border p-2">{{ $order->destination_address }}</td>
                                    <td class="border p-2">
                                        @if($order->pan_vat_bill)
                                            <a href="{{ asset('storage/'.$order->pan_vat_bill) }}" target="_blank" class="text-blue-600">View</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="border p-2">
                                        <span class="px-2 py-1 rounded text-xs 
                                            @if($order->status == 'pending') bg-yellow-200 text-yellow-800
                                            @elseif($order->status == 'accepted') bg-blue-200 text-blue-800
                                            @elseif($order->status == 'delivered') bg-green-200 text-green-800
                                            @else bg-gray-200 @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="border p-2">
                                        {{ $order->vehicle->registration_number ?? 'Not assigned' }}
                                        @if($order->vehicle)
                                            ({{ $order->vehicle->driver_name }})
                                        @endif
                                    </td>
                                    <td class="border p-2">
                                        @if($order->status == 'delivered')
                                            @if($order->partner_paid)
                                                <span class="text-green-600 font-semibold">Paid</span>
                                            @else
                                                <form method="POST" action="{{ route('admin.jobs.mark-paid', ['type' => 'dispatch', 'id' => $order->id]) }}">
                                                    @csrf
                                                    <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Mark Paid</button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="border p-2">
                                        @if($order->status == 'pending')
                                            <form method="POST" action="{{ route('admin.dispatch.assign', $order->id) }}" class="inline-flex items-center gap-2">
                                                @csrf
                                                <select name="vehicle_id" required class="border rounded p-1">
                                                    <option value="">Select vehicle</option>
                                                    @foreach($vehicles as $v)
                                                        <option value="{{ $v->id }}">{{ $v->registration_number }} ({{ $v->type }})</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Assign</button>
                                            </form>
                                        @elseif($order->status == 'accepted')
                                            <span class="text-green-600">Driver accepted</span>
                                        @elseif($order->status == 'delivered')
                                            @if($order->proof_of_delivery_photo)
                                                <a href="{{ asset('storage/'.$order->proof_of_delivery_photo) }}" target="_blank" class="text-blue-600">View Proof</a>
                                            @else
                                                <form method="POST" action="{{ route('admin.dispatch.proof', $order->id) }}" enctype="multipart/form-data" class="inline">
                                                    @csrf
                                                    <input type="file" name="proof_photo" required accept="image/*">
                                                    <button type="submit" class="bg-yellow-500 text-white px-2 py-1 rounded">Upload Proof</button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                </td>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500">No dispatch orders yet.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>