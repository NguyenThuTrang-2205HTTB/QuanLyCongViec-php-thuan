<?php
session_start();
require_once __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user_id'])) header('Location: ../auth/login.php');

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: ../dashboard.php'); exit; }

$stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$task) { header('Location: ../dashboard.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? null;
    $status = isset($_POST['status']) ? 1 : 0;
    if ($title === '') $errors[] = 'Tiêu đề bắt buộc.';
    if (!$errors) {
        $stmt = $pdo->prepare('UPDATE tasks SET title=?, description=?, due_date=?, status=? WHERE id=? AND user_id=?');
        $stmt->execute([$title, $description, $due_date, $status, $id, $_SESSION['user_id']]);
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
  <title>Sửa công việc</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
   background: linear-gradient(135deg,  #e17ae7ff, #c6a6e2ff);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .form-control, .form-check-input {
      border-radius: 0.5rem;
    }
    .btn-primary {
      border-radius: 0.5rem;
      padding: 0.5rem 1.5rem;
    }
    .alert {
      border-radius: 0.5rem;
    }
    .container {
      max-width: 600px;
    }
  </style>
</head>
<body>
<div class="container py-5">
  <a href="../dashboard.php" class="btn btn-outline-secondary mb-4">← Quay lại</a>
  <div class="card p-4 bg-white">
    <h4 class="card-title mb-4 text-center">Sửa công việc</h4>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <?php echo implode('<br>', $errors); ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label fw-semibold">Tiêu đề</label>
        <input class="form-control" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Mô tả</label>
        <textarea class="form-control" name="description" rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Hạn</label>
        <input type="date" class="form-control" name="due_date" value="<?php echo $task['due_date']; ?>">
      </div>
      <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" name="status" id="status" <?php if($task['status']) echo 'checked'; ?>>
        <label class="form-check-label fw-semibold" for="status">Hoàn thành</label>
      </div>
      <div class="d-grid">
        <button class="btn btn-primary">Lưu</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>
