<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Dispatch Order
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('dispatch.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="warehouse_request_id" value="{{ $warehouseRequest->id }}">

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Destination Address</label>
                        <input type="text" name="destination_address" required class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>
<div class="mb-4">
    <label class="block font-medium text-gray-700">Contact Person Name</label>
    <input type="text" name="contact_person" required class="w-full border-gray-300 rounded-md shadow-sm">
</div>

<div class="mb-4">
    <label class="block font-medium text-gray-700">Contact Phone Number</label>
    <input type="text" name="contact_phone" required class="w-full border-gray-300 rounded-md shadow-sm">
</div>
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">PAN/VAT Bill (PDF/JPG/PNG)</label>
                        <input type="file" name="pan_vat_bill">
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Select Items</label>
                        @foreach($stocks as $index => $stock)
                            <div class="border-t pt-2 mt-2">
                                <strong>{{ $stock->product_name }}</strong> (Available: {{ $stock->quantity }})
                                <input type="hidden" name="items[{{ $index }}][stock_id]" value="{{ $stock->id }}">
                                <div class="mt-1">
                                    Quantity: <input type="number" name="items[{{ $index }}][quantity]" min="1" max="{{ $stock->quantity }}" class="border-gray-300 rounded w-24">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" style="display:inline-block; background-color:#3b82f6; color:white; font-weight:bold; padding:8px 16px; border-radius:6px; border:none; cursor:pointer;">
                            Submit Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>