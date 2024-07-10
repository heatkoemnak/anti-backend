<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'event_name' => 'required|string|max:255',
        'event_description' => 'required|string',
        'event_date' => 'required|date',
        'event_location' => 'required|string|max:255',
        'event_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:4096', // Validation for images
    ]);

        // Store event details
        $event = new Event();
$event->event_name = $request->event_name;
$event->event_description = $request->event_description;
$event->event_date = $request->event_date;
$event->event_location = $request->event_location;
$event->save();

        // Handle event images
        if ($request->hasFile('event_images')) {
            foreach ($request->file('event_images') as $image) {
                // Example: store each image in 'public' disk under 'events' directory
                $path = $image->store('events', 'public');

                // Save image path or URL to database
                $event->images()->create(['url' => $path]);
            }
        }
    }

}
