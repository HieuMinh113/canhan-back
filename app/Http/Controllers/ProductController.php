<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query=Product::query();
       if($request->filled('search')){
        $query->where('name', 'like', '%' . $request->search . '%');
       }
       if($request->filled('type')){
            $query->where('type', $request->type );
        }
        if($request->filled('category')){
            $query->where('category', $request->category );
        }
       return response()->json($query->get());
    }
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('products', $imageName, 'public');
            $fullUrl = URL::to('/') . '/storage/' . $path;
            return response()->json(['url' => $fullUrl], 200);
        }
        return response()->json(['error' => 'No image uploaded'], 400);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'required|string',
            'type' => 'required|string',
            'stock'=>'required|integer',
            'tag'=>'required|string'
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'required|string',
            'type' => 'required|string',
            'stock'=>'required|integer',
            'tag'=>'required|string'
        ]);

        $product->update($validated);
        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        if ($product->image) {
            $imagePath = str_replace(URL::to('/') . '/storage/', '', $product->image);
            Storage::disk('public')->delete($imagePath);
        }
        return response()->json([
            'message' => 'Xoá sản phẩm thành công'
        ]);
    }
    public function show($id){
        $product=Product::find($id);
        if(!$product){
            return response()->json(['message'=>'khong tim thay san pham'],404);
        }
        return response()->json($product);
    }
    public function getproductstock(){
        $product = Product::select('id','name','stock');
        return response()->json($product);
    }
    public function countproductstock(){
        $totalStock = Product::sum('stock');
        return response()->json(['total_stock' => $totalStock]);
    }
    public function getcategory(){
        $category = Product::select('category')
        ->distinct()
        ->pluck('category');
        return response()->json($category);
    }
}
