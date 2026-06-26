<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index()
    {
        $posts = CommunityPost::query()
            ->with(['user', 'comments.user'])
            ->withCount('comments')
            ->latest()
            ->paginate(10);

        return view('community.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'category' => ['required', 'in:request,discussion'],
            'body' => ['required', 'string', 'max:2000'],
        ], [
            'title.required' => 'Add a short title for your post.',
            'body.required' => 'Write a little detail so others know what you mean.',
        ]);

        CommunityPost::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'category' => $validated['category'],
            'body' => $validated['body'],
            'status' => 'open',
        ]);

        return redirect()->route('community.index')->with('success', 'Your post is live.');
    }

    public function comment(Request $request, CommunityPost $post)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ], [
            'body.required' => 'Write your comment before posting.',
        ]);

        $post->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        return redirect()->route('community.index')->with('success', 'Comment added.');
    }
}
