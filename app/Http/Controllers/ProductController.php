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
        foreach ($products as $product) {
            $product->img = array_map(fn($path) => url('storage/' . $path), json_decode($product->img, true));
        }
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'categories' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'img.*' => 'required|file|mimes:jpg,jpeg,png'
        ]);

        $photoUrls = [];
        if ($request->hasFile('img')) {
            foreach ($request->file('img') as $file) {
                $path = $file->store('public/images');
                $photoUrls[] = str_replace('public/', '', $path);
            }
        }
        $validatedData['img'] = json_encode($photoUrls);

        $product = Product::create($validatedData);
        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->img = array_map(fn($path) => url('storage/' . $path), json_decode($product->img, true));
        return response()->json($product);
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'description' => 'required|string',
        'contact_number' => 'required|string|max:15',
        'location' => 'required|string|max:255',
        'category_id' => 'exists:categories,id',
        'user_id' => 'exists:users,id',
        'img.*' => 'file|mimes:jpg,jpeg,png'
    ]);

    $product = Product::findOrFail($id);

    $product->update($request->except('img'));

    if ($request->hasFile('img')) {
        $photoUrls = [];
        foreach ($request->file('img') as $file) {
            $path = $file->store('public/images');
            $photoUrls[] = str_replace('public/', '', $path);
        }
        $product->img = json_encode($photoUrls);
    }

    $product->save();

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
