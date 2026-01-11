<?php
session_start();
include "db.php";

/* เช็คว่าเป็น admin */
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: index.php");
  exit;
}

/*
 ตารางที่สมมติ:
 users(id, fullname)
 books(id, title)
 borrows(id, user_id, book_id, borrow_date, return_date)
*/

$sql = "
  SELECT 
    users.fullname,
    books.title,
    borrows.borrow_date,
    borrows.return_date
  FROM borrows
  JOIN users ON borrows.user_id = users.id
  JOIN books ON borrows.book_id = books.id
  ORDER BY borrows.borrow_date DESC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Admin - ประวัติการยืม</title>
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<h2>📊 ประวัติการยืมหนังสือทั้งหมด</h2>

<div class="admin-container">
  <table>
    <thead>
      <tr>
        <th>ผู้ยืม</th>
        <th>หนังสือ</th>
        <th>วันที่ยืม</th>
        <th>วันที่คืน</th>
        <th>สถานะ</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
          <td data-label="ผู้ยืม"><?= htmlspecialchars($row['fullname']) ?></td>
          <td data-label="หนังสือ"><?= htmlspecialchars($row['title']) ?></td>
          <td data-label="วันที่ยืม"><?= $row['borrow_date'] ?></td>
          <td data-label="วันที่คืน">
            <?= $row['return_date'] ? $row['return_date'] : '-' ?>
          </td>
          <td data-label="สถานะ">
            <?php if ($row['return_date']) { ?>
              <span class="status returned">คืนแล้ว</span>
            <?php } else { ?>
              <span class="status borrowed">ยังไม่คืน</span>
            <?php } ?>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

</body>
</html>
