<?php

namespace App\Http\Controllers;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(){
        $contact=Contact::all();
        return response()->json($contact);
    }
    public function store(Request $request){
        $validated=$request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email',
            'message'=>'nullable|string',
        ]);
        $contact=Contact::create([
            'name'=>$validated['name'],
            'email'=>$validated['email'],
            'message'=>$validated['message'],
        ]);
        return response()->json(['message'=>'cam on ban da gui lien he','data'=>$contact],201);
    }
    public function destroy($id){
        $contact=Contact::findOrFail($id);
        $contact->delete();
        return response()->json($contact);
    }
}
