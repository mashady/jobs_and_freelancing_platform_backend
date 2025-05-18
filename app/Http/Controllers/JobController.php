<?php

namespace App\Http\Controllers;
// import employer profile model
use App\Models\EmployerProfile;
use App\Models\Job;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $jobs = Job::with('skills')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'data' => $jobs,
                'message' => 'Jobs retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving jobs: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve jobs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $job = Job::with('skills')->findOrFail($id);

            return response()->json([
                'data' => $job,
                'message' => 'Job retrieved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Job not found',
                'error' => 'Job with the given ID does not exist.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving job: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve job',
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function store(Request $request): JsonResponse
{
    DB::beginTransaction();

    try {
        $validatedData = $request->validate([
            /* 'title' => ['required', 'string', 'max:255'], */
            'position_name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'offered_salary' => ['required', 'numeric', 'min:0'],
            'job_description' => ['required', 'string'],
            'job_responsibility' => ['required', 'string'],
            'experience_years' => ['required', 'integer', 'min:0'],
            'type' => ['required', 'string', Rule::in(['fulltime', 'parttime', 'contract'])],
            'category_id' => ['required', 'exists:categories,id'],
            'skills' => ['required', 'array'],
            'skills.*' => ['string', 'max:255']
        ]);

        $employerProfile = EmployerProfile::where('user_id', auth()->id())->first();

        if (!$employerProfile) {
            throw new \Exception('Employer profile not found. Please complete your employer profile first.');
        }

        $validatedData['employer_id'] = $employerProfile->id;
        $validatedData['status'] = 'open';

        $job = Job::create($validatedData);

        $this->syncSkills($job, $validatedData['skills']);

        DB::commit();

        return response()->json([
            'data' => $job->load(['skills', 'category']),
            'message' => 'Job created successfully'
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Job creation failed: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to create job',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function update(Request $request, $id): JsonResponse
{
    DB::beginTransaction();

    try {
        $job = Job::findOrFail($id);

        // Authorization check
        /* if ($job->employer_id !== auth()->user()->employerProfile->id) {
            return response()->json(['message' => 'Unauthorized to update this job'], 403);
        } */

        $validatedData = $request->validate([
            /* 'title' => ['sometimes', 'string', 'max:255'], */
            'position_name' => ['sometimes', 'string', 'max:255'],
            'location' => ['sometimes', 'string', 'max:255'],
            'offered_salary' => ['sometimes', 'numeric', 'min:0'],
            'job_description' => ['sometimes', 'string'],
            'experience_years' => ['sometimes', 'integer', 'min:0'],
            'job_responsibility' => ['sometimes', 'string'],
            'type' => ['sometimes', 'string', Rule::in(['fulltime', 'parttime', 'contract'])],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'status' => ['sometimes', Rule::in(['open', 'in_progress', 'completed'])],
            'skills' => ['sometimes', 'array'],
            'skills.*' => ['string', 'max:255']
        ]);

        $job->update($validatedData);

        if (isset($validatedData['skills'])) {
            $this->syncSkills($job, $validatedData['skills']);
        }

        DB::commit();

        return response()->json([
            'data' => $job->load(['skills', 'category']),
            'message' => 'Job updated successfully'
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Job not found',
            'error' => 'Job with the given ID does not exist.'
        ], 404);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating job: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to update job',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function destroy($id): JsonResponse
    {
        try {
            $job = Job::findOrFail($id);

            DB::beginTransaction();
            $job->skills()->detach();
            $job->delete();
            DB::commit();

            return response()->json([
                'message' => 'Job deleted successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Job not found',
                'error' => 'Job with the given ID does not exist.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting job: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete job',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function syncSkills(Job $job, array $skills): void
    {
        $skillIds = [];

        foreach ($skills as $skillName) {
            $skillName = trim($skillName);
            if (empty($skillName)) continue;

            $skill = Skill::firstOrCreate(['name' => $skillName]);
            $skillIds[] = $skill->id;
        }

        $job->skills()->sync($skillIds);
    }
}
