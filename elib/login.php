<?php
session_start();
include "db.php"; // ไฟล์เชื่อมต่อฐานข้อมูล

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    // แปลงรหัสผ่านที่กรอกเป็น MD5
    $password = md5($_POST['password']);

    // ค้นหาผู้ใช้
    $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // ตรวจสอบรหัสผ่าน (MD5)
        if ($password === $user['password']) {
            $_SESSION['user'] = $user;

            // แยกหน้า admin / user
            if ($user['role'] === 'admin') {
                header("Location: admin_add.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบบัญชีผู้ใช้";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style_login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form method="post">
            <h1>Login</h1>

            <?php if ($error) { ?>
                <p style="color:red; text-align:center;">❌ <?= $error ?></p>
            <?php } ?>

            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <i class="bx bxs-user"></i>
            </div>

            <div class="input-box">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <i class="bx bx-show" id="togglePassword" style="cursor:pointer;"></i>
            </div>

            <div class="remember-forgot">
                <label><input type="checkbox"> Remember me</label>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="btn">Login</button>

            <div class="register-link">
                <p>Don't have an account? <a href="#">Register</a></p>
            </div>
        </form>
    </div>

    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", () => {
            const isPassword = password.getAttribute("type") === "password";
            password.setAttribute("type", isPassword ? "text" : "password");
            togglePassword.classList.toggle("bx-show", isPassword);
            togglePassword.classList.toggle("bx-hide", !isPassword);
        });
    </script>
</body>
</html>