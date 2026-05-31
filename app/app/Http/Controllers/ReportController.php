<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseRequest;
use App\Models\DispatchOrder;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // Basic counts
        $totalWarehouses = Warehouse::count();
        $approvedWarehouses = Warehouse::where('status', 'approved')->count();
        $pendingWarehouses = Warehouse::where('status', 'pending')->count();

        $totalRequests = WarehouseRequest::count();
        $pendingRequests = WarehouseRequest::where('status', 'pending')->count();
        $assignedRequests = WarehouseRequest::where('status', 'assigned')->count();

        $totalDispatchOrders = DispatchOrder::count();
        $pendingOrders = DispatchOrder::where('status', 'pending')->count();
        $assignedOrders = DispatchOrder::where('status', 'assigned')->count();
        $deliveredOrders = DispatchOrder::where('status', 'delivered')->count();

        // For chart: monthly data (last 6 months) – example using dispatch orders
        $monthlyOrders = DispatchOrder::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();

        return view('admin.reports', compact(
            'totalWarehouses', 'approvedWarehouses', 'pendingWarehouses',
            'totalRequests', 'pendingRequests', 'assignedRequests',
            'totalDispatchOrders', 'pendingOrders', 'assignedOrders', 'deliveredOrders',
            'monthlyOrders'
        ));
    }
}