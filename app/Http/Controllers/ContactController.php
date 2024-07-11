<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'nullable|string',
        ]);

        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        return response()->json(['message' => 'Contact saved successfully', 'contact' => $contact], 201);
    }

    public function index()
    {
        $contacts = Contact::all();
        return response()->json($contacts);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'nullable|string',
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
