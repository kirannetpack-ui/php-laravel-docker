<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchOrder extends Model
{
    use HasFactory;

    protected $fillable = [
    'warehouse_request_id', 'destination_address', 'pan_vat_bill',
    'status', 'assigned_vehicle_id', 'proof_of_delivery_photo',
    'contact_person', 'contact_phone'   // <-- add these
    ];

    public function warehouseRequest()
    {
        return $this->belongsTo(WarehouseRequest::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'assigned_vehicle_id');
    }

public function recalculateTotal()
{
    $this->total_quantity = $this->items->sum('quantity');
    $this->saveQuietly(); // saves without firing events
}

}