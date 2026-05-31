<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarginTier extends Model
{
    use HasFactory;

    protected $fillable = ['min_amount', 'max_amount', 'margin_percentage'];

    public static function getMargin($proposedPrice)
    {
        $tier = self::where('min_amount', '<=', $proposedPrice)
                    ->where(function ($q) use ($proposedPrice) {
                        $q->where('max_amount', '>=', $proposedPrice)
                          ->orWhereNull('max_amount');
                    })->first();
        return $tier ? $tier->margin_percentage : 0;
    }
}