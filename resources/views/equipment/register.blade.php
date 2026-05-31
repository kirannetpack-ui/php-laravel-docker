<x-app-layout>
    <x-slot name="header"><h2>Register Equipment</h2></x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">
                <form method="POST" action="{{ route('equipment.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label>Type (e.g., Crane, Forklift)</label>
                        <input type="text" name="type" required class="w-full border p-2">
                    </div>
                    <div class="mb-4">
                        <label>Model</label>
                        <input type="text" name="model" class="w-full border p-2">
                    </div>
                    <div class="mb-4">
                        <label>Capacity (kg)</label>
                        <input type="number" name="capacity_kg" step="0.01" class="w-full border p-2">
                    </div>
                    <div class="mb-4">
                        <label>Base Charge (NPR)</label>
                        <input type="number" name="base_charge" step="0.01" class="w-full border p-2">
                    </div>
                    <div class="mb-4">
                        <label><input type="checkbox" name="is_negotiable" value="1"> Rate Negotiable</label>
                    </div>
                    <div class="mb-4">
                        <label>Photo</label>
                        <input type="file" name="photo" accept="image/*">
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Register</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>