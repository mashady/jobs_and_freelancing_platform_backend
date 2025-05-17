<?php

use App\Http\Controllers\JobController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SkillController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\FreelancerProfileController;
use App\Http\Controllers\EmployerProfileController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);

// protected
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('freelancer-profiles', FreelancerProfileController::class);
    Route::apiResource('employer-profiles', EmployerProfileController::class);
    Route::post("/logout", [AuthController::class, "logout"]);
});

Route::apiResource('categories', CategoryController::class);
Route::apiResource('skills', SkillController::class);
Route::apiResource('jobs', JobController::class);
Route::apiResource('projects', ProjectController::class);


/* Route::get('/email/verify', function () {
    return view('auth.verify-email'); // Or whatever view you have
})->middleware(['auth'])->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/home'); //  Where to redirect after successful verification
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])
    ->middleware(['auth', 'throttle:6,1'])->name('verification.send');
 */
