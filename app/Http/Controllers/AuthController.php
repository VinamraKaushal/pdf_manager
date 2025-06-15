<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PendingRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use App\Models\OtpAuthLog;
class AuthController extends Controller
{
    // Handle Login Attempt - Step 1
    public function initiateAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid email or password'], 401);
        }

        // Check if user is locked
        if ($user->locked_until && now()->lt($user->locked_until)) {
            return response()->json(['error' => 'Too many failed attempts. Try again later.'], 403);
        }

        // 👉 Check if same IP has authenticated within last 6 hours
        $ip = $request->ip(); // Or FacadeRequest::ip()
        $recent = OtpAuthLog::where('user_id', $user->id)
            ->where('ip_address', $ip)
            ->where('authenticated_at', '>=', now()->subHours(6))
            ->latest()
            ->first();

        if ($recent) {
            Auth::login($user);
            return response()->json(['otp_required' => false, 'message' => 'Login successful (OTP skipped)']);
        }

        // Prevent resend OTP within 2 minutes
        if (Session::has('last_otp_sent') && now()->diffInSeconds(Session::get('last_otp_sent')) < 120) {
            return response()->json(['error' => 'Please wait before requesting another OTP.'], 429);
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        Session::put('auth_user_id', $user->id);
        Session::put('auth_otp', $otp);
        Session::put('last_otp_sent', now());

        // Send OTP
        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json(['otp_required' => true, 'message' => 'OTP sent']);
    }

    // Verify Login OTP - Step 2
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $userId = Session::get('auth_user_id');
        $otp = Session::get('auth_otp');

        if (!$userId || !$otp) {
            return response()->json(['error' => 'Session expired or invalid'], 403);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($request->otp != $otp) {
            $user->otp_attempts += 1;

            if ($user->otp_attempts >= 3) {
                $user->locked_until = now()->addMinutes(10);
                $user->save();
                return response()->json(['error' => 'Too many failed attempts. Try again later.'], 403);
            }

            $user->save();
            return response()->json(['error' => 'Incorrect OTP'], 401);
        }

        // Reset attempts
        $user->otp_attempts = 0;
        $user->locked_until = null;
        $user->save();

        Auth::login($user);

        // Log this OTP-based login
        OtpAuthLog::create([
            'user_id'         => $user->id,
            'email'           => $user->email,
            'ip_address'      => $request->ip(),
            'authenticated_at'=> now(),
        ]);

        Session::forget(['auth_user_id', 'auth_otp', 'last_otp_sent']);

        return response()->json(['message' => 'Login successful']);
    }

    // Registration Step 1: Store in pending_registrations and send OTP
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email|unique:pending_registrations,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Rate limit OTP resend
        if (Session::has('reg_last_otp_sent') && now()->diffInSeconds(Session::get('reg_last_otp_sent')) < 120) {
            return response()->json(['error' => 'Please wait before requesting another OTP.'], 429);
        }

        $otp = rand(100000, 999999);

        PendingRegistration::updateOrCreate(
            ['email' => $request->email],
            [
                'name'       => $request->name,
                'password'   => Hash::make($request->password),
                'otp'        => $otp,
                'expires_at' => now()->addMinutes(10),
            ]
        );

        Session::put('reg_last_otp_sent', now());

        // Simulate sending OTP (add mail logic here)
        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json([
            'otp_required' => true,
            'message' => 'OTP sent to your email.'
        ]);
    }

    // Registration Step 2: Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        $pending = PendingRegistration::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$pending) {
            return response()->json(['error' => 'Invalid or expired OTP'], 422);
        }

        $user = User::create([
            'name' => $pending->name,
            'email' => $pending->email,
            'password' => $pending->password,
        ]);

        $pending->delete();

        Auth::login($user);

        return response()->json(['message' => 'Registration complete.']);
    }

    // Resend OTP for registration
    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $pending = PendingRegistration::where('email', $request->email)->first();

        if (!$pending) {
            return response()->json(['error' => 'No pending registration found'], 404);
        }

        // Limit resend to once every 2 mins
        if (Session::has('reg_last_otp_sent') && now()->diffInSeconds(Session::get('reg_last_otp_sent')) < 120) {
            return response()->json(['error' => 'Please wait before requesting another OTP.'], 429);
        }

        $otp = rand(100000, 999999);
        $pending->update(['otp' => $otp, 'expires_at' => now()->addMinutes(10)]);
        Session::put('reg_last_otp_sent', now());

        // Send OTP (placeholder)
        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json(['message' => 'OTP resent']);
    }
}
