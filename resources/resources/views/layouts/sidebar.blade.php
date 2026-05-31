<aside class="w-64 bg-gray-800 text-white flex-shrink-0 hidden md:block">
    <div class="p-4 font-bold text-lg border-b border-gray-700">KTM-WDC</div>
    <nav class="mt-4">
        @auth
            @if(Auth::user()->is_admin)
                <a href="{{ route('dashboard') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
                <a href="{{ route('admin.pending') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-warehouse mr-2"></i> Pending Warehouses</a>
                <a href="{{ route('admin.all-warehouses') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-list mr-2"></i> All Warehouses</a>
                <a href="{{ route('admin.requests') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-clipboard-list mr-2"></i> Client Requests</a>
                <a href="{{ route('admin.pending.stocks') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-boxes mr-2"></i> Verify Stock</a>
                <a href="{{ route('admin.vehicles') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-truck mr-2"></i> Vehicles</a>
                <a href="{{ route('admin.dispatch') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-dolly mr-2"></i> Dispatch Orders</a>
                <a href="{{ route('admin.pickup') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-hand-holding-heart mr-2"></i> Pickup Requests</a>
                <a href="{{ route('admin.equipment.jobs') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-crane mr-2"></i> Equipment Jobs</a>
                <a href="{{ route('admin.reports') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-chart-line mr-2"></i> Reports</a>
                <a href="{{ route('admin.roles.index') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-tag mr-2"></i> User Roles</a>
                <a href="{{ route('admin.clients') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-users mr-2"></i> Clients</a>
                <a href="{{ route('admin.margin-tiers') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-percent mr-2"></i> Margin Tiers</a>
                <a href="{{ route('admin.invoices') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-file-invoice-dollar mr-2"></i> All Invoices</a>
                <a href="{{ route('admin.partner-earnings') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-chart-line mr-2"></i> Partner Earnings</a>
                <a href="{{ route('admin.insurance.list') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-shield-alt mr-2"></i> Insurance</a>
		<a href="{{ route('profile') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-user-circle mr-2"></i> My Profile</a>
            @elseif(Auth::user()->is_driver)
                <a href="{{ route('driver.jobs') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
                <a href="{{ route('driver.available-jobs') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-search mr-2"></i> Available Jobs</a>
                <a href="{{ route('driver.pickups') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-hand-holding-heart mr-2"></i> Pickup Jobs</a>
                <a href="{{ route('driver.vehicles.index') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-truck mr-2"></i> My Vehicles</a>
		<a href="{{ route('profile') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-user-circle mr-2"></i> My Profile</a>
            @elseif(Auth::user()->is_equipment_owner)
                <a href="{{ route('equipment.dashboard') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
                <a href="{{ route('equipment.register') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-plus-circle mr-2"></i> Register Equipment</a>
                <a href="{{ route('equipment.jobs') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-briefcase mr-2"></i> Equipment Jobs</a>
		<a href="{{ route('profile') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-user-circle mr-2"></i> My Profile</a>
            @elseif(Auth::user()->is_property_owner)
                <a href="{{ route('dashboard') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-home mr-2"></i> Dashboard</a>
                <a href="{{ route('warehouses.create') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-plus-circle mr-2"></i> Add Warehouse</a>
                <a href="{{ route('warehouses.index') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-list mr-2"></i> My Warehouses</a>
		<a href="{{ route('profile') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-user-circle mr-2"></i> My Profile</a>
            @elseif(Auth::user()->is_client)
                <a href="{{ route('dashboard') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-home mr-2"></i> Dashboard</a>
                <a href="{{ route('my-requests.create') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-paper-plane mr-2"></i> Request Space</a>
                <a href="{{ route('my-requests.index') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-history mr-2"></i> My Requests</a>
                <a href="{{ route('my-stock') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-boxes mr-2"></i> My Stock</a>
                <a href="{{ route('dispatch.index') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-truck mr-2"></i> Dispatch Orders</a>
                <a href="{{ route('pickup.index') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-hand-holding-heart mr-2"></i> Pickup Requests</a>
                <a href="{{ route('invoices.client-index') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-file-invoice mr-2"></i> My Invoices</a>
                <a href="{{ route('client.proposals') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-file-signature mr-2"></i> Proposals</a>
                <a href="{{ route('my-insurance') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-shield-alt mr-2"></i> My Insurance</a>
		<a href="{{ route('profile') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-user-circle mr-2"></i> My Profile</a>
            @endif
            <hr class="my-2 border-gray-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left py-2 px-4 hover:bg-gray-700"><i class="fas fa-sign-out-alt mr-2"></i> Logout</button>
            </form>
        @endauth
    </nav>
</aside>