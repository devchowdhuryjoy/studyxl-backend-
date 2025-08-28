<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Agent;
use App\Mail\Websitemail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function dashboard()
    {
        $agents = Agent::all();
        return view('admin.dashboard', compact('agents'));
    }
    public function login()
    {
        return view('admin.login');
    }
    public function login_submit(Request $request)
    {
        // return $request;
        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // Attempt login using the 'admin' guard
        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();

            // Optional: Generate token (requires Laravel Sanctum or Passport)
            // $token = $admin->createToken('AdminAPIToken')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login successful.',
                'admin' => $admin,
                // 'token' => $token // Uncomment if using token auth
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid email or password.'
        ], 401);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('success', 'Logout Success');
    }
    public function forget_password()
    {
        return view('admin.forget-password');
    }
    public function forget_password_submit(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email',
        ]);

        // Find admin by email
        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Email not found.'
            ], 404);
        }

        // Generate reset token and store it
        $token = hash('sha256', time());
        $admin->token = $token;
        $admin->save();

        // Create reset link
        $reset_link = url('admin/reset_password/' . $token . '/' . $request->email);

        // Email content
        $subject = "Reset Password";
        $message = '<a href="' . $reset_link . '">Click here to reset your password</a>';

        // Send email
        Mail::to($request->email)->send(new Websitemail($subject, $message));

        // Return JSON response
        return response()->json([
            'status' => true,
            'message' => 'Reset password link has been sent to your email.',

        ], 200);
    }


    public function reset_password($token, $email)
    {
        $admin = Admin::where('email', $email)->where('token', $token)->first();

        if (!$admin) {
            return redirect()->route('login')->with('error', 'Invalid or expired reset link');
        }

        // Show reset password form
        return view('admin.reset_password', compact('email', 'token'));
    }


    public function reset_password_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
            'email' => 'required|email',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $admin_data = Admin::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$admin_data) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token or email'
            ], 400);
        }

        $admin_data->password = Hash::make($request->password);
        $admin_data->token = null; // clear token
        $admin_data->save();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ], 200);
    }



   public function approveAgent($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->is_approved = true;
        $agent->save();

        return response()->json([
            'success' => true,
            'message' => 'Agent approved successfully!',
            'agent'   => $agent
        ], 200);
    }

   public function deactivateAgent($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->status = 'inactive';
        $agent->save();

        return response()->json([
            'success' => true,
            'message' => 'Agent deactivated successfully!',
            'agent'   => $agent
        ], 200);
    }

   public function activateAgent($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->status = 'active';
        $agent->save();

        return response()->json([
            'success' => true,
            'message' => 'Agent activated successfully!',
            'agent'   => $agent
        ], 200);
    }

}
