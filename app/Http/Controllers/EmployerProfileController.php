<?php

namespace App\Http\Controllers;

use App\Models\EmployerProfile;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmployerProfileController extends Controller
{

    public function index(): JsonResponse
    {
        try {
            $profiles = EmployerProfile::with(['user', 'category'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'data' => $profiles,
                'message' => 'Employer profiles retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving employer profiles: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve employer profiles',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id): JsonResponse
    {
        try {
            $profile = EmployerProfile::with(['user', 'category'])
                ->findOrFail($id);

            return response()->json([
                'data' => $profile,
                'message' => 'Employer profile retrieved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Employer profile not found',
                'error' => 'Profile with the given ID does not exist.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving employer profile: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve employer profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'company_name' => ['required', 'string', 'max:255'],
                'company_description' => ['nullable', 'string', 'max:1000'],
                'about' => ['nullable', 'string', 'max:2000'],
                'category_id' => ['required', 'exists:categories,id'],
                'employees_count' => ['nullable', 'integer', 'min:0'],
                'founded_date' => ['nullable', 'date', 'before_or_equal:today'],
                'location' => ['required', 'string', 'max:255'],
            ]);

            $user = $request->user();
            if ($user->employerProfile) {
                return response()->json([
                    'message' => 'User already has an employer profile'
                ], 400);
            }

            $employerProfile = EmployerProfile::create([
                'user_id' => auth()->id(),
                'company_name' => $validatedData['company_name'],
                'company_description' => $validatedData['company_description'] ?? null,
                'about' => $validatedData['about'] ?? null,
                'category_id' => $validatedData['category_id'],
                'employees_count' => $validatedData['employees_count'] ?? null,
                'founded_date' => $validatedData['founded_date'] ?? null,
                'location' => $validatedData['location'],
            ]);

            return response()->json([
                'message' => 'Employer profile created successfully',
                'data' => $employerProfile->load(['user', 'category'])
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Employer profile creation failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create employer profile',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }


    public function update(Request $request, $id): JsonResponse
    {
        try {
            $profile = EmployerProfile::findOrFail($id);

            if ($profile->user_id !== auth()->id()) {
                return response()->json([
                    'message' => 'Unauthorized to update this profile'
                ], 403);
            }

            $validatedData = $request->validate([
                'company_name' => ['sometimes', 'string', 'max:255'],
                'company_description' => ['nullable', 'string', 'max:1000'],
                'about' => ['nullable', 'string', 'max:2000'],
                'category_id' => ['sometimes', 'exists:categories,id'],
                'employees_count' => ['nullable', 'integer', 'min:0'],
                'founded_date' => ['nullable', 'date', 'before_or_equal:today'],
                'location' => ['sometimes', 'string', 'max:255'],
            ]);

            $profile->update($validatedData);

            return response()->json([
                'message' => 'Employer profile updated successfully',
                'data' => $profile->fresh(['user', 'category'])
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Employer profile update failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update employer profile',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }


    public function destroy($id): JsonResponse
    {
        try {
            $profile = EmployerProfile::findOrFail($id);

            if ($profile->user_id !== auth()->id()) {
                return response()->json([
                    'message' => 'Unauthorized to delete this profile'
                ], 403);
            }

            $profile->delete();

            return response()->json([
                'message' => 'Employer profile deleted successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Employer profile not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Employer profile deletion failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete employer profile',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }
}
