<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $events = Event::paginate(10); // Adjust pagination as needed
            return response()->json($events, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching events: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching events', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'event_name' => 'required|string|max:255',
                'description' => 'required|string',
                'date' => 'required|date',
                'location' => 'required|string|max:255',
                'photo.*' => 'nullable|file|mimes:jpg,jpeg,png', // Validation for images
            ]);

            $event = new Event();
            $event->event_name = $request->event_name;
            $event->description = $request->description;
            $event->date = $request->date;
            $event->location = $request->location;

            // Handle event photo if uploaded
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('events/photos', 'public');
                $event->photo = $photoPath;
            }

            $event->save();

            return response()->json(['message' => 'Event created successfully', 'event' => $event], 201);
        } catch (\Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage());
            return response()->json(['message' => 'Error creating event', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified event in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);

            $request->validate([
                'event_name' => 'required|string|max:255',
                'description' => 'required|string',
                'date' => 'required|date',
                'location' => 'required|string|max:255',
                'photo.*' => 'nullable|file|mimes:jpg,jpeg,png', // Validation for images
            ]);

            $event->event_name = $request->event_name;
            $event->description = $request->description;
            $event->date = $request->date;
            $event->location = $request->location;

            // Handle event photo update if uploaded
            if ($request->hasFile('photo')) {
                // Delete previous photo if exists
                if ($event->photo && Storage::disk('public')->exists($event->photo)) {
                    Storage::disk('public')->delete($event->photo);
                }

                $photoPath = $request->file('photo')->store('events/photos', 'public');
                $event->photo = $photoPath;
            }

            $event->save();

            return response()->json(['message' => 'Event updated successfully', 'event' => $event], 200);
        } catch (\Exception $e) {
            Log::error('Error updating event: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating event', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified event from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);

            // Delete associated photo if exists
            if ($event->photo && Storage::disk('public')->exists($event->photo)) {
                Storage::disk('public')->delete($event->photo);
            }

            $event->delete();

            return response()->json(['message' => 'Event deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting event: ' . $e->getMessage());
            return response()->json(['message' => 'Error deleting event', 'error' => $e->getMessage()], 500);
        }
    }
}
