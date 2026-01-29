<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

session_start();
require_once 'db.php';

/* ==================== CHECK LOGIN ==================== */
if (!isset($_SESSION['user'])) {
    echo json_encode([
        'success' => false,
        'message' => 'กรุณาเข้าสู่ระบบ'
    ]);
    exit;
}

/* ==================== CHECK REQUEST ==================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
    exit;
}

if (!isset($_POST['book_id']) || !is_numeric($_POST['book_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่พบ book_id'
    ]);
    exit;
}

$user_id = (int)$_SESSION['user']['id'];
$book_id = (int)$_POST['book_id'];

/* ==================== TOGGLE FAVORITE ==================== */
try {

    // เช็คว่ามีอยู่แล้วหรือไม่
    $stmt = $conn->prepare(
        "SELECT id FROM favorites WHERE user_id = ? AND book_id = ?"
    );
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        // ลบออก
        $stmt->close();
        $stmt = $conn->prepare(
            "DELETE FROM favorites WHERE user_id = ? AND book_id = ?"
        );
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();

        echo json_encode([
            'success'    => true,
            'action'     => 'removed',
            'isFavorite' => false
        ]);
    } else {
        // เพิ่มเข้า
        $stmt->close();
        $stmt = $conn->prepare(
            "INSERT INTO favorites (user_id, book_id) VALUES (?, ?)"
        );
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();

        echo json_encode([
            'success'    => true,
            'action'     => 'added',
            'isFavorite' => true
        ]);
    }

    $stmt->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}

$conn->close();
exit;
