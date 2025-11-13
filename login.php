<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $password === '') {
        $errors[] = 'Vui lòng điền đầy đủ thông tin.';
    } else {
        $stmt = $pdo->prepare('SELECT id, password FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: ../dashboard.php');
            exit;
        } else {
            $errors[] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
        }
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Đăng nhập - ToDoListApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #74ebd5, #ACB6E5);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .login-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      padding: 2.5rem;
      max-width: 400px;
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
  <div class="login-card">
    <div class="brand">
      <i class="bi bi-check2-square"></i>
      <h4>ToDoListApp</h4>
      <p class="text-muted">Quản lý công việc hiệu quả</p>
    </div>
    <?php if ($errors): ?>
      <div class="alert alert-danger py-2">
        <?= implode('<br>', $errors); ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label">Tên đăng nhập</label>
        <input class="form-control form-control-lg" name="username" placeholder="Nhập tên đăng nhập" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Mật khẩu</label>
        <input type="password" class="form-control form-control-lg" name="password" placeholder="Nhập mật khẩu" required>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="register.php">Tạo tài khoản mới</a>
      </div>
      <button class="btn btn-primary w-100 btn-lg" type="submit">Đăng nhập</button>
    </form>
  </div>

  <!-- Bootstrap icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>