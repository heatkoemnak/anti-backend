<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Waste;
use App\Models\WasteCate;
use App\Models\WasteImage;
use Illuminate\Support\Facades\Storage;

class WasteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wastes = Waste::with('images')->get();
        return response()->json($wastes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'owner' => 'nullable|string|max:255',
            'categories' => 'required|string|max:225',
            'location' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'item_amount' => 'required|string|max:255',
            'price' => 'required|string|max:20',
            'description' => 'nullable|string',
            'waste_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Create Waste entry
        $waste = Waste::create([
            'name' => $request->name,
            'owner' => $request->owner,
            'categories' => $request->categories,
            'location' => $request->location,
            'contact_number' => $request->contact_number,
            'item_amount' => $request->item_amount,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        // Handle Waste images
        if ($request->hasFile('waste_images')) {
            foreach ($request->file('waste_images') as $image) {
                $path = $image->store('waste_images', 'public');
                WasteImage::create([
                    'waste_id' => $waste->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json(['message' => 'Waste posted successfully!'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Waste $waste
     * @return \Illuminate\Http\Response
     */
    public function show(Waste $waste)
    {
        $waste = Waste::with('images')->find($waste->id);

        return response()->json($waste);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Waste $waste
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Waste $waste)
    {
        // Validate request data
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'owner' => 'nullable|string|max:255',
            'categories' => 'sometimes|required|string|max:225',
            'location' => 'sometimes|required|string|max:255',
            'contact_number' => 'sometimes|required|string|max:20',
            'item_amount' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|string|max:20',
            'description' => 'nullable|string',
            'waste_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Update Waste entry
        $waste->update($request->only([
            'name',
            'owner',
            'categories',
            'location',
            'contact_number',
            'item_amount',
            'price',
            'description'
        ]));

        // Handle Waste images
        if ($request->hasFile('waste_images')) {
            foreach ($request->file('waste_images') as $image) {
                $path = $image->store('waste_images', 'public');
                WasteImage::create([
                    'waste_id' => $waste->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json(['message' => 'Waste updated successfully!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Waste $waste
     * @return \Illuminate\Http\Response
     */
    public function destroy(Waste $waste)
    {
        // Delete waste images from storage
        foreach ($waste->images as $image) {
            Storage::delete('public/' . $image->image_path);
            $image->delete();
        }

        $waste->delete();

        return response()->json(['message' => 'Waste deleted successfully!'], 200);
    }
}
