<?php

namespace App\Http\Controllers\Agent;

use App\Models\Agent;
use App\Mail\Websitemail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password; // ✅ Correct namespace

class AgentController extends Controller
{
    public function create()
    {
        return view('agent.auth.register');
    }
    
    public function store(Request $request): JsonResponse
    {
        // Check if email already exists
        if (Agent::where('email', $request->email)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'This email address is already registered.'
            ], 409); // 409 Conflict
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            // Check for password confirmation specifically
            if ($validator->errors()->has('password')) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first('password') // e.g. "The password confirmation does not match."
                ], 422);
            }

            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $errors
            ], 422);
        }

        // Create agent
        $agent = Agent::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($agent));
        Auth::login($agent);

        return response()->json([
            'status' => true,
            'message' => 'Agent registered successfully.',
            'agent' => $agent
        ], 201);
    }
   public function dashboard()
    {
        return view('agent.auth.dashboard');
    }
     public function logout()
    {
        // return "ok";
         Auth::guard('agent')->logout();
         return redirect()->route('agent.login')->with('success','Logout Success');
    }
    public function forget_password()
    {
        return view('agent.auth.forget-password');
    }
     public function forget_password_submit(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if the agent exists
        $agent = Agent::where('email', $request->email)->first();

        if (!$agent) {
            return response()->json([
                'status' => false,
                'message' => 'Email not found.'
            ], 404);
        }

        // Generate a secure token
        $token = hash('sha256', time());

        // Save the token to the agent record (make sure 'token' column exists)
        $agent->token = $token;
        $agent->save();

        // Build the reset link
        $reset_link = url('agent/reset_password/' . $token . '/' . $request->email);

        // Email content
        $subject = "Reset Password";
        $message = '<a href="' . $reset_link . '">Click here to reset your password</a>';

        // Send email
        Mail::to($request->email)->send(new Websitemail($subject, $message));

        // Return JSON response
        return response()->json([
            'status' => true,
            'message' => 'Please check your email for the password reset link.',
        ], 200);
    }

        public function reset_password($token, $email)
        {
            $agent = Agent::where('email', $email)->where('token', $token)->first();

            if (!$agent) {
                return redirect()->route('login')->with('error', 'Invalid or expired reset link');
            }

            // Show reset password form
            return view('agent.auth.reset-password', compact('email', 'token'));
        }
        public function reset_password_submit(Request $request)
        {
            // Validate input
            $request->validate([
                'email' => 'required|email',
                'token' => 'required',
                'password' => 'required|confirmed|min:6',
            ]);

            // Find agent by email + token
            $agent = Agent::where('email', $request->email)
                        ->where('token', $request->token)
                        ->first();

            if (!$agent) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid token or email.'
                ], 400); // 400 Bad Request
            }

            // Update password
            $agent->password = Hash::make($request->password);
            $agent->token = null; // clear reset token
            $agent->save();

            return response()->json([
                'status' => true,
                'message' => 'Password reset successfully.'
            ], 200);
        }


}
