<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\User;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $handled = $request->query('handled');
        $query = Feedback::query();
        if (!is_null($handled)) {
            $query->where('handled', $handled);
        }
        return response()->json($query->latest()->get());
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email',
            'rating'=>'required|integer|min:1|max:5',
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);
        $feedback = Feedback::create([
            'name'=>$validated['name'],
            'email'=>$validated['email'],
            'rating'=>$validated['rating'],
            'user_id' => $validated['user_id'],
            'message' => $validated['message'],
            'handled' => false,
        ]);
        return response()->json(['message' => 'Feedback đã được gửi', 'data' => $feedback]);
    }
    public function update(Request $request, $id)
    {
        $feedback = Feedback::findOrFail($id);

        $validated = $request->validate([
            'handled' => 'required|boolean',
        ]);
        $feedback->handled = $validated['handled'];
        $feedback->save();
        return response()->json(['message' => 'Feedback đã được cập nhật']);
    }
    public function allUsers()
    {
        return User::select('id', 'name', 'role')->get();
    }
}
