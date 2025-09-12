<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Event;
use App\Events\AddAgent;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{ 
public function index(Request $request)
{
    $query = User::query();

    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }
    return response()->json($query->get());
}

    // public function index(Request $request)
    // {
        
        // $user = User::leftJoin('user_profiles', 'users.id', 'user_profiles.user_id')
        // ->select('users.id','users.name','users.email','users.role','user_profiles.phone');
        
        // if (!empty($phone)) {
        //     $user->where('user_profiles.phone', '123123123');
        // }

        // $user->get();
        // return response()->json($user);
        
        // $user = User::with('UserProfile')
                // ->whereHas('UserProfile', function ($query) {
                //     $query->where('phone', '123123123');
                // })  
                // ->get();
        
    //     return response()->json($query->get());
    // }
    public function getuser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Người dùng không tồn tại'], 404);
        }
        return response()->json($user);
    }
    public function showStaff($id){
        try {
             $staff=User::select('id','name','email','role','created_at')
            ->where('id',$id)
            ->where('role','!=','user')
            ->first();
            if(!$staff){
                return response()->json(['message'=>'khong tim thay nhan vien'],404);
            }
            return response()->json($staff);
        } catch (\Exception $e) {
            return response()->json([
                'message'=>'loi serve',
                'error'=>$e->getMessage()
            ],500);
        }
    }
    
    public function showDoctor($id){
        try {
             $doctor=User::select('id','name','email','role','created_at')
            ->where('id',$id)
            ->where('role','!=','user')
            ->first();
            if(!$doctor){
                return response()->json(['message'=>'khong tim thay bac si'],404);
            }
            return response()->json($doctor);
        } catch (\Exception $e) {
            return response()->json([
                'message'=>'loi serve',
                'error'=>$e->getMessage()
            ],500);
        }
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,staff,doctor',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role'     => $validated['role'],
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        event(new AddAgent($user,$token));
        return response()->json([
            'message' => 'Thêm người dùng thành công',
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|min:6',
            'role'     => 'required|in:admin,staff,doctor',
        ]);
        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->role = $validated['role'];
        $user->save();

        return response()->json([
            'message' => 'Cập nhật thành công',
            'user'    => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return response()->json([
            'message' => 'Xoá người dùng thành công'
        ]);
    }
    
}
