<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}

if (!isset($_GET['id'])) {
  die("ไม่พบ id หนังสือ");
}

$user_id = $_SESSION['user']['id'];
$book_id = (int)$_GET['id'];

// เช็คว่าหนังสือยังว่างอยู่ไหม
$check = $conn->query("SELECT status FROM books WHERE id = $book_id");
if ($check->num_rows !== 1) {
  die("ไม่พบหนังสือ");
}

$book = $check->fetch_assoc();
if ($book['status'] !== 'available') {
  die("หนังสือเล่มนี้ถูกยืมไปแล้ว");
}

// กำหนดวันคืน (7 วัน)
$due_date = date('Y-m-d', strtotime('+7 days'));

// บันทึกการยืม
$sql1 = "
  INSERT INTO borrows (user_id, book_id, borrow_date, due_date)
  VALUES ($user_id, $book_id, NOW(), '$due_date')
";

if (!$conn->query($sql1)) {
  die("ERROR INSERT: " . $conn->error);
}

// อัปเดตสถานะหนังสือ
$sql2 = "UPDATE books SET status='borrowed' WHERE id=$book_id";

if (!$conn->query($sql2)) {
  die("ERROR UPDATE: " . $conn->error);
}

// กลับไปหน้า detail พร้อม popup
header("Location: book_detail.php?id=$book_id&borrow=success&due=$due_date");
exit;
