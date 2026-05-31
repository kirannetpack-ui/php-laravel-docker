<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_request_id', 'product_name', 'description', 'quantity', 'sku'
    ];

    public function warehouseRequest()
    {
        return $this->belongsTo(WarehouseRequest::class);
    }
}