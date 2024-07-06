<?php

namespace App\Http\Controllers;

use App\Models\Waste;
use Illuminate\Http\Request;

class WasteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Waste::all());

    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'seller' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'amount' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

      // Save the uploaded image
      if ($request->hasFile('img')) {
        $validatedData['img'] = $request->file('img')->store('images', 'public');
    }

    $waste = Waste::create($validatedData);

    return response()->json($waste, 201);
    }



    public function show($id)
    {
       $waste = Waste::find($id);

        if (is_null($waste)) {
            return response()->json(['message' => 'waste not found'], 404);
        }

        // Add the full URL for the image
        $waste->img = url('storage/' . $waste->img);

        return response()->json($waste);
    }

    public function update(Request $request, $id)
    {
        $waste = Waste::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:255',
            'seller' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'contact' => 'sometimes|string|max:255',
            'amount' => 'sometimes|integer',
            'price' => 'sometimes|numeric',
            'image' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $waste = Waste::find($id);

        if (is_null($waste)) {
            return response()->json(['message' => 'waste not found'], 404);
        }


        if ($request->hasFile('img')) {
            $validatedData['img'] = $request->file('img')->store('images', 'public');
        }


        $waste->update($validatedData);

        return response()->json($waste);
    }

    public function destroy($id)
    {
        $waste = Waste::find($id);

        if (is_null($waste)) {
            return response()->json(['message' => 'waste not found'], 404);
        }

        $waste->delete();

        return response()->json(['message' => 'waste deleted successfully']);
    }
}
