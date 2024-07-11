<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
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
            $product->image = url('storage/' . $product->image);
        }
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string|max:255', // Add this line
            'contact_number' => 'required|string|max:15',
            'location' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2000',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
        ]);


        // Save the uploaded image
        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('images', 'public');
        }

        $product = Product::create($validatedData);
        //     return response()->json(['message' => 'Images uploaded successfully', 'paths' => $uploadedImages], 200);
        // }

        $product = Product::create($request->all());

        return response()->json(['success' => 'Product created successfully.', 'product' => $product], 201);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Add the full URL for the image
        $product->image = url('storage/' . $product->image);

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

        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }


        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('images', 'public');
        }


        $product->update($validatedData);

        return response()->json($product);
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
    //rate & comment on product
    public function rateProduct(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $product = Product::find($id);
        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // // Assuming you have a Rating model and ratings table
        // $rating = new Rating;
        // $rating->product_id = $product->id;
        // $rating->rating = $request->input('rating');
        // $rating->save();

        // // Update product's average rating
        // $averageRating = Rating::where('product_id', $product->id)->avg('rating');
        // $product->rating = $averageRating;
        // $product->save();

        // return response()->json(['message' => 'Rating added successfully', 'average_rating' => $averageRating]);
        $product->ratings()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
        ]);

        // Optionally update the product's average rating
        $product->average_rating = $product->ratings()->avg('rating');
        $product->save();

        return response()->json(['message' => 'Rating submitted successfully'], 200);
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

        $comment = new Comment;
        $comment->product_id = $product->id;
        $comment->user_id = auth()->id();
        $comment->comment = $request->input('comment');
        $comment->save();

        return response()->json(['message' => 'Comment added successfully']);
    }
}

