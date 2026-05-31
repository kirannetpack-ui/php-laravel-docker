<?php

namespace App\Http\Controllers;

use App\Models\PickupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse;


class PickupRequestController extends Controller
{
   public function create()
{
    $warehouses = Warehouse::where('status', 'approved')->get();
    return view('pickup.create', compact('warehouses'));
}



    public function store(Request $request)
    {
        $validated = $request->validate([
            'pickup_address' => 'required|string',
            'description' => 'nullable|string',
            'estimated_boxes' => 'required|integer|min:1',
            'contact_person' => 'required|string',
            'contact_phone' => 'required|string',
        ]);
        $validated['client_id'] = Auth::id();
        PickupRequest::create($validated);
        return redirect()->route('pickup.index')->with('success', 'Pickup request submitted.');
    }

    public function index()
    {
        $pickups = PickupRequest::where('client_id', Auth::id())->get();
        return view('pickup.index', compact('pickups'));
    }
}