<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAgentApproval
{
    public function handle($request, Closure $next)
    {
        $agent = Auth::guard('agent')->user();

        if ($agent && (!$agent->is_approved || !$agent->isActive())) {
            Auth::guard('agent')->logout();
            return redirect()->route('agent.login')
                ->withErrors(['Your account is pending approval or deactivated by admin.']);
        }

        return $next($request);
    }
}

