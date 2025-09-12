<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Events\AppointmentCreated;
use App\Models\Service;
use Carbon\Carbon;


class AppointmentController extends Controller
{
    public function index(Request $request)
{
    $handled = $request->query('handled');
    // $service = $request->query('service');

    $query = Appointment::with('staff:id,name','service:id,name');

    if (!is_null($handled)) {
        $query->where('handled', $handled);
    }

    // if (!is_null($service)) {
    //     $query->where('service', $service); 
    // }
    
    return response()->json($query->latest()->get());
}
    public function store(Request $request){
        $validated=$request->validate([
            'name'=>'required|string|max:255',
            'date'=>'required|date',
            'time'=>'required',
            'service_id'=>'required|string|max:255',
            'staff_id'=>'required|exists:users,id',
            'email' => 'required|email',
            'owner'=>'required|string|max:255',
        ]);
        $time = Carbon::parse($validated['time']);
            $start = Carbon::parse('08:00');
            $end = Carbon::parse('20:00');

            if ($time->lt($start) || $time->gt($end)) {
                return response()->json(['message' => 'Giờ đặt lịch phải từ 08:00 đến 20:00'], 422);
            }
        $isoff=WorkSchedule::where('user_id',$validated['staff_id'])
        ->where('date',$validated['date'])
        ->where('status','off')
        ->exists();
        if ($isoff){
            return response()->json(['message' => 'Nhân viên này đã nghỉ vào ngày đã chọn'], 409);
        }
        $exists= Appointment::where('staff_id', $validated['staff_id'])
        ->where('date',$validated['date'])
        ->where('time',$validated['time'])
        ->exists();
        if($exists){
            return response()->json(['message'=>'nhan vien nay da co lich dat'],409);
        }
        $service = Service::where('id', $validated['service_id'])
        ->where('type', 'staff')
        ->first();

        if (!$service) {
         return response()->json(['message' => 'Không có dịch vụ staff này'], 409);
        }
        $serviceName=$service->name;
        $commission=$service->price*0.1;
        $appointment=Appointment::create([
            'name'=>$validated['name'],
            'date'=>$validated['date'],
            'time'=>$validated['time'],
            'service_id'=>$validated['service_id'],
            'staff_id'=>$validated['staff_id'],
            'email'=>$validated['email'],
            'owner'=>$validated['owner'],
            'handled' => false,
            'commission'=>$commission
        ]);
        event(new AppointmentCreated($appointment));
        return response()->json([
            'appointment'=>$appointment,
            'service_id'=>$serviceName,
        ]);
    }
    public function update(Request $request, $id)
{
    $appointment = Appointment::findOrFail($id);

    $validated = $request->validate([
        'handled' => 'required|boolean',
    ]);

    $appointment->handled = $validated['handled'];
    $appointment->save();

    return response()->json(['message' => 'Lịch hẹn đã được cập nhật']);
}
    public function getAppointments($id)
    {
        $appointments = Appointment::with(['staff','service'])->where('staff_id', $id)
        
        ->get();
        return response()->json($appointments);
    }
    public function show($id){
        $appointment = Appointment::findOrFail($id);
        return response()->json($appointment);
    }
    public function staff(){
        $staff=User::where('role','staff')->select('id','name')->get();
        return response()->json($staff);
    }
    public function service(){
        $serviceName=Service::where('type','staff')->select('id','name')->get();
        return response()->json($serviceName);
    }
    // public function commissionstaff(){
    //     $data = Appointment::select('staff_id',DB::raw('SUM(commission) as total_commission'))
    //     ->groupBy('staff_id')
    //     ->with('staff:id,name')
    //     ->get();
    //     return response()->json($data);
    // }
}
