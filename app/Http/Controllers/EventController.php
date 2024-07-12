<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = Event::all();

            // Format photo URLs if photos exist
            foreach ($events as $event) {
                if ($event->photo) {
                    $event->photo = url('storage/' . $event->photo);
                }
            }

            return response()->json($events, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching events: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching events', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'event_name' => 'required|string|max:255',
                'description' => 'required|string',
                'date' => 'required|date',
                'location' => 'required|string|max:255',
                'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // Adjust size limit as needed
            ]);

            $event = new Event();
            $event->event_name = $validatedData['event_name'];
            $event->description = $validatedData['description'];
            $event->date = $validatedData['date'];
            $event->location = $validatedData['location'];

            // Handle event photo if uploaded
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('events/photos', 'public');
                $event->photo = str_replace('public/', '', $photoPath);
            }

            $event->save();

            // Return the event with formatted photo URL
            if ($event->photo) {
                $event->photo = url('storage/' . $event->photo);
            }

            return response()->json(['message' => 'Event created successfully', 'event' => $event], 201);
        } catch (\Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage());
            return response()->json(['message' => 'Error creating event', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $event = Event::findOrFail($id);

            // Format photo URL if photo exists
            if ($event->photo) {
                $event->photo = url('storage/' . $event->photo);
            }

            return response()->json($event, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching event: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching event', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'event_name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'date' => 'sometimes|required|date',
            'location' => 'sometimes|required|string|max:255',
            'photo.*' => 'nullable|file|mimes:jpg,jpeg,png', // Nullable if not updating photos
        ]);

        $event = Event::find($id);

        if (is_null($event)) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Handle photo upload if new photos are provided
        if ($request->hasFile('photo')) {
            $photoUrls = [];
            foreach ($request->file('photo') as $file) {
                $path = $file->store('public/events/photos');
                $photoUrls[] = str_replace('public/', '', $path);
            }
            $validatedData['photo'] = implode(',', $photoUrls);

            // Delete old photos if they exist
            if ($event->photo) {
                $oldPhotos = explode(',', $event->photo);
                foreach ($oldPhotos as $oldPhoto) {
                    Storage::delete('public/' . $oldPhoto);
                }
            }
        }

        // Update event fields based on validated data
        $event->update($validatedData);

        // Format photo URLs for response
        if ($event->photo) {
            $event->photo = array_map(function ($photo) {
                return url('storage/' . $photo);
            }, explode(',', $event->photo));
        }

        return response()->json(['message' => 'Event updated successfully', 'event' => $event]);
    }

    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);

            // Delete associated photo if exists
            if ($event->photo && Storage::disk('public')->exists('events/' . $event->photo)) {
                Storage::disk('public')->delete('events/' . $event->photo);
            }

            $event->delete();

            return response()->json(['message' => 'Event deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting event: ' . $e->getMessage());
            return response()->json(['message' => 'Error deleting event', 'error' => $e->getMessage()], 500);
        }
    }
}
