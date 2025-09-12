<h2>🐾 Xin chào {{ $bill->customer_name }}!</h2>
z
<p>Cảm ơn bạn đã đặt hàng tại hệ thống HmmPetShop:</p>
<ul>
    <li>Với số điện thoại {{ $bill->phone}}</li>
    <li>Thành Phố: {{ $bill->city }}</li>
    <li>Đường: {{ $bill->district }}</li>
    <li>Phường: {{ $bill->ward }}</li>
    <li>Địa chỉ chi tiết: {{ $bill->description}}</li>
    <li>Hình thức thanh toán: {{ $bill->payment_method}}</li>
    <li>Mã giảm giá: {{ $bill->coupon_id ?? 'khong co'}}</li>
    <li>Số tiền giảm: {{ $bill->discount ?? 'khong co'}}</li>
    <li>Tổng tiền: {{ $bill->total_price}}</li>
</ul>
