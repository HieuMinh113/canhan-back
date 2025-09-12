<h2>🐾 Xin chào <?php echo e($appointment->owner); ?>!</h2>

<p>Bạn đã đặt lịch khám thành công cho thú cưng:</p>

<ul>
  <li><strong>Tên thú cưng:</strong> <?php echo e($appointment->name); ?></li>
  <li><strong>Dich vu:</strong> <?php echo e($appointment->service); ?></li>
  <li><strong>Ngày:</strong> <?php echo e($appointment->date); ?></li>
  <li><strong>Giờ:</strong> <?php echo e($appointment->time); ?></li>
</ul>

<p>Chúng tôi sẽ liên hệ lại nếu cần xác nhận thêm.</p>

<p>Trân trọng,<br>Hmm PetStore</p>
<?php /**PATH C:\xampp\htdocs\canhan\backend\resources\views/Thankforappointment.blade.php ENDPATH**/ ?>