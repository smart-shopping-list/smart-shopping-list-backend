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
