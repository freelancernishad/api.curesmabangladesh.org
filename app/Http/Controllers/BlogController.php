<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function store(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category' => 'required|string|max:255',  // Validate the category input
        ]);

        // Create a new blog post
        $blog = Blog::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'], // Set the category directly
        ]);

        return response()->json(['message' => 'Blog post created successfully!', 'blog' => $blog], 201);
    }

    public function index()
    {
        // Get all blog posts
        $blogs = Blog::all();
        return response()->json($blogs);
    }
}
