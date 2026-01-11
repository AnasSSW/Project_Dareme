<?php
include "db.php";
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email    = $_POST['email'];
    $password = md5($_POST['password']);

    // เช็ค email ซ้ำ
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $error = "❌ อีเมลนี้ถูกใช้งานแล้ว";
    } else {
        $sql = "INSERT INTO users (fullname, email, password, role)
                VALUES ('$fullname', '$email', '$password', 'user')";
        if ($conn->query($sql)) {
            $success = "✅ สมัครสมาชิกสำเร็จ";
        } else {
            $error = "❌ สมัครไม่สำเร็จ";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="css/login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

<div class="wrapper">
    <form method="post">
        <h1>Register</h1>

        <?php if ($error) { ?>
            <p style="color:red; text-align:center;"><?= $error ?></p>
        <?php } ?>

        <?php if ($success) { ?>
            <p style="color:lightgreen; text-align:center;"><?= $success ?></p>
        <?php } ?>

        <div class="input-box">
            <input type="text" name="fullname" placeholder="Fullname" required>
            <i class="bx bxs-user"></i>
        </div>

        <div class="input-box">
            <input type="email" name="email" placeholder="Email" required>
            <i class="bx bxs-envelope"></i>
        </div>

        <div class="input-box">
            <input type="password" name="password" placeholder="Password" required>
            <i class="bx bxs-lock-alt"></i>
        </div>

        <button type="submit" class="btn">Register</button>

        <div class="register-link">
            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>
    </form>
</div>

    <script>
        document.addEventListener("mousemove", (e) => {
            const x = (e.clientX / window.innerWidth) * 100;
            const y = (e.clientY / window.innerHeight) * 100;

            document.body.style.setProperty('--x', `${x}%`);
            document.body.style.setProperty('--y', `${y}%`);
        });
    </script>
</body>
</html>
