<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_request_id', 'equipment_id', 'status', 'agreed_rate', 'scheduled_at', 'notes'
    ];

    public function warehouseRequest()
    {
        return $this->belongsTo(WarehouseRequest::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}