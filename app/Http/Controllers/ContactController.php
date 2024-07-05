<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $contact = Contact::create([
            'email' => $request->email,
        ]);
        $contact->save();

        return response()->json(['message' => 'Email saved successfully', 'contact' => $contact], 201);
    }

    public function index()
    {
        $contacts = Contact::all();
        return response()->json($contacts);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $contact = Contact::findOrFail($id);
        $contact->update([
            'email' => $request->email,
        ]);

        return response()->json($contact);
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json(null, 204);
    }
}
