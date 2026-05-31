<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupRequest extends Model
{
    protected $fillable = [
        'client_id', 'pickup_address', 'description', 'estimated_boxes',
        'contact_person', 'contact_phone', 'status', 'assigned_vehicle_id'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'assigned_vehicle_id');
    }

public function destinationWarehouse()
{
    return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
}

}