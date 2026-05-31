<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverVehicleController extends Controller
{
    // No constructor – middleware applied in routes

    public function index()
    {
        $vehicles = Vehicle::where('driver_user_id', Auth::id())->get();
        return view('driver.vehicle.index', compact('vehicles'));
    }

    public function create()
    {
        return view('driver.vehicle.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:vehicles',
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'capacity_boxes' => 'nullable|integer|min:0',
            'license_photo' => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        $data['driver_user_id'] = Auth::id();
        $data['is_available'] = true;

        if ($request->hasFile('license_photo')) {
            $data['driver_license_photo'] = $request->file('license_photo')->store('licenses', 'public');
        }

        Vehicle::create($data);

        return redirect()->route('driver.vehicles.index')->with('success', 'Vehicle registered.');
    }

    public function edit($id)
    {
        $vehicle = Vehicle::where('driver_user_id', Auth::id())->findOrFail($id);
        return view('driver.vehicle.edit', compact('vehicle'));
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::where('driver_user_id', Auth::id())->findOrFail($id);
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:vehicles,registration_number,' . $vehicle->id,
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'capacity_boxes' => 'nullable|integer|min:0',
            'license_photo' => 'nullable|image|max:2048',
        ]);

        $vehicle->update($validated);
        if ($request->hasFile('license_photo')) {
            $vehicle->driver_license_photo = $request->file('license_photo')->store('licenses', 'public');
            $vehicle->save();
        }
        return redirect()->route('driver.vehicles.index')->with('success', 'Vehicle updated.');
    }

    public function destroy($id)
    {
        $vehicle = Vehicle::where('driver_user_id', Auth::id())->findOrFail($id);
        $vehicle->delete();
        return redirect()->route('driver.vehicles.index')->with('success', 'Vehicle deleted.');
    }
}