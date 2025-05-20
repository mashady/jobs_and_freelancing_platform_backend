<?php

use App\Http\Controllers\JobController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\FreelancerProfileController;
use App\Http\Controllers\EmployerProfileController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobCommentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'show']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('freelancer-profiles', [FreelancerProfileController::class, 'index']);
    Route::get('freelancer-profiles/{freelancer_profile}', [FreelancerProfileController::class, 'show']);
    Route::get('employer-profiles', [EmployerProfileController::class, 'index']);
    Route::get('employer-profiles/{employer_profile}', [EmployerProfileController::class, 'show']);

    Route::apiResource('freelancer-profiles', FreelancerProfileController::class);
    Route::apiResource('employer-profiles', EmployerProfileController::class);
    Route::apiResource('projects', ProjectController::class);

    Route::get('/jobs/inactive', [JobController::class, 'inactiveJobs']);
    Route::get('/myJobs', [JobController::class, 'getEmployerJobs']);
    Route::get('/employer/jobs/{jobId}/applications', [JobController::class, 'getJobApplications']);
    Route::patch('/jobs/{id}/activate', [JobController::class, 'activateJob']);

     Route::apiResource('jobs', JobController::class);  // protected routes (store, update, destroy)
    Route::patch('/application-status/{jobApplication}', [JobApplicationController::class, 'updateStatus']);
    Route::apiResource('job-applications', JobApplicationController::class);


    // Custom job routes (must come BEFORE /jobs/{id})
    //Route::get('/jobs/inactive', [JobController::class, 'inactiveJobs']);
   // Route::patch('/jobs/{id}/activate', [JobController::class, 'activateJob'])->where('id', '[0-9]+');

    // Job comments
    //Route::get('/jobs/{job}/comments', [JobCommentController::class, 'index'])->where('job', '[0-9]+');
    Route::post('/comments', [JobCommentController::class, 'store']);
    Route::delete('/comments/{id}', [JobCommentController::class, 'destroy'])->where('id', '[0-9]+');

    Route::post("/logout", [AuthController::class, "logout"]);
});

Route::apiResource('categories', CategoryController::class);
Route::apiResource('skills', SkillController::class);


Route::get('/employer-profiles', [EmployerProfileController::class, 'index']);
Route::get('/employer-profiles/{employer_profile}', [EmployerProfileController::class, 'show']);
Route::get('/freelancer-profiles', [FreelancerProfileController::class, 'index']);
Route::get('/freelancer-profiles/{freelancer_profile}', [FreelancerProfileController::class, 'show']);

/* Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'show']); */
/* Route::get('/jobs', [JobController::class, 'index']); */
