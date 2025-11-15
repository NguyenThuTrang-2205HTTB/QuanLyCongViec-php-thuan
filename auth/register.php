<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? null);
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validate form
    if (!$username || !$password || !$confirm) {
        $errors[] = 'Vui lòng điền đầy đủ thông tin.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Mật khẩu xác nhận không khớp.';
    }

    if (!$errors) {
        // Kiểm tra username/email đã tồn tại chưa
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR (email IS NOT NULL AND email = ?)');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = 'Tên đăng nhập hoặc email đã tồn tại.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$username, $email ?: null, $hash]);

            // Lưu session và redirect
            $_SESSION['user_id'] = $pdo->lastInsertId();
            header('Location: ../dashboard.php');
            exit;
        }
    }
}
?>


<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Đăng ký tài khoản - App Quản Lý Công Việc</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #e17ae7ff, #c6a6e2ff);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .register-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      padding: 2.5rem;
      max-width: 450px;
      width: 100%;
    }
    .brand {
      text-align: center;
      margin-bottom: 1.5rem;
    }
    .brand i {
      font-size: 2.5rem;
      color: #5563DE;
    }
    .brand h4 {
      margin-top: 0.5rem;
      font-weight: 600;
    }
    .btn-primary {
      background-color: #5563DE;
      border-color: #5563DE;
      font-weight: 500;
    }
    .btn-primary:hover {
      background-color: #4451c7;
    }
    a {
      color: #5563DE;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="register-card">
    <div class="brand">
      <i class="bi bi-person-plus"></i>
      <h4>Tạo tài khoản mới</h4>
      <p class="text-muted">Quản lý công việc cùng App Quản Lý Công Việc</p>
    </div>

    <?php if ($errors): ?>
      <div class="alert alert-danger py-2">
        <?= implode('<br>', $errors); ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label">Tên đăng nhập</label>
        <input class="form-control form-control-lg" name="username" placeholder="Tên đăng nhập" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control form-control-lg" name="email" placeholder="example@email.com" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Mật khẩu</label>
        <input type="password" class="form-control form-control-lg" name="password" placeholder="Nhập mật khẩu" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Xác nhận mật khẩu</label>
        <input type="password" class="form-control form-control-lg" name="confirm" placeholder="Nhập lại mật khẩu" required>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="login.php">Đã có tài khoản? Đăng nhập</a>
      </div>
      <button class="btn btn-primary w-100 btn-lg" type="submit">Đăng ký</button>
    </form>
  </div>

  <!-- Bootstrap icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>