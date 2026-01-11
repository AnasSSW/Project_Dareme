<?php
include "db.php";
include "includes/navbar.php";

$cat_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

/* ดึงชื่อหมวด */
$catRes = $conn->query("SELECT name FROM categories WHERE id=$cat_id");
$cat = $catRes ? $catRes->fetch_assoc() : null;

/* ดึงหนังสือ */
$sql = "
SELECT * FROM books
WHERE category_id = $cat_id
ORDER BY title
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>หมวดหมู่หนังสือ</title>

    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/category1.css">
</head>

<body>

<div class="container">

  <?php if(!$cat): ?>
    <div class="empty">❌ ไม่พบหมวดหมู่</div>
  <?php else: ?>

    <div class="page-title">
      <h2>📚 หมวดหมู่</h2>
      <span><?= htmlspecialchars($cat['name']) ?></span>
    </div>

    <p class="subtitle">
      หนังสือทั้งหมดในหมวด <strong><?= htmlspecialchars($cat['name']) ?></strong>
    </p>

    <?php if($result->num_rows === 0): ?>
      <div class="empty">
        📭 ยังไม่มีหนังสือในหมวดนี้
      </div>
    <?php else: ?>

    <div class="list">
      <?php while($row = $result->fetch_assoc()): ?>
      <a href="book_detail.php?id=<?= $row['id'] ?>" style="text-decoration:none;color:inherit;">
          <div class="card">
          <img src="<?= htmlspecialchars($row['cover']) ?>">
          <div class="body">
              <h3><?= htmlspecialchars($row['title']) ?></h3>
              <p>✍ <?= htmlspecialchars($row['author']) ?></p>
          </div>
          </div>
      </a>
      <?php endwhile; ?>
      </div>


    <?php endif; ?>

  <?php endif; ?>

</div>
<?php include "includes/footer.php"; ?>
</body>
</html>
