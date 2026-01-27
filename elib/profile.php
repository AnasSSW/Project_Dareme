<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "db.php";
include "includes/navbar.php";

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$uid = $_SESSION['user']['id'];

/* ดึงข้อมูล user */
$stmt = $conn->prepare("SELECT fullname, email, phone, avatar FROM users WHERE id=?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$success = $error = "";

/* UPDATE PROFILE */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullname = trim($_POST['fullname']);
  $email    = trim($_POST['email']);
  $phone    = trim($_POST['phone']);
  $avatar   = trim($_POST['avatar'] ?? '');

  if ($fullname && $email) {
    $stmt = $conn->prepare(
      "UPDATE users SET fullname=?, email=?, phone=?, avatar=? WHERE id=?"
    );
    $stmt->bind_param("ssssi", $fullname, $email, $phone, $avatar, $uid);

    if ($stmt->execute()) {
      $_SESSION['user']['fullname'] = $fullname;
      $_SESSION['user']['email']    = $email;
      $_SESSION['user']['phone']    = $phone;
      $_SESSION['user']['avatar']   = $avatar;

      $user['fullname'] = $fullname;
      $user['email']    = $email;
      $user['phone']    = $phone;
      $user['avatar']   = $avatar;

      $success = "บันทึกข้อมูลเรียบร้อยแล้ว";
    } else {
      $error = "เกิดข้อผิดพลาดในการบันทึก";
    }
  } else {
    $error = "กรุณากรอกชื่อและอีเมล";
  }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ข้อมูลส่วนตัว</title>
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/profile.css">

  <style>
        @import url('https://fonts.googleapis.com/css2?family=Itim&family=Prompt:wght@300;400;500;600;700&display=swap');
  </style>
</head>

<body class="profile-page">

<div class="container">
  <div class="card">

    <div class="header">
      <div class="avatar">
        <?php if(!empty($user['avatar'])): ?>
          <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="avatar">
        <?php else: ?>
          <?= strtoupper(substr($user['fullname'],0,1)) ?>
        <?php endif; ?>
      </div>

      <div>
        <h2><?= htmlspecialchars($user['fullname']) ?></h2>
        <div class="subtitle">ข้อมูลส่วนตัว</div>

        <!-- ปุ่มเปลี่ยนรูป -->
        <button type="button" class="btn-avatar" onclick="toggleAvatarInput()">
          🖼 เปลี่ยนรูปโปรไฟล์
        </button>
      </div>
    </div>

    <?php if($success): ?>
      <div class="msg-success">✅ <?= $success ?></div>
    <?php endif; ?>

    <?php if($error): ?>
      <div class="msg-error">❌ <?= $error ?></div>
    <?php endif; ?>

    <form method="post" class="profile-form">

      <!-- ซ่อนช่องใส่ลิงก์รูป -->
      <div class="form-group avatar-input" id="avatarInput">
        <label>ลิงก์รูปโปรไฟล์</label>
        <input type="url" name="avatar"
          placeholder="https://example.com/avatar.jpg"
          value="<?= htmlspecialchars($user['avatar'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>ชื่อ-นามสกุล</label>
        <input type="text" name="fullname"
          value="<?= htmlspecialchars($user['fullname']) ?>" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email"
          value="<?= htmlspecialchars($user['email']) ?>" required>
      </div>

      <div class="form-group">
        <label>เบอร์โทรศัพท์</label>
        <input type="text" name="phone"
          placeholder="เช่น 0812345678"
          value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
      </div>

      <button type="submit">💾 บันทึกข้อมูล</button>
    </form>

  </div>
</div>

<?php include "includes/footer.php"; ?>

<script>
function toggleAvatarInput(){
  const el = document.getElementById('avatarInput');
  el.style.display = (el.style.display === 'block') ? 'none' : 'block';
}
</script>

</body>
</html>
