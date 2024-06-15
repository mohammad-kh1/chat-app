<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Notifications\SendOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {

        $data = $request->validated();

        $user = Auth::attempt($data);
        if ($user) {
            $user = User::where("email", $data["email"])->first();
            if ($user->two_step_verification !== null) {
                $user->notify(new SendOtpNotification());
                return response()->json(["message" => "Please verify your email first"], 400);
            } else {
                $user->is_online = true;
                return response($user->createToken($user->name)->plainTextToken, 200);
            }
        } else {
            return response()->json(["message" => "Invalid Credentials"], 400);
        }
    }
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);


        $user->notify(new SendOtpNotification());

        return response()->json(["message" => "Verification Code has been sent to your email. Please check your email."], 200);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            "otp" => "required|string|min:6|max:6|",
            "email" => "required|string|email|exists:users,email",
        ]);
        $user = User::where("two_step_verification", $request->otp)->first();
        if ($user) {
            $user->update(["two_step_verification" => null]);
            $user->is_online = true;
            return response($user->createToken($request->otp)->plainTextToken, 200);
        } else {
            return response()->json(["message" => "Invalid"], 400);
        }
    }
    public function checkEmail(Request $request)
    {

        $email = User::where("email", $request->email)->first();
        if ($email) {
            return response()->json(["message" => "Email already exists"], 400);
        } else {
            return response()->json(["message" => "Email is available"], 200);
        }
    }
}
