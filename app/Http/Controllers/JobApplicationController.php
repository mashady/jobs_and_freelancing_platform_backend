<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\Job;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
class JobApplicationController extends Controller
{

    public function createSession(Request $request)
    {
        $application = JobApplication::findOrFail($request->application_id);
        $amount = 5000000;
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Applicant Hiring Fee'],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',//http://127.0.0.1:5732/
            'success_url' => url("http://localhost:5732/update-application?application_id={$application->id}&status=accepted"),
            'cancel_url' => url('http://localhost:5732/employer/applications'),
        ]);

        return response()->json(['sessionId' => $session->id]);
    }
    public function getEmployerApplications()
{
    $user = Auth::user();

    // Applications submitted by this user (freelancer view)
    $userApplications = JobApplication::with(['job'])
        ->where('user_id', $user->id)
        ->latest()
        ->get();

    // Applications for this employer's jobs (employer view)
    $employerApplications = [];

    if ($user->role === 'employer') {
        $employerJobs = Job::whereHas('employer', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->pluck('id');

        $employerApplications = JobApplication::with(['job', 'user'])
            ->whereIn('job_id', $employerJobs)
            ->latest()
            ->get();
    }

    return response()->json([
        'success' => true,
        'freelancer_applications' => $userApplications,
        'employer_applications' => $employerApplications
    ]);
}

    public function index()
    {
        $applications = JobApplication::with(['job'])
    ->where('user_id', Auth::id())
    ->latest()
    ->paginate(10);


        return response()->json([
            'success' => true,
            'data' => $applications
        ]);
    }

public function store(Request $request)
{
    // Validate input
    $validator = Validator::make($request->all(), [
        'job_id' => 'required|exists:jobs,id',
        'cover_letter' => 'required|string|max:1000',
        'resume_path' => 'required|file|mimes:pdf,doc,docx|max:2048'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Store resume file and get the storage path
    if ($request->hasFile('resume_path')) {
        $file = $request->file('resume_path');
        $storedPath = $file->store('resumes', 'public'); // stored in storage/app/public/resumes
    } else {
        $storedPath = null;
    }

    // Create job application
    $jobApplication = JobApplication::create([
        'job_id' => $request->job_id,
        'user_id' => Auth::id(),
        'resume_path' => $storedPath, // save actual stored path
        'cover_letter' => $request->cover_letter,
        'status' => 'pending'
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Application submitted successfully!',
        'data' => $jobApplication
    ], 201);
}

    public function show(JobApplication $jobApplication)
    {


        return response()->json([
            'success' => true,
            'data' => $jobApplication->load('job', 'user')
        ]);
    }

    public function updateStatus(Request $request, JobApplication $jobApplication)
    {
         if ($request->status === 'accepted' && $oldStatus !== 'accepted') {
        event(new \App\Events\ApplicationApproved($jobApplication));
    }

        $request->validate([
            'status' => 'required|in:pending,accepted,rejected'

        ]);

        $jobApplication->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Application status updated.',
            'data' => $jobApplication
        ]);
    }

    public function destroy(JobApplication $jobApplication)
    {


        $jobApplication->delete();

        return response()->json([
            'success' => true,
            'message' => 'Application deleted.'
        ]);
    }
}
