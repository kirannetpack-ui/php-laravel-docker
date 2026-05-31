<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id', 'type', 'model', 'capacity_kg', 'base_charge',
        'is_negotiable', 'is_available', 'photo_path'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function jobs()
    {
        return $this->hasMany(EquipmentJob::class);
    }
}