<h2>🐾 Xin chào <?php echo e($bill->customer_name); ?>!</h2>
z
<p>Cảm ơn bạn đã đặt hàng tại hệ thống HmmPetShop:</p>
<ul>
    <li>Với số điện thoại <?php echo e($bill->phone); ?></li>
    <li>Thành Phố: <?php echo e($bill->city); ?></li>
    <li>Đường: <?php echo e($bill->district); ?></li>
    <li>Phường: <?php echo e($bill->ward); ?></li>
    <li>Địa chỉ chi tiết: <?php echo e($bill->description); ?></li>
    <li>Hình thức thanh toán: <?php echo e($bill->payment_method); ?></li>
    <li>Mã giảm giá: <?php echo e($bill->id|| 'khong co'); ?></li>
    <li>Số tiền giảm: <?php echo e($bill->discount || 'khong co'); ?></li>
    <li>Tổng tiền: <?php echo e($bill->total_price); ?></li>
</ul>
<?php /**PATH C:\xampp\htdocs\canhan\backend\resources\views/Thankforbill.blade.php ENDPATH**/ ?>