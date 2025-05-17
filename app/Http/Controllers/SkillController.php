<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SkillRequest;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Http\Resources\SkillResource;
class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $skills = Skill::all();
        return SkillResource::collection($skills);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SkillRequest $request)
    {
        //
        $skill = Skill::create($request->all());
        return SkillResource::make($skill);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $skill = Skill::findOrFail($id);
        return $skill;

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SkillRequest $request, string $id)
    {
        $skill = Skill::findOrFail($id);
        $skill->update($request->all());
        return new SkillResource($skill);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $skill = Skill::findOrFail($id);
        $skill->delete();
    }
}
