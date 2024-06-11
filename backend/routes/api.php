<?php

use App\Http\Controllers\Auth\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Login And Register Routes
Route::post("/login", [AuthController::class, "login"])->name("login");
Route::post("/register", [AuthController::class, "register"])->name("register");
Route::post("/verify-otp", [AuthController::class, "verifyOtp"])->name("verify");
Route::post("/check-email", [AuthController::class, "checkEmail"])->name("check-email");


// Panel Routes
Route::middleware(["auth:sanctum"])->group(function () {
    Route::get("/dashboard", function () {
        return auth()->user();
    })->name("dashboard");
    Route::get("/users", function () {
        $users = User::whereNot("id", auth()->user()->id)->get();
        return response()->json($users, 200);
    });
});
