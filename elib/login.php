<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($password === $user['password']) {
            $_SESSION['user'] = $user;

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
    <link rel="stylesheet" href="css/login.css">
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
                <input type="text" name="email" placeholder="E-mail" required>
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
                <p>Don't have an account? <a href="register.php">Register</a></p>
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

        document.addEventListener("mousemove", (e) => {
            const x = (e.clientX / window.innerWidth) * 100;
            const y = (e.clientY / window.innerHeight) * 100;

            document.body.style.setProperty('--x', `${x}%`);
            document.body.style.setProperty('--y', `${y}%`);
        });
    </script>
</body>
</html>