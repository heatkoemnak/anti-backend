<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display a listing of the resource with pagination.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::with('images')->get();
        foreach ($events as $event) {
            foreach ($event->images as $image) {
                $image->image_url = Storage::url($image->image_path);
            }
        }
        return response()->json($events);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string|max:255',
            'event_description' => 'required|string',
            'event_date' => 'required|date',
            'event_location' => 'required|string|max:255',
            'event_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $event = Event::create([
            'name' => $request->event_name,
            'description' => $request->event_description,
            'date' => $request->event_date,
            'location' => $request->event_location,
        ]);

        if ($request->hasFile('event_images')) {
            foreach ($request->file('event_images') as $image) {
                $path = $image->store('event_images', 'public');
                EventImage::create([
                    'event_id' => $event->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json(['message' => 'Event created successfully!'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::with('images')->find($id);

        if (is_null($event)) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        foreach ($event->images as $image) {
            $image->image_url = Storage::url($image->image_path);
        }

        return response()->json($event);
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
            'event_name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'start_time' => 'sometimes|required|date_format:Y-m-d H:i:s',
            'end_time' => 'sometimes|required|date_format:Y-m-d H:i:s|after:start_time',
            'date' => 'sometimes|required|date',
        ]);

        $event = Event::find($id);

        if (is_null($event)) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $event->update($validatedData);

        return response()->json($event);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::find($id);

        if (is_null($event)) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }

    /**
     * Search for events.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = Event::query();

        if ($request->has('event_name')) {
            $query->where('name', 'like', '%' . $request->query('event_name') . '%');
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->query('location') . '%');
        }

        if ($request->has('date')) {
            $query->whereDate('date', $request->query('date'));
        }

        $events = $query->with('images')->get();
        foreach ($events as $event) {
            foreach ($event->images as $image) {
                $image->image_url = Storage::url($image->image_path);
            }
        }

        return response()->json($events);
    }
}
