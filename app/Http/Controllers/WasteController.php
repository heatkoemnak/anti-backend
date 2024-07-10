<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Waste;
use Illuminate\Support\Facades\Storage;

class WasteController extends Controller
{
    /**
     * Display a listing of the wastes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wastes = Waste::all();
        foreach ($wastes as $waste) {
            $waste->photo_path = url('storage/' . $waste->photo_path);
        }
        return response()->json($wastes, 200);
    }

    /**
     * Store a newly created waste in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $validated = $this->validateRequest($request);

        // Handle file upload if necessary
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $validated['photo_path'] = $path;
        }

        // Create and save the waste record
        $waste = Waste::create($validated);

        return response()->json($waste, 201);
    }

    /**
     * Display the specified waste.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Find the waste record by id
        $waste = Waste::findOrFail($id);
        $waste->photo_path = url('storage/' . $waste->photo_path);
        return response()->json($waste, 200);
    }

    /**
     * Update the specified waste in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $validated = $this->validateRequest($request);

        // Handle file upload if necessary
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $validated['photo_path'] = $path;
        }

        // Find the waste record by id
        $waste = Waste::findOrFail($id);

        // Update the waste record with validated data
        $waste->update($validated);

        return response()->json($waste, 200);
    }

    /**
     * Remove the specified waste from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find the waste record by id
        $waste = Waste::findOrFail($id);

        // Delete the waste record
        $waste->delete();

        // Delete associated photo if exists
        if ($waste->photo_path) {
            Storage::delete($waste->photo_path);
        }

        return response()->json(['message' => 'Waste deleted successfully'], 200);
    }

    /**
     * Validate incoming request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'owner' => 'required|string|max:255',
            'price' => 'required|numeric',
            'categories' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15',
            'location' => 'required|string|max:255',
            'item_amount' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
    }
}
