<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EquipmentJob;

class EquipmentController extends Controller
{
    public function create()
    {
        return view('equipment.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'model' => 'nullable|string|max:255',
            'capacity_kg' => 'nullable|numeric|min:0',
            'base_charge' => 'nullable|numeric|min:0',
            'is_negotiable' => 'boolean',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        $data['owner_id'] = Auth::id();
        $data['is_available'] = true;

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('equipment', 'public');
        }

        Equipment::create($data);

        return redirect()->route('equipment.dashboard')->with('success', 'Equipment registered.');
    }

  public function dashboard()
{
    $user = Auth::user();
    $equipment = $user->equipment; // All equipment owned by this user

    // Pending jobs (equipment_jobs where equipment_id is one of the user's equipment and status = 'pending')
    $pendingJobs = EquipmentJob::whereIn('equipment_id', $equipment->pluck('id'))
                    ->where('status', 'pending')
                    ->with('warehouseRequest')
                    ->get();

    // Accepted jobs (where status = 'accepted' and equipment belongs to user)
    $acceptedJobs = EquipmentJob::whereIn('equipment_id', $equipment->pluck('id'))
                    ->where('status', 'accepted')
                    ->with('warehouseRequest')
                    ->get();

    return view('equipment.dashboard', compact('equipment', 'pendingJobs', 'acceptedJobs'));
}

public function destroy($id)
{
    $equipment = Equipment::findOrFail($id);
    if ($equipment->owner_id != Auth::id()) abort(403);
    $equipment->delete();
    return redirect()->route('equipment.dashboard')->with('success', 'Equipment removed.');
}

}