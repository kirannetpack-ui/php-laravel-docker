<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientRequestHandler;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverVehicleController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentJobController;
use App\Http\Controllers\PickupRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\MarginController;
use App\Http\Controllers\ClientProposalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/ping', function() {
    return 'pong';
});

Route::get('/clear-cache', function() {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return 'Cache cleared. Now try login again.';
});

Route::view('/', 'welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Profile routes (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Warehouse routes (property owner)
Route::middleware(['auth'])->group(function () {
    Route::resource('warehouses', WarehouseController::class)->only(['create', 'store', 'index', 'show']);
});

// Client request routes
Route::middleware(['auth'])->group(function () {
    Route::get('/my-requests', [ClientRequestHandler::class, 'index'])->name('my-requests.index');
    Route::get('/my-requests/create', [ClientRequestHandler::class, 'create'])->name('my-requests.create');
    Route::post('/my-requests', [ClientRequestHandler::class, 'store'])->name('my-requests.store');
    Route::get('/my-stock', [ClientRequestHandler::class, 'myStock'])->name('my-stock');
    Route::get('/my-requests/{requestId}/add-stock', [ClientRequestHandler::class, 'showAddStock'])->name('client.add-stock');
    Route::post('/my-requests/{requestId}/add-stock', [ClientRequestHandler::class, 'storeStock'])->name('client.store-stock');
    Route::get('/my-requests/{requestId}/stocks', [ClientRequestHandler::class, 'clientStocks'])->name('client.stocks');
    Route::get('/my-insurance', [ClientRequestHandler::class, 'myInsurance'])->name('my-insurance');
});

// Dispatch routes (client)
Route::middleware(['auth'])->group(function () {
    Route::get('/dispatch/create/{requestId}', [DispatchController::class, 'create'])->name('dispatch.create');
    Route::post('/dispatch', [DispatchController::class, 'store'])->name('dispatch.store');
    Route::get('/dispatch', [DispatchController::class, 'index'])->name('dispatch.index');
    Route::get('/dispatch/{id}', [DispatchController::class, 'show'])->name('dispatch.show');
});

// Pickup routes (client)
Route::middleware(['auth'])->group(function () {
    Route::get('/pickup/create', [PickupRequestController::class, 'create'])->name('pickup.create');
    Route::post('/pickup', [PickupRequestController::class, 'store'])->name('pickup.store');
    Route::get('/pickup', [PickupRequestController::class, 'index'])->name('pickup.index');
});

// Client proposals
Route::middleware(['auth'])->group(function () {
    Route::get('/my-proposals', [ClientProposalController::class, 'index'])->name('client.proposals');
    Route::post('/my-proposals/{id}/accept', [ClientProposalController::class, 'accept'])->name('client.proposals.accept');
    Route::post('/my-proposals/{id}/reject', [ClientProposalController::class, 'reject'])->name('client.proposals.reject');
    Route::post('/my-proposals/{id}/negotiate', [ClientProposalController::class, 'negotiate'])->name('client.proposals.negotiate');
});

// Client invoices (closure routes)
Route::middleware(['auth'])->group(function () {
    Route::get('/invoices', function () {
        $invoices = \App\Models\Invoice::whereHas('warehouseRequest', function ($q) {
            $q->where('client_id', auth()->id());
        })->orderBy('created_at', 'desc')->get();
        return view('invoices.client-index', compact('invoices'));
    })->name('invoices.client-index');

    Route::get('/invoices/{id}', function ($id) {
        $invoice = \App\Models\Invoice::whereHas('warehouseRequest', function ($q) {
            $q->where('client_id', auth()->id());
        })->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    })->name('invoices.show');
});

// Driver routes
Route::middleware(['auth', 'driver'])->prefix('driver')->name('driver.')->group(function () {
    Route::get('/jobs', [DriverController::class, 'jobs'])->name('jobs');
    Route::post('/jobs/{order}/accept', [DriverController::class, 'accept'])->name('jobs.accept');
    Route::post('/jobs/{order}/deliver', [DriverController::class, 'deliver'])->name('jobs.deliver');
    Route::post('/jobs/{order}/proof', [DriverController::class, 'uploadProof'])->name('jobs.proof');
    Route::get('/pickups', [DriverController::class, 'pickupJobs'])->name('pickups');
    Route::post('/pickups/{id}/complete', [DriverController::class, 'completePickup'])->name('pickups.complete');
    Route::get('/vehicles', [DriverVehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/register', [DriverVehicleController::class, 'create'])->name('vehicles.register');
    Route::post('/vehicles', [DriverVehicleController::class, 'store'])->name('vehicles.store');
    Route::get('/vehicles/{id}/edit', [DriverVehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('/vehicles/{id}', [DriverVehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{id}', [DriverVehicleController::class, 'destroy'])->name('vehicles.destroy');
    Route::post('/propose-price/{id}', [DriverController::class, 'proposePrice'])->name('propose-price');
    Route::get('/available-jobs', [DriverController::class, 'availableJobs'])->name('available-jobs');
    Route::post('/accept-counter/{proposalId}', [DriverController::class, 'acceptCounter'])->name('accept-counter');
    Route::post('/repropose/{proposalId}', [DriverController::class, 'repropose'])->name('repropose');
});

// Equipment owner routes
Route::middleware(['auth', 'equipment.owner'])->prefix('equipment')->name('equipment.')->group(function () {
    Route::get('/dashboard', [EquipmentController::class, 'dashboard'])->name('dashboard');
    Route::get('/register', [EquipmentController::class, 'create'])->name('register');
    Route::post('/', [EquipmentController::class, 'store'])->name('store');
    Route::get('/jobs', [EquipmentJobController::class, 'index'])->name('jobs');
    Route::post('/jobs/{job}/accept', [EquipmentJobController::class, 'accept'])->name('jobs.accept');
    Route::delete('/{id}', [EquipmentController::class, 'destroy'])->name('delete');
    Route::post('/propose-price/{id}', [EquipmentJobController::class, 'proposePrice'])->name('propose-price');
});

Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile');
Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
Route::post('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change-password');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/pending', [AdminController::class, 'pending'])->name('pending');
    Route::post('/approve/{warehouse}', [AdminController::class, 'approve'])->name('approve');
    Route::post('/reject/{warehouse}', [AdminController::class, 'reject'])->name('reject');
    Route::post('/warehouse/{id}/update-price', [AdminController::class, 'updateWarehousePrice'])->name('warehouse.updatePrice');
    Route::get('/all-warehouses', [AdminController::class, 'allWarehouses'])->name('all-warehouses');
    Route::get('/warehouse/{warehouse}/tenants', [AdminController::class, 'warehouseTenants'])->name('warehouse.tenants');
    Route::delete('/warehouse/{warehouse}/release/{request}', [AdminController::class, 'releaseTenant'])->name('warehouse.release');
    Route::get('/requests', [AdminController::class, 'requests'])->name('requests');
    Route::post('/requests/{id}/assign', [AdminController::class, 'assign'])->name('requests.assign');
    Route::get('/requests/{id}/assign-multi', [AdminController::class, 'assignMultiForm'])->name('requests.assign-multi');
    Route::post('/requests/{id}/assign-multi', [AdminController::class, 'assignMultiStore'])->name('requests.assign-multi.store');
    Route::get('/stocks/pending', [AdminController::class, 'pendingStocks'])->name('pending.stocks');
    Route::post('/stocks/{stockId}/verify', [AdminController::class, 'verifyStock'])->name('verify.stock');
    Route::get('/stocks/{requestId}', [AdminController::class, 'manageStock'])->name('stocks');
    Route::post('/stocks/{requestId}/add', [AdminController::class, 'addStock'])->name('stocks.add');
    Route::put('/stocks/{stockId}', [AdminController::class, 'updateStock'])->name('stocks.update');
    Route::delete('/stocks/{stockId}', [AdminController::class, 'deleteStock'])->name('stocks.delete');
    Route::get('/vehicles', [AdminController::class, 'vehicles'])->name('vehicles');
    Route::post('/vehicles', [AdminController::class, 'storeVehicle'])->name('vehicles.store');
    Route::delete('/vehicles/{id}', [AdminController::class, 'deleteVehicle'])->name('vehicles.delete');
    Route::get('/dispatch', [AdminController::class, 'dispatchOrders'])->name('dispatch');
    Route::post('/dispatch/{orderId}/assign', [AdminController::class, 'assignVehicle'])->name('dispatch.assign');
    Route::post('/dispatch/{orderId}/deliver', [AdminController::class, 'markDelivered'])->name('dispatch.deliver');
    Route::post('/dispatch/{orderId}/proof', [AdminController::class, 'uploadProof'])->name('dispatch.proof');
    Route::get('/pickup-requests', [AdminController::class, 'pickupRequests'])->name('pickup');
    Route::post('/pickup-requests/{id}/assign', [AdminController::class, 'assignPickupVehicle'])->name('pickup.assign');
    Route::get('/equipment-jobs', [EquipmentJobController::class, 'adminIndex'])->name('equipment.jobs');
    Route::post('/equipment-jobs/{job}/assign', [EquipmentJobController::class, 'adminAssign'])->name('equipment.jobs.assign');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles/{id}/toggle', [RoleController::class, 'toggleRole'])->name('roles.toggle');
    Route::get('/clients', [AdminController::class, 'clients'])->name('clients');
    Route::get('/clients/{id}', [AdminController::class, 'clientShow'])->name('clients.show');
    Route::get('/margin-tiers', [MarginController::class, 'index'])->name('margin-tiers');
    Route::post('/margin-tiers', [MarginController::class, 'store'])->name('margin-tiers.store');
    Route::put('/margin-tiers/{id}', [MarginController::class, 'update'])->name('margin-tiers.update');
    Route::delete('/margin-tiers/{id}', [MarginController::class, 'destroy'])->name('margin-tiers.destroy');
    Route::get('/invoices', [AdminController::class, 'invoices'])->name('invoices');
    Route::get('/invoices/{id}', [AdminController::class, 'invoiceShow'])->name('invoices.show');
    Route::get('/partner-earnings', [AdminController::class, 'partnerEarnings'])->name('partner-earnings');
    Route::post('/jobs/{type}/{id}/mark-paid', [AdminController::class, 'markPartnerPaid'])->name('jobs.mark-paid');
    Route::get('/insurance/{requestId}', [AdminController::class, 'manageInsurance'])->name('insurance');
    Route::post('/insurance/{id}/update', [AdminController::class, 'updateInsurance'])->name('insurance.update');
    Route::get('/insurance', [AdminController::class, 'insuranceList'])->name('insurance.list');
    Route::get('/insurance/{requestId}/show', [AdminController::class, 'viewInsurance'])->name('insurance.show');
});

// Authentication routes (Breeze)
require __DIR__.'/auth.php';