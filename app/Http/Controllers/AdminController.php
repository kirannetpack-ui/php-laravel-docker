<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseRequest;
use App\Models\DispatchOrder;
use App\Models\Vehicle;
use App\Models\Stock;
use App\Models\PickupRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WarehouseApproved;
use App\Mail\WarehouseRejected;
use App\Mail\RequestAssigned;
use App\Mail\DispatchStatus;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use App\Models\PartnerJobOffer;
use App\Models\Invoice;
use App\Models\EquipmentJob;
use App\Models\Insurance;


class AdminController extends Controller
{
    // ==================== WAREHOUSE APPROVAL ====================
    public function pending()
    {
        $warehouses = Warehouse::where('status', 'pending')->get();
        return view('admin.warehouses.pending', compact('warehouses'));
    }

    public function approve(Warehouse $warehouse)
    {
        $warehouse->status = 'approved';
        $warehouse->save();
        Mail::to($warehouse->owner->email)->send(new WarehouseApproved($warehouse));
        return redirect()->route('admin.pending')->with('success', 'Warehouse approved.');
    }

    public function reject(Warehouse $warehouse)
    {
        $warehouse->status = 'rejected';
        $warehouse->save();
        Mail::to($warehouse->owner->email)->send(new WarehouseRejected($warehouse));
        return redirect()->route('admin.pending')->with('success', 'Warehouse rejected.');
    }

    // ==================== CLIENT REQUESTS ====================
    public function requests()
    {
        $requests = WarehouseRequest::with('client', 'assignedWarehouse', 'assignedWarehouses')->orderBy('created_at', 'desc')->get();
        $warehouses = Warehouse::where('status', 'approved')->get();
        return view('admin.requests.index', compact('requests', 'warehouses'));
    }

    public function assign(Request $request, $id)
{
    $warehouseRequest = WarehouseRequest::findOrFail($id);
    $warehouse = Warehouse::findOrFail($request->warehouse_id);

    $available = $warehouse->total_capacity - $warehouse->allocated_space;
    if ($warehouseRequest->required_space > $available) {
        return back()->with('error', "Not enough space. Required: {$warehouseRequest->required_space} m³, Available: {$available} m³");
    }

    if (!$warehouse->allow_shared && $warehouse->allocated_space > 0) {
        return back()->with('error', 'This warehouse is exclusive and already occupied.');
    }

    // Assign warehouse
    $warehouseRequest->assigned_warehouse_id = $warehouse->id;
    $warehouseRequest->status = 'assigned';
    
    // Set pricing and contract details
    $warehouseRequest->agreed_price_per_unit = $warehouse->price_per_unit;
    $warehouseRequest->monthly_rent = $warehouse->price_per_unit * $warehouseRequest->required_space;
    $warehouseRequest->security_deposit = $warehouse->calculateDeposit($warehouseRequest->required_space);
    $warehouseRequest->contract_end_date = now()->addMonths($warehouseRequest->duration_months);
    $warehouseRequest->last_invoice_date = now(); // first invoice will be generated next month
    $warehouseRequest->save();

    // Update warehouse allocated space
    $warehouse->allocated_space += $warehouseRequest->required_space;
    $warehouse->save();

    Mail::to($warehouseRequest->client->email)->send(new RequestAssigned($warehouseRequest));

    return redirect()->route('admin.requests')->with('success', 'Request assigned successfully.');
}

    // ==================== MULTI‑WAREHOUSE ASSIGNMENT ====================
    private function findWarehouseCombination($requiredSpace)
    {
        $warehouses = Warehouse::where('status', 'approved')
                    ->whereRaw('total_capacity - allocated_space > 0')
                    ->orderBy('distance_from_city', 'asc')
                    ->get(['id', 'name', 'total_capacity', 'allocated_space', 'distance_from_city', 'type']);

        $combination = [];
        $remaining = $requiredSpace;

        foreach ($warehouses as $wh) {
            $free = $wh->total_capacity - $wh->allocated_space;
            if ($free <= 0) continue;
            $take = min($free, $remaining);
            $combination[] = [
                'warehouse' => $wh,
                'allocated' => $take,
                'unit' => $wh->type === 'building' ? 'm³' : 'm²'
            ];
            $remaining -= $take;
            if ($remaining <= 0) break;
        }

        return ($remaining <= 0) ? $combination : null;
    }

    public function assignMultiForm($id)
    {
        $warehouseRequest = WarehouseRequest::with('client')->findOrFail($id);
        $required = $warehouseRequest->required_space;
        $suggestion = $this->findWarehouseCombination($required);

        if (!$suggestion) {
            return redirect()->route('admin.requests')->with('error', 'Not enough space available in any warehouse combination.');
        }

        $allWarehouses = Warehouse::where('status', 'approved')
                        ->orderBy('distance_from_city')
                        ->get();

        return view('admin.requests.assign-multi', compact('warehouseRequest', 'suggestion', 'allWarehouses'));
    }

