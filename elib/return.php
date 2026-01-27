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

/* ==================== คืนหนังสือ ==================== */
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

/* ==================== ดึงหนังสือที่ยืมอยู่ ==================== */
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

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Itim&family=Prompt:wght@400;600;700&display=swap');
  </style>
</head>

<body>

<div class="return-page">
  <div class="return-container">

    <h2 class="return-title">📖 คืนหนังสือ</h2>

    <?php if ($success): ?>
      <div class="return-alert success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="return-alert error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows == 0): ?>
      <div class="return-empty">
        🎉 ตอนนี้คุณไม่มีหนังสือที่ต้องคืน
      </div>
    <?php else: ?>

    <div class="return-grid">

      <?php while ($row = $result->fetch_assoc()): ?>
      <?php $isOverdue = strtotime($row['due_date']) < time(); ?>

      <div class="return-card <?= $isOverdue ? 'is-overdue' : '' ?>">

        <div class="return-cover">
          <img src="<?= $row['cover'] ?>" alt="<?= $row['title'] ?>">
        </div>

        <div class="return-content">
          <h3 class="return-book-title"><?= $row['title'] ?></h3>
          <p class="return-author">✍ <?= $row['author'] ?></p>

          <div class="return-date">
            📅 ยืม: <?= date('d/m/Y', strtotime($row['borrow_date'])) ?>
          </div>

          <div class="return-date">
            ⏰ กำหนดคืน: <?= date('d/m/Y', strtotime($row['due_date'])) ?>
          </div>

          <div class="return-status <?= $isOverdue ? 'overdue' : 'normal' ?>">
            <?= $isOverdue ? '⛔ เกินกำหนดคืน' : '⏳ ยังไม่ถึงกำหนด' ?>
          </div>

          <form class="return-form" method="post">
            <input type="hidden" name="borrow_id" value="<?= $row['borrow_id'] ?>">
            <input type="hidden" name="book_id" value="<?= $row['book_id'] ?>">

            <button type="submit"
                    class="return-btn"
                    data-due="<?= $row['due_date'] ?>">
              📥 คืนหนังสือ
            </button>
          </form>
        </div>

      </div>
      <?php endwhile; ?>

    </div>
    <?php endif; ?>

  </div>
</div>

<?php include "includes/footer.php"; ?>

<!-- ==================== POP-UP ค่าปรับ ==================== -->
<script>
document.querySelectorAll('.return-form').forEach(form => {
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const btn = form.querySelector('.return-btn');
    const dueDate = new Date(btn.dataset.due);
    const today = new Date();

    dueDate.setHours(0,0,0,0);
    today.setHours(0,0,0,0);

    let daysLate = 0;
    let fine = 0;

    if (today > dueDate) {
      const diff = today - dueDate;
      daysLate = Math.ceil(diff / (1000 * 60 * 60 * 24));
      fine = daysLate * 20;
    }

    let msg = '';

    if (fine > 0) {
      msg =
        `!!!กรณีส่งคืนหนังสือเกินกำหนด จะมีค่าปรับวันละ 20 บาท!!!\n\n` +
        `ยืนยันการคืนหนังสือหรือไม่?`;
    } else {
      msg = 'ยืนยันการคืนหนังสือเล่มนี้?';
    }

    if (confirm(msg)) {
      form.submit();
    }
  });
});
</script>

</body>
</html>
