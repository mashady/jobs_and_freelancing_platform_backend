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
use App\Http\Controllers\JobApplicationController;


use App\Http\Controllers\JobCommentController;
use App\Http\Controllers\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);

// protected



Route::middleware(['auth:sanctum'])->group(function () {

    Route::apiResource('freelancer-profiles', FreelancerProfileController::class);
    Route::apiResource('employer-profiles', EmployerProfileController::class);
    Route::apiResource('projects', ProjectController::class);
    /* Route::apiResource('jobs', JobController::class); */

    Route::get('/jobs/inactive', [JobController::class, 'inactiveJobs']);
    Route::get('/myJobs', [JobController::class, 'getEmployerJobs']);

    Route::get('/employer-applications', [JobApplicationController::class, 'getEmployerApplications']);



    Route::get('/employer/jobs/{jobId}/applications', [JobController::class, 'getJobApplications']);
    Route::patch('/jobs/{id}/activate', [JobController::class, 'activateJob']);

     Route::apiResource('jobs', JobController::class)->except(['index', 'show']);  // protected routes (store, update, destroy)
    Route::patch('/application-status/{jobApplication}', [JobApplicationController::class, 'updateStatus']);
    Route::apiResource('job-applications', JobApplicationController::class);


    // Custom job routes (must come BEFORE /jobs/{id})
    //Route::get('/jobs/inactive', [JobController::class, 'inactiveJobs']);
   // Route::patch('/jobs/{id}/activate', [JobController::class, 'activateJob'])->where('id', '[0-9]+');

    // Job comments
    //Route::get('/jobs/{job}/comments', [JobCommentController::class, 'index'])->where('job', '[0-9]+');
    Route::post('/comments', [JobCommentController::class, 'store']);
    Route::get('/comments/{id}', [JobCommentController::class, 'index']);
    Route::delete('/comments/{id}', [JobCommentController::class, 'destroy'])->where('id', '[0-9]+');

    Route::post("/logout", [AuthController::class, "logout"]);
    /*Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
     Route::get('/notifications/unread', function () {
        return response()->json([
            'notifications' => auth()->user()->unreadNotifications,
            'unreadCount' => auth()->user()->unreadNotifications->count()
        ]);
    });

    Route::post('/notifications/{notification}/mark-as-read', function ($notificationId) {
        $notification = auth()->user()->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    });

    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }); */
});
Route::get('/jobs/{id}', [JobController::class, 'show']);
Route::get('/jobs', [JobController::class, 'index']);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('skills', SkillController::class);


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


 Route::middleware('auth:sanctum')->group(function () {
    Route::get('/jobs/{job}/comments', [JobCommentController::class, 'index']);
    Route::post('/comments', [JobCommentController::class, 'store']);
    Route::delete('/comments/{id}', [JobCommentController::class, 'destroy']);
});
Route::apiResource('payments', PaymentController::class);


Route::get('/employer-profiles', [EmployerProfileController::class, 'index']);
Route::get('/employer-profiles/{employer_profile}', [EmployerProfileController::class, 'show']);
Route::get('/freelancer-profiles', [FreelancerProfileController::class, 'index']);
Route::get('/freelancer-profiles/{freelancer_profile}', [FreelancerProfileController::class, 'show']);

/* Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'show']); */
/* Route::get('/jobs', [JobController::class, 'index']); */


Route::post('/create-checkout-session', [JobApplicationController::class, 'createSession']);
