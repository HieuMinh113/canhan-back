<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index(){
         $news = News::all();
         return response()->json($news);
    }
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('news', $imageName, 'public');
            $fullUrl = URL::to('/') . '/storage/' . $path;
            return response()->json(['url' => $fullUrl], 200);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }
    public function store(Request $request){
        $validate = $request->validate([
            'title'=>'required|string|max:255',
            'description'=> 'nullable|string',
            'image'=>'required|string',
        ]);
        $news= News::create($validate);
        return response()->json($news,201);
    }
    public function update(Request $request,$id){
        $news = News::findOrFail($id);
        $validated= $request->validate([
            'title'=>'required|string|max:255',
            'description'=>'nullable||string',
            'image'=>'required|string',
        ]);
        $news->update($validated);
        return response()->json($news,200);
    }
    public function destroy($id){
        $news = News::findOrFail($id);
        $news ->delete();
        if ($news->image) {
            $imagePath = str_replace(URL::to('/') . '/storage/', '', $news->image);
            Storage::disk('public')->delete($imagePath);
        }
        return response()->json([
            'message' => 'Xoá tin thành công'
        ]);
    }
    public function show($id){
        $news=News::find($id);
        if(!$news){
            return response()->json(['message'=>'khong tim thay tin tuc'],404);
        }
        return response()->json($news);
    }
}
