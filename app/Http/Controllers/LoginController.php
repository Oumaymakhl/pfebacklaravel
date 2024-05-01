<?php

namespace App\Http\Controllers;

use App\Models\Sadmin;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{/**
 * Handle an authentication attempt.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 */public function authenticate(Request $request)
{
    $request->validate([
        'login' => 'required',
        'password' => 'required',
    ]);

    $user = null;
    $type = null;

    // Check if user is a Participant
    if (!$user) {
        // Try to find the user with the complete login
        $user = User::where('login', $request->login)->first();

        // If not found, try with just the username (without the domain)
        if (!$user) {
            $username = explode('@', $request->login)[0]; // Get the username part
            \Log::info("Username without domain: ".$username); // Log the username without domain
            $user = User::where('login', $username)->first();
            \Log::info("User found with username: ".json_encode($user)); // Log the user found with username
        }

        $type = $user ? 'user' : $type;
    }

 // Check if user is an Admin
if (!$user) {
    $user = Admin::where('login', $request->login)->first();
    $type = $user ? 'admin' : $type;

    // If not found, try with just the username (without the domain)
    if (!$user) {
        $username = explode('@', $request->login)[0]; // Get the username part
        $user = Admin::where('login', $username)->first();
    }
}

// Check if user is a superadmin
if (!$user) {
    $user = Sadmin::where('login', $request->login)->first();
    $type = $user ? 'superadmin' : $type;

    // If not found, try with just the username (without the domain)
    if (!$user) {
        $username = explode('@', $request->login)[0]; // Get the username part
        $user = Sadmin::where('login', $username)->first();
    }
}

    if ($user && Hash::check($request->password, $user->password)) {
        // The passwords match...
        return response()->json([
            'success' => true,
            'user' => $user,
            'type' => $type
        ]);
    }

    // Authentication failed
    return response()->json([
        'success' => false,
        'message' => 'Invalid credentials'
    ], 401);
}

}