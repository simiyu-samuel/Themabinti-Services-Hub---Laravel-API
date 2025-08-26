<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of published blog posts.
     */
    public function index(Request $request)
    {
        $query = Blog::query()->where('status', 'published')
                            ->where('published_at', '<=', now())
                            ->with('admin'); // Eager load the author

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('content', 'like', '%' . $searchTerm . '%')
                    ->orWhere('excerpt', 'like', '%' . $searchTerm . '%');
            });
        }

        $blogs = $query->latest('published_at')->paginate(10);

        return response()->json($blogs, 200);
    }

    /**
     * Display the specified published blog post by slug.
     */
    public function show(string $slug)
    {
        $blog = Blog::where('slug', $slug)
                    ->where('status', 'published')
                    ->where('published_at', '<=', now())
                    ->with('admin')
                    ->firstOrFail();

        return response()->json($blog, 200);
    }
}