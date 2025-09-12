<h2>🐾 Xin chào {{ $booking->owner }}!</h2>

<p>Bạn đã đặt lịch khám thành công cho thú cưng:</p>

<ul>
  <li><strong>Tên thú cưng:</strong> {{ $booking->name }}</li>
  <li><strong>Loại:</strong> {{ $booking->type }}</li>
  <li><strong>Ngày:</strong> {{ $booking->date }}</li>
  <li><strong>Giờ:</strong> {{ $booking->time }}</li>
</ul>

<p>Chúng tôi sẽ liên hệ lại nếu cần xác nhận thêm.</p>

<p>Trân trọng,<br>Hmm PetStore</p>
