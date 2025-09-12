<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Events\BookingCreated;
use App\Models\Appointment;
use App\Models\WorkSchedule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $handled = $request->query('handled');
        $query = Booking::with('doctor:id,name');
        if (!is_null($handled)) {
            $query->where('handled', $handled);
        }
        return response()->json($query->latest()->get());
    }
    public function store(Request $request){
        $validated=$request->validate([
            'name'=>'required|string|max:255',
            'type'=>'required|string|max:255',
            'owner'=>'required|string|max:255',
            'phone'=>'required|string|max:255',
            'date'=>'required|date',
            'time'=>'required',
            'doctor_id'=>'required|exists:users,id',
            'email' => 'required|email',
        ]);
        $time = Carbon::parse($validated['time']);
            $start = Carbon::parse('08:00');
            $end = Carbon::parse('20:00');

            if ($time->lt($start) || $time->gt($end)) {
                return response()->json(['message' => 'Giờ đặt lịch phải từ 08:00 đến 20:00'], 422);
            }
        $isoff=WorkSchedule::where('user_id',$validated['doctor_id'])
        ->where('date',$validated['date'])
        ->where('status','off')
        ->exists();
        if ($isoff) {
        return response()->json(['message' => 'Bác sĩ này đã nghỉ vào ngày đã chọn'], 409);
        }
        $exists= Booking::where('doctor_id', $validated['doctor_id'])
        ->where('date',$validated['date'])
        ->where('time',$validated['time'])
        ->exists();
        if($exists){
            return response()->json(['message'=>'bac si nay da co lich dat'],409);
        }
        $booking=Booking::create([
            'name'=>$validated['name'],
            'type'=>$validated['type'],
            'owner'=>$validated['owner'],
            'phone'=>$validated['phone'],
            'date'=>$validated['date'],
            'time'=>$validated['time'],
            'doctor_id'=>$validated['doctor_id'],
            'email'=>$validated['email'],
            'handled' => false,
            'commission'=>20000,
        ]);

        event(new BookingCreated($booking));
        return response()->json(['message'=>'dat lich thanh cong','data' =>$booking],201);
    }
    public function update(Request $request, $id)
{
    $booking=Booking::findOrFail($id);

    $validated = $request->validate([
        'handled' => 'required|boolean',
    ]);

    $booking->handled = $validated['handled'];
    $booking->save();

    return response()->json(['message' => 'Lịch hẹn đã được cập nhật']);
}
    public function getBookings($id)
    {
        $booking = Booking::with('doctor')->where('doctor_id', $id)->get();
        return response()->json($booking);
    }
    public function show($id){
        $booking = Booking::findOrFail($id);
        return response()->json($booking);
    }
    public function doctor(){
        $doctors=User::where('role','doctor')->select('id','name')->get();
        return response()->json($doctors);
    }
//     public function commissionsdoctor($doctorId)
// {
//     $total = Booking::where('doctor_id', $doctorId)
//         ->sum('commission');
//     return response()->json([
//         'doctor_id' => $doctorId,
//         'total_commission' => $total
//     ]);
// }
}
