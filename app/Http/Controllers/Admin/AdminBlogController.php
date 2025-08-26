<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // For slug generation
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Blog\StoreBlogRequest; // Import
use App\Http\Requests\Admin\Blog\UpdateBlogRequest; // Import

class AdminBlogController extends Controller
{
    /**
     * Display a listing of blog posts for admin (includes drafts).
     */
    public function index(Request $request)
    {
        $query = Blog::query()->with('admin'); // Eager load author

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        $blogs = $query->latest()->paginate(15);

        return response()->json($blogs, 200);
    }

    /**
     * Store a newly created blog post.
     */
    public function store(StoreBlogRequest $request) // Use Form Request
    {
        $blogData = $request->validated();
        $blogData['admin_id'] = $request->user()->id; // Assign current admin as author
        $blogData['slug'] = Str::slug($blogData['title']); // Generate slug

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('blog_images', 'public');
            $blogData['featured_image'] = Storage::url($path);
        }

        if ($blogData['status'] === 'published' && !isset($blogData['published_at'])) {
            $blogData['published_at'] = now(); // Set publish date if status is published and not provided
        } elseif ($blogData['status'] === 'draft') {
            $blogData['published_at'] = null; // Clear publish date if draft
        }

        $blog = Blog::create($blogData);

        return response()->json([
            'message' => 'Blog post created successfully.',
            'blog' => $blog,
        ], 201);
    }

    /**
     * Display the specified blog post.
     */
    public function show(Blog $blog)
    {
        return response()->json($blog->load('admin'), 200);
    }

    /**
     * Update the specified blog post.
     */
    public function update(UpdateBlogRequest $request, Blog $blog) // Use Form Request
    {
        $blogData = $request->validated();

        if (isset($blogData['title'])) {
            $blogData['slug'] = Str::slug($blogData['title']);
        }

        // Handle featured image update
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($blog->featured_image && Storage::disk('public')->exists(str_replace('/storage/', '', $blog->featured_image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $blog->featured_image));
            }
            $path = $request->file('featured_image')->store('blog_images', 'public');
            $blogData['featured_image'] = Storage::url($path);
        } elseif (array_key_exists('featured_image', $blogData) && $blogData['featured_image'] === null) {
            // If explicitly set to null, remove existing image
            if ($blog->featured_image && Storage::disk('public')->exists(str_replace('/storage/', '', $blog->featured_image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $blog->featured_image));
            }
            $blogData['featured_image'] = null;
        }

        if (isset($blogData['status'])) {
            if ($blogData['status'] === 'published' && !$blog->published_at) {
                $blogData['published_at'] = now();
            } elseif ($blogData['status'] === 'draft') {
                $blogData['published_at'] = null;
            }
        }

        $blog->update($blogData);

        return response()->json([
            'message' => 'Blog post updated successfully.',
            'blog' => $blog,
        ], 200);
    }

    /**
     * Remove the specified blog post.
     */
    public function destroy(Blog $blog)
    {
        // Delete associated featured image
        if ($blog->featured_image && Storage::disk('public')->exists(str_replace('/storage/', '', $blog->featured_image))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $blog->featured_image));
        }

        $blog->delete();

        return response()->json(['message' => 'Blog post deleted successfully.'], 200);
    }
}