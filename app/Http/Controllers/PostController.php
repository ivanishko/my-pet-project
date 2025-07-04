<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->latest()->get();

        return Inertia::render('Posts/Index', [
            'posts' => $posts
        ]);
    }

    public function show(Post $post)
    {
        return Inertia::render('Posts/Show', [
            'post' => $post->load('user')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Создаем пост через отношение
        $post = $request->user()->posts()->create([
            'title' => $request->title,
            'content' => $request->content
        ]);

        return response()->json($post, 201);
    }

    public function update(Request $request, Post $post)
    {
        // Проверка авторизации и прав
        if (!Auth::check() || Auth::id() !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post->update($request->all());

        return response()->json($post);
    }

    public function destroy(Post $post)
    {
        // Проверка авторизации и прав
        if (!Auth::check() || Auth::id() !== $post->user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $post->delete();

        return response()->json(null, 204);
    }
}