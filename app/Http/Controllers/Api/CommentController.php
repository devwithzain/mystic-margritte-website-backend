<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $blogId)
    {
        $blog = Blog::find($blogId);

        if (!$blog) {
            return response()->json(['error' => 'Blog not found.'], 404);
        }

        $comment = Comment::create([
            'blog_id' => $blogId,
            'user_id' => Auth::id(),
            'content' => $request->validated()['content'],
        ]);

        return response()->json([
            'message' => 'Comment added successfully.',
            'comment' => $comment
        ], 201);
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['error' => 'Comment not found.'], 404);
        }

        if (Auth::id() !== $comment->user_id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.']);
    }
}