<?php

namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        // return "ok";
        return view('agent.auth.login');
    }
    
   public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('agent')->attempt($credentials)) {
            $agent = Auth::guard('agent')->user();

            // Check if agent is approved and active
            if (!$agent->is_approved || $agent->status !== 'active') {
                Auth::guard('agent')->logout(); // logout if not approved or inactive

                return response()->json([
                    'status' => false,
                    'message' => 'Your account is pending approval or has been deactivated. Please contact admin support.'
                ], 403); // 403 Forbidden
            }

            // Login successful
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'agent' => $agent
            ], 200);
        }

        // Invalid credentials
        return response()->json([
            'status' => false,
            'message' => 'Invalid email or password'
        ], 401);
    }
   
}
