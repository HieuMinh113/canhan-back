<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class PetController extends Controller
{
    public function store(Request $request){
        $validated = $request->validate([
            'name'=>'required|string|max:255',
            'type'=>'required|string|max:255',
            'age'=>'required|integer',
            'gender'=>'required|string|max:255',
            'description'=>'nullable|string',
            'color'=>'required|string|max:255',
            'image'=>'required|string',
            'price'=>'required|numeric',
            'stock'=>'required|integer',
            'breed'=>'required|string|max:255'
        ]);
        $pet=Pet::create($validated);
        return response()->json($pet,201);
    }
    public function update(Request $request,$id){
        $pet=Pet::findOrFail($id);
        $validated = $request->validate([
            'name'=>'required|string|max:255',
            'type'=>'required|string|max:255',
            'age'=>'required|integer',
            'gender'=>'required|string|max:255',
            'description'=>'nullable|string',
            'color'=>'required|string|max:255',
            'image'=>'required|string',
            'price'=>'required|numeric',
            'stock'=>'required|integer',
            'breed'=>'required|string|max:255'
        ]);
        $pet->update($validated);
        return response()->json($pet,200);
    }
    public function destroy($id){
        $pet=Pet::findOrFail($id);
        $pet->delete();
        return response()->json(['message'=>'xoa thanh cong']);
    }
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('pets', $imageName, 'public');
            $fullUrl = URL::to('/') . '/storage/' . $path;
            return response()->json(['url' => $fullUrl], 200);
        }
        return response()->json(['error' => 'No image uploaded'], 400);
    }
    public function index(Request $request)
    {
        $query=Pet::query();
       if($request->filled('search')){
        $query->where('name', 'like', '%' . $request->search . '%');
       }
       if($request->filled('breed')){
        $query->where('breed', $request->breed );
       }
       if($request->filled('type')){
        $query->where('type', $request->type );
       }
       return response()->json($query->get());
    }
    public function show($id){
        $pet=Pet::find($id);
        if(!$pet){
            return response()->json(['message'=>'khong tim thay con cho'],404);
        }
        return response()->json($pet);
        }
    public function getpetstock(){
        $pet = Pet::select('id','name','stock');
        return response()->json($pet);
    }
    public function countpetstock(){
        $totalStock = Pet::sum('stock');
        return response()->json(['total_stock' => $totalStock]);
    }
    public function getbreed(Request $request){
        $query = Pet::select('breed')
        ->distinct();
        if($request->filled('type')){
            $query->where('type',$request->type);
        }
        $breeds = $query->pluck('breed');
        return response()->json($breeds);
    }
}

