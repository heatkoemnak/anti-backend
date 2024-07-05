<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        return response()->json(Contact::all());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        $contact = Contact::create($validatedData);

        return response()->json($contact, 201);
    }
    public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'message' => 'required|nullable|string',
    ]);

    $contact = Contact::find($id);

    if (is_null($contact)) {
        return response()->json(['message' => 'Contact not found'], 404);
    }

    $contact->update($validatedData);

    return response()->json($contact);
}

public function destroy($id)
{
    $contact = Contact::find($id);

    if (is_null($contact)) {
        return response()->json(['message' => 'Contact not found'], 404);
    }

    $contact->delete();

    return response()->json(['message' => 'Contact deleted successfully']);
}
}