    public function assignMultiStore(Request $request, $id)
{

    $request->validate([
        'agreed_price_per_unit' => 'required|numeric|min:0',
        'security_deposit' => 'required|numeric|min:0',
        'contract_end_date' => 'required|date|after:today',
    ]);

    $warehouseRequest = WarehouseRequest::findOrFail($id);
    $allocations = $request->allocations;

    $totalAllocated = 0;
    $totalMonthlyRent = 0;
    $securityDeposit = 0;

    foreach ($allocations as $whId => $space) {
        $space = floatval($space);
        if ($space <= 0) continue;
        $warehouse = Warehouse::find($whId);
        if (!$warehouse) continue;
        $free = $warehouse->total_capacity - $warehouse->allocated_space;
        if ($space > $free) {
            return back()->with('error', "Not enough space in warehouse {$warehouse->name}. Max available: {$free}");
        }
        $totalAllocated += $space;
        // Use the warehouse's price per unit for calculation if admin didn't override? But we have a single agreed price for the request.
        // For simplicity, we'll not calculate rent per warehouse here; we'll use the global agreed price.
        $warehouseRequest->assignedWarehouses()->attach($whId, ['allocated_space' => $space]);
        $warehouse->allocated_space += $space;
        $warehouse->save();
    }

    if ($totalAllocated < $warehouseRequest->required_space) {
        return back()->with('error', "Total allocated space ($totalAllocated) is less than required ({$warehouseRequest->required_space}).");
    }

    // Save pricing and contract details from form
    $warehouseRequest->agreed_price_per_unit = $request->agreed_price_per_unit;
    $warehouseRequest->security_deposit = $request->security_deposit;
    $warehouseRequest->contract_end_date = $request->contract_end_date;
    $warehouseRequest->monthly_rent = $request->agreed_price_per_unit * $warehouseRequest->required_space;
    $warehouseRequest->last_invoice_date = now(); // first invoice will be generated next month
    $warehouseRequest->status = 'assigned';
    $warehouseRequest->save();

    return redirect()->route('admin.requests')->with('success', 'Warehouses assigned successfully.');
}

    // ==================== STOCK MANAGEMENT ====================
    public function pendingStocks()
    {
        $pendingStocks = Stock::where('status', 'pending')->with('warehouseRequest.client')->get();
        return view('admin.stocks.pending', compact('pendingStocks'));
    }

    public function verifyStock(Request $request, $stockId)
    {
        $stock = Stock::findOrFail($stockId);
        $validated = $request->validate([
            'status' => 'required|in:verified,rejected',
            'admin_notes' => 'nullable|string',
        ]);
        $stock->status = $validated['status'];
        $stock->admin_notes = $validated['admin_notes'];
        $stock->save();

        if ($stock->status == 'verified') {
            $warehouseRequest = $stock->warehouseRequest;
            $warehouse = $warehouseRequest->assignedWarehouse;
            $qrData = [
                'stock_id' => $stock->id,
                'client' => $warehouseRequest->client->name,
                'warehouse' => $warehouse ? $warehouse->name : 'Multiple',
                'total_boxes' => $stock->quantity,
                'invoice' => $warehouseRequest->invoice_path,
                'packing_list' => $warehouseRequest->packing_list_path,
            ];
            $qrContent = json_encode($qrData);
            $qrCode = QrCode::size(200)->generate($qrContent);
            $qrPath = 'qrcodes/stock_' . $stock->id . '.png';
            Storage::disk('public')->put($qrPath, $qrCode);
            $stock->qr_code = $qrPath;
            $stock->save();
        }

        return redirect()->route('admin.pending.stocks')->with('success', 'Stock verified.');
    }

    public function manageStock($requestId)
    {
        $warehouseRequest = WarehouseRequest::with('stocks')->findOrFail($requestId);
        if ($warehouseRequest->status !== 'assigned') {
            return redirect()->route('admin.requests')->with('error', 'Request not assigned to a warehouse.');
        }
        return view('admin.stocks.index', compact('warehouseRequest'));
    }

