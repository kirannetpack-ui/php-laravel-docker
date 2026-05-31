<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentJob;
use App\Models\PartnerProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentJobController extends Controller
{
    public function index()
    {
        $jobs = EquipmentJob::where('status', 'pending')->with('warehouseRequest')->get();
        return view('equipment.jobs', compact('jobs'));
    }

    public function accept($jobId)
    {
        $job = EquipmentJob::findOrFail($jobId);
        $user = Auth::user();
        $equipment = Equipment::where('owner_id', $user->id)->first();

        if (!$equipment) {
            return back()->with('error', 'You do not have registered equipment.');
        }

        // Check if client has accepted the proposal
        if ($job->status !== 'accepted_by_client') {
            return back()->with('error', 'This job has not been accepted by the client yet.');
        }

        if ($job->status !== 'pending' || $job->equipment_id !== null) {
            return back()->with('error', 'This job is no longer available.');
        }

        $job->equipment_id = $equipment->id;
        $job->status = 'accepted';
        $job->save();

        $equipment->is_available = false;
        $equipment->save();

        return redirect()->route('equipment.jobs')->with('success', 'Equipment job accepted.');
    }

    public function proposePrice(Request $request, $id)
    {
        $user = Auth::user();
        $jobType = $request->job_type;
        $model = null;

        $map = [
            'App\Models\DispatchOrder' => \App\Models\DispatchOrder::class,
            'App\Models\PickupRequest' => \App\Models\PickupRequest::class,
            'App\Models\EquipmentJob' => EquipmentJob::class,
        ];

        if (isset($map[$jobType])) {
            $model = $map[$jobType]::findOrFail($id);
        } else {
            abort(400, 'Invalid job type');
        }

        $proposedPrice = $request->proposed_price;
        $marginPercent = \App\Models\MarginTier::getMargin($proposedPrice);
        $adminMargin = ($proposedPrice * $marginPercent) / 100;

        PartnerProposal::create([
            'job_type' => $jobType,
            'job_id' => $model->id,
            'partner_id' => $user->id,
            'proposed_price' => $proposedPrice,
            'admin_margin' => $adminMargin,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Price proposed. Client will review.');
    }

    // Admin methods
    public function adminIndex()
    {
        $jobs = EquipmentJob::with('warehouseRequest')->orderBy('created_at', 'desc')->get();
        $equipment = Equipment::all();
        return view('admin.equipment-jobs', compact('jobs', 'equipment'));
    }

    public function adminAssign(Request $request, $jobId)
    {
        $job = EquipmentJob::findOrFail($jobId);
        $job->equipment_id = $request->equipment_id;
        $job->status = 'pending'; // equipment owner still needs to accept
        $job->save();
        return redirect()->route('admin.equipment.jobs')->with('success', 'Equipment assigned.');
    }
}