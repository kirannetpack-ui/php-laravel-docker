<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'required_space', 'duration_months',
        'invoice_path', 'packing_list_path', 'insurance_path',
        'vehicle_number', 'phone_number', 'preferred_warehouse_id',
        'assigned_warehouse_id', 'status', 'needs_equipment', 'equipment_notes',
        'agreed_price_per_unit', 'security_deposit', 'monthly_rent',
        'contract_end_date', 'last_invoice_date', 'goods_auctioned'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Single warehouse assignment (legacy)
    public function assignedWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'assigned_warehouse_id');
    }

    // Multi‑warehouse assignment (many-to-many)
    public function assignedWarehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'warehouse_request_warehouse')
                    ->withPivot('allocated_space');
    }

    public function preferredWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'preferred_warehouse_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function dispatchOrders()
    {
        return $this->hasMany(DispatchOrder::class);
    }

    public function equipmentJob()
    {
        return $this->hasOne(EquipmentJob::class);
    }

    // Invoices relation – **only one definition**
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

public function insurance()
{
    return $this->hasOne(Insurance::class);
}

}