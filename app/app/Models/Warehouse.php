<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id', 
        'name', 
        'type',
        'address', 
        'latitude', 
        'longitude',
        'length', 
        'width', 
        'height', 
        'area_sq_m',
        'total_capacity',
        'allocated_space',
        'allow_shared',
        'has_cctv', 
        'has_security_guard', 
        'guard_count',
        'has_labors', 
        'is_motorable', 
        'distance_from_city',
        'camera_stream_url', 
        'status',
        // pricing fields
        'price_per_unit',
        'price_unit_type',
        'security_deposit_percentage',
        'security_deposit_fixed',
	'usable_capacity',
'total_capacity', 
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function photos()
    {
        return $this->hasMany(WarehousePhoto::class);
    }

    public function assignedRequests()
    {
        return $this->belongsToMany(WarehouseRequest::class, 'warehouse_request_warehouse')
                    ->withPivot('allocated_space');
    }

    protected static function booted()
{
    static::saving(function ($warehouse) {
        if ($warehouse->type === 'building') {
            $warehouse->total_capacity = $warehouse->length * $warehouse->width * $warehouse->height;
        } elseif ($warehouse->type === 'open_field') {
            $warehouse->total_capacity = $warehouse->area_sq_m ?? ($warehouse->length * $warehouse->width);
        }
        // Usable capacity = 90% of total capacity
        $warehouse->usable_capacity = $warehouse->total_capacity * 0.9;
    });
}

    public function getCapacityUnitAttribute()
    {
        return $this->type === 'building' ? 'm³' : 'm²';
    }

    public function calculateDeposit($requiredSpace)
    {
        $deposit = 0;
        if ($this->security_deposit_percentage) {
            $deposit = ($this->price_per_unit * $requiredSpace) * ($this->security_deposit_percentage / 100);
        } elseif ($this->security_deposit_fixed) {
            $deposit = $this->security_deposit_fixed;
        }
        return $deposit;
    }
}