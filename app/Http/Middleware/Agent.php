<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Agent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the agent is logged in
        if (!Auth::guard('agent')->check()) {
            return redirect()->route('agent.login')
                ->withErrors(['Please login as agent first.']);
        }

        $agent = Auth::guard('agent')->user();

        // Check if agent is approved and active
        if (!$agent->is_approved || $agent->status !== 'active') {
            Auth::guard('agent')->logout(); // logout if not approved or inactive
            return redirect()->route('agent.login')
                ->withErrors(['Your account is pending approval or has been deactivated. Please contact admin support.']);
        }

        // All good → allow request
        return $next($request);
    }
}
