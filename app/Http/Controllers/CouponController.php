<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function store(Request $request)
    {
        $validated=$request->validate([
            'code'=>'required|string|max:255',
            'percent'=>'required|integer|min:1|max:100',
            'usage_limit'=>'nullable|integer|min:1',
            'expires_at'=>'nullable|date',
        ]);
        $coupon=Coupon::create($validated);
        return response()->json($coupon);
    }
    public function update(Request $request,$id){
        $coupon=Coupon::find($id);
        $validated=$request->validate([
            'code'=>'required|string|max:255',
            'percent'=>'required|integer|min:1|max:100',
            'usage_limit'=>'nullable|integer|min:1',
            'expires_at'=>'nullable|date',
        ]);
        $coupon->update($validated);
        return response()->json($coupon);
    }
    public function destroy($id){
        $coupon=Coupon::find($id);
        $coupon->delete();
        return response()->json(['message'=>'xoa thanh cong']);
    }
    public function index(){
        $coupons=Coupon::all();
        return response()->json($coupons);
    }
    public function apply(Request $request){
        $validated=$request->validate([
            'code'=>'required|string',
            'total'=>'required|numeric',
        ]);
        $coupon=Coupon::where('code',$validated['code'])->first();
        if(!$coupon){
            return response()->json(['message'=>'ma giam gia khong ton tai']);
        }
        if(!$coupon->isValid()){
            return response()->json(['message'=>'ma giam gia da het han hoac het luot su dung']);
        }
        $discount= $validated['total']* ($coupon ->percent/100 );
        $discount=min($discount,$validated['total']);

        $finaltotal = $validated['total'] - $discount;
        return response()->json([
            'message'=>'giam gia thanh cong',
            'original_total' => $validated['total'], 
            'discount' => $discount,           
            'final_total' => $finaltotal,       
            'coupon_id' => $coupon->id,
            'coupon_code'=>$coupon->code,
        ]);
    }
}
