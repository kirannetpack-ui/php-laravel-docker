<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Driver Jobs</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('error'))
                    <div class="bg-red-100 text-red-700 p-3 mb-4">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 mb-4">{{ session('success') }}</div>
                @endif

                <!-- Pending Jobs (awaiting acceptance) -->
                <h3 class="text-lg font-bold mb-2">Pending Jobs</h3>
                @if($pendingJobs->count())
                    <table class="min-w-full border mb-6">
                        <thead class="bg-gray-100">
                            <tr><th class="border p-2">Order #</th><th>Destination</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @foreach($pendingJobs as $order)
                            <tr>
                                <td class="border p-2">#{{ $order->id }}</td>
                                <td class="border p-2">{{ $order->destination_address }}</td>
                                <td class="border p-2">
                                    <form method="POST" action="{{ route('driver.jobs.accept', $order->id) }}">
                                        @csrf
                                        <button type="submit" style="background-color:#22c55e; color:white; padding:6px 12px; border-radius:4px;">Accept Job</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="mb-6">No pending jobs.</p>
                @endif

                <!-- Accepted Jobs (awaiting delivery and proof) -->
                <h3 class="text-lg font-bold mb-2">Accepted Jobs (Awaiting Delivery)</h3>
                @if($acceptedJobs->count())
                    <table class="min-w-full border mb-6">
                        <thead class="bg-gray-100">
                            <tr><th class="border p-2">Order #</th><th>Destination</th><th>Action</th><th>Proof Upload</th></tr>
                        </thead>
                        <tbody>
                            @foreach($acceptedJobs as $order)
                            <tr>
                                <td class="border p-2">#{{ $order->id }}</td>
                                <td class="border p-2">{{ $order->destination_address }}</td>
                                <td class="border p-2">
                                    <form method="POST" action="{{ route('driver.jobs.deliver', $order->id) }}">
                                        @csrf
                                        <button type="submit" style="background-color:#3b82f6; color:white; padding:6px 12px; border-radius:4px;">Mark Delivered</button>
                                    </form>
                                </td>
                                <td class="border p-2">
                                    <form method="POST" action="{{ route('driver.jobs.proof', $order->id) }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" name="proof_photo" required style="display:inline-block;">
                                        <button type="submit" style="background-color:#eab308; color:white; padding:4px 8px; border-radius:4px;">Upload</button>
                                    </form>
                                    @if($order->proof_of_delivery_photo)
                                        <a href="{{ asset('storage/'.$order->proof_of_delivery_photo) }}" target="_blank" class="text-blue-600 block mt-1">View current proof</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="mb-6">No accepted jobs.</p>
                @endif

                <!-- Delivered Jobs (history) -->
                <h3 class="text-lg font-bold mb-2">Delivered Orders</h3>
                @if($deliveredJobs->count())
                    <table class="min-w-full border">
                        <thead class="bg-gray-100">
                            <tr><th class="border p-2">Order #</th><th>Destination</th><th>Proof of Delivery</th></tr>
                        </thead>
                        <tbody>
                            @foreach($deliveredJobs as $order)
                            <tr>
                                <td class="border p-2">#{{ $order->id }}</td>
                                <td class="border p-2">{{ $order->destination_address }}</td>
                                <td class="border p-2">
                                    @if($order->proof_of_delivery_photo)
                                        <a href="{{ asset('storage/'.$order->proof_of_delivery_photo) }}" target="_blank" class="text-blue-600">View Proof</a>
                                    @else
                                        <span class="text-gray-400">No proof uploaded</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No delivered orders yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Auto-refresh every 15 seconds -->
    <meta http-equiv="refresh" content="15">
</x-app-layout>