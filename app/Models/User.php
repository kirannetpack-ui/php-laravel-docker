<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

   protected $fillable = [
    'name', 'email', 'phone', 'password',
    'is_admin', 'is_driver', 'is_equipment_owner', 'is_property_owner', 'is_client'
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

public function equipment()
{
    return $this->hasMany(Equipment::class, 'owner_id');
}

public function isEquipmentOwner()
{
    return $this->is_equipment_owner;
}

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

public function warehouseRequests()
{
    return $this->hasMany(WarehouseRequest::class, 'client_id');
}

    // Relationship with warehouses (FIXED)
    public function warehouses()
    {
        return $this->hasMany(Warehouse::class, 'owner_id');
    }
}