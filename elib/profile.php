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
$stmt = $conn->prepare("SELECT fullname, email, phone FROM users WHERE id=?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$success = $error = "";

/* UPDATE PROFILE */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fullname = trim($_POST['fullname']);
  $email    = trim($_POST['email']);
  $phone    = trim($_POST['phone']);

  if ($fullname && $email) {
    $stmt = $conn->prepare(
      "UPDATE users SET fullname=?, email=?, phone=? WHERE id=?"
    );
    $stmt->bind_param("sssi", $fullname, $email, $phone, $uid);

    if ($stmt->execute()) {
      $_SESSION['user']['fullname'] = $fullname;
      $_SESSION['user']['email']    = $email;
      $_SESSION['user']['phone']    = $phone;
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
    <link rel="stylesheet" href="css/profile1.css">
</head>

<body>

<div class="container">
  <div class="card">

    <div class="header">
      <div class="avatar">
        <?= strtoupper(substr($user['fullname'],0,1)) ?>
      </div>
      <div>
        <h2 style="color: white;"><?= htmlspecialchars($user['fullname']) ?></h2>
        <div style="color:white;font-size:14px;">
          ข้อมูลส่วนตัว
        </div>
      </div>
    </div>

    <?php if($success): ?>
      <div class="msg-success">✅ <?= $success ?></div>
    <?php endif; ?>

    <?php if($error): ?>
      <div class="msg-error">❌ <?= $error ?></div>
    <?php endif; ?>

    <form method="post">
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

      <button type="submit" style="background-color: darkblue;">💾 บันทึกข้อมูล</button>
    </form>

  </div>
</div>

<?php include "includes/footer.php"; ?>
</body>
</html>
