<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function store(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category' => 'required|string|max:255',  // Validate the category input
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('post/banner', $fileName, 'protected');
            $validated['image'] = $filePath;
        }

        // Create a new blog post
        $blog = Blog::create($validated);

        return response()->json(['message' => 'Blog post created successfully!', 'blog' => $blog], 201);
    }

    public function index()
    {
        // Get all blog posts
        $blogs = Blog::orderBy('id','desc')->get()->map(function ($blog) {
            // Add the full image URL if it exists
            if ($blog->image) {
                $blog->image_url = route('protected.image', ['path' => $blog->image]);
            }
            return $blog;
        });

        return response()->json($blogs);
    }

    // Show a specific blog post by ID
    public function show($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['error' => 'Blog post not found'], 404);
        }

        // Add the image URL if an image is uploaded
        if ($blog->image) {
            $blog->image_url = route('protected.image', ['path' => $blog->image]);
        }

        return response()->json($blog);
    }

    // Update an existing blog post
    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['error' => 'Blog post not found'], 404);
        }

        // Validate the input
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required',
            'category' => 'sometimes|required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image
        ]);

        // Handle image upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($blog->image && Storage::disk('protected')->exists($blog->image)) {
                Storage::disk('protected')->delete($blog->image);
            }

            // Store the new image
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('post/banner', $fileName, 'protected');
            $validated['image'] = $filePath;
        }

        // Update the blog post with new values
        $blog->update($validated);

        // Add the new image URL if an image is uploaded
        if ($blog->image) {
            $blog->image_url = route('protected.image', ['path' => $blog->image]);
        }

        return response()->json(['message' => 'Blog post updated successfully!', 'blog' => $blog]);
    }

    // Delete a blog post
    public function destroy($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['error' => 'Blog post not found'], 404);
        }

        // Delete the image file if it exists
        if ($blog->image && Storage::disk('protected')->exists($blog->image)) {
            Storage::disk('protected')->delete($blog->image);
        }

        $blog->delete();

        return response()->json(['message' => 'Blog post deleted successfully!']);
    }
}
