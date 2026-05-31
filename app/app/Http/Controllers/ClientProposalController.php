<?php

namespace App\Http\Controllers;

use App\Models\PartnerProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientProposalController extends Controller
{
    // No constructor – middleware applied in routes

    public function index()
    {
        $proposals = PartnerProposal::whereHas('job', function ($q) {
            $q->whereHas('warehouseRequest', function ($q2) {
                $q2->where('client_id', Auth::id());
            });
        })->orderBy('created_at', 'desc')->get();

        return view('client.proposals.index', compact('proposals'));
    }

    public function accept($id)
{
    $proposal = PartnerProposal::whereHas('job', function ($q) {
        $q->whereHas('warehouseRequest', function ($q2) {
            $q2->where('client_id', Auth::id());
        });
    })->findOrFail($id);

    $job = $proposal->job;
    $job->assigned_driver_id = $proposal->partner_id;
    $job->agreed_price = $proposal->proposed_price;
    $job->status = 'accepted_by_client';
    $job->save();

    $proposal->status = 'accepted';
    $proposal->save();

    return redirect()->route('client.proposals')->with('success', 'Proposal accepted. The driver can now proceed.');
}

    public function reject($id)
    {
        $proposal = PartnerProposal::whereHas('job', function ($q) {
            $q->whereHas('warehouseRequest', function ($q2) {
                $q2->where('client_id', Auth::id());
            });
        })->findOrFail($id);

        $proposal->status = 'rejected';
        $proposal->save();

        return redirect()->route('client.proposals')->with('success', 'Proposal rejected.');
    }

    public function negotiate(Request $request, $id)
{
    $proposal = PartnerProposal::whereHas('job', function ($q) {
        $q->whereHas('warehouseRequest', function ($q2) {
            $q2->where('client_id', Auth::id());
        });
    })->findOrFail($id);

    $proposal->status = 'negotiating';
    $proposal->negotiation_notes = $request->notes;
    $proposal->counter_offer = $request->counter_price; // add this column to table
    $proposal->save();

    return redirect()->route('client.proposals')->with('success', 'Negotiation sent. Partner will review.');
}
}