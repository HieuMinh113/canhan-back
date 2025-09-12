<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;


class ServiceController extends Controller
{
    public function store(Request $request){
        $validated=$request->validate([
            'name'=>'required|string|max:255',
            'price'=>'required|numeric',
            'type'=>'required|string|in:staff,doctor'
        ]);
        $service=Service::create($validated);
        return response()->json($service);
    }
    public function update(Request $request,$id){
        $service=Service::findOrFail($id);
        $validated=$request->validate([
            'name'=>'required|string|max:255',
            'price'=>'required|numeric',
            'type'=>'required|string|in:staff,doctor'
        ]);
        $service->update($validated);
        return response()->json($service);
    }
    public function destroy($id){
        $service=Service::findOrFail($id);
        $service->delete();
        return response()->json(['message'=>'xoa thanh cong']);
    }
    public function index(){
        $service=Service::all();
        return response()->json($service);
    }
    public function showservice($id){
       $service=Service::where('id',$id)
       ->where('type','staff')
       ->select('name')
       ->first();
       if(!$service){
        return response()->json(['message'=>'khong tim thay dich vu'],404);
       }
       return response()->json(['name'=>$service]);
    }
}