<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";

/* ==================== SESSION ==================== */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$uid  = $user['id'];

/* ==================== SEARCH ==================== */
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

/* ==================== PAGINATION ==================== */
$perPage = 12; // หนังสือต่อหน้า (ห้ามเป็น 0)
$page = isset($_GET['page']) && is_numeric($_GET['page'])
        ? (int)$_GET['page']
        : 1;

if ($page < 1) $page = 1;
$offset = ($page - 1) * $perPage;

/* ==================== COUNT BOOKS ==================== */
$countSql = "SELECT COUNT(*) AS total FROM books";

if ($search !== '') {
    $searchEscaped = $conn->real_escape_string($search);
    $countSql .= " WHERE title LIKE '%$searchEscaped%' 
                   OR author LIKE '%$searchEscaped%'";
}

$countResult = $conn->query($countSql);
$totalBooks = (int)$countResult->fetch_assoc()['total'];
$totalPages = ceil($totalBooks / $perPage);

/* ==================== FETCH BOOKS ==================== */
$sql = "SELECT * FROM books";

if ($search !== '') {
    $sql .= " WHERE title LIKE '%$searchEscaped%' 
              OR author LIKE '%$searchEscaped%'";
}

$sql .= "
    ORDER BY 
        CASE WHEN status = 'available' THEN 0 ELSE 1 END,
        title ASC
    LIMIT $perPage OFFSET $offset
";

$result = $conn->query($sql);

/* ==================== INCLUDE NAVBAR ==================== */
include "includes/navbar.php";
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>E-Library ICC</title>

    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/component.css">
    <link rel="stylesheet" href="css/index1.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Itim&family=Prompt:wght@300;400;500;600;700&display=swap');
    </style>
</head>

<body>

<div class="index-page">
<div class="container">

<!-- ================== NEWS IMAGE SLIDER ================== -->
<div class="news-slider">
    <div class="news-slides">
        <img src="img/news1.png" class="slide active">
        <img src="img/news2.jpg" class="slide">
        <img src="img/news3.png" class="slide">
        <img src="img/news4.png" class="slide">
        <img src="img/news5.png" class="slide">
    </div>

    <div class="slide-zone prev-zone"></div>
    <div class="slide-zone next-zone"></div>
    <div class="indicators"></div>
</div>

<!-- ================== BOOK LIST ================== -->
<h2 class="section-title">📚 หนังสือทั้งหมด</h2>

<div class="book-list">
<?php while ($b = $result->fetch_assoc()): 
    $bid = (int)$b['id'];

    $favCheck = $conn->query(
        "SELECT id FROM favorites WHERE user_id=$uid AND book_id=$bid"
    );
    $isFav = $favCheck->num_rows > 0;
?>
    <div class="book-card">

        <a href="book_detail.php?id=<?= $b['id'] ?>">
            <img src="<?= htmlspecialchars($b['cover']) ?>" alt="book">
        </a>

        <h3>
            <a href="book_detail.php?id=<?= $b['id'] ?>" style="color:inherit;text-decoration:none;">
                <?= htmlspecialchars($b['title']) ?>
            </a>
        </h3>

        <p><?= htmlspecialchars($b['author']) ?></p>
        <p>สถานะ: <?= htmlspecialchars($b['status']) ?></p>

        <div class="book-actions">
            <a href="favorite_toggle.php?id=<?= $b['id'] ?>"
               class="btn btn-fav <?= $isFav ? 'active' : '' ?>">
                ⭐
            </a>
        </div>

    </div>
<?php endwhile; ?>
</div>

<!-- ================== PAGINATION ================== -->
<?php if ($totalPages > 1): ?>
<div class="pagination">

    <!-- Prev -->
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&q=<?= urlencode($search) ?>" class="nav">
            « Prev
        </a>
    <?php else: ?>
        <span class="disabled nav">« Prev</span>
    <?php endif; ?>

    <!-- Page numbers -->
    <?php
    $start = max(1, $page - 2);
    $end   = min($totalPages, $page + 2);
    ?>

    <?php if ($start > 1): ?>
        <a href="?page=1&q=<?= urlencode($search) ?>">1</a>
        <?php if ($start > 2): ?>
            <span class="dots">...</span>
        <?php endif; ?>
    <?php endif; ?>

    <?php for ($i = $start; $i <= $end; $i++): ?>
        <a href="?page=<?= $i ?>&q=<?= urlencode($search) ?>"
           class="<?= $i == $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($end < $totalPages): ?>
        <?php if ($end < $totalPages - 1): ?>
            <span class="dots">...</span>
        <?php endif; ?>
        <a href="?page=<?= $totalPages ?>&q=<?= urlencode($search) ?>">
            <?= $totalPages ?>
        </a>
    <?php endif; ?>

    <!-- Next -->
    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>" class="nav">
            Next »
        </a>
    <?php else: ?>
        <span class="disabled nav">Next »</span>
    <?php endif; ?>

</div>
<?php endif; ?>


<?php include "includes/footer.php"; ?>

<!-- ================== SLIDER SCRIPT ================== -->
<script>
const slider = document.querySelector('.news-slides');
const indicatorsBox = document.querySelector('.indicators');
let slides = slider.children;
let index = 0;
let isAnimating = false;
let timer;

/* indicator */
for (let i = 0; i < slides.length; i++) {
    const dot = document.createElement('span');
    if (i === 0) dot.classList.add('active');
    indicatorsBox.appendChild(dot);
}
let dots = indicatorsBox.children;

function updateDots() {
    [...dots].forEach(d => d.classList.remove('active'));
    dots[index % dots.length].classList.add('active');
}

function nextSlide() {
    if (isAnimating) return;
    isAnimating = true;

    slider.style.transform = 'translateX(-100%)';
    setTimeout(() => {
        slider.appendChild(slides[0]);
        slider.style.transition = 'none';
        slider.style.transform = 'translateX(0)';
        slider.offsetHeight;
        slider.style.transition = 'transform 0.8s ease';

        isAnimating = false;
        index++;
        updateDots();
    }, 800);
}

function prevSlide() {
    if (isAnimating) return;
    isAnimating = true;

    slider.insertBefore(slides[slides.length - 1], slides[0]);
    slider.style.transition = 'none';
    slider.style.transform = 'translateX(-100%)';
    slider.offsetHeight;
    slider.style.transition = 'transform 0.8s ease';
    slider.style.transform = 'translateX(0)';

    setTimeout(() => {
        isAnimating = false;
        index = (index - 1 + dots.length) % dots.length;
        updateDots();
    }, 800);
}

function startAuto() {
    timer = setInterval(nextSlide, 4000);
}
startAuto();

document.querySelector('.next-zone').onclick = () => {
    nextSlide();
    clearInterval(timer);
    startAuto();
};

document.querySelector('.prev-zone').onclick = () => {
    prevSlide();
    clearInterval(timer);
    startAuto();
};

const sliderWrapper = document.querySelector('.news-slider');
sliderWrapper.addEventListener('mouseenter', () => clearInterval(timer));
sliderWrapper.addEventListener('mouseleave', startAuto);
</script>

</body>
</html>
