<p>Xin chào {{ $user->name ?? 'bạn' }},</p>
<p>Bạn đã đổi mật khẩu thành công lúc {{ now()->format('H:i:s d/m/Y') }}.</p>
<p>Nếu bạn không thực hiện thao tác này, hãy liên hệ với chúng tôi ngay.</p>
