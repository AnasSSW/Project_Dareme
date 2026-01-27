<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "db.php";
include "includes/navbar.php";

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$uid = $_SESSION['user']['id'];
$filter = $_GET['filter'] ?? 'all';

$where = "WHERE br.user_id = $uid";

if ($filter === 'borrowing') {
  $where .= " AND br.return_date IS NULL AND br.due_date >= CURDATE()";
} elseif ($filter === 'returned') {
  $where .= " AND br.return_date IS NOT NULL";
} elseif ($filter === 'overdue') {
  $where .= " AND br.return_date IS NULL AND br.due_date < CURDATE()";
}

$sql = "
SELECT b.title, b.author, b.cover,
       br.borrow_date, br.due_date, br.return_date
FROM borrows br
JOIN books b ON br.book_id = b.id
$where
ORDER BY br.borrow_date DESC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ประวัติการยืม</title>

    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/borrow_history.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Itim&family=Prompt:wght@300;400;500;600;700&display=swap');
    </style>
</head>

<body>

<div class="container">
  <h2>📚 ประวัติการยืมหนังสือ</h2>

  <div class="filters">
    <a href="?filter=all" class="<?= $filter=='all'?'active':'' ?>">ทั้งหมด</a>
    <a href="?filter=borrowing" class="<?= $filter=='borrowing'?'active':'' ?>">กำลังยืม</a>
    <a href="?filter=returned" class="<?= $filter=='returned'?'active':'' ?>">คืนแล้ว</a>
    <a href="?filter=overdue" class="<?= $filter=='overdue'?'active':'' ?>">เกินกำหนด</a>
  </div>

  <?php if($result->num_rows==0): ?>
    <div class="empty">ยังไม่มีประวัติการยืม 📭</div>
  <?php endif; ?>

  <div class="list">
  <?php while($row=$result->fetch_assoc()): ?>
  <?php
    if ($row['return_date']) {
      $status = '<span class="status returned">✔ คืนแล้ว</span>';
    } elseif (strtotime($row['due_date']) < time()) {
      $status = '<span class="status overdue">⛔ เกินกำหนดคืน</span>';
    } else {
      $status = '<span class="status borrowing">⏳ กำลังยืม</span>';
    }
  ?>
    <div class="card">
      <img src="<?= htmlspecialchars($row['cover']) ?>">
      <div class="body">
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <p>✍ <?= htmlspecialchars($row['author']) ?></p>
        <p>📅 ยืม: <?= date('d/m/Y',strtotime($row['borrow_date'])) ?></p>
        <p>⏰ กำหนดคืน: <?= date('d/m/Y',strtotime($row['due_date'])) ?></p>
        <?= $status ?>
      </div>
    </div>
  <?php endwhile; ?>
  </div>
</div>

<?php include "includes/footer.php"; ?>
</body>
</html>
