<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Partner Earnings (Margin Received)</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4">Total Earnings: Rs. {{ number_format($totalEarnings, 2) }}</h3>

                <h4 class="font-bold mt-4">Dispatch Orders</h4>
                @if($dispatchOrders->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-100">
                                <tr><th class="border p-2">Order #</th><th class="border p-2">Amount</th><th class="border p-2">Partner</th></tr>
                            </thead>
                            <tbody>
                                @foreach($dispatchOrders as $o)
                                <tr><td class="border p-2">{{ $o->id }}</td><td class="border p-2">Rs. {{ number_format($o->agreed_price,2) }}</td><td class="border p-2">{{ $o->driver->name ?? 'N/A' }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No paid dispatch orders.</p>
                @endif

                <h4 class="font-bold mt-4">Pickup Requests</h4>
                @if($pickupRequests->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-100">
                                <tr><th class="border p-2">Pickup #</th><th class="border p-2">Amount</th><th class="border p-2">Partner</th></tr>
                            </thead>
                            <tbody>
                                @foreach($pickupRequests as $p)
                                <td><td class="border p-2">{{ $p->id }}</td><td class="border p-2">Rs. {{ number_format($p->agreed_price,2) }}</td><td class="border p-2">{{ $p->vehicle->driver_name ?? 'N/A' }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No paid pickups.</p>
                @endif

                <h4 class="font-bold mt-4">Equipment Jobs</h4>
                @if($equipmentJobs->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-100">
                                <tr><th class="border p-2">Job #</th><th class="border p-2">Amount</th><th class="border p-2">Partner</th></tr>
                            </thead>
                            <tbody>
                                @foreach($equipmentJobs as $ej)
                                <tr><td class="border p-2">{{ $ej->id }}</td><td class="border p-2">Rs. {{ number_format($ej->agreed_price,2) }}</td><td class="border p-2">{{ $ej->equipment->owner->name ?? 'N/A' }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No paid equipment jobs.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>