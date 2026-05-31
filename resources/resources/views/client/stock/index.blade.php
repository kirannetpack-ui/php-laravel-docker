<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Stock Items</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">
                @if($stocks->count())
                    <table class="min-w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th>Product</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>QR Code</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stocks as $stock)
                            <tr>
                                <td>{{ $stock->product_name }}</td>
                                <td>{{ $stock->description ?? '-' }}</td>
                                <td>{{ $stock->quantity }}</td>
                                <td>{{ ucfirst($stock->status) }}</td>
                                <td>
                                    @if($stock->qr_code)
                                        <a href="{{ asset('storage/'.$stock->qr_code) }}" target="_blank">View QR</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No stock items found.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>