<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "db.php";
include "includes/navbar.php";

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$uid = $_SESSION['user']['id'];
$success = $error = "";

/* เมื่อกดคืนหนังสือ */
if (isset($_POST['borrow_id'], $_POST['book_id'])) {
  $borrow_id = (int)$_POST['borrow_id'];
  $book_id   = (int)$_POST['book_id'];

  $conn->begin_transaction();

  try {
    $stmt = $conn->prepare(
      "UPDATE borrows 
       SET return_date = NOW() 
       WHERE id=? AND user_id=?"
    );
    $stmt->bind_param("ii", $borrow_id, $uid);
    $stmt->execute();

    $stmt = $conn->prepare(
      "UPDATE books 
       SET status='available' 
       WHERE id=?"
    );
    $stmt->bind_param("i", $book_id);
    $stmt->execute();

    $conn->commit();
    $success = "คืนหนังสือเรียบร้อยแล้ว 🎉";
  } catch (Exception $e) {
    $conn->rollback();
    $error = "เกิดข้อผิดพลาดในการคืนหนังสือ";
  }
}

/* ดึงหนังสือที่กำลังยืมอยู่ */
$sql = "
SELECT br.id AS borrow_id,
       br.borrow_date,
       br.due_date,
       b.id AS book_id,
       b.title,
       b.author,
       b.cover
FROM borrows br
JOIN books b ON br.book_id = b.id
WHERE br.user_id = $uid
AND br.return_date IS NULL
ORDER BY br.due_date ASC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>คืนหนังสือ</title>
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/return1.css">
</head>

<body>

<div class="container">
<h2>📖 คืนหนังสือ</h2>

<?php if($success): ?>
  <div class="msg-success"><?= $success ?></div>
<?php endif; ?>

<?php if($error): ?>
  <div class="msg-error"><?= $error ?></div>
<?php endif; ?>

<?php if($result->num_rows == 0): ?>
  <p>🎉 ตอนนี้คุณไม่มีหนังสือที่ต้องคืน</p>
<?php else: ?>

<div class="list">
<?php while($row = $result->fetch_assoc()): ?>
<?php $isOverdue = strtotime($row['due_date']) < time(); ?>

<div class="card">
  <img src="<?= $row['cover'] ?>">
  <div class="body">
    <h3><?= $row['title'] ?></h3>
    <p>✍ <?= $row['author'] ?></p>
    <p>📅 ยืม: <?= date('d/m/Y',strtotime($row['borrow_date'])) ?></p>
    <p>⏰ กำหนดคืน: <?= date('d/m/Y',strtotime($row['due_date'])) ?></p>

    <div class="status <?= $isOverdue ? 'overdue' : 'normal' ?>">
      <?= $isOverdue ? '⛔ เกินกำหนดคืน' : '⏳ ยังไม่ถึงกำหนด' ?>
    </div>

    <form method="post" onsubmit="return confirm('ยืนยันการคืนหนังสือเล่มนี้?');">
      <input type="hidden" name="borrow_id" value="<?= $row['borrow_id'] ?>">
      <input type="hidden" name="book_id" value="<?= $row['book_id'] ?>">
      <button type="submit" class="btn-return">📥 คืนหนังสือ</button>
    </form>
  </div>
</div>

<?php endwhile; ?>
</div>
<?php endif; ?>

</div>
<?php include "includes/footer.php"; ?>
</body>
</html>
