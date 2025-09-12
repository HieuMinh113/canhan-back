<?php

namespace App\Http\Controllers;

use App\Events\WorkCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkSchedule;


class WorkScheduleController extends Controller
{
    public function store(Request $request){
        
        $request->validate([
            'dates'=>'required|array',
            'dates.*'=>'required|date',
            'status'=>'required|string|in:work,off'
        ]);

        $user=Auth::user();
        $error=[];
        $workSchedules =[];
        foreach ($request->dates as $date) {
    if ($request->status == 'off') {
        $exists = WorkSchedule::where('date', $date)
            ->whereHas('user', function ($query) use ($user) {
                $query->where('role', $user->role);
            })
            ->where('status', 'off')
            ->exists();
        if ($exists) {
            $error[] = "Người có vai trò giống bạn đã đăng ký nghỉ vào ngày $date.";
            continue;
        }
    }

    $schedule = WorkSchedule::updateOrCreate(
        [
            'user_id' => $user->id,
            'date'    => $date,
        ],
        [
            'status'  => $request->status,
        ]
    );

    $workSchedules[] = $schedule;
    }


    if (count($workSchedules)) {
    event(new WorkCreated($workSchedules));
    }
        if (count($error)) {
            return response()->json(['error' => $error], 422);
        }
        return response()->json(['message' => 'Đăng ký thành công']);
    }
    public function index(){
    $user = Auth::user();
    if ($user->role == 'admin') {  
        $workSchedules = WorkSchedule::with('user:id,name,role')
            ->orderBy('created_at', 'desc')
            ->get();
    } elseif ($user->role == 'staff'||$user->role == 'doctor') { 
        $workSchedules = WorkSchedule::where('user_id', $user->id)
            ->with('user:id,name,role')
            ->orderBy('created_at', 'desc')
            ->get();
    } else {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    return response()->json($workSchedules);
    }
}
