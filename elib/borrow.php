<?php
include "db.php";

$book_id = $_GET['id'];
$user_id = $_SESSION['user']['id'];

$conn->query(
  "INSERT INTO borrows (user_id, book_id, borrow_date)
   VALUES ($user_id, $book_id, CURDATE())"
);

$conn->query(
  "UPDATE books SET status='borrowed' WHERE id=$book_id"
);

header("Location: index.php");
exit;
