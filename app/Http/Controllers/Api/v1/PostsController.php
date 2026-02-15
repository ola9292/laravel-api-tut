<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Post::with('user')->paginate();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:2'],
            'body' => ['required', 'string', 'min:2'],
        ]);

        $data['user_id'] = auth()->user()->id;

        $post = Post::create($data);

        return response()->json([
            'data' => $post,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $user_id = auth()->user()->id;

        if ($post->user_id != $user_id) {
            abort(403, 'Access Denied');
        }

        return response()->json([
            'data' => $post->load('user'),
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => ['required'],
            'body' => ['required'],
        ]);
        if ($post->user_id != $user_id) {
            abort(403, 'Access Denied');
        }
        // $post = Post::findOrFail($id);

        $post->update($data);

        return response()->json([
            'data' => $post,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->user_id != $user_id) {
            abort(403, 'Access Denied');
        }

        $post->delete();

        return response()->noContent();
    }
}
