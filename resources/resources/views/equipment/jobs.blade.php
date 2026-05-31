<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Available Equipment Jobs</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 mb-4">{{ session('success') }}</div>
                @endif

                @if($jobs->count())
                    <table class="min-w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2">Request #</th>
                                <th class="border p-2">Client</th>
                                <th class="border p-2">Equipment Notes</th>
                                <th class="border p-2">Propose Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                            <tr>
                                <td class="border p-2">#{{ $job->warehouse_request_id }}</td>
                                <td class="border p-2">{{ $job->warehouseRequest->client->name ?? 'N/A' }}<br>{{ $job->warehouseRequest->client->email ?? '' }}</td>
                                <td class="border p-2">{{ $job->notes ?? '-' }}</td>
                                <td class="border p-2">
                                    <form method="POST" action="{{ route('equipment.propose-price', $job->id) }}">
                                        @csrf
                                        <input type="hidden" name="job_type" value="App\Models\EquipmentJob">
                                        <input type="number" step="0.01" name="proposed_price" placeholder="Your price (NPR)" required class="w-24 border rounded p-1">
                                        <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded ml-1">Propose</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No pending equipment jobs.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>