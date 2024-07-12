<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Comment;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15',
            'location' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath(), [
            'folder' => 'products', // Custom folder name
            'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET')
        ])->getSecurePath();

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'contact_number' => $request->contact_number,
            'location' => $request->location,
            'image' => $uploadedFileUrl,
            'category_id' => $request->category_id,
            'user_id' => $request->user_id,
        ]);

        return response()->json(['success' => 'Product created successfully.', 'product' => $product], 201);
    }

    public function show($id)
    {
        $product = Product::with('category', 'user')->find($id);

        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15',
            'location' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $product = Product::findOrFail($id);

        $product->update($request->except('image'));

        if ($request->hasFile('image')) {
            $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath(), [
                'folder' => 'products', // Custom folder name
                'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET')
            ])->getSecurePath();
            $product->image = $uploadedFileUrl;
            $product->save();
        }

        return response()->json(['success' => 'Product updated successfully.', 'product' => $product]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['success' => 'Product deleted successfully.']);
    }

    public function getRelatedProductsByCategory(Category $category)
    {
        if (!Category::where('id', $category->id)->exists()) {
            return response()->json(['error' => 'Category not found.'], 404);
        }

        $products = Product::where('category_id', $category->id)->get();
        return response()->json($products, 200);
    }

    public function rateProduct(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $product = Product::find($id);
        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->ratings()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
        ]);

        $product->average_rating = $product->ratings()->avg('rating');
        $product->save();

        return response()->json(['message' => 'Rating submitted successfully', 'average_rating' => $product->average_rating], 200);
    }

    public function commentProduct(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $product = Product::find($id);
        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Comment added successfully'], 200);
    }
}
