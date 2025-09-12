<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Pet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class CartController extends Controller
{
    private function getCartKey()
    {
        if (Auth::check()) {
            return "cart_user_" . Auth::id();
        } else {
            return "cart_khach_" . request()->ip();
        }
    }

//redis
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'pet_id'     => 'nullable|exists:pets,id',
            'quantity'   => 'required|integer|min:1',
            'image'      => 'nullable|string',
        ]);
        $key = $this->getCartKey();
        $cartJson = Redis::get($key);
        $cart = $cartJson ? json_decode($cartJson, true) : [];

        $quantity = $request->input('quantity');

        if ($request->filled('product_id')) {
            $productId = $request->input('product_id');
            $itemKey = "product_" . $productId;
            $product = Product::findOrFail($productId);

            $curentQuantity = isset($cart[$itemKey]) ? $cart[$itemKey]['quantity'] : 0;
            $newQuantity = $curentQuantity + $quantity;

            if($newQuantity > $product -> stock){
            return response()->json([
                'message'=>'Số lượng sản phẩm không đủ',
                'stock' => $product->stock,
            ],400);
            }

            if (isset($cart[$itemKey])) {
                $cart[$itemKey]['quantity'] = $newQuantity;
            } else {
                $cart[$itemKey] = [
                    'product_id' => $productId,
                    'pet_id'     => null,
                    'quantity'   => $quantity,
                    'name'       => $product->name,
                    'price'      => $product->price,
                    'image'      => $product->image,
                ];
            }
        }

        if ($request->filled('pet_id')) {
            $petId = $request->input('pet_id');
            $itemKey = "pet_" . $petId;
            $pet = Pet::findOrFail($petId);

            $curentQuantity = isset($cart[$itemKey]) ? $cart[$itemKey]['quantity'] : 0;
            $newQuantity = $curentQuantity + $quantity;

            if($newQuantity > $pet -> stock){
            return response()->json([
                'message'=>'Số lượng sản phẩm không đủ',
                'stock' => $pet->stock,
            ],400);
            }

            if (isset($cart[$itemKey])) {
                $cart[$itemKey]['quantity'] = $newQuantity;
            } else {
                $cart[$itemKey] = [
                    'product_id' => null,
                    'pet_id'     => $petId,
                    'quantity'   => $quantity,
                    'name'       => $pet->name,
                    'price'      => $pet->price,
                    'image'      => $pet->image,
                ];
            }
        }

        Redis::set($key, json_encode($cart));
        return response()->json(['message' => 'Thêm vào giỏ hàng thành công']);
    }
    public function getCart()
{
    $key = $this->getCartKey();
    $cartJson = Redis::get($key);
    $cart = $cartJson ? json_decode($cartJson, true) : [];

    $items = [];
    foreach ($cart as $item) {
        $items[] = [
            'id'       => $item['product_id'] ?? $item['pet_id'],
            'quantity' => $item['quantity'],
            'product'  => $item['product_id'] ? [
                'id'    => $item['product_id'],
                'name'  => $item['name'],
                'price' => $item['price'],
                'image' => $item['image'],
            ] : null,
            'pet'  => $item['pet_id'] ? [
                'id'    => $item['pet_id'],
                'name'  => $item['name'],
                'price' => $item['price'],
                'image' => $item['image'],
            ] : null,
        ];
    }

    return response()->json($items);
}
    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|integer',
            'pet_id'     => 'nullable|integer',
            'quantity'   => 'required|integer|min:1'
        ]);

        $key = $this->getCartKey();
        $cartJson = Redis::get($key);
        $cart = $cartJson ? json_decode($cartJson, true) : [];

        $quantity = $request->input('quantity');

        $itemKey = $request->filled('product_id')
            ? "product_" . $request->input('product_id')
            : "pet_" . $request->input('pet_id');

        if(!isset($cart[$itemKey])){
            return response()->json(['message'=>'Không tìm thấy sản phẩm/thú cưng trong giỏ hàng'],404);
        }

        if($request->filled('product_id')){
            $product = Product::find($request->input('product_id'));
            if(!$product) return response()->json(['message'=>'khong tim thay san pham trong gio hang'],404);
            if ($quantity > $product->stock) {
        return response()->json([
            'message' => 'Số lượng vượt quá tồn kho sản phẩm',
            'available_stock' => $product->stock
        ], 400);
        }
        }
        
        if($request->filled('pet_id')){
            $pet = Product::find($request->input('pet_id'));
            if(!$pet) return response()->json(['message'=>'khong tim thay san pham trong gio hang'],404);
            if ($quantity > $pet->stock) {
        return response()->json([
            'message' => 'Số lượng vượt quá tồn kho sản phẩm',
            'available_stock' => $pet->stock
        ], 400);
        }
        }

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] = $quantity;
            Redis::set($key, json_encode($cart));
            return response()->json(['message' => 'Cập nhật giỏ hàng thành công']);
        }

        return response()->json(['message' => 'Không tìm thấy sản phẩm/thú cưng trong giỏ hàng'], 404);
    }

    public function removeFromCart(Request $request)
    {
        $key = $this->getCartKey();
        $cartJson = Redis::get($key);
        $cart = $cartJson ? json_decode($cartJson, true) : [];

        $itemKey = $request->filled('product_id')
            ? "product_" . $request->input('product_id')
            : "pet_" . $request->input('pet_id');

        if (isset($cart[$itemKey])) {
            unset($cart[$itemKey]);
            Redis::set($key, json_encode($cart));
            return response()->json(['message' => 'Xoá khỏi giỏ hàng thành công']);
        }

        return response()->json(['message' => 'Không tìm thấy sản phẩm/thú cưng trong giỏ hàng'], 404);
    }

    public function clearCart()
    {
        $key = $this->getCartKey();
        Redis::del($key);
        return response()->json(['message' => 'Xoá giỏ hàng thành công']);
    }
