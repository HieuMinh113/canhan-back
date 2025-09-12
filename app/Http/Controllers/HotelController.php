<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    public function index()
    {
        $hotel = Hotel::all();
        return response()->json($hotel);
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('hotels', $imageName, 'public');
            $fullUrl = URL::to('/') . '/storage/' . $path;
            return response()->json(['url' => $fullUrl], 200);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'required|string',
        ]);
        $hotel = Hotel::create($validated);
        return response()->json($hotel, 201);
    }

    public function update(Request $request, $id)
    {
        $hotel = Hotel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'required|string',
        ]);

        $hotel->update($validated);
        return response()->json($hotel, 200);
    }

    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);
        if ($hotel->image) {
            $imagePath = str_replace(URL::to('/') . '/storage/', '', $hotel->image);
            Storage::disk('public')->delete($imagePath);
        }
        $hotel->delete();
        return response()->json([
            'message' => 'Xoá khách sạn thành công'
        ]);
    }
    public function show($id){
        $hotel=Hotel::find($id);
        if(!$hotel){
            return response()->json(['message'=>'khong tim thay khach san'],404);
        }
        return response()->json($hotel);
    }
}
