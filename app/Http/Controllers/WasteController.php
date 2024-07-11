<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Waste;
use App\Models\WasteCate;
use App\Models\WasteImage;
use Illuminate\Support\Facades\Storage;

class WasteController extends Controller
{
    public function index()
    {
        $wastes = Waste::all();

        // Format photo URLs if photos exist
        foreach ($wastes as $waste) {
            if ($waste->photo) {
                $waste->photo = array_map(function($photo) {
                    return url('storage/' . $photo);
                }, explode(',', $waste->photo));
            }
        }

        return response()->json($wastes);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'owner' => 'required|string|max:255',
            'price' => 'required|numeric',
            'categories' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15',
            'location' => 'required|string|max:255',
            'item_amount' => 'required|numeric',
            'description' => 'required|string|max:255',
            'photo.*' => 'required|file|mimes:jpg,jpeg,png'
        ]);

        $photoUrls = [];
        if ($request->hasFile('photo')) {
            foreach ($request->file('photo') as $file) {
                $path = $file->store('public/photo');
                $photoUrls[] = str_replace('public/', '', $path);
            }
        }

        $waste = new Waste();
        $waste->name = $validatedData['name'];
        $waste->owner = $validatedData['owner'];
        $waste->price = $validatedData['price'];
        $waste->categories = $validatedData['categories'];
        $waste->contact_number = $validatedData['contact_number'];
        $waste->location = $validatedData['location'];
        $waste->item_amount = $validatedData['item_amount'];
        $waste->description = $validatedData['description'];
        $waste->photo = implode(',', $photoUrls); // Store as comma-separated string
        $waste->save();

        return response()->json($waste, 201);
    }

    public function show($id)
    {
        $waste = Waste::find($id);

        if (is_null($waste)) {
            return response()->json(['message' => 'Waste not found'], 404);
        }

        if ($waste->photo) {
            $waste->photo = array_map(function($photo) {
                return url('storage/' . $photo);
            }, explode(',', $waste->photo));
        }

        return response()->json($waste);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'owner' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'categories' => 'sometimes|required|string|max:255',
            'contact_number' => 'sometimes|required|string|max:15',
            'location' => 'sometimes|required|string|max:255',
            'item_amount' => 'sometimes|required|numeric',
            'description' => 'sometimes|required|string|max:255',
            'photo.*' => 'nullable|file|mimes:jpg,jpeg,png', // Nullable if not updating photos
        ]);

        $waste = Waste::find($id);

        if (is_null($waste)) {
            return response()->json(['message' => 'Waste not found'], 404);
        }

        if ($request->hasFile('photo')) {
            $photoUrls = [];
            foreach ($request->file('photo') as $file) {
                $path = $file->store('public/photo');
                $photoUrls[] = str_replace('public/', '', $path);
            }
            $validatedData['photo'] = implode(',', $photoUrls);

            // Delete old photos
            $oldPhotos = explode(',', $waste->photo);
            foreach ($oldPhotos as $oldPhoto) {
                Storage::delete('public/' . $oldPhoto);
            }
        }

        $waste->update($validatedData);

        if ($waste->photo) {
            $waste->photo = array_map(function($photo) {
                return url('storage/' . $photo);
            }, explode(',', $waste->photo));
        }

        return response()->json($waste);
    }

    public function destroy($id)
    {
        $waste = Waste::find($id);

        if (is_null($waste)) {
            return response()->json(['message' => 'Waste not found'], 404);
        }

        // Delete associated photos
        if ($waste->photo) {
            $photos = explode(',', $waste->photo);
            foreach ($photos as $photo) {
                Storage::delete('public/' . $photo);
            }
        }

        $waste->delete();

        return response()->json(['message' => 'Waste deleted successfully']);
    }
}
