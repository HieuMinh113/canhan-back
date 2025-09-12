<?php

namespace App\Http\Controllers;

use App\Models\BookingHotel;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingHotelController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'phone' => 'required|string|min:1',
            'email' => 'required|email',
            'name' => 'required|string|max:255',
        ]);
        $check_in_time = Carbon::parse($validated['check_in']);
        $check_out_time = Carbon::parse($validated['check_out']);
        $start = Carbon::parse('08:00');
        $end = Carbon::parse('20:00');
        if ($check_in_time->lt($start) || $check_in_time->gt($end)) {
        return response()->json(['message' => 'Giờ check-in phải từ 08:00 đến 20:00'], 422);
        }
        if ($check_out_time->lt($start) || $check_out_time->gt($end)) {
            return response()->json(['message' => 'Giờ check-out phải từ 08:00 đến 20:00'], 422);
        }


        $hotel = Hotel::findOrFail($validated['hotel_id']);
        $nights = Carbon::parse($validated['check_in'])->diffInDays(Carbon::parse($validated['check_out']));
        $total_price = $hotel->price * max(1, $nights); 
        $bookinghotel = BookingHotel::create([
            'hotel_id' => $validated['hotel_id'],
            'name' => $validated['name'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'check_in_time' => $validated['check_in_time'],
            'check_out_time' => $validated['check_out_time'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'total_price' => $total_price,
            'handled'=>false,
        ]);
        return response()->json([
            'message' => 'Đặt phòng thành công',
            'bookinghotel' => $bookinghotel,   
        ]);
    }
    public function update(Request $request, $id)
    {
    $bookinghotel = BookingHotel::findOrFail($id);
    $validated = $request->validate([
        'handled' => 'required|boolean',
    ]);
    $bookinghotel->handled = $validated['handled'];
    $bookinghotel->save();
    return response()->json(['message' => 'Lịch hẹn đã được cập nhật']);
    }
    public function index(Request $request)
    {
        $handled=$request->query('handled');
        $check_in=$request->query('check_in');
        $check_out=$request->query('check_out');
        $query=BookingHotel::with('hotel:id,name');
        if($request->filled('handled')){
            $query->where('handled',$handled);
        }
        if($request->filled('check_in')){
            $query->where('check_in',$check_in);
        }
        if($request->filled('check_out')){
            $query->where('check_out',$check_out);
        }
        return response()->json($query->latest()->get());
    }
    public function show($id)
    {
        $bookinghotel = BookingHotel::with('hotel')->find($id);
        if (!$bookinghotel) {
            return response()->json(['message' => 'Không tìm thấy khách sạn'], 404);
        }
        return response()->json($bookinghotel);
    }
}
