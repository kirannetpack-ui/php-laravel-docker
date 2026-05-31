<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'KTM-WDC') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <div class="flex">
            @include('layouts.sidebar')
            <div class="flex-1">
                <!-- Mobile top bar -->
                <div class="md:hidden bg-white shadow-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex items-center">
                                <a href="{{ route('dashboard') }}" class="font-bold text-xl">KTM-WDC</a>
                            </div>
                            <div class="flex items-center space-x-4">
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="text-gray-500 focus:outline-none">
                                        <i class="fas fa-bars fa-lg"></i>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                        @auth
                                            @if(Auth::user()->is_admin)
                                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700">Dashboard</a>
                                                <a href="{{ route('admin.pending') }}" class="block px-4 py-2 text-gray-700">Pending Warehouses</a>
                                                <a href="{{ route('admin.all-warehouses') }}" class="block px-4 py-2 text-gray-700">All Warehouses</a>
                                                <a href="{{ route('admin.requests') }}" class="block px-4 py-2 text-gray-700">Client Requests</a>
                                                <a href="{{ route('admin.pending.stocks') }}" class="block px-4 py-2 text-gray-700">Verify Stock</a>
                                                <a href="{{ route('admin.vehicles') }}" class="block px-4 py-2 text-gray-700">Vehicles</a>
                                                <a href="{{ route('admin.dispatch') }}" class="block px-4 py-2 text-gray-700">Dispatch Orders</a>
                                                <a href="{{ route('admin.pickup') }}" class="block px-4 py-2 text-gray-700">Pickup Requests</a>
                                                <a href="{{ route('admin.equipment.jobs') }}" class="block px-4 py-2 text-gray-700">Equipment Jobs</a>
                                                <a href="{{ route('admin.reports') }}" class="block px-4 py-2 text-gray-700">Reports</a>
                                                <a href="{{ route('admin.roles.index') }}" class="block px-4 py-2 text-gray-700">User Roles</a>
                                                <a href="{{ route('admin.clients') }}" class="block px-4 py-2 text-gray-700">Clients</a>
                                                <a href="{{ route('admin.margin-tiers') }}" class="block px-4 py-2 text-gray-700">Margin Tiers</a>
                                                <a href="{{ route('admin.invoices') }}" class="block px-4 py-2 text-gray-700">All Invoices</a>
                                                <a href="{{ route('admin.partner-earnings') }}" class="block px-4 py-2 text-gray-700">Partner Earnings</a>
                                                <a href="{{ route('admin.insurance.list') }}" class="block px-4 py-2 text-gray-700">Insurance</a>
                                            @elseif(Auth::user()->is_driver)
						<a href="{{ route('driver.available-jobs') }}" class="block py-2 px-4 hover:bg-gray-700"><i class="fas fa-search mr-2">							</i> Available Jobs</a>
                                                <a href="{{ route('driver.jobs') }}" class="block px-4 py-2 text-gray-700">Delivery Jobs</a>
                                                <a href="{{ route('driver.pickups') }}" class="block px-4 py-2 text-gray-700">Pickup Jobs</a>
                                                <a href="{{ route('driver.vehicles.index') }}" class="block px-4 py-2 text-gray-700">My Vehicles</a>
                                            @elseif(Auth::user()->is_equipment_owner)
                                                <a href="{{ route('equipment.dashboard') }}" class="block px-4 py-2 text-gray-700">Dashboard</a>
                                                <a href="{{ route('equipment.register') }}" class="block px-4 py-2 text-gray-700">Register Equipment</a>
                                                <a href="{{ route('equipment.jobs') }}" class="block px-4 py-2 text-gray-700">Equipment Jobs</a>
                                            @elseif(Auth::user()->is_property_owner)
                                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700">Dashboard</a>
                                                <a href="{{ route('warehouses.create') }}" class="block px-4 py-2 text-gray-700">Add Warehouse</a>
                                                <a href="{{ route('warehouses.index') }}" class="block px-4 py-2 text-gray-700">My Warehouses</a>
                                            @elseif(Auth::user()->is_client)
                                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700">Dashboard</a>
                                                <a href="{{ route('my-requests.create') }}" class="block px-4 py-2 text-gray-700">Request Space</a>
                                                <a href="{{ route('my-requests.index') }}" class="block px-4 py-2 text-gray-700">My Requests</a>
                                                <a href="{{ route('my-stock') }}" class="block px-4 py-2 text-gray-700">My Stock</a>
                                                <a href="{{ route('dispatch.index') }}" class="block px-4 py-2 text-gray-700">Dispatch Orders</a>
                                                <a href="{{ route('pickup.index') }}" class="block px-4 py-2 text-gray-700">Pickup Requests</a>
                                                <a href="{{ route('invoices.client-index') }}" class="block px-4 py-2 text-gray-700">My Invoices</a>
                                                <a href="{{ route('client.proposals') }}" class="block px-4 py-2 text-gray-700">Proposals</a>
                                                <a href="{{ route('my-insurance') }}" class="block px-4 py-2 text-gray-700">My Insurance</a>
                                            @endif
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700">Logout</button>
                                            </form>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>
    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>