<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dispatch Order #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-2 gap-4">
                    <dt class="font-bold">Destination:</dt><dd>{{ $order->destination_address }}</dd>
                    <dt class="font-bold">Status:</dt><dd>{{ ucfirst($order->status) }}</dd>
                    <dt class="font-bold">Vehicle:</dt><dd>{{ $order->vehicle->registration_number ?? 'Not assigned' }} ({{ $order->vehicle->driver_name ?? '' }})</dd>
                    <dt class="font-bold">PAN/VAT Bill:</dt>
                    <dd>@if($order->pan_vat_bill) <a href="{{ asset('storage/'.$order->pan_vat_bill) }}" target="_blank" class="text-blue-600">Download</a> @else - @endif</dd>
                </dl>

                <div class="mt-4">
                    <h3 class="font-bold">Items</h3>
                    <table class="min-w-full border mt-2">
                        <thead><tr><th class="border p-2">Product</th><th class="border p-2">Quantity</th></tr></thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr><td class="border p-2">{{ $item->stock->product_name }}</td><td class="border p-2">{{ $item->quantity }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($order->proof_of_delivery_photo)
                    <div class="mt-4">
                        <strong>Proof of Delivery:</strong>
                        <a href="{{ asset('storage/'.$order->proof_of_delivery_photo) }}" target="_blank" class="text-blue-600 block mt-1">View Photo</a>
                    </div>
                @endif

                <div class="mt-6">
                    <a href="{{ route('dispatch.index') }}" class="text-blue-600 hover:underline">← Back to my orders</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>