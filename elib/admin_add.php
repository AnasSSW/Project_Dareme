<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "db.php";

// ตรวจสอบ admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  die("หน้านี้สำหรับ Admin เท่านั้น");
}

$success = $error = "";

/* ดึงหมวดหมู่ทั้งหมด */
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");

/* เมื่อ submit */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $title       = $_POST['title'];
  $author      = $_POST['author'];
  $category_id = (int)$_POST['category_id'];
  $description = $_POST['description'];
  $cover       = $_POST['cover'];

  $stmt = $conn->prepare("
    INSERT INTO books 
      (title, author, category_id, description, cover, status)
    VALUES (?, ?, ?, ?, ?, 'available')
  ");

  $stmt->bind_param(
    "ssiss",
    $title,
    $author,
    $category_id,
    $description,
    $cover
  );

  if ($stmt->execute()) {
    $success = "เพิ่มหนังสือเรียบร้อยแล้ว ✅";
  } else {
    $error = "เกิดข้อผิดพลาด: " . $conn->error;
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

  <style>
    .form-box{
      max-width:420px;
      background:var(--card,#111);
      padding:24px;
      border-radius:16px;
      box-shadow:0 10px 30px rgba(0,0,0,.25)
    }
    label{font-weight:600}
    input, textarea, select{
      width:100%;
      color: white;
      padding:10px 14px;
      border-radius:10px;
      border:1px solid #ccc;
      margin-top:6px
    }
    button{
      margin-top:14px;
      padding:10px;
      width:100%;
      border:none;
      border-radius:12px;
      font-weight:700;
      cursor:pointer;
      background:linear-gradient(135deg,#fbbf24,#fde047)
    }
  </style>
</head>
<body>

<div class="container">
  <h2 class="section-title">➕ เพิ่มหนังสือใหม่</h2>

  <?php if ($success): ?>
    <p style="color:#22c55e;">✅ <?= $success ?></p>
  <?php endif; ?>

  <?php if ($error): ?>
    <p style="color:#ef4444;">❌ <?= $error ?></p>
  <?php endif; ?>

  <form method="post" class="form-box">

    <label>ชื่อหนังสือ</label>
    <input type="text" name="title" required>

    <label>ผู้แต่ง</label>
    <input type="text" name="author" required>

    <label>หมวดหมู่</label>
    <select name="category_id" required>
      <option value="">-- เลือกหมวดหมู่ --</option>
      <?php while($c = $categories->fetch_assoc()): ?>
        <option value="<?= $c['id'] ?>">
          <?= htmlspecialchars($c['name']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label>รายละเอียดหนังสือ</label>
    <textarea name="description" rows="4" required></textarea>

    <label>URL รูปปก</label>
    <input type="url" name="cover" required>

    <button type="submit">💾 บันทึกหนังสือ</button>
  </form>

  <br>
  <a href="index.php">⬅ กลับหน้าแรก</a>
</div>

</body>
</html>
