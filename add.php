<?php
session_start();
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id'])) header('Location: ../auth/login.php');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? null;
    if ($title === '') $errors[] = 'Tiêu đề bắt buộc.';
    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO tasks (user_id, title, description, due_date, status) VALUES (?, ?, ?, ?, 0)');
        $stmt->execute([$_SESSION['user_id'], $title, $description, $due_date]);
        header('Location: ../dashboard.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Thêm công việc</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #74ebd5, #ACB6E5);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .card {
      width: 100%;
      max-width: 520px;
      border: none;
      border-radius: 15px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
      animation: fadeIn 0.4s ease;
    }
    .card-body {
      padding: 2rem;
    }
    .btn-primary {
      background: #4e73df;
      border: none;
      transition: 0.3s;
    }
    .btn-primary:hover {
      background: #3757c8;
      transform: translateY(-1px);
    }
    .form-control:focus {
      border-color: #4e73df;
      box-shadow: 0 0 0 0.25rem rgba(78,115,223,0.25);
    }
    .back-btn {
      color: #4e73df;
      text-decoration: none;
      font-weight: 500;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: 0.2s;
    }
    .back-btn:hover {
      color: #2e59d9;
      transform: translateX(-3px);
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="card-body">
      <a href="../dashboard.php" class="back-btn mb-3">
        ← Quay lại
      </a>
      <h3 class="text-center mb-4">📝 Thêm công việc mới</h3>
      <?php if ($errors): ?>
        <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
          <input class="form-control" name="title" placeholder="Nhập tiêu đề công việc..." required>
        </div>
        <div class="mb-3">
          <label class="form-label">Mô tả</label>
          <textarea class="form-control" name="description" rows="3" placeholder="Thêm mô tả chi tiết..."></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Hạn hoàn thành</label>
          <input type="date" class="form-control" name="due_date">
        </div>
        <button class="btn btn-primary w-100 py-2">Thêm công việc</button>
      </form>
    </div>
  </div>
</body>
</html>
