<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehousePhoto;
use App\Http\Requests\StoreWarehouseRequest;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function create()
    {
        return view('warehouses.create');
    }

    public function store(StoreWarehouseRequest $request)
    {
        $data = $request->validated();

        $warehouse = Warehouse::create([
            'owner_id' => Auth::id(),
            'name' => $data['name'],
            'length' => $data['length'],
            'width' => $data['width'],
            'height' => $data['height'],
            'has_cctv' => $data['has_cctv'] ?? false,
            'has_security_guard' => $data['has_security_guard'] ?? false,
            'guard_count' => $data['guard_count'] ?? null,
            'has_labors' => $data['has_labors'] ?? false,
            'is_motorable' => $data['is_motorable'] ?? false,
            'distance_from_city' => $data['distance_from_city'] ?? null,
            'status' => 'pending',
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('warehouse_photos', 'public');
                WarehousePhoto::create([
                    'warehouse_id' => $warehouse->id,
                    'photo_path' => $path,
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Warehouse submitted for approval.');
    }

    public function index()
    {
        $warehouses = Warehouse::where('owner_id', Auth::id())->get();
        return view('warehouses.index', compact('warehouses'));
    }

    public function show(Warehouse $warehouse)
{
    // Admin can view any warehouse
    if (auth()->user()->is_admin) {
        return view('warehouses.show', compact('warehouse'));
    }
    // Otherwise, only the owner can view
    if ($warehouse->owner_id !== auth()->id()) {
        abort(403);
    }
    return view('warehouses.show', compact('warehouse'));
}
}