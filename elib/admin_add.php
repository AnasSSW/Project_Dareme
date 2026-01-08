<?php
include "db.php";

// ตรวจสอบว่า login และเป็น admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
  die("หน้านี้สำหรับ Admin เท่านั้น");
}

// เมื่อกด submit ฟอร์ม
if ($_POST) {
  $title  = $_POST['title'];
  $author = $_POST['author'];
  $cover  = $_POST['cover']; // URL รูปปก

  $sql = "INSERT INTO books (title, author, cover) 
          VALUES ('$title', '$author', '$cover')";

  if ($conn->query($sql)) {
    $success = "เพิ่มหนังสือเรียบร้อยแล้ว";
  } else {
    $error = "เกิดข้อผิดพลาดในการเพิ่มหนังสือ";
  }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เพิ่มหนังสือ | Admin</title>
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
</head>
<body>

<div class="container">
  <h2 class="section-title">➕ เพิ่มหนังสือใหม่</h2>

  <?php if (!empty($success)) { ?>
    <p style="color:green;">✅ <?= $success ?></p>
  <?php } ?>

  <?php if (!empty($error)) { ?>
    <p style="color:red;">❌ <?= $error ?></p>
  <?php } ?>

  <form method="post" style="max-width:400px;">
    <label>ชื่อหนังสือ</label><br>
    <input type="text" name="title" required><br><br>

    <label>ผู้แต่ง</label><br>
    <input type="text" name="author" required><br><br>

    <label>URL รูปปกหนังสือ</label><br>
    <input type="url" name="cover" placeholder="https://example.com/book.jpg" required><br><br>

    <button type="submit">บันทึกหนังสือ</button>
  </form>

  <br>
  <a href="index.php">⬅ กลับหน้าแรก</a>
</div>

</body>
</html>