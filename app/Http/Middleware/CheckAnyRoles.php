<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckAnyRoles
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // \Log::warning('Unauthorized access attempt', ['user_id' => $user->id]);
        abort(403, 'Unauthorized');
    }
}
