<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class WebPostController extends Controller
{
    /**
     * Display a listing of posts depending on role.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'author') {
            $posts = Post::where('user_id', $user->id)->with(['approvedBy'])->latest()->paginate(10);
        } else {
            // Managers and Admins
            $posts = Post::with(['user', 'approvedBy'])->latest()->paginate(10);
        }

        return view('posts.index', compact('posts', 'user'));
    }

    /**
     * Show the form for creating a new post (Authors only)
     */
    public function create()
    {
        if (Auth::user()->role !== 'author') {
            abort(403, 'Only authors can create posts.');
        }

        return view('posts.create');
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'author') {
            abort(403);
        }

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

        \App\Models\PostLog::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'action' => 'created'
        ]);

        return redirect()->route('dashboard')->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified post to view/approve/reject
     */
    public function show($id)
    {
        $post = Post::with(['user', 'approvedBy', 'logs.user'])->findOrFail($id);
        $user = Auth::user();

        // Check view perm
        if ($user->role === 'author' && $post->user_id !== $user->id) {
            abort(403, 'Unauthorized viewing.');
        }

        return view('posts.show', compact('post', 'user'));
    }

    /**
     * Show the form for editing the specified post
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'author' || $post->user_id !== $user->id) {
            abort(403, 'You can only edit your own posts.');
        }

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the post
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'author' || $post->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $post->update([
            'title' => $request->title,
            'body' => $request->body,
            'status' => 'pending',
            'approved_by' => null,
            'rejected_reason' => null
        ]);

        \App\Models\PostLog::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'action' => 'updated'
        ]);

        return redirect()->route('dashboard')->with('success', 'Post updated and set to pending.');
    }

    /**
     * Approve
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['manager', 'admin'])) {
            abort(403);
        }

        $post = Post::findOrFail($id);
        $post->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'rejected_reason' => null
        ]);

        \App\Models\PostLog::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'action' => 'approved'
        ]);

        return back()->with('success', 'Post approved.');
    }

    /**
     * Reject
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['manager', 'admin'])) {
            abort(403);
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

        \App\Models\PostLog::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'action' => 'rejected'
        ]);

        return back()->with('success', 'Post rejected.');
    }

    /**
     * Delete
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403);
        }

        $post = Post::findOrFail($id);

        \App\Models\PostLog::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'action' => 'deleted'
        ]);

        $post->delete();

        return redirect()->route('dashboard')->with('success', 'Post deleted.');
    }
}
