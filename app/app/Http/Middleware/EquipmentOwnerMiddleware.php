<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EquipmentOwnerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->is_equipment_owner) {
            abort(403, 'Equipment owner access only.');
        }
        return $next($request);
    }
}