<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;


class BannerController extends Controller
{
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('banners', $imageName, 'public');
            $fullUrl = URL::to('/') . '/storage/' . $path;
            return response()->json(['url' => $fullUrl], 200);
        }
        return response()->json(['error' => 'No image uploaded'], 400);
    }
    public function store(Request $request){
        $validated = $request->validate([
            'image' => 'required|string',
            'link'=>'nullable|string'
        ]);
        $banner=Banner::create($validated);
        return response()->json($banner,201);
    }
    public function update(Request $request,$id){
        $banner=Banner::findOrFail($id);
        $validated = $request->validate([
            'image' => 'required|string',
            'link'=>'nullable|string'
        ]);
        $banner=Banner::update($validated);
        return response()->json($banner,200);
    }
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        if ($banner->image) {
            $imagePath = str_replace(URL::to('/') . '/storage/', '', $banner->image);
            Storage::disk('public')->delete($imagePath);
        }
        return response()->json([
            'message' => 'Xoá sản phẩm thành công'
        ]);
    }
    public function index(){
        $banner=Banner::all();
        return response()->json($banner);
    }
}
