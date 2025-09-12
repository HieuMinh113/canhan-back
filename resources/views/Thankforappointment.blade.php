<h2>🐾 Xin chào {{ $appointment->owner }}!</h2>

<p>Bạn đã đặt lịch khám thành công cho thú cưng:</p>

<ul>
  <li><strong>Tên thú cưng:</strong> {{ $appointment->name }}</li>
  <li><strong>Dich vu:</strong> {{ $appointment->service->name }}</li>
  <li><strong>Ngày:</strong> {{ $appointment->date }}</li>
  <li><strong>Giờ:</strong> {{ $appointment->time }}</li>
</ul>

<p>Chúng tôi sẽ liên hệ lại nếu cần xác nhận thêm.</p>

<p>Trân trọng,<br>Hmm PetStore</p>
