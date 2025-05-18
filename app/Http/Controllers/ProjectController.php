<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Skill;
use Illuminate\Http\Request;
use App\Models\EmployerProfile;
use App\Models\ProjectAttachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index(): JsonResponse
    {
        try {
            $projects = Project::with(['employer', 'category', 'skills', 'attachments'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'data' => $projects,
                'message' => 'Projects retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving projects: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve projects',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'category_id' => ['required', 'exists:categories,id'],
                'budget_min' => ['required', 'numeric', 'min:0'],
                'budget_max' => ['required', 'numeric', 'gte:budget_min'],
                'duration' => ['required', 'integer', 'min:1'],
                'english_level' => ['required', 'in:beginner,intermediate,advanced,fluent,native'],
                'project_language' => ['required', 'string', 'max:50'],
                'general_level' => ['required', 'in:entry,intermediate,expert'],
                'deadline' => ['required', 'date', 'after:today'],
                'project_type' => ['required', 'in:fixed,hourly'],
                'skills' => ['required', 'array'],
                'skills.*' => ['string', 'max:255'],
                'attachments' => ['sometimes', 'array'],
                'attachments.*' => ['file', 'max:2048']
            ]);

            $employerProfile = EmployerProfile::where('user_id', auth()->id())->firstOrFail();

            $validatedData['employer_id'] = $employerProfile->id;
            $validatedData['status'] = 'open';

            $project = Project::create($validatedData);

            $this->syncSkills($project, $validatedData['skills']);

            if ($request->hasFile('attachments')) {
                $this->storeAttachments($project, $request->file('attachments'));
            }

            DB::commit();

            return response()->json([
                'data' => $project->load(['skills', 'category', 'attachments']),
                'message' => 'Project created successfully'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Project creation failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified project
     */
    public function show(string $id): JsonResponse
    {
        try {
            $project = Project::with(['employer', 'category', 'skills', 'attachments'])
                ->findOrFail($id);

            return response()->json([
                'data' => $project,
                'message' => 'Project retrieved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Project not found',
                'error' => 'Project with the given ID does not exist.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving project: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified project
     */
    public function update(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $project = Project::findOrFail($id);

            $validatedData = $request->validate([
                'title' => ['sometimes', 'string', 'max:255'],
                'description' => ['sometimes', 'string'],
                'category_id' => ['sometimes', 'exists:categories,id'],
                'budget_min' => ['sometimes', 'numeric', 'min:0'],
                'budget_max' => ['sometimes', 'numeric', 'gte:budget_min'],
                'duration' => ['sometimes', 'integer', 'min:1'],
                'english_level' => ['sometimes', 'in:beginner,intermediate,advanced,fluent,native'],
                'project_language' => ['sometimes', 'string', 'max:50'],
                'general_level' => ['sometimes', 'in:entry,intermediate,expert'],
                'deadline' => ['sometimes', 'date', 'after:today'],
                'status' => ['sometimes', 'in:open,in-progress,completed,cancelled'],
                'skills' => ['sometimes', 'array'],
                'skills.*' => ['string', 'max:255'],
                'attachments' => ['sometimes', 'array'],
                'attachments.*' => ['file', 'max:2048']
            ]);

            $project->update($validatedData);

            if (isset($validatedData['skills'])) {
                $this->syncSkills($project, $validatedData['skills']);
            }

            if ($request->hasFile('attachments')) {
                $this->storeAttachments($project, $request->file('attachments'));
            }

            DB::commit();

            return response()->json([
                'data' => $project->load(['skills', 'category', 'attachments']),
                'message' => 'Project updated successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Project not found',
                'error' => 'Project with the given ID does not exist.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating project: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified project
     */
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $project = Project::findOrFail($id);

            foreach ($project->attachments as $attachment) {
                Storage::delete($attachment->file_path);
                $attachment->delete();
            }

            $project->skills()->detach();

            $project->delete();

            DB::commit();

            return response()->json([
                'message' => 'Project deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Project not found',
                'error' => 'Project with the given ID does not exist.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting project: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete project',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync project skills
     */
    protected function syncSkills(Project $project, array $skills): void
    {
        $skillIds = [];

        foreach ($skills as $skillName) {
            $skillName = trim($skillName);
            if (empty($skillName)) continue;

            $skill = Skill::firstOrCreate(['name' => $skillName]);
            $skillIds[] = $skill->id;
        }

        $project->skills()->sync($skillIds);
    }

    /**
     * Store project attachments
     */
    protected function storeAttachments(Project $project, array $attachments): void
    {
        foreach ($attachments as $file) {
            $path = $file->store('project_attachments');

            ProjectAttachment::create([
                'project_id' => $project->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType()
            ]);
        }
    }
}
