<?php
include "db.php";

if ($_POST) {
  $username = $_POST['username'];
  $password = md5($_POST['password']);
  $role = 'user';

  $sql = "INSERT INTO users (username, password, role)
          VALUES ('$username', '$password', '$role')";
  $conn->query($sql);

  echo "สมัครสมาชิกเรียบร้อย <a href='login.php'>เข้าสู่ระบบ</a>";
}
?>

<h2>สมัครสมาชิก</h2>
<form method="post">
  <input name="username" placeholder="Username" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <button type="submit">Submit</button>
</form>
