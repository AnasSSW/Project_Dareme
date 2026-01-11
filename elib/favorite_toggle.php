<?php
include "db.php";

if (!isset($_SESSION)) {
  session_start();
}

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

$user_id = intval($_SESSION['user']['id']);
$book_id = intval($_GET['id'] ?? 0);

if ($book_id === 0) {
  header("Location: index.php");
  exit;
}

/* เช็คว่ามีในรายการโปรดหรือยัง */
$chk = $conn->query(
  "SELECT id FROM favorites 
   WHERE user_id = $user_id AND book_id = $book_id"
);

if ($chk === false) {
  die("DB Error: " . $conn->error);
}

if ($chk->num_rows > 0) {
  // ลบออก
  $conn->query(
    "DELETE FROM favorites 
     WHERE user_id = $user_id AND book_id = $book_id"
  );
} else {
  // เพิ่มเข้า
  $conn->query(
    "INSERT INTO favorites (user_id, book_id) 
     VALUES ($user_id, $book_id)"
  );
}

/* กลับหน้าที่กดมา */
$back = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: $back");
exit;
