<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";
include "includes/navbar.php";

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$user = $_SESSION['user'];
$uid  = $user['id'];

// รับค่าค้นหาจาก ?q=...
$search = isset($_GET['q']) ? trim($_GET['q']) : '';


// ถ้ามี search ให้ filter title หรือ author
if ($search !== '') {
    $searchEscaped = $conn->real_escape_string($search);
    $sql = "
      SELECT *
      FROM books
      WHERE title LIKE '%$searchEscaped%' OR author LIKE '%$searchEscaped%'
      ORDER BY 
        CASE 
          WHEN status = 'available' THEN 0
          ELSE 1
        END,
        title ASC
    ";
} else {
    $sql = "
      SELECT *
      FROM books
      ORDER BY 
        CASE 
          WHEN status = 'available' THEN 0
          ELSE 1
        END,
        title ASC
    ";
}

$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>E-Library ICC</title>
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/component.css">
  <link rel="stylesheet" href="css/index.css"> <!-- CSS เฉพาะหน้า -->
</head>
<body>

<div class="index-page">

  <div class="container">
    <h2 class="section-title">📚 หนังสือทั้งหมด</h2>

    <div class="book-list">
      <?php while ($b = $result->fetch_assoc()) { 
        $bid = $b['id'];

        // เช็คว่าเป็นรายการโปรดหรือไม่
        $favCheck = $conn->query(
          "SELECT id FROM favorites WHERE user_id=$uid AND book_id=$bid"
        );
        $isFav = $favCheck->num_rows > 0;
      ?>
        <div class="book-card">

          <a href="book_detail.php?id=<?= $b['id'] ?>">
            <img src="<?= $b['cover'] ?>" alt="book">
          </a>

          <h3>
            <a href="book_detail.php?id=<?= $b['id'] ?>" style="color:inherit;text-decoration:none;">
              <?= $b['title'] ?>
            </a>
          </h3>

          <p><?= $b['author'] ?></p>
          <p>สถานะ: <?= $b['status'] ?></p>

          <div class="book-actions">
            <a href="favorite_toggle.php?id=<?= $b['id'] ?>"
               class="btn btn-fav <?= $isFav ? 'active' : '' ?>">
               ⭐
            </a>
          </div>

        </div>
      <?php } ?>
    </div>

  </div>

</div>

<?php include "includes/footer.php"; ?>
</body>
</html>
