<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/signup", [AuthController::class, "signup"])->name("signup");
Route::post("/login", [AuthController::class, "login"])->name("login");

Route::middleware("auth:sanctum")->group(function () {
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::get("/user", function (Request $request) {
        return $request->user();
    });
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::controller(AuthController::class)->group(function () {
//     Route::post("signup", "signup");
//     // Route::post("login", [AuthController::class, "login"]);
// });
