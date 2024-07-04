<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::paginate(30); // Adjust pagination as needed
        return response()->json($events);
    }

    public function store(Request $request)
{
    $validatedData = $request->validate([
        'event_name' => 'required|string|max:255',
        'location' => 'string|max:255',
        'start_time' => 'date_format:Y-m-d H:i:s',
        'end_time' => 'date_format:Y-m-d H:i:s',
        'date' => 'date',
        'description' => 'nullable|string',
        'photo' => 'nullable|image|max:2048', // Validate photo
    ]);

    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('photos', 'public');
        $validatedData['photo'] = $path;
    }

    $event = Event::create($validatedData);

    return response()->json($event, 201);
}


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'event_name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'start_time' => 'sometimes|required|date_format:Y-m-d H:i:s',
            'end_time' => 'sometimes|required|date_format:Y-m-d H:i:s',
            'date' => 'sometimes|required|date',
            'description' => 'sometimes|nullable|string',
            'photo' => 'sometimes|nullable|image|max:2048', // Validate photo
        ]);

        $event = Event::find($id);

        if (is_null($event)) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        if ($request->hasFile('photo')) {
            // Delete the old photo if it exists
            if ($event->photo) {
                Storage::disk('public')->delete($event->photo);
            }
            $path = $request->file('photo')->store('photos', 'public');
            $validatedData['photo'] = $path;
        }

        $event->update($validatedData);

        return response()->json($event);
    }

    public function destroy($id)
    {
        $event = Event::find($id);

        if (is_null($event)) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Delete the event photo if it exists
        if ($event->photo) {
            Storage::disk('public')->delete($event->photo);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }
}
