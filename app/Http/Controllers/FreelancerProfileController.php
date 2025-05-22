<?php

namespace App\Http\Controllers;

use App\Models\FreelancerProfile;
use App\Models\WorkExperience;
use App\Models\Education;
use App\Models\Skill;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FreelancerProfileController extends Controller
{
    /**
     * Store a newly created freelancer profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function index(): JsonResponse
    {
        try {
            $profiles = FreelancerProfile::with(['user', 'workExperiences', 'educations', 'category', 'skills' ])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'data' => $profiles,
                'message' => 'Freelancer profiles retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving freelancer profiles: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve freelancer profiles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $profile = FreelancerProfile::with(['user', 'workExperiences', 'educations', 'category', 'skills'])
                ->where('user_id', $id)
                ->firstOrFail();

            return response()->json([
                /* 'userid'=>auth()->id(), */
                'data' => $profile,
                'message' => 'Freelancer profile retrieved successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Freelancer profile not found',
                'error' => 'Profile with the given ID does not exist.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving freelancer profile: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve freelancer profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function update(Request $request, $id): JsonResponse
{
    try {
        $user = User::findOrFail($id);

      /*   if ($user->id !== auth()->id()) {
            return response()->json([
                'message' => 'You are not authorized to update this account.'
            ], 403);
        }
 */
        $userValidatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'url', 'max:2048'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        DB::beginTransaction();

        if (isset($userValidatedData['password'])) {
            $userValidatedData['password'] = bcrypt($userValidatedData['password']);
        }

        $user->update($userValidatedData);

        $profile = FreelancerProfile::where('user_id', $user->id)->firstOrFail();

        $freelancerValidatedData = $request->validate([
            'city' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'gender' => ['nullable', 'in:male,female,other'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'job_title' => ['required', 'string', 'max:255'],
            'min_hourly_rate' => ['required', 'numeric', 'min:0'],
            'max_hourly_rate' => ['required', 'numeric', 'gte:min_hourly_rate'],
            'category_id' => ['required', 'exists:categories,id'],
            'english_level' => ['required', 'in:beginner,intermediate,advanced,fluent,native'],
            'payment_method' => ['required', 'in:paypal,bank_transfer,crypto,other,stripe'],
            'resume' => ['nullable', 'url', 'max:2048'],
            'work_experiences' => ['nullable', 'array'],
            'skills' => ['nullable', 'array'],
        ]);

        $profile->update([
            'city' => $freelancerValidatedData['city'],
            'address' => $freelancerValidatedData['address'] ?? null,
            'bio' => $freelancerValidatedData['bio'] ?? null,
            'gender' => $freelancerValidatedData['gender'] ?? null,
            'birth_date' => $freelancerValidatedData['birth_date'] ?? null,
            'job_title' => $freelancerValidatedData['job_title'],
            'min_hourly_rate' => $freelancerValidatedData['min_hourly_rate'],
            'max_hourly_rate' => $freelancerValidatedData['max_hourly_rate'],
            'category_id' => $freelancerValidatedData['category_id'],
            'english_level' => $freelancerValidatedData['english_level'],
            'payment_method' => $freelancerValidatedData['payment_method'],
            'resume' => $freelancerValidatedData['resume'] ?? null,
        ]);

        if (isset($freelancerValidatedData['work_experiences'])) {
            foreach ($freelancerValidatedData['work_experiences'] as $workExperienceData) {
                if (isset($workExperienceData['id'])) {
                    $workExperience = WorkExperience::find($workExperienceData['id']);
                    if ($workExperience) {
                        $workExperience->update($workExperienceData);
                    }
                } else {
                    WorkExperience::create([
                        'freelancer_id' => $profile->id,
                        'company_name' => $workExperienceData['company_name'],
                        'position' => $workExperienceData['position'],
                        'description' => $workExperienceData['description'] ?? null,
                        'start_date' => $workExperienceData['start_date'],
                        'end_date' => $workExperienceData['end_date'] ?? null,
                    ]);
                }
            }
        }

        if (isset($freelancerValidatedData['skills'])) {
            $profile->skills()->sync([]); // Remove old skills
            foreach ($freelancerValidatedData['skills'] as $skillName) {
                $skill = Skill::firstOrCreate(['name' => $skillName]);
                $profile->skills()->attach($skill->id);
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'User and freelancer profile updated successfully',
            'user' => $user,
            'freelancer_profile' => $profile,
        ], 200);

    } catch (ValidationException $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating user and freelancer profile: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to update user and freelancer profile',
            'error' => $e->getMessage()
        ], 500);
    }
}



    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'city' => ['required', 'string', 'max:255'],
                'address' => ['nullable', 'string', 'max:500'],
                'email' => ['required', 'email', 'max:255'],
                'bio' => ['nullable', 'string', 'max:1000'],
                'gender' => ['nullable', 'in:male,female,other'],
                'birth_date' => ['nullable', 'date', 'before:today'],
                'job_title' => ['required', 'string', 'max:255'],
                'min_hourly_rate' => ['required', 'numeric', 'min:0'],
                'max_hourly_rate' => ['required', 'numeric', 'gte:min_hourly_rate'],
                'category_id' => ['required', 'exists:categories,id'],
                'english_level' => ['required', 'in:beginner,intermediate,advanced,fluent,native'],
                'payment_method' => ['required', 'in:paypal,bank_transfer,crypto,other,stripe'],
                'resume' => ['nullable', 'url', 'max:2048'],
                'work_experiences' => ['required', 'array'],
                'work_experiences.*.company_name' => ['required', 'string', 'max:255'],
                'work_experiences.*.position' => ['required', 'string', 'max:255'],
                'work_experiences.*.description' => ['nullable', 'string', 'max:1000'],
                'work_experiences.*.start_date' => ['required', 'date', 'before_or_equal:today'],
                'work_experiences.*.end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
                'educations' => ['required', 'array'],
                'educations.*.institution' => ['required', 'string', 'max:255'],
                'educations.*.degree' => ['required', 'string', 'max:255'],
                'educations.*.description' => ['required', 'string', 'max:255'],
                'educations.*.field_of_study' => ['nullable', 'string', 'max:1000'],
                'educations.*.start_date' => ['required', 'date', 'before_or_equal:today'],
                'educations.*.end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
                'skills' => ['required', 'array'],
                ]);

                $user = $request->user();
                 if ($user->freelancerProfile) {
                    return response()->json([
                        'message' => 'User already has a profile'
                    ], 400);
                }

            DB::beginTransaction();

            $freelancerProfile = FreelancerProfile::create([
                'user_id' => auth()->id(),
                'city' => $validatedData['city'],
                'address' => $validatedData['address'] ?? null,
                'email' => $validatedData['email'],
                'bio' => $validatedData['bio'] ?? null,
                'gender' => $validatedData['gender'] ?? null,
                'birth_date' => $validatedData['birth_date'] ?? null,
                'job_title' => $validatedData['job_title'],
                'min_hourly_rate' => $validatedData['min_hourly_rate'],
                'max_hourly_rate' => $validatedData['max_hourly_rate'],
                'category_id' => $validatedData['category_id'],
                'english_level' => $validatedData['english_level'],
                'payment_method' => $validatedData['payment_method'],
                'resume' => $validatedData['resume'] ?? null,
            ]);

            $experienceIds = [];

            foreach ($validatedData['work_experiences'] as $experience) {
                $exp = WorkExperience::create([
                    'freelancer_id' => $freelancerProfile->id,
                    'company_name' => $experience['company_name'],
                    'position' => $experience['position'],
                    'description' => $experience['description'] ?? null,
                    'start_date' => $experience['start_date'],
                    'end_date' => $experience['end_date'] ?? null,
                ]);

                $experienceIds[] = $exp->id;
            }

            foreach ($validatedData['educations'] as $experience) {
                $exp = Education::create([
                    'freelancer_id' => $freelancerProfile->id,
                    'institution' => $experience['institution'],
                    'degree' => $experience['degree'],
                    'field_of_study' => $experience['field_of_study'] ?? null,
                    'start_date' => $experience['start_date'],
                    'end_date' => $experience['end_date'] ?? null,
                ]);

            }
            $skillIds = [];
            foreach ($validatedData['skills'] as $skillName) {
                $skill = Skill::firstOrCreate(['name' => $skillName]);
                $skillIds[] = $skill->id;
            }
            foreach ($skillIds as $skillId) {
                DB::table('freelancer_skills')->insert([
                    'freelancer_id' => $freelancerProfile->id,
                    'skill_id' => $skillId
                ]);
            }




    DB::commit();


    return response()->json([
        'message' => 'Freelancer profile created successfully',

    ], 201);



        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Freelancer profile creation failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'message' => 'Failed to create freelancer profile',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
{
    try {
        $profile = FreelancerProfile::findOrFail($id);

        DB::beginTransaction();

        $profile->workExperiences()->delete();
        $profile->educations()->delete();
        $profile->skills()->detach();

        $profile->delete();

        /* if ($user) {
            $user->delete();
        } */

        DB::commit();

        return response()->json([
            'message' => 'Freelancer profile deleted successfully'
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Freelancer profile not found',
            'error' => 'Profile with the given ID does not exist.'
        ], 404);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error deleting freelancer profile: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to delete freelancer profile',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
