<?php
include "db.php";
include "includes/navbar.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$uid = $user['id'];

/* ดึงหนังสือที่อยู่ในรายการโปรด */
$sql = "
  SELECT books.*
  FROM favorites
  JOIN books ON favorites.book_id = books.id
  WHERE favorites.user_id = $uid
  ORDER BY favorites.created_at DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายการโปรดของฉัน</title>
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/favorites.css">
</head>
<body class="fav-page">

<div class="container">
    <h2 class="section-title">⭐ รายการโปรดของฉัน</h2>

    <?php if ($result->num_rows == 0) { ?>
        <p class="empty-fav">ยังไม่มีหนังสือในรายการโปรด 📚</p>
    <?php } else { ?>
        <div class="book-list">
            <?php while ($b = $result->fetch_assoc()) { ?>
                <div class="book-card">
                    <a href="book_detail.php?id=<?= $b['id'] ?>">
                        <img src="<?= $b['cover'] ?>" alt="book">
                    </a>
                    <h3><?= $b['title'] ?></h3>
                    <p><?= $b['author'] ?></p>
                    <p>สถานะ: <?= $b['status'] ?></p>

                    <div class="book-actions">
                        <a href="favorite_toggle.php?id=<?= $b['id'] ?>" class="btn btn-fav active">⭐</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<?php include "includes/footer.php"; ?>
</body>
</html>
