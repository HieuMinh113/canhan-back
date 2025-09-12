<h2>🐾 Xin chào <?php echo e($booking->owner); ?>!</h2>

<p>Bạn đã đặt lịch khám thành công cho thú cưng:</p>

<ul>
  <li><strong>Tên thú cưng:</strong> <?php echo e($booking->name); ?></li>
  <li><strong>Loại:</strong> <?php echo e($booking->type); ?></li>
  <li><strong>Ngày:</strong> <?php echo e($booking->date); ?></li>
  <li><strong>Giờ:</strong> <?php echo e($booking->time); ?></li>
</ul>

<p>Chúng tôi sẽ liên hệ lại nếu cần xác nhận thêm.</p>

<p>Trân trọng,<br>Hmm PetStore</p>
<?php /**PATH C:\xampp\htdocs\canhan\backend\resources\views/thankforbooking.blade.php ENDPATH**/ ?>