<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Feedback;
use App\Models\Booking;
use App\Models\Appointment;
use App\Models\BookingHotel;
use App\Models\Bill;
use App\Models\Contact;
use App\Models\Service;
use App\Models\Pet;
use App\Models\Hotel;



class DashBoard extends Controller
{
    public function index(){
        return response()->json([
            'totalUser'=>User::count(),
            'totalProduct'=>Product::count(),
            'totalFeedback'=>Feedback::count(),
            'totalContact'=>Contact::count(),
            'totalBooking'=>Booking::count(),
            'totalAppointment'=>Appointment::count(), 
            'totalBookingHotel'=>BookingHotel::count(),
            'totalService'=>Service::count(),
            'totalBill'=>Bill::count(), 
            'totalPet'=>Pet::count(),
            'totalHotel'=>Hotel::count(),
    ]);
    }
    // public function bag(){
    //     $count = Product::select('product_id', DB:raw());
    // }
    public function commissionsAll()
{
    $staffCommissions = Appointment::select('staff_id', DB::raw('SUM(commission) as total_commission'))
        ->groupBy('staff_id')
        ->with(['staff:id,name'])
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->staff_id,
                'name' => $item->staff->name,
                'role' => 'staff',
                'total_commission' => $item->total_commission
            ];
        });
    $doctorCommissions = Booking::select('doctor_id', DB::raw('SUM(commission) as total_commission'))
        ->groupBy('doctor_id')
        ->with(['doctor:id,name'])
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->doctor_id,
                'name' => $item->doctor->name,
                'role' => 'doctor',
                'total_commission' => $item->total_commission
            ];
        });

    $data = $staffCommissions->merge($doctorCommissions)->values();

    return response()->json($data);
    }
    public function total(){
        $total = Bill::sum('total_price');
        return response()->json(['total_bill_price' => $total]);
    }
public function totalbill()
{
    $productTotal = 0;
    $petTotal = 0;
    $serviceTotal = 0;

    $bills = Bill::where('status', 'handled')
        ->with(['products', 'pets', 'services'])
        ->get();

    foreach ($bills as $bill) {
        foreach ($bill->products as $product) {
            $productTotal += $product->pivot->quantity * $product->price;
        }
        foreach ($bill->pets as $pet) {
            $petTotal += $pet->pivot->quantity * $pet->price;
        }
        foreach ($bill->services as $service) {
            $serviceTotal += $service->price; 
        }
    }
    return response()->json([
        'total_product' => $productTotal,
        'total_pet' => $petTotal,
        'total_service' => $serviceTotal,
    ]);
}
    public function totalproduct(){
    $products = Product::select('category',DB::raw('COUNT(*) as total'))
    ->groupBy('category')
    ->get();
    return response()->json($products);
    }
    public function totalpet(){
        $pets = Pet::select('type',DB::raw('COUNT(*) as total'))
        ->groupBy('type')
        ->get();
        return response()->json($pets);
    }
    public function totaluser(){
        $users = User::select('role',DB::raw('COUNT(*) as total'))
        ->groupBy('role')
        ->get();
        return response()->json($users);
    }
    public function totalhotel(){
        $hotels = Hotel::select('category',DB::raw('COUNT(*) as total'))
        ->groupBy('category')
        ->get();
        return response()->json($hotels);
    }
    public function bestsellerproduct(){
        $bestSeller = Product::select('products.*',DB::raw('SUM(bill_products.quantity) as total_quantity'))
        ->join('bill_products','products.id','=','bill_products.product_id')
        ->join('bills','bills.id','=','bill_products.bill_id')
        ->where('bills.status','handled')
        ->groupBy('products.id')
        ->orderBy(DB::raw('SUM(bill_products.quantity)'), 'desc')
        ->take(4)
        ->get();
        return response()->json($bestSeller);
    }
    public function bestsellerpet(){
        $bestSeller = Pet::select('pets.*',DB::raw('SUM(bill_pets.quantity) as total_quantity'))
        ->join('bill_pets','pets.id','=','bill_pets.pet_id')
        ->join('bills','bills.id','=','bill_pets.bill_id')
        ->where('bills.status','handled')
        ->groupBy('pets.id')
        ->orderBy(DB::raw('SUM(bill_pets.quantity)'), 'desc')
        ->take(4)
        ->get();
        return response()->json($bestSeller);
    }

}
