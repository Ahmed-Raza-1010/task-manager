<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Step 1: Send OTP to Email
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:users']);

        $otp = rand(100000, 999999); // Generate a 6-digit OTP
        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(5)); // Store OTP for 5 minutes

        // Send OTP via Email
        Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Your OTP Code');
        });

        return response()->json(['message' => 'OTP sent to email'], 200);
    }

    // Step 2: Register User After OTP Verification
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'otp' => 'required|digits:6',
        ]);

        // Check OTP
        $storedOtp = Cache::get('otp_' . $request->email);
        if (!$storedOtp || $storedOtp != $request->otp) {
            return response()->json(['error' => 'Invalid or expired OTP'], 422);
        }

        // Register user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Cache::forget('otp_' . $request->email); // Remove OTP after successful registration

        return response()->json(['message' => 'User registered successfully'], 201);
    }



    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
