<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\Job;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobApplicationController extends Controller
{
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
        if ($request->hasFile('resume_path')) {
            $profileImage = $request->file('resume_path')->store('resumes', 'public');
        }

        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:jobs,id',
            'cover_letter' => 'required|string|max:1000',
            'resume_path' => 'required|file|mimes:pdf,doc,docx|max:2048'
        ]);

        if ($request->hasFile('resume_path')) {
            $request->merge(['resume_path' => $profileImage]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $jobApplication = JobApplication::create([
            'job_id' => $request->job_id,
            'user_id' => Auth::id(),
            'resume_path' => $request->resume_path,
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
