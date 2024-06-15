<?php

use App\Events\OnlineUsers;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChatController;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

// Login And Register Routes
Route::post("/login", [AuthController::class, "login"])->name("login");
Route::post("/register", [AuthController::class, "register"])->name("register");
Route::post("/verify-otp", [AuthController::class, "verifyOtp"])->name("verify");
Route::post("/check-email", [AuthController::class, "checkEmail"])->name("check-email");


// Panel Routes
Route::middleware(["auth:sanctum"])->group(function () {
    Route::get("/dashboard", function () {
        $user = auth()->user();
        $user->is_online = ($user->is_online == false) ? true : true;
        OnlineUsers::dispatch($user);
        return $user;
    })->name("dashboard");
    Route::get("/users", function () {
        $users = User::whereNot("id", auth()->user()->id)->get();
        return response()->json($users, 200);
    });

    Route::post("/chat", [ChatController::class, "messages"])->name("chat");
    Route::get("/online_users", [ChatController::class, "online_users"])->name("online-users");
    Route::post("/online_users", [ChatController::class, "online_disconnetd"])->name("online_disconnetd");
    Route::post("/send_message", [ChatController::class, "send_message"])->name("send_message");
});
