<?php

namespace App\Http\Controllers;

use App\Models\JobComment;
use Illuminate\Http\Request;
use App\Http\Requests\StoreJobCommentRequest;

class JobCommentController extends Controller
{
    public function index($jobId)
    {
        $comments = JobComment::where('job_id', $jobId)
            ->with('user')
            ->latest()
            ->get();

        return response()->json($comments);
    }

    public function store(StoreJobCommentRequest $request)
    {
        $comment = JobComment::create([
            'user_id' => auth()->id(),
            'job_id' => $request->job_id,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment,
        ], 201);
    }

    public function destroy($id)
    {
        $comment = JobComment::findOrFail($id);

        // Optional: تحقق إن المستخدم هو اللي كتب التعليق
        if ($comment->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
