<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
  protected $fillable = [
    'type', 'registration_number', 'driver_name', 'driver_phone',
    'driver_license_photo', 'is_available', 'capacity_boxes', 'current_load',
    'driver_user_id'
];

    public function driverUser()
    {
        return $this->belongsTo(User::class, 'driver_user_id');
    }

    public function dispatchOrders()
    {
        return $this->hasMany(DispatchOrder::class, 'assigned_vehicle_id');
    }
}