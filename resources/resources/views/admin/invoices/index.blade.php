<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">All Invoices</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if($invoices->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border p-2">Invoice #</th>
                                    <th class="border p-2">Client</th>
                                    <th class="border p-2">Amount</th>
                                    <th class="border p-2">Due Date</th>
                                    <th class="border p-2">Status</th>
                                    <th class="border p-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $inv)
                                <tr>
                                    <td class="border p-2">{{ $inv->invoice_number }}</td>
                                    <td class="border p-2">
                                        {{ $inv->warehouseRequest->client->name }}<br>
                                        {{ $inv->warehouseRequest->client->email }}
                                    </td>
                                    <td class="border p-2">Rs. {{ number_format($inv->amount, 2) }}</td>
                                    <td class="border p-2">{{ \Carbon\Carbon::parse($inv->due_date)->format('d M Y') }}</td>
                                    <td class="border p-2">
                                        <span class="px-2 py-1 rounded text-xs 
                                            @if($inv->status == 'paid') bg-green-200 text-green-800
                                            @elseif($inv->status == 'pending') bg-yellow-200 text-yellow-800
                                            @else bg-red-200 text-red-800 @endif">
                                            {{ ucfirst($inv->status) }}
                                        </span>
                                    </td>
                                    <td class="border p-2">
                                        <a href="{{ route('admin.invoices.show', $inv->id) }}" class="text-blue-600">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No invoices found.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>