<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'contact_number' => 'required|string|max:15',
            'location' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2000',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
        ]);
        // if ($request->hasFile('image')) {
        //     $uploadedImages = [];
        //     foreach ($request->file('image') as $image) {
        //         $path = $image->store('uploads', 'public'); // Save the file in the 'public/uploads' directory
        //         $uploadedImages[] = $path;
        //     }
        //     // Optionally, you can store paths in the database or do other operations with $uploadedImages array

        //     return response()->json(['message' => 'Images uploaded successfully', 'paths' => $uploadedImages], 200);
        // }

        $product = Product::create($request->all());

        return response()->json(['success' => 'Product created successfully.', 'product' => $product], 201);
    }

    public function show($id)
    {
        $product = Product::with('category', 'user')->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'contact_number' => 'required|string|max:15',
            'location' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());

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
        // Validate that the category exists
        if (!Category::where('id', $category->id)->exists()) {
            return response()->json(['error' => 'Category not found.'], 404);
        }

        // Retrieve products by category_id
        $products = Product::where('category_id', $category->id)->get();

        return response()->json($products,200);
    }
}
