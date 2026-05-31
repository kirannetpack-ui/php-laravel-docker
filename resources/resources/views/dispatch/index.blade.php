<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Dispatch Orders</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">
                @if(session('success')) <div class="bg-green-100 p-2 mb-4">{{ session('success') }}</div> @endif
                @if($orders->count())
                    <table class="min-w-full border">
                        <thead class="bg-gray-100">
                            <tr><th>Destination</th><th>Status</th><th>Vehicle</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->destination_address }}</td>
                                <td>{{ ucfirst($order->status) }}</td>
                                <td>{{ $order->vehicle->registration_number ?? 'Not assigned' }}</td>
                                <td><a href="{{ route('dispatch.show', $order->id) }}" class="text-blue-600">Details</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No dispatch orders yet.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>