    public function addStock(Request $request, $requestId)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100',
        ]);
        $validated['warehouse_request_id'] = $requestId;
        Stock::create($validated);
        return redirect()->route('admin.stocks', $requestId)->with('success', 'Stock added.');
    }

    public function updateStock(Request $request, $stockId)
    {
        $stock = Stock::findOrFail($stockId);
        $validated = $request->validate(['quantity' => 'required|integer|min:0']);
        $stock->update($validated);
        return redirect()->back()->with('success', 'Stock updated.');
    }

    public function deleteStock($stockId)
    {
        $stock = Stock::findOrFail($stockId);
        $requestId = $stock->warehouse_request_id;
        $stock->delete();
        return redirect()->route('admin.stocks', $requestId)->with('success', 'Stock removed.');
    }

    // ==================== VEHICLE MANAGEMENT ====================
    public function vehicles()
    {
        $vehicles = Vehicle::all();
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function storeVehicle(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:bike,van,truck',
            'registration_number' => 'required|unique:vehicles',
            'driver_name' => 'required|string',
            'driver_phone' => 'required|string',
            'capacity_boxes' => 'nullable|integer|min:0',
            'license_photo' => 'nullable|image|mimes:jpg,png|max:2048',
        ]);
        if ($request->hasFile('license_photo')) {
            $validated['driver_license_photo'] = $request->file('license_photo')->store('licenses', 'public');
        }
        $validated['is_available'] = true;
        Vehicle::create($validated);
        return redirect()->route('admin.vehicles')->with('success', 'Vehicle added.');
    }

    public function deleteVehicle($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        if ($vehicle->dispatchOrders()->exists()) {
            return back()->with('error', 'Cannot delete vehicle with assigned orders.');
        }
        $vehicle->delete();
        return redirect()->route('admin.vehicles')->with('success', 'Vehicle deleted.');
    }

    // ==================== DISPATCH ORDER MANAGEMENT ====================
    public function dispatchOrders()
    {
        $orders = DispatchOrder::with('warehouseRequest.client')->orderBy('created_at', 'desc')->get();
        $vehicles = Vehicle::where('is_available', true)->get();
        return view('admin.dispatch.index', compact('orders', 'vehicles'));
    }

    public function assignVehicle(Request $request, $orderId)
    {
        $order = DispatchOrder::findOrFail($orderId);
        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        $order->assigned_vehicle_id = $vehicle->id;
        $order->save();

        $vehicle->is_available = false;
        $vehicle->save();
        return redirect()->route('admin.dispatch')->with('success', 'Vehicle assigned. Driver will accept the job.');
    }

    public function markDelivered($orderId)
    {
        $order = DispatchOrder::findOrFail($orderId);
        $order->status = 'delivered';
        $order->save();

        if ($order->vehicle) {
            $order->vehicle->is_available = true;
            $order->vehicle->save();
        }
        Mail::to($order->warehouseRequest->client->email)->send(new DispatchStatus($order));
        return redirect()->route('admin.dispatch')->with('success', 'Order marked delivered.');
    }

    public function uploadProof(Request $request, $orderId)
    {
        $request->validate(['proof_photo' => 'required|image|max:2048']);
        $order = DispatchOrder::findOrFail($orderId);
        $path = $request->file('proof_photo')->store('proofs', 'public');
        $order->proof_of_delivery_photo = $path;
        $order->save();
        return redirect()->route('admin.dispatch')->with('success', 'Proof uploaded.');
    }

    // ==================== PICKUP REQUESTS ====================
    public function pickupRequests()
    {
        $pickups = PickupRequest::where('status', 'pending')->get();
        $vehicles = Vehicle::where('is_available', true)->get();
        return view('admin.pickup.index', compact('pickups', 'vehicles'));
    }

    public function assignPickupVehicle(Request $request, $id)
    {
        $pickup = PickupRequest::findOrFail($id);
        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $pickup->assigned_vehicle_id = $vehicle->id;
        $pickup->status = 'accepted';
        $pickup->save();
        return redirect()->route('admin.pickup')->with('success', 'Vehicle assigned.');
    }

    // ==================== ADMIN WAREHOUSE LISTING & TENANT REMOVAL ====================
    public function allWarehouses()
    {
        $warehouses = Warehouse::with('owner')->orderBy('id')->get();
        return view('admin.warehouses.all', compact('warehouses'));
    }

    public function warehouseTenants(Warehouse $warehouse)
    {
        $assignedRequests = WarehouseRequest::where('assigned_warehouse_id', $warehouse->id)->get();
        $pivotRequests = $warehouse->assignedRequests;
        $tenants = $assignedRequests->merge($pivotRequests);
        return view('admin.warehouses.tenants', compact('warehouse', 'tenants'));
    }

    public function releaseTenant(Warehouse $warehouse, WarehouseRequest $request)
    {
        if ($request->assigned_warehouse_id == $warehouse->id) {
            $warehouse->allocated_space -= $request->required_space;
            $warehouse->save();
            $request->assigned_warehouse_id = null;
            $request->status = 'released';
            $request->save();
            return back()->with('success', 'Tenant removed, space freed.');
        }
        $pivot = $warehouse->assignedRequests()->where('warehouse_request_id', $request->id)->first();
        if ($pivot) {
            $allocated = $pivot->pivot->allocated_space;
            $warehouse->allocated_space -= $allocated;
            $warehouse->save();
            $warehouse->assignedRequests()->detach($request->id);
            if ($request->assignedWarehouses()->count() == 0) {
                $request->status = 'released';
                $request->save();
            }
            return back()->with('success', 'Tenant removed from this warehouse.');
        }
        return back()->with('error', 'No assignment found.');
    }

    // ==================== CLIENTS MANAGEMENT ====================
    public function clients()
    {
        $clients = User::where('is_client', true)
                    ->orWhereHas('warehouseRequests')
                    ->orderBy('name')
                    ->get();
        return view('admin.clients.index', compact('clients'));
    }

