<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Partner Price Proposals</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 mb-4">{{ session('success') }}</div>
                @endif

                @if($proposals->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border p-2">Job</th>
                                    <th class="border p-2">Proposed Price</th>
                                    <th class="border p-2">Status</th>
                                    <th class="border p-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proposals as $prop)
                                <tr>
                                    <td class="border p-2">{{ class_basename($prop->job_type) }} #{{ $prop->job_id }}</td>
                                    <td class="border p-2">Rs. {{ number_format($prop->proposed_price, 2) }}</td>
                                    <td class="border p-2">{{ ucfirst($prop->status) }}</td>
                                    <td class="border p-2">
                                        @if($prop->status == 'pending')
                                            <form method="POST" action="{{ route('client.proposals.accept', $prop->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded">Accept</button>
                                            </form>
                                            <form method="POST" action="{{ route('client.proposals.reject', $prop->id) }}" class="inline ml-2">
                                                @csrf
                                                <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Reject</button>
                                            </form>
                                            <button onclick="showNegotiateForm({{ $prop->id }})" class="bg-blue-500 text-white px-2 py-1 rounded ml-2">Negotiate</button>
                                            <div id="negotiate-form-{{ $prop->id }}" style="display:none;" class="mt-2">
                                                <form method="POST" action="{{ route('client.proposals.negotiate', $prop->id) }}">
                                                    @csrf
                                                    <input type="number" step="0.01" name="counter_price" placeholder="Your counter‑offer (NPR)" class="border p-1 w-32">
                                                    <textarea name="notes" rows="2" class="border p-1 w-64 mt-1" placeholder="Optional message"></textarea>
                                                    <button type="submit" class="bg-yellow-500 text-white px-2 py-1 rounded mt-1">Send Negotiation</button>
                                                </form>
                                            </div>
                                            <script>
                                                function showNegotiateForm(id) {
                                                    document.getElementById('negotiate-form-'+id).style.display = 'block';
                                                }
                                            </script>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No proposals yet.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>