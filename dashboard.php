<?php
session_start();
require_once __DIR__ . '/config/db.php';
if (!isset($_SESSION['user_id'])) header('Location: auth/login.php');

// fetch user
$stmt = $pdo->prepare('SELECT id, username FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Nhận tham số lọc và sắp xếp
$status_filter = $_GET['status'] ?? 'all'; // all, pending, done
$order = $_GET['order'] ?? 'asc'; // asc, desc

// Tạo câu truy vấn
$where = 'WHERE user_id = ?';
$params = [$_SESSION['user_id']];

if ($status_filter === 'done') $where .= ' AND status = 1';
elseif ($status_filter === 'pending') $where .= ' AND status = 0';

$order_sql = ($order === 'desc') ? 'DESC' : 'ASC';
$sql = "SELECT * FROM tasks $where ORDER BY 
        CASE WHEN due_date IS NULL THEN 1 ELSE 0 END, 
        due_date $order_sql, id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard - App Quản Lý Công Việc</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* ==== NỀN CHUNG ==== */
    body {
      background: linear-gradient(to right, #e17ae7ff, #c6a6e2ff);
      min-height: 100vh;
      color: #333;
      font-family: "Segoe UI", sans-serif;
    }

    /* ==== THANH NAV ==== */
    .navbar {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* ==== KHUNG DANH SÁCH CÔNG VIỆC ==== */
    .bg-container {
      background: linear-gradient(145deg, #ffffff, #f6f7fb);
      border-radius: 20px;
      padding: 2.5rem;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
      position: relative;
      overflow: hidden;
      transition: all 0.4s ease;
    }

    /* Hiệu ứng ánh sáng mềm */
    .bg-container::before {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.5), transparent 60%);
      opacity: 0;
      transition: opacity 0.6s ease;
      z-index: 0;
      pointer-events: none; /* ✅ Cho phép click bình thường */
    }

    .bg-container:hover::before {
      opacity: 1;
    }

    .bg-container:hover {
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
      transform: translateY(-3px);
    }

    /* ==== TIÊU ĐỀ ==== */
    .bg-container h3 {
      position: relative;
      display: inline-block;
      background: linear-gradient(90deg, #7b2ff7, #f107a3);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      transition: background-position 0.5s ease;
      background-size: 200%;
    }

    .bg-container h3:hover {
      background-position: right center;
    }

    /* ==== CARD CÔNG VIỆC ==== */
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
      transition: transform 0.3s, box-shadow 0.3s;
      background: #fff;
      position: relative;
      z-index: 1;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.25);
    }

    .badge-status {
      font-weight: 500;
      padding: 0.5em 0.75em;
      font-size: 0.85rem;
    }

    /* ==== FORM LỌC ==== */
    .filter-form select {
      min-height: 50px;
      border-radius: 10px;
      border: 1px solid #ddd;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }

    .filter-form select:hover {
      border-color: #a66ad4;
      box-shadow: 0 3px 8px rgba(166,106,212,0.15);
    }

    /* ==== CÁC HIỆU ỨNG KHÁC ==== */
    .due-soon { color: #dc3545; font-weight: 600; }

    .btn-primary {
      background: linear-gradient(90deg, #7b2ff7, #f107a3);
      border: none;
      transition: background-position 0.4s ease;
      background-size: 200%;
    }

    .btn-primary:hover {
      background-position: right center;
    }

  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
  <div class="container">
    <!-- Logo và tên ứng dụng -->
    <a class="navbar-brand d-flex align-items-center fw-bold" href="#">
      <img src="asset/logo.png" alt="Logo" width="40" height="40" class="me-2 rounded-circle shadow-sm">
      App Quản Lý Công Việc 
    </a>
    <div class="d-flex align-items-center ms-auto">
      <span class="me-3 text-muted">Xin chào, <?php echo htmlspecialchars($user['username']); ?></span>
      <a class="btn btn-outline-secondary btn-sm me-2" href="auth/logout.php">Đăng xuất</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="bg-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold">Danh sách công việc</h3>
      <a href="tasks/add.php" class="btn btn-primary btn-lg shadow-sm">+ Thêm công việc</a>
    </div>

    <!-- Bộ lọc & Sắp xếp -->
    <form method="get" class="row g-3 mb-4 filter-form">
      <div class="col-md-4">
        <select name="status" class="form-select" onchange="this.form.submit()">
          <option value="all" <?= $status_filter==='all'?'selected':'' ?>>Tất cả trạng thái</option>
          <option value="pending" <?= $status_filter==='pending'?'selected':'' ?>>Đang làm</option>
          <option value="done" <?= $status_filter==='done'?'selected':'' ?>>Hoàn thành</option>
        </select>
      </div>
      <div class="col-md-4">
        <select name="order" class="form-select" onchange="this.form.submit()">
          <option value="asc" <?= $order==='asc'?'selected':'' ?>>Hạn ↑ (sớm trước)</option>
          <option value="desc" <?= $order==='desc'?'selected':'' ?>>Hạn ↓ (muộn trước)</option>
        </select>
      </div>
    </form>

    <?php if (count($tasks) === 0): ?>
      <div class="alert alert-info text-center py-3">Không có công việc phù hợp với bộ lọc.</div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($tasks as $task):
          $badge_class = $task['status'] ? 'bg-success text-white' : 'bg-warning text-dark';
          $due_class = '';
          if ($task['due_date'] && strtotime($task['due_date']) <= strtotime('+1 day') && !$task['status']) {
              $due_class = 'due-soon';
          }
        ?>
          <div class="col-md-6 col-lg-4">
            <div class="card h-100">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($task['title']); ?></h5>
                <p class="card-text flex-grow-1"><?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
                <?php if ($task['due_date']): ?>
                  <p class="<?= $due_class ?>">Hạn: <?php echo $task['due_date']; ?></p>
                <?php endif; ?>
                <span class="badge badge-status <?= $badge_class ?> mb-2"><?php echo $task['status'] ? 'Hoàn thành' : 'Đang làm'; ?></span>
                <div class="mt-auto d-flex gap-2">
                  <a href="tasks/edit.php?id=<?php echo $task['id']; ?>" class="btn btn-outline-primary btn-sm flex-grow-1">Sửa</a>
                  <a href="tasks/delete.php?id=<?php echo $task['id']; ?>" class="btn btn-outline-danger btn-sm flex-grow-1" onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
