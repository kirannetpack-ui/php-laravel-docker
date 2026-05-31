<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Warehouse') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('warehouses.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Warehouse Name --}}
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Warehouse Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="rounded-md shadow-sm border-gray-300 w-full" required>
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Warehouse Type --}}
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Warehouse Type</label>
                        <select name="type" id="warehouse_type" class="w-full border-gray-300 rounded" required>
                            <option value="building">Building (indoor, volume in m³)</option>
                            <option value="open_field">Open Field / Land (area in m²)</option>
                        </select>
                    </div>

                    {{-- Building Fields --}}
                    <div id="building_fields">
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div><label>Length (m)</label><input type="number" step="0.01" name="length" value="{{ old('length') }}" class="w-full rounded border-gray-300"></div>
                            <div><label>Width (m)</label><input type="number" step="0.01" name="width" value="{{ old('width') }}" class="w-full rounded border-gray-300"></div>
                            <div><label>Height (m)</label><input type="number" step="0.01" name="height" value="{{ old('height') }}" class="w-full rounded border-gray-300"></div>
                        </div>
                    </div>

                    {{-- Open Field Fields --}}
                    <div id="open_field_fields" style="display:none;">
                        <div class="mb-4"><label>Area (square meters)</label><input type="number" step="0.01" name="area_sq_m" value="{{ old('area_sq_m') }}" class="w-full rounded border-gray-300"></div>
                    </div>

                    {{-- Address & Map --}}
                    <div class="mb-4">
                        <label class="block font-medium text-gray-700">Warehouse Address</label>
                        <input type="text" name="address" id="address" class="w-full border-gray-300 rounded" placeholder="Street, city, etc." value="{{ old('address') }}">
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                        <div id="locationMap" style="height: 300px; margin-top: 10px;"></div>
                        <p class="text-sm text-gray-500 mt-1">Click on map to set exact location</p>
                    </div>
<div class="mb-4">
    <label class="block font-medium text-gray-700">Contact Person Name</label>
    <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="w-full border-gray-300 rounded">
</div>
<div class="mb-4">
    <label class="block font-medium text-gray-700">Contact Phone Number</label>
    <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" class="w-full border-gray-300 rounded">
</div>

                    {{-- Amenities --}}
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <label><input type="checkbox" name="has_cctv" value="1"> CCTV Installed</label>
                        <label><input type="checkbox" name="has_security_guard" value="1" id="has_guard"> Security Guard Available</label>
                        <div id="guard_count_div" style="display: none;" class="col-span-2 ml-6"><label>Number of Guards:</label><input type="number" name="guard_count" class="rounded border-gray-300 w-24"></div>
                        <label><input type="checkbox" name="has_labors" value="1"> Labors for Loading/Unloading</label>
                        <label><input type="checkbox" name="is_motorable" value="1"> Motorable (40' Trailer)</label>
                    </div>

                    {{-- Distance --}}
                    <div class="mb-4"><label>Distance from City Center (km)</label><input type="number" step="0.1" name="distance_from_city" value="{{ old('distance_from_city') }}" class="w-full rounded border-gray-300"></div>

                    {{-- CCTV URL --}}
                    <div class="mb-4"><label>Live CCTV URL (iframe embed link)</label><input type="text" name="camera_stream_url" class="w-full rounded border-gray-300" placeholder="https://..."></div>

                    {{-- Pricing Fields --}}
                    <div class="mb-4"><label class="block font-medium">Price per unit (NPR)</label><input type="number" step="0.01" name="price_per_unit" value="{{ old('price_per_unit') }}" class="w-full rounded border-gray-300"></div>
                    <div class="mb-4"><label class="block font-medium">Price unit type</label><select name="price_unit_type" class="w-full rounded border-gray-300"><option value="fixed">Fixed (per m³/m²)</option><option value="percentage">Percentage of total capacity</option></select></div>
                    <div class="grid grid-cols-2 gap-4 mb-4"><div><label>Security deposit (%)</label><input type="number" step="0.01" name="security_deposit_percentage" value="{{ old('security_deposit_percentage') }}" class="w-full rounded border-gray-300"></div><div><label>Security deposit (fixed NPR)</label><input type="number" step="0.01" name="security_deposit_fixed" value="{{ old('security_deposit_fixed') }}" class="w-full rounded border-gray-300"></div></div>

                    {{-- Photos --}}
                    <div class="mb-4"><label>Photos (up to 5, max 2MB each)</label><input type="file" name="photos[]" multiple accept="image/*" class="w-full"></div>

                    <div class="flex justify-end"><button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Submit for Approval</button></div>
                </form>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const typeSelect = document.getElementById('warehouse_type');
        const buildingDiv = document.getElementById('building_fields');
        const openFieldDiv = document.getElementById('open_field_fields');
        function toggleFields() {
            if (typeSelect.value === 'building') {
                buildingDiv.style.display = 'block';
                openFieldDiv.style.display = 'none';
            } else {
                buildingDiv.style.display = 'none';
                openFieldDiv.style.display = 'block';
            }
        }
        typeSelect.addEventListener('change', toggleFields);
        toggleFields();

        var defaultLat = 27.7172, defaultLng = 85.3240;
        var oldLat = document.getElementById('latitude').value;
        var oldLng = document.getElementById('longitude').value;
        if (oldLat && oldLng) { defaultLat = parseFloat(oldLat); defaultLng = parseFloat(oldLng); }
        var map = L.map('locationMap').setView([defaultLat, defaultLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
        var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
        function updateCoordinates(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }
        marker.on('dragend', function(e) { updateCoordinates(marker.getLatLng().lat, marker.getLatLng().lng); });
        map.on('click', function(e) { marker.setLatLng(e.latlng); updateCoordinates(e.latlng.lat, e.latlng.lng); });
        updateCoordinates(defaultLat, defaultLng);

        document.getElementById('has_guard').addEventListener('change', function() {
            document.getElementById('guard_count_div').style.display = this.checked ? 'block' : 'none';
        });
    </script>
</x-app-layout>