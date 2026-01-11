<?php
include "db.php";
include "includes/navbar.php";

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

if (!isset($_GET['id'])) {
  header("Location: index.php");
  exit;
}

$user = $_SESSION['user'];
$id = (int)$_GET['id'];

$result = $conn->query("SELECT * FROM books WHERE id = $id");
if ($result->num_rows !== 1) {
  echo "ไม่พบหนังสือ";
  exit;
}

$book = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title><?= $book['title'] ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSS เดียวกับ index -->
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/book_detail.css">

</head>
<body>

<!-- 🔷 DETAIL BOOK -->
<div class="book-detail-page">
  <div class="book-detail-card">

    <div class="book-cover">
      <img src="<?= $book['cover'] ?>" alt="book">

      <?php if ($book['status'] == 'available') { ?>
        <span class="book-status">พร้อมให้ยืม</span>
      <?php } else { ?>
        <span class="book-status unavailable">ไม่พร้อมให้ยืม</span>
      <?php } ?>
    </div>

    <div class="book-info">
      <h1><?= $book['title'] ?></h1>
      <div class="book-author">✍ <?= $book['author'] ?></div>

      <div class="book-description">
        <?= nl2br($book['description'] ?? '-') ?>
      </div>

      <div class="book-actions">
        <?php if ($book['status'] == 'available') { ?>
          <a href="borrow.php?id=<?= $book['id'] ?>" class="borrow-btn">ยืมหนังสือ</a>
        <?php } ?>

        <a href="index.php" class="back-btn">← กลับหน้าหลัก</a>
      </div>
    </div>

  </div>
</div>


<!-- 🔷 FOOTER (เหมือน index.php) -->
<?php include "includes/footer.php"; ?>

<?php if (isset($_GET['borrow']) && $_GET['borrow'] == 'success') { 
  $due = $_GET['due'];
?>
<script>
  window.onload = function () {
    alert(
      "📚 ยืมหนังสือสำเร็จ!\n\n" +
      "🗓 กำหนดคืน: <?= date('d/m/Y', strtotime($due)) ?>"
    );
  };
</script>
<?php } ?>

</body>
</html>
