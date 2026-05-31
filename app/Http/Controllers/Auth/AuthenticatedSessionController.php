<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
if ($user->is_admin) return redirect()->route('dashboard');
if ($user->is_driver) return redirect()->route('driver.jobs');
if ($user->is_equipment_owner) return redirect()->route('equipment.dashboard');
if ($user->is_property_owner) return redirect()->route('dashboard');
if ($user->is_client) return redirect()->route('dashboard');
return redirect()->intended(route('dashboard'));
    }

public function store(LoginRequest $request)
{
    $request->authenticate();

    $request->session()->regenerate();

    $user = Auth::user();
    
    if ($user->is_driver) return redirect()->route('driver.jobs');
    if ($user->is_equipment_owner) return redirect()->route('equipment.dashboard');
    if ($user->is_admin) return redirect()->route('admin.pending');
    if ($user->is_property_owner) return redirect()->route('dashboard');
    if ($user->is_client) return redirect()->route('dashboard');
    
    return redirect()->intended(route('dashboard'));
}

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}