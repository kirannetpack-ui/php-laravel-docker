<?php

namespace App\Http\Controllers;

use App\Models\WarehouseRequest;
use App\Models\Warehouse;
use App\Models\Stock;
use App\Models\EquipmentJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientRequestHandler extends Controller
{
    public function create()
    {
        $warehouses = Warehouse::where('status', 'approved')
            ->get(['id', 'name', 'address', 'latitude', 'longitude', 'total_capacity', 'allocated_space', 'type', 'price_per_unit', 'price_unit_type', 'usable_capacity'])
            ->map(function ($warehouse) {
                $warehouse->available_space = ($warehouse->usable_capacity ?? 0) - ($warehouse->allocated_space ?? 0);
                return $warehouse;
            })
            ->filter(fn($w) => $w->available_space > 0);

        return view('client_requests.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'required_space' => 'required|numeric|min:0.1',
            'duration_months' => 'required|integer|min:1',
            'vehicle_number' => 'nullable|string|max:50',
            'phone_number' => 'nullable|string|max:20',
            'invoice' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'packing_list' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'insurance' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'preferred_warehouse_id' => 'nullable|exists:warehouses,id',
            'needs_equipment' => 'boolean',
            'equipment_notes' => 'nullable|string',
        ]);

        $data = $validated;
        $data['client_id'] = Auth::id();
        $data['status'] = 'pending';

        if ($request->hasFile('invoice')) {
            $data['invoice_path'] = $request->file('invoice')->store('requests/invoices', 'public');
        }
        if ($request->hasFile('packing_list')) {
            $data['packing_list_path'] = $request->file('packing_list')->store('requests/packing_lists', 'public');
        }
        if ($request->hasFile('insurance')) {
            $data['insurance_path'] = $request->file('insurance')->store('requests/insurances', 'public');
        }

        $warehouseRequest = WarehouseRequest::create($data);

        // Create insurance record
        \App\Models\Insurance::create([
            'warehouse_request_id' => $warehouseRequest->id,
            'start_date' => now(),
            'end_date' => now()->addMonths($warehouseRequest->duration_months),
            'status' => 'active',
        ]);

        if ($request->needs_equipment) {
            EquipmentJob::create([
                'warehouse_request_id' => $warehouseRequest->id,
                'status' => 'pending',
                'notes' => $request->equipment_notes,
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Request submitted. Insurance notification sent.');
    }

    public function index()
    {
        $requests = WarehouseRequest::where('client_id', Auth::id())->get();
        return view('client_requests.index', compact('requests'));
    }

    public function myStock()
    {
        $stocks = Stock::whereHas('warehouseRequest', function ($q) {
            $q->where('client_id', auth()->id());
        })->get();
        return view('client.stock.index', compact('stocks'));
    }

    public function showAddStock($requestId)
    {
        $warehouseRequest = WarehouseRequest::where('client_id', Auth::id())
                            ->where('status', 'assigned')
                            ->findOrFail($requestId);
        return view('client.stock.create', compact('warehouseRequest'));
    }

    public function storeStock(Request $request, $requestId)
    {
        $warehouseRequest = WarehouseRequest::where('client_id', Auth::id())
                            ->where('status', 'assigned')
                            ->findOrFail($requestId);

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'sku' => 'nullable|string|max:100',
        ]);

        $validated['warehouse_request_id'] = $warehouseRequest->id;
        $validated['status'] = 'pending';
        Stock::create($validated);

        return redirect()->route('client.stocks', $warehouseRequest->id)
            ->with('success', 'Stock submitted for verification.');
    }

    public function clientStocks($requestId)
    {
        $warehouseRequest = WarehouseRequest::where('client_id', Auth::id())->findOrFail($requestId);
        $stocks = $warehouseRequest->stocks;
        return view('client.stock.show', compact('warehouseRequest', 'stocks'));
    }

    public function myInsurance()
    {
        $insurances = \App\Models\Insurance::whereHas('warehouseRequest', function ($q) {
            $q->where('client_id', auth()->id());
        })->with('warehouseRequest')->get();

        return view('client.insurance.index', compact('insurances'));
    }
}