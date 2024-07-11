<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Comment;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // ===============Display a listing of the resource.================

    public function index()
    {
        $products = Product::all();
        foreach ($products as $product) {
            $product->img = url('storage/' . $product->img);
        }
        return response()->json($products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15',
            'price' => 'required|numeric',
            'img' => 'required|file',
            'description' => 'required|string|max:255', // Add this line

        ]);

        // Log validated data
        // \Log::info('Validated Data: ', $validatedData);

        // Save the uploaded image
        if ($request->hasFile('img')) {
            $validatedData['img'] = $request->file('img')->store('images', 'public');
        }

        $product = Product::create($validatedData);

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // ================== Display the specified resource.============

    public function show($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Add the full URL for the image
        $product->img = url('storage/' . $product->img);

        return response()->json($product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'owner_name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'contact_number' => 'sometimes|required|string|max:15',
            'price' => 'sometimes|required|numeric',
            'img' => 'required|file',
            'description' => 'nullable|string|max:255', // Nullable if default value is set

        ]);

        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }


        if ($request->hasFile('img')) {
            $validatedData['img'] = $request->file('img')->store('images', 'public');
        }


        $product->update($validatedData);

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
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

