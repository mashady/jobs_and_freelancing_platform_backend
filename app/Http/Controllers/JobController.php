<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;


class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $jobs = Job::all();
        return JobResource::collection($jobs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JobRequest $request)
    {
        //
        $job = job::create($request->all());
        return JobResource::make($job);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $job = Job::findOrFail($id);
        return new JobResource($job);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JobRequest $request, string $id)
    {
        //
        $job = Job::findOrFail($id);
        $job->update($request->all());
        return new JobResource($job);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $job = Job::findOrFail($id);
        $job->delete();
    }
}
