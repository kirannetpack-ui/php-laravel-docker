<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Stock for Request #{{ $warehouseRequest->id }} (Client: {{ $warehouseRequest->client->name }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 mb-4">{{ session('success') }}</div>
                @endif

                <!-- Add new stock form -->
                <form method="POST" action="{{ route('admin.stocks.add', $warehouseRequest->id) }}" class="mb-6 border-b pb-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="text" name="product_name" placeholder="Product name" class="border rounded p-2" required>
                        <input type="text" name="description" placeholder="Description" class="border rounded p-2">
                        <input type="number" name="quantity" placeholder="Quantity" class="border rounded p-2" required>
                        <input type="text" name="sku" placeholder="SKU (optional)" class="border rounded p-2">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Stock</button>
                    </div>
                </form>

                <!-- Existing stock list -->
                <table class="min-w-full border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2">Product</th>
                            <th class="border p-2">Description</th>
                            <th class="border p-2">Quantity</th>
                            <th class="border p-2">SKU</th>
                            <th class="border p-2">Status</th>
                            <th class="border p-2">QR Code</th>
                            <th class="border p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouseRequest->stocks as $stock)
                        <tr>
                            <td class="border p-2">{{ $stock->product_name }}</td>
                            <td class="border p-2">{{ $stock->description ?? '-' }}</td>
                            <td class="border p-2">
                                <form method="POST" action="{{ route('admin.stocks.update', $stock->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="quantity" value="{{ $stock->quantity }}" class="w-20 border rounded p-1">
                                    <button type="submit" class="bg-yellow-500 text-white px-2 py-1 rounded">Update</button>
                                </form>
                            </td>
                            <td class="border p-2">{{ $stock->sku ?? '-' }}</td>
                            <td class="border p-2">
                                <span class="px-2 py-1 rounded text-xs 
                                    @if($stock->status == 'pending') bg-yellow-200
                                    @elseif($stock->status == 'verified') bg-green-200
                                    @else bg-red-200 @endif">
                                    {{ ucfirst($stock->status) }}
                                </span>
                            </td>
                            <td class="border p-2 text-center">
                                @if($stock->qr_code)
                                    <a href="{{ asset('storage/'.$stock->qr_code) }}" target="_blank" class="text-blue-600">View QR</a>
                                @else
                                    <span class="text-gray-400">Not generated</span>
                                @endif
                            </td>
                            <td class="border p-2">
                                <form method="POST" action="{{ route('admin.stocks.delete', $stock->id) }}" onsubmit="return confirm('Delete this stock?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr><td colspan="7" class="p-4 text-center">No stock added yet. Use the form above to add.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    <a href="{{ route('admin.requests') }}" class="text-blue-600">← Back to Requests</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>