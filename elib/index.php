<?php
include "db.php";

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$user = $_SESSION['user'];
$result = $conn->query("SELECT * FROM books");
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>E-Library ICC</title>

  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
</head>
<body>

<nav class="navbar">
  <div class="navbar-container">
    <div class="navbar-top">
      <div class="brand">
        <img src="https://lh5.googleusercontent.com/proxy/pg2Dj_tPgZ0xpH_qYAbrUWtmZnsvHuR_Q_qtOyb2Ji2h7vJqK5OrRSafK282WorIEbaFcDve_jog5W_6ggF5geIPmRGivygAFBTlMIkQ-DpgJUcklu4APw" alt="logo">
        <div class="brand-text">
          <span class="brand-title">E-Library</span>
          <small class="brand-subtitle">Intrachai Commercial College</small>
        </div>
      </div>

      <div class="search-box">
        <form method="get">
          <input type="text" name="q" placeholder="ค้นหาหนังสือ...">
          <button>ค้นหา</button>
        </form>
      </div>

      <div>
        <span style="color:white">👤 <?= $user['username'] ?></span>
        <a href="logout.php" class="login-btn">Logout</a>
      </div>
    </div>

    <ul class="menu">
      <li><a href="index.php">หน้าแรก</a></li>
      <li><a href="index.php">รายการโปรด</a></li>
      <li><a href="index.php">คืนหนังสือ</a></li>
      <?php if ($user['role'] == 'admin') { ?>
        <li><a href="admin_add.php">เพิ่มหนังสือ</a></li>
      <?php } ?>
    </ul>
  </div>
</nav>

<div class="container">
  <h2 class="section-title">📚 หนังสือทั้งหมด</h2>

  <div class="book-list">
    <?php while ($b = $result->fetch_assoc()) { ?>
      <div class="book-card">
        <img src="<?= $b['cover'] ?>" alt="book">
        <h3><?= $b['title'] ?></h3>
        <p><?= $b['author'] ?></p>
        <p>สถานะ: <?= $b['status'] ?></p>

        <?php if ($b['status'] == 'available') { ?>
          <a href="borrow.php?id=<?= $b['id'] ?>">ยืม</a>
        <?php } else {
          $uid = $user['id'];
          $bid = $b['id'];
          $chk = $conn->query("SELECT * FROM borrows WHERE user_id=$uid AND book_id=$bid AND return_date IS NULL");
          if ($chk->num_rows == 1) {
        ?>
          <a href="return.php?id=<?= $b['id'] ?>">คืน</a>
        <?php }} ?>
      </div>
    <?php } ?>
  </div>
</div>

<footer>
  <div class="footer-content">
    <p>© 2025 E-Library ICC | ห้องสมุดดิจิทัลเพื่อการเรียนรู้</p>
    <p>present by Anas Srisuwan</p>
  </div>
</footer>

</body>
</html>