//db
    public function addToCartDB(Request $request)
{
    $userId = Auth::id();
    if (!$userId) {
        return response()->json(['message' => 'Chưa đăng nhập'], 401);
    }

    $request->validate([
        'product_id' => 'nullable|exists:products,id',
        'pet_id'     => 'nullable|exists:pets,id',
        'quantity'   => 'required|integer|min:1',
        'image'      => 'nullable|string'
    ]);
    $productId = $request->input('product_id');
    $image = $request->input('image');
    $petId     = $request->input('pet_id');
    $quantity  = $request->input('quantity', 1);
    $cartItem = CartItem::where('user_id', $userId)
        ->where('product_id', $productId)
        ->where('pet_id', $petId)
        ->first();

    $newQuantity = $cartItem ? $cartItem->quantity + $quantity : $quantity;
    if ($productId) {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }
        if ($newQuantity > $product->stock) {
            return response()->json([
                'message' => 'Số lượng sản phẩm không đủ',
                'stock'   => $product->stock
            ], 400);
        }
    } elseif ($petId) {
        $pet = Pet::find($petId);
        if (!$pet) {
            return response()->json(['message' => 'Không tìm thấy thú cưng'], 404);
        }
        if ($newQuantity > $pet->stock) {
            return response()->json([
                'message' => 'Số lượng thú cưng không đủ',
                'stock'   => $pet->stock
            ], 400);
        }
    } else {
        return response()->json(['message' => 'Thiếu product_id hoặc pet_id'], 422);
    }
    if ($cartItem) {
        $cartItem->quantity = $newQuantity;
        $cartItem->save();
    } else {
        CartItem::create([
            'user_id'    => $userId,
            'product_id' => $productId,
            'pet_id'     => $petId,
            'quantity'   => $quantity,
            'image' => $image
        ]);
    }

    return response()->json(['message' => 'Đã thêm vào giỏ hàng']);
}
    public function getCartDB()
    {
        $items = 
        CartItem::with(['product:id,name,price,image', 'pet:id,name,price,image'])
            ->where('user_id', Auth::id())
            ->get();
        return response()->json($items);
    }

   public function updateCartDB(Request $request)
{
    $request->validate([
        'product_id' => 'nullable|integer',
        'pet_id'     => 'nullable|integer',
        'quantity'   => 'required|integer|min:1'
    ]);

    $userId    = Auth::id();
    $productId = $request->input('product_id');
    $petId     = $request->input('pet_id');
    $quantity  = $request->input('quantity');

    $cartItem = CartItem::where('user_id', $userId)
        ->where('product_id', $productId)
        ->where('pet_id', $petId)
        ->first();

    if (!$cartItem) {
        return response()->json(['message' => 'Không tồn tại trong giỏ hàng'], 404);
    }
    if ($productId) {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }
        if ($quantity > $product->stock) {
            return response()->json([
                'message' => 'Số lượng sản phẩm không đủ',
                'stock'   => $product->stock
            ], 400);
        }
    } elseif ($petId) {
        $pet = Pet::find($petId);
        if (!$pet) {
            return response()->json(['message' => 'Không tìm thấy thú cưng'], 404);
        }
        if ($quantity > $pet->stock) {
            return response()->json([
                'message' => 'Số lượng thú cưng không đủ',
                'stock'   => $pet->stock
            ], 400);
        }
    } else {
        return response()->json(['message' => 'Thiếu product_id hoặc pet_id'], 422);
    }
    if ($quantity > 0) {
        $cartItem->quantity = $quantity;
        $cartItem->save();
        return response()->json(['message' => 'Cập nhật số lượng thành công']);
    } else {
        $cartItem->delete();
        return response()->json(['message' => 'Đã xoá sản phẩm']);
    }
}

    public function removeFromCartDB(Request $request)
    {
        CartItem::where('user_id', Auth::id())
            ->where('product_id', $request->input('product_id'))
            ->where('pet_id', $request->input('pet_id'))
            ->delete();

        return response()->json(['message' => 'Xóa sản phẩm thành công']);
    }

    public function clearCartDB()
    {
        CartItem::where('user_id', Auth::id())->delete();
        return response()->json(['message' => 'Xoá giỏ hàng thành công']);
    }

    // ===================== SYNC =====================
    public function syn(Request $request)
{
    $userId = Auth::id();
    if (!$userId) {
        return response()->json(['message' => 'Chưa đăng nhập'], 401);
    }

    $guestCartKey = "cart_khach_" . $request->ip();
    $cartJson = Redis::get($guestCartKey);
    $cart = $cartJson ? json_decode($cartJson, true) : [];

    if (empty($cart)) {
        return response()->json(['message' => 'Giỏ hàng trống']);
    }
    foreach ($cart as $itemKey => $item) {
        $productId = $item['product_id'] ?? null;
        $image = $item['image'] ?? null;
        $petId     = $item['pet_id'] ?? null;
        $quantity  = $item['quantity'] ?? 1;
        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('pet_id', $petId)
            ->first();
        $maxStock = null;
        if ($productId) {
            $product = Product::find($productId);
            if (!$product) continue; 
            $maxStock = $product->stock;
        } elseif ($petId) {
            $pet = Pet::find($petId);
            if (!$pet) continue; 
            $maxStock = $pet->stock;
        }

        if ($maxStock === null) continue;

        if ($cartItem) {
            $newQty = $cartItem->quantity + $quantity;
            $cartItem->quantity = min($newQty, $maxStock); 
            $cartItem->save();
        } else {
            CartItem::create([
                'user_id'    => $userId,
                'product_id' => $productId,
                'pet_id'     => $petId,
                'quantity'   => min($quantity, $maxStock),
                'image' =>$image
            ]);
        }
    }

    Redis::del($guestCartKey);
    return response()->json(['message' => 'Đồng bộ giỏ hàng thành công']);
}
}
