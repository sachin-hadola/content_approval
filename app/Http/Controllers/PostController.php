<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostLog;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'author') {
            return response()->json(Post::where('user_id', $user->id)->with(['user', 'approvedBy', 'logs'])->get());
        }

        // Managers and Admins can view all posts
        return response()->json(Post::with(['user', 'approvedBy', 'logs'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'body' => $request->body,
            'status' => 'pending',
        ]);

        PostLog::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'action' => 'created'
        ]);

        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::with(['user', 'approvedBy', 'logs'])->findOrFail($id);
        $user = Auth::user();

        if ($user->role === 'author' && $post->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::findOrFail($id);
        $user = Auth::user();

        if ($post->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized. You can only update your own posts.'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $post->update([
            'title' => $request->title,
            'body' => $request->body,
            'status' => 'pending', // Revert to pending when updated
            'approved_by' => null,
            'rejected_reason' => null
        ]);

        PostLog::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'action' => 'updated'
        ]);

        return response()->json($post);
    }

    /**
     * Approve the post.
     */
    public function approve(Request $request, string $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['manager', 'admin'])) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $post = Post::findOrFail($id);
        $post->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'rejected_reason' => null
        ]);

        PostLog::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'action' => 'approved'
        ]);

        return response()->json($post);
    }

    /**
     * Reject the post.
     */
    public function reject(Request $request, string $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['manager', 'admin'])) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'rejected_reason' => 'required|string'
        ]);

        $post = Post::findOrFail($id);
        $post->update([
            'status' => 'rejected',
            'rejected_reason' => $request->rejected_reason,
            'approved_by' => null
        ]);

        PostLog::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'action' => 'rejected'
        ]);

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized. Only admins can delete.'], 403);
        }

        $post = Post::findOrFail($id);
        
        PostLog::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'action' => 'deleted'
        ]);

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully.']);
    }
}
