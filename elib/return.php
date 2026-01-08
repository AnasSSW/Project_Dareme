<?php
include "db.php";

$book_id = $_GET['id'];

$conn->query(
  "UPDATE borrows 
   SET return_date=CURDATE()
   WHERE book_id=$book_id AND return_date IS NULL"
);

$conn->query(
  "UPDATE books SET status='available' WHERE id=$book_id"
);

header("Location: index.php");
exit;
