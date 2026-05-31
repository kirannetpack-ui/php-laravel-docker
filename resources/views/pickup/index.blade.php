<x-app-layout>
    <x-slot name="header"><h2>My Pickup Requests</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">
                <a href="{{ route('pickup.create') }}" class="bg-green-500 text-white px-4 py-2 rounded inline-block mb-4">New Pickup Request</a>
                @if($pickups->count())
                    <table class="w-full border">
                        <thead><tr><th>Pickup Address</th><th>Boxes</th><th>Status</th><th>Assigned Vehicle</th></tr></thead>
                        <tbody>
                            @foreach($pickups as $p)
                            <tr>
                                <td>{{ $p->pickup_address }}</td>
                                <td>{{ $p->estimated_boxes }}</td>
                                <td>{{ ucfirst($p->status) }}</td>
                                <td>{{ $p->vehicle->registration_number ?? 'Not assigned' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No pickup requests.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>