<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome Staff</title>
</head>
<body>
    <h1>Chào mừng {{ $user->name }} đến với hệ thống Hmm petshop</h1>
    <h2>Email: {{ $user->email }}</h2>
    <h2>Vai trò: {{ ucfirst($user->role) }}</h2>
    <p>Password mặc định:123456</p>
    <p>Cảm ơn bạn đã ứng tuyển đến hệ thống Hmm petshop</p>
    <p>Chúc bạn một ngày tốt lành!</p>
</body>
</html>
