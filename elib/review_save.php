<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'], $_POST['book_id'])) {
  header("Location: index.php");
  exit;
}

$uid = $_SESSION['user']['id'];
$book_id = (int)$_POST['book_id'];
$rating = (int)$_POST['rating'];
$comment = trim($_POST['comment']);

$stmt = $conn->prepare("
  INSERT INTO reviews (user_id, book_id, rating, comment)
  VALUES (?, ?, ?, ?)
");
$stmt->bind_param("iiis", $uid, $book_id, $rating, $comment);
$stmt->execute();

header("Location: book_detail.php?id=$book_id");
exit;
