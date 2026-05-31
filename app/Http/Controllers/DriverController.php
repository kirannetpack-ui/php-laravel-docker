<?php

namespace App\Http\Controllers;

use App\Models\DispatchOrder;
use App\Models\Vehicle;
use App\Models\PickupRequest;
use App\Models\PartnerProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    public function jobs()
    {
        $user = Auth::user();
        $vehicle = Vehicle::where('driver_phone', $user->phone ?? '')->first();

        $pendingOrders = DispatchOrder::where('status', 'accepted_by_client')
                        ->where('assigned_driver_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();

        $acceptedOrders = DispatchOrder::where('driver_id', $user->id)
                        ->where('status', 'accepted')
                        ->orderBy('created_at', 'desc')
                        ->get();

        $deliveredOrders = DispatchOrder::where('driver_id', $user->id)
                        ->where('status', 'delivered')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('driver.dashboard', compact('pendingOrders', 'acceptedOrders', 'deliveredOrders', 'vehicle'));
    }

    public function accept($orderId)
    {
        $order = DispatchOrder::findOrFail($orderId);
        $user = Auth::user();

        if ($order->status !== 'accepted_by_client' || $order->assigned_driver_id != $user->id) {
            return back()->with('error', 'This order is not assigned to you or client has not accepted your proposal.');
        }

        $order->driver_id = $user->id;
        $order->accepted_at = now();
        $order->status = 'accepted';
        $order->save();

        return redirect()->route('driver.jobs')->with('success', 'Job accepted.');
    }

    public function deliver($orderId)
    {
        $order = DispatchOrder::findOrFail($orderId);
        $user = Auth::user();
        if ($order->driver_id != $user->id) {
            abort(403);
        }
        $order->status = 'delivered';
        $order->save();

        if ($order->vehicle) {
            $order->vehicle->is_available = true;
            $order->vehicle->save();
        }

        return redirect()->route('driver.jobs')->with('success', 'Order marked delivered.');
    }

    public function uploadProof(Request $request, $orderId)
    {
        $request->validate(['proof_photo' => 'required|image|max:2048']);
        $order = DispatchOrder::findOrFail($orderId);
        if ($order->driver_id != Auth::id()) abort(403);

        $path = $request->file('proof_photo')->store('proofs', 'public');
        $order->proof_of_delivery_photo = $path;
        $order->save();

        return back()->with('success', 'Proof uploaded.');
    }

    public function availableJobs()
    {
        $dispatchOrders = DispatchOrder::where('status', 'pending')
                            ->whereNull('assigned_driver_id')
                            ->get();
        $pickupRequests = PickupRequest::where('status', 'pending')
                            ->whereNull('assigned_driver_id')
                            ->get();

        return view('driver.available-jobs', compact('dispatchOrders', 'pickupRequests'));
    }

    public function proposePrice(Request $request, $id)
    {
        $user = Auth::user();
        $jobType = $request->job_type;
        $model = null;

        $map = [
            'App\Models\DispatchOrder' => DispatchOrder::class,
            'App\Models\PickupRequest' => PickupRequest::class,
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

    public function acceptCounter($proposalId)
    {
        $proposal = PartnerProposal::findOrFail($proposalId);
        $proposal->status = 'accepted';
        $proposal->proposed_price = $proposal->counter_offer;
        $proposal->counter_offer = null;
        $proposal->save();

        return redirect()->route('driver.available-jobs')->with('success', 'Counter‑offer accepted. Client will be notified.');
    }

    public function repropose(Request $request, $proposalId)
    {
        $proposal = PartnerProposal::findOrFail($proposalId);
        $proposal->proposed_price = $request->new_price;
        $proposal->negotiation_notes = $request->notes;
        $proposal->status = 'pending';
        $proposal->counter_offer = null;
        $proposal->save();

        return redirect()->route('driver.available-jobs')->with('success', 'New price proposed. Client will review.');
    }

    // Pickup methods
    public function pickupJobs()
    {
        $user = Auth::user();
        $vehicle = Vehicle::where('driver_phone', $user->phone ?? '')->first();
        if (!$vehicle) {
            return view('driver.pickups', ['pickups' => collect()])->with('error', 'No vehicle assigned.');
        }

        $pickups = PickupRequest::where('assigned_vehicle_id', $vehicle->id)
                    ->where('status', 'accepted')
                    ->get();

        return view('driver.pickups', compact('pickups'));
    }

    public function completePickup($id)
    {
        $pickup = PickupRequest::findOrFail($id);
        $user = Auth::user();
        $vehicle = Vehicle::where('driver_phone', $user->phone ?? '')->first();

        if (!$vehicle || $pickup->assigned_vehicle_id != $vehicle->id) {
            abort(403);
        }

        $pickup->status = 'completed';
        $pickup->save();

        return redirect()->route('driver.pickups')->with('success', 'Pickup completed.');
    }
}