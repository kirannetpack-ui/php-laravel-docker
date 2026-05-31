<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Available Jobs</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Dispatch Orders -->
                <h3 class="text-lg font-bold mb-2">Dispatch Orders</h3>
                @if($dispatchOrders->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border mb-6">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border p-2">Order #</th>
                                    <th class="border p-2">Pickup Address</th>
                                    <th class="border p-2">Destination</th>
                                    <th class="border p-2">Your Proposal</th>
                                    <th class="border p-2">Client Negotiation</th>
                                    <th class="border p-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dispatchOrders as $order)
                                @php
                                    $proposal = \App\Models\PartnerProposal::where('job_id', $order->id)
                                                ->where('job_type', 'App\Models\DispatchOrder')
                                                ->where('partner_id', auth()->id())
                                                ->first();
                                @endphp
                                <tr>
                                    <td class="border p-2">#{{ $order->id }}</td>
                                    <td class="border p-2">{{ $order->origin_address ?? 'N/A' }}</td>
                                    <td class="border p-2">{{ $order->destination_address }}</td>
                                    <td class="border p-2">
                                        @if($proposal)
                                            Rs. {{ number_format($proposal->proposed_price, 2) }}
                                            <span class="text-xs text-gray-500">({{ ucfirst($proposal->status) }})</span>
                                        @else
                                            <form method="POST" action="{{ route('driver.propose-price', $order->id) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="job_type" value="App\Models\DispatchOrder">
                                                <input type="number" step="0.01" name="proposed_price" placeholder="Your price" required class="w-24 border rounded p-1">
                                                <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded ml-1">Propose</button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="border p-2">
                                        @if($proposal && $proposal->counter_offer)
                                            <div class="bg-yellow-100 p-2 rounded">
                                                <strong>Counter‑offer:</strong> Rs. {{ number_format($proposal->counter_offer, 2) }}
                                                <p class="text-xs mt-1">{{ $proposal->negotiation_notes ?? '' }}</p>
                                                <div class="flex space-x-2 mt-2">
                                                    <form method="POST" action="{{ route('driver.accept-counter', $proposal->id) }}">
                                                        @csrf
                                                        <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded text-xs">Accept</button>
                                                    </form>
                                                    <button onclick="showReProposeForm({{ $proposal->id }})" class="bg-blue-500 text-white px-2 py-1 rounded text-xs">Propose New</button>
                                                </div>
                                                <div id="repropose-{{ $proposal->id }}" style="display:none;" class="mt-2">
                                                    <form method="POST" action="{{ route('driver.repropose', $proposal->id) }}">
                                                        @csrf
                                                        <input type="number" step="0.01" name="new_price" placeholder="New price" class="border p-1 w-32">
                                                        <textarea name="notes" rows="1" placeholder="Message" class="border p-1 w-full mt-1"></textarea>
                                                        <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded text-xs mt-1">Send New Proposal</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @elseif($proposal && $proposal->status == 'negotiating')
                                            <span class="text-yellow-600">Waiting for client response...</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="border p-2">
                                        @if($proposal && $proposal->status == 'accepted')
                                            <span class="text-green-600">Accepted – client will assign</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No dispatch orders available.</p>
                @endif

                <!-- Pickup Requests (similar structure can be added) -->
                <h3 class="text-lg font-bold mb-2 mt-6">Pickup Requests</h3>
                @if($pickupRequests->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border p-2">Request #</th>
                                    <th class="border p-2">Pickup Address</th>
                                    <th class="border p-2">Destination Warehouse</th>
                                    <th class="border p-2">Boxes</th>
                                    <th class="border p-2">Your Proposal</th>
                                    <th class="border p-2">Client Negotiation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pickupRequests as $p)
                                @php
                                    $proposal = \App\Models\PartnerProposal::where('job_id', $p->id)
                                                ->where('job_type', 'App\Models\PickupRequest')
                                                ->where('partner_id', auth()->id())
                                                ->first();
                                @endphp
                                <tr>
                                    <td class="border p-2">#{{ $p->id }}</td>
                                    <td class="border p-2">{{ $p->pickup_address }}</td>
                                    <td class="border p-2">{{ $p->destinationWarehouse->name ?? 'Not specified' }}</td>
                                    <td class="border p-2">{{ $p->estimated_boxes }}</td>
                                    <td class="border p-2">
                                        @if($proposal)
                                            Rs. {{ number_format($proposal->proposed_price, 2) }}
                                            <span class="text-xs">({{ ucfirst($proposal->status) }})</span>
                                        @else
                                            <form method="POST" action="{{ route('driver.propose-price', $p->id) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="job_type" value="App\Models\PickupRequest">
                                                <input type="number" step="0.01" name="proposed_price" placeholder="Your price" required class="w-24 border rounded p-1">
                                                <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded ml-1">Propose</button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="border p-2">
                                        @if($proposal && $proposal->counter_offer)
                                            <div class="bg-yellow-100 p-2 rounded">
                                                <strong>Counter‑offer:</strong> Rs. {{ number_format($proposal->counter_offer, 2) }}
                                                <p class="text-xs">{{ $proposal->negotiation_notes ?? '' }}</p>
                                                <div class="flex space-x-2 mt-1">
                                                    <form method="POST" action="{{ route('driver.accept-counter', $proposal->id) }}">
                                                        @csrf
                                                        <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded text-xs">Accept</button>
                                                    </form>
                                                    <button onclick="showReProposeForm({{ $proposal->id }})" class="bg-blue-500 text-white px-2 py-1 rounded text-xs">Propose New</button>
                                                </div>
                                                <div id="repropose-{{ $proposal->id }}" style="display:none;" class="mt-2">
                                                    <form method="POST" action="{{ route('driver.repropose', $proposal->id) }}">
                                                        @csrf
                                                        <input type="number" step="0.01" name="new_price" placeholder="New price" class="border p-1 w-32">
                                                        <textarea name="notes" rows="1" placeholder="Message" class="border p-1 w-full mt-1"></textarea>
                                                        <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded text-xs mt-1">Send</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @elseif($proposal && $proposal->status == 'negotiating')
                                            <span class="text-yellow-600">Waiting for client response...</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No pickup requests available.</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        function showReProposeForm(id) {
            document.getElementById('repropose-'+id).style.display = 'block';
        }
    </script>
<meta http-equiv="refresh" content="10">
</x-app-layout>