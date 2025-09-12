<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Pet;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Events\BillCreated; 
use App\Services\BillService;
use App\Services\LoyaltyService;
use App\Models\User;

class BillController extends Controller
{
    protected $billService;
    public function __construct(BillService $billService)
    {
        $this->billService = $billService;
    }
    public function index()
    {
        return Bill::with(['services','products','creator','pets','coupon:id,code','hotels'])->get();
    }
    public function store(Request $request , LoyaltyService $loyalty)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'phone' => 'nullable|string',
            'city'=>'nullable|string',
            'district'=>'nullable|string',
            'ward'=>'nullable|string',
            'description' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'payment_type' => 'required|string',
            'services' => 'nullable|array',
            'services.*.id' => 'required_with:services|exists:services,id',
            'services.*.price' => 'required_with:services|numeric|min:0',
            'hotels'=>'nullable|array',
            'hotels.*.id' => 'required_with:hotels|exists:hotels,id',
            'hotels.*.price' => 'required_with:hotels|numeric|min:0',
            'pets' => 'nullable|array',
            'pets.*.id' => 'required_with:pets|exists:pets,id',
            'pets.*.price' => 'required_with:pets|numeric|min:0',
            'pets.*.quantity' => 'required_with:pets|integer|min:1',
            'products' => 'nullable|array',
            'products.*.id' => 'required_with:products|exists:products,id',
            'products.*.price' => 'required_with:products|numeric|min:0',
            'products.*.quantity' => 'required_with:products|integer|min:1',
            'coupon_id'=>'nullable|exists:coupons,id',
            'notes'=>'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $bill = Bill::create([
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'city' => $validated['city'] ?? null,
                'district' => $validated['district'] ?? null,
                'ward' => $validated['ward'] ?? null,
                'description' => $validated['description'] ?? null,
                'payment_method' => $validated['payment_method'] ?? null,
                'payment_type' => $validated['payment_type'] ,
                'status' => 'pending',
                'created_by' => Auth::id() ?? null,
                'total_price' => 0,
                'coupon_id'=>$validated['coupon_id']??null,
                'notes'=>$validated['notes']??null,
            ]);
            $total = 0;
            if (!empty($validated['services'])) {
                $serviceData = [];
                foreach ($validated['services'] as $s) {
                    $serviceData[$s['id']] = ['price' => $s['price']];
                    $total += $s['price'];
                }
                $bill->services()->attach($serviceData);
            }
            if (!empty($validated['hotels'])) {
                $hotelData = [];
                foreach ($validated['hotels'] as $h) {
                    $hotelData[$h['id']] = ['price' => $h['price']];
                    $total += $h['price'];
                }
                $bill->hotels()->attach($hotelData);
            }
            if (!empty($validated['pets'])) {
                $petData = [];
                foreach ($validated['pets'] as $pe) {
                $pet = Pet::find($pe['id']);
                if (!$pet) {
                    throw new \Exception("Pet không tồn tại");
                }
                if ($pet->stock < $pe['quantity']) {
                    throw new \Exception("Số lượng thú cưng không đủ");
                }
                    $pet->decrement('stock', $pe['quantity']);

                    $petData[$pe['id']] = [
                        'price' => $pe['price'],
                        'quantity' => $pe['quantity']
                    ];
                    $total += $pe['price'] * $pe['quantity'];
                }
                $bill->pets()->attach($petData);
            }
            
            if (!empty($validated['products'])) {
                $productData = [];
                foreach ($validated['products'] as $p) {
                    $product = Product::find($p['id']);
                    if (!$product) {
                        throw new \Exception("Sản phẩm không tồn tại");
                    }
                    if ($product->stock < $p['quantity']) {
                        throw new \Exception("Sản phẩm {$product->name} không đủ tồn kho");
                    }

                    $product->decrement('stock', $p['quantity']);

                    $productData[$p['id']] = [
                        'price' => $p['price'],
                        'quantity' => $p['quantity']
                    ];
                    $total += $p['price'] * $p['quantity'];
                }
                $bill->products()->attach($productData);
            }
            $discount=0;
            if(!empty($validated['coupon_id'])){
                $coupon=Coupon::find($validated['coupon_id']);
                if($coupon && $coupon->isValid()){
                    $discount = $total * ($coupon->percent / 100);
                    $discount = min($discount, $total);
                    $total -= $discount;
                    $coupon->increment('used');
                }else{
                    $bill->update(['coupon_id'=>null]);
                }
            }
            $user = User::where('email',$bill->customer_email)->first();
            $rank = null;
            $discountLoyalty = 0;
            if($user && $user->UserProfile){
                $profile = $user->UserProfile;
                $rank = $loyalty->calculateRank($profile);
                $discountLoyalty = $loyalty->calculateDiscount($rank);
                $total -= $discountLoyalty;
            }
            $bill->update(['total_price' => $total , 'discount' => $discount, 'discount_loyalty'=>$discountLoyalty]);
            DB::commit();
            event(new BillCreated($bill));
            return response()->json([
                'message' => 'Bill created',
                'bill' => $bill->load(['services','products','creator','pets','coupon','hotels']),
                'rank'=>$rank,
                'discount' =>$discount,
                'discount_loyalty' => $discountLoyalty
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function show($id , LoyaltyService $loyalty){
        $bill=Bill::with([
            'creator',
            'services',
            'products',
            'pets',
            'coupon:id,code',
            'hotels'
        ])->findOrFail($id);
        $user = User::where('email',$bill->customer_email)->first();
        $rank = null;
        $discountLoyalty = 0;
        if($user && $user->UserProfile){
            $profile = $user->UserProfile;
            if(!$profile){
                $profile = $user->UserProfile()->create([
                 'point' => 0  
                ]);
            }
        $rank = $loyalty->calculateRank($profile);
        $discountLoyalty = $loyalty->calculateDiscount($rank);
        }
        return response()->json([
            'bill'=>$bill,
            'rank'=>$rank,
            'discountLoyalty' => $discountLoyalty
            
        ]);
    }
    public function indexx(Request $request){
        $query=Bill::query();
        if($request->filled('search')){
        $query->where('customer_email', 'like', '%' . $request->search . '%');
       }
       return response()->json($query->get());
    }
    public function update(Request $request, $id)
{
    $bill = Bill::findOrFail($id);

    $validated = $request->validate([
        'status' => 'required|string',
    ]);

    $bill->status = $validated['status'];
    $bill->save();

    if ($bill->status === 'handled') {
        $user = User::where('email', $bill->customer_email)->first();
        if ($user && $user->UserProfile) {
            $loyalty = app(\App\Services\LoyaltyService::class);
            $profile = $user->UserProfile;
            $earned = $loyalty->calculateEarnPoint($profile->rank, $bill->total_price);
            $profile->point += $earned;
            $profile->rank = $loyalty->calculateRank($profile);
            $profile->save();
        }
    }
    return response()->json([
        'message' => 'Hóa đơn đã được cập nhật thành công',
        'status' => $bill->status
    ]);
}
    public function add(Request $request , LoyaltyService $loyalty){
        $validated = $request->validate([
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'phone' => 'required|string',
            'city'=>'required|string',
            'district'=>'required|string',
            'ward'=>'required|string',
            'description' => 'required|string',
            'payment_method' => 'required|string',
            'payment_type' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*.id' => 'required_with:services|exists:services,id',
            'services.*.price' => 'required_with:services|numeric|min:0',
            'hotels'=>'nullable|array',
            'hotels.*.id' => 'required_with:hotels|exists:hotels,id',
            'hotels.*.price' => 'required_with:hotels|numeric|min:0',
            'pets' => 'nullable|array',
            'pets.*.id' => 'required_with:pets|exists:pets,id',
            'pets.*.price' => 'required_with:pets|numeric|min:0',
            'pets.*.quantity' => 'required_with:pets|integer|min:1',
            'products' => 'nullable|array',
            'products.*.id' => 'required_with:products|exists:products,id',
            'products.*.price' => 'required_with:products|numeric|min:0',
            'products.*.quantity' => 'required_with:products|integer|min:1',
            'coupon_id'=>'nullable|exists:coupons,id',
            'notes'=>'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $bill = Bill::create([
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ,
                'phone' => $validated['phone'] ,
                'city' => $validated['city'] ,
                'district' => $validated['district'] ,
                'ward' => $validated['ward'] ,
                'description' => $validated['description'] ,
                'payment_method' => $validated['payment_method'] ,
                'payment_type' => $validated['payment_type'] ?? null ,
                'status' => 'pending',
                'created_by' => Auth::id() ?? null,
                'total_price' => 0,
                'coupon_id'=>$validated['coupon_id']??null,
                'notes'=>$validated['note']??null,
            ]);
            $total = 0;
            if (!empty($validated['services'])) {
                $serviceData = [];
                foreach ($validated['services'] as $s) {
                    $serviceData[$s['id']] = ['price' => $s['price']];
                    $total += $s['price'];
                }
                $bill->services()->attach($serviceData);
            }
            if (!empty($validated['hotels'])) {
                $hotelData = [];
                foreach ($validated['hotels'] as $h) {
                    $hotelData[$h['id']] = ['price' => $h['price']];
                    $total += $h['price'];
                }
                $bill->hotels()->attach($hotelData);
            }
            if (!empty($validated['pets'])) {
                $petData = [];
                foreach ($validated['pets'] as $pe) {
                $pet = Pet::find($pe['id']);
                if (!$pet) {
                    throw new \Exception("Pet không tồn tại");
                }
                if ($pet->stock < $pe['quantity']) {
                    throw new \Exception("Số lượng thú cưng không đủ");
                }
                    $pet->decrement('stock', $pe['quantity']);

                    $petData[$pe['id']] = [
                        'price' => $pe['price'],
                        'quantity' => $pe['quantity']
                    ];
                    $total += $pe['price'] * $pe['quantity'];
                }
                $bill->pets()->attach($petData);
            }
            
            if (!empty($validated['products'])) {
                $productData = [];
                foreach ($validated['products'] as $p) {
                    $product = Product::find($p['id']);
                    if (!$product) {
                        throw new \Exception("Sản phẩm không tồn tại");
                    }
                    if ($product->stock < $p['quantity']) {
                        throw new \Exception("Sản phẩm {$product->name} không đủ tồn kho");
                    }

                    $product->decrement('stock', $p['quantity']);

                    $productData[$p['id']] = [
                        'price' => $p['price'],
                        'quantity' => $p['quantity']
                    ];
                    $total += $p['price'] * $p['quantity'];
                }
                $bill->products()->attach($productData);
            }
            $discount=0;
            if(!empty($validated['coupon_id'])){
                $coupon=Coupon::find($validated['coupon_id']);
                if($coupon && $coupon->isValid()){
                    $discount = $total * ($coupon->percent / 100);
                    $discount = min($discount, $total);
                    $total -= $discount;
                    $coupon->increment('used');
                }else{
                    $bill->update(['coupon_id'=>null]);
                }
            }
            $user = User::where('email',$bill->customer_email)->first();
            $rank = null;
            $discountLoyalty = 0;
            if($user && $user->UserProfile){
                $profile = $user->UserProfile;
                $rank = $loyalty->calculateRank($profile);
                $discountLoyalty = $loyalty->calculateDiscount($rank);
                $total -= $discountLoyalty;
            }
            $bill->update(['total_price' => $total , 'discount' => $discount, 'discount_loyalty'=>$discountLoyalty]);
            DB::commit();
            event(new BillCreated($bill));
            return response()->json([
                'message' => 'Bill created',
                'bill' => $bill->load(['services','products','creator','pets','coupon','hotels']),
                'rank'=>$rank,
                'discount' =>$discount,
                'discount_loyalty' => $discountLoyalty
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
