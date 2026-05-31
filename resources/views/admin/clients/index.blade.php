<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clients</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if($clients->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border p-2">Name</th>
                                    <th class="border p-2">Email</th>
                                    <th class="border p-2">Phone</th>
                                    <th class="border p-2">Requests</th>
                                    <th class="border p-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $client)
                                <tr>
                                    <td class="border p-2">{{ $client->name }}</td>
                                    <td class="border p-2">{{ $client->email }}</td>
                                    <td class="border p-2">{{ $client->phone ?? '-' }}</td>
                                    <td class="border p-2">{{ $client->warehouseRequests->count() }}</td>
                                    <td class="border p-2">
                                        <a href="{{ route('admin.clients.show', $client->id) }}" class="text-blue-600">View Details</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No clients found.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>