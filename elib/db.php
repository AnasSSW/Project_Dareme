<?php
$conn = new mysqli("localhost", "root", "", "elibrary");
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลไม่ได้");
}
session_start();
?>