public function clientShow($id)
{
    $client = User::with(['warehouseRequests.stocks', 'warehouseRequests.assignedWarehouse'])->findOrFail($id);
    return view('admin.clients.show', compact('client'));
}

public function partnerOffers()
{
    $offers = PartnerJobOffer::with('partner')->where('status', 'pending')->get();
    
    // Map old job_type strings to full class names for polymorphic relation
    foreach ($offers as $offer) {
        $jobType = $offer->job_type;
        if ($jobType === 'dispatch_order') {
            $offer->job_type = 'App\\Models\\DispatchOrder';
        } elseif ($jobType === 'pickup_request') {
            $offer->job_type = 'App\\Models\\PickupRequest';
        } elseif ($jobType === 'equipment_job') {
            $offer->job_type = 'App\\Models\\EquipmentJob';
        }
        // Reload the relation manually
        $offer->load('job');
    }
    
    return view('admin.partner-offers', compact('offers'));
}

public function approvePartnerOffer(Request $request, $id)
{
    $offer = PartnerJobOffer::findOrFail($id);
    $marginType = $request->margin_type; // 'fixed' or 'percentage'
    $marginValue = $request->margin_value;

    $finalPrice = $offer->proposed_price;
    if ($marginType === 'percentage') {
        $finalPrice += $finalPrice * ($marginValue / 100);
    } elseif ($marginType === 'fixed') {
        $finalPrice += $marginValue;
    }

    $offer->admin_final_price = $finalPrice;
    $offer->status = 'approved';
    $offer->admin_notes = $request->admin_notes;
    $offer->save();

    // Update the job's agreed_price
    $job = $offer->job;
    $job->agreed_price = $finalPrice;
    $job->save();

    // Optionally send email to driver/equipment owner
    // Mail::to($offer->partner->email)->send(new JobPriceApproved($offer));

    return redirect()->route('admin.partner-offers')->with('success', 'Offer approved.');
}

public function updateWarehousePrice(Request $request, $id)
{
    $warehouse = Warehouse::findOrFail($id);
    $warehouse->price_per_unit = $request->price_per_unit;
    $warehouse->save();
    return back()->with('success', 'Price updated.');
}

//INVOICE VIEW BY ADMIN
public function invoices()
{
    $invoices = Invoice::with('warehouseRequest.client')->orderBy('created_at', 'desc')->get();
    return view('admin.invoices.index', compact('invoices'));
}

public function invoiceShow($id)
{
    $invoice = Invoice::with('warehouseRequest.client')->findOrFail($id);
    return view('admin.invoices.show', compact('invoice'));
}

public function markPartnerPaid($type, $id)
{
    $model = null;
    if ($type === 'dispatch') {
        $model = DispatchOrder::findOrFail($id);
    } elseif ($type === 'pickup') {
        $model = PickupRequest::findOrFail($id);
    } elseif ($type === 'equipment') {
        $model = EquipmentJob::findOrFail($id);
    } else {
        abort(404);
    }
    $model->partner_paid = true;
    $model->save();
    return back()->with('success', 'Partner payment marked as received.');
}

public function partnerEarnings()
{
    $dispatchOrders = DispatchOrder::where('partner_paid', true)->get();
    $pickupRequests = PickupRequest::where('partner_paid', true)->get();
    $equipmentJobs = EquipmentJob::where('partner_paid', true)->get();

    $totalEarnings = $dispatchOrders->sum('agreed_price') + $pickupRequests->sum('agreed_price') + $equipmentJobs->sum('agreed_price');

    return view('admin.partner-earnings', compact('dispatchOrders', 'pickupRequests', 'equipmentJobs', 'totalEarnings'));
}

//----------------------INSURANCE---------------------
public function insuranceList()
{
    $requests = WarehouseRequest::with('client', 'insurance')->orderBy('id', 'desc')->get();
    return view('admin.insurance.list', compact('requests'));
}

public function viewInsurance($requestId)
{
    $warehouseRequest = WarehouseRequest::with('client', 'insurance')->findOrFail($requestId);
    return view('admin.insurance.show', compact('warehouseRequest'));
}

}