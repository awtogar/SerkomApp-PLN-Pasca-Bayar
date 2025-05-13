<?php

// app/Http/Middleware/RedirectBasedOnRole.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Asumsikan ada atribut 'role' di model User
            // Sesuaikan dengan struktur database Anda
            if ($user->role === 'admin') {
                return redirect()->route('filament.admin.pages.dashboard');
            } elseif ($user->role === 'agen') {
                return redirect()->route('filament.agen.pages.dashboard');
            }
        }
        
        return $next($request);
    }
}
