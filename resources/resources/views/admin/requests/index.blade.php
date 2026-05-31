<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Client Requests</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 mb-4">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 text-red-700 p-3 mb-4">{{ session('error') }}</div>
                @endif

                @if($requests->count())
                    <table class="min-w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2">Client</th>
                                <th class="border p-2">Space (m³)</th>
                                <th class="border p-2">Duration</th>
                                <th class="border p-2">Status</th>
                                <th class="border p-2">Assigned Warehouse(s)</th>
                                <th class="border p-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                            <tr>
                                <td class="border p-2">{{ $req->client->name }}<br>{{ $req->client->email }}</td>
                                <td class="border p-2">{{ $req->required_space }}</td>
                                <td class="border p-2">{{ $req->duration_months }} months</td>
                                <td class="border p-2">{{ ucfirst($req->status) }}</td>
                                <td class="border p-2">
                                    @if($req->assignedWarehouse)
                                        {{ $req->assignedWarehouse->name }}
                                    @elseif($req->assignedWarehouses->count())
                                        @foreach($req->assignedWarehouses as $aw)
                                            {{ $aw->name }} ({{ $aw->pivot->allocated_space }} m³)<br>
                                        @endforeach
                                    @else
                                        Not assigned
                                    @endif
                                </td>
                                <td class="border p-2">
                                    @if($req->status == 'pending')
                                        <a href="{{ route('admin.requests.assign-multi', $req->id) }}" style="display:inline-block; background-color:#8b5cf6; color:white; font-weight:bold; padding:4px 12px; border-radius:6px; text-decoration:none; margin-bottom:4px;">Multi‑Assign</a>
                                        {{-- Optionally keep the single dropdown --}}
                                        <form method="POST" action="{{ route('admin.requests.assign', $req->id) }}" class="mt-1">
                                            @csrf
                                            <select name="warehouse_id" required class="border rounded p-1">
                                                <option value="">Single warehouse</option>
                                                @foreach($warehouses as $wh)
                                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">Assign</button>
                                        </form>
                                    @elseif($req->status == 'assigned')
                                        <a href="{{ route('admin.stocks', $req->id) }}" class="bg-green-500 text-white px-2 py-1 rounded">Manage Stock</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No requests found.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>