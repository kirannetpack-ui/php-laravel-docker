<?php

namespace App\Http\Controllers;

use App\Models\WarehouseRequest;
use App\Models\DispatchOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispatchController extends Controller
{
    
    public function create($requestId)
    {
        $warehouseRequest = WarehouseRequest::where('client_id', Auth::id())
                            ->where('status', 'assigned')
                            ->findOrFail($requestId);
        $stocks = $warehouseRequest->stocks;
        return view('dispatch.create', compact('warehouseRequest', 'stocks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_request_id' => 'required|exists:warehouse_requests,id',
            'destination_address' => 'required|string',
            'pan_vat_bill' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'items' => 'required|array',
            'items.*.stock_id' => 'required|exists:stocks,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $data = $validated;
        $data['status'] = 'pending';

        if ($request->hasFile('pan_vat_bill')) {
            $data['pan_vat_bill'] = $request->file('pan_vat_bill')->store('dispatch_bills', 'public');
        }

        $dispatchOrder = DispatchOrder::create($data);

        foreach ($request->items as $item) {
            $dispatchOrder->items()->create($item);
        }

        return redirect()->route('dispatch.index')->with('success', 'Dispatch order created.');
    }

    public function index()
    {
        $orders = DispatchOrder::whereHas('warehouseRequest', function($q) {
            $q->where('client_id', Auth::id());
        })->get();
        return view('dispatch.index', compact('orders'));
    }

    public function show($id)
    {
        $order = DispatchOrder::whereHas('warehouseRequest', function($q) {
            $q->where('client_id', Auth::id());
        })->findOrFail($id);
        return view('dispatch.show', compact('order'));
    }
}