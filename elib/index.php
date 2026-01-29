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
$uid  = (int)$user['id'];

/* ==================== SEARCH ==================== */
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

/* ==================== PAGINATION ==================== */
$perPage = 12;
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $perPage;

/* ==================== COUNT BOOKS ==================== */
$where = "";
if ($search !== '') {
    $searchEscaped = $conn->real_escape_string($search);
    $where = "WHERE title LIKE '%$searchEscaped%' OR author LIKE '%$searchEscaped%'";
}

$countSql = "SELECT COUNT(*) AS total FROM books $where";
$totalBooks = (int)$conn->query($countSql)->fetch_assoc()['total'];
$totalPages = ceil($totalBooks / $perPage);

/* ==================== FETCH BOOKS ==================== */
$sql = "
    SELECT * FROM books
    $where
    ORDER BY 
        CASE WHEN status='available' THEN 0 ELSE 1 END,
        title ASC
    LIMIT $perPage OFFSET $offset
";
$result = $conn->query($sql);

/* ==================== FAVORITES (LOAD ONCE) ==================== */
$favBooks = [];
$favRes = $conn->query("SELECT book_id FROM favorites WHERE user_id=$uid");
while ($row = $favRes->fetch_assoc()) {
    $favBooks[] = (int)$row['book_id'];
}

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

<!-- ✅ สำคัญ: wrapper สำหรับ CSS isolation -->
<div id="elib-index" class="index-page">
<div class="container">

<!-- ================== NEWS SLIDER ================== -->
<div class="news-slider">
    <div class="news-slides">
        <img src="img/news1.png">
        <img src="img/news2.jpg">
        <img src="img/news3.png">
        <img src="img/news4.png">
        <img src="img/news5.png">
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
    $isFav = in_array($bid, $favBooks);
?>
<div class="book-card">

    <a href="book_detail.php?id=<?= $bid ?>">
        <img src="<?= htmlspecialchars($b['cover']) ?>" alt="book">
    </a>

    <h3>
        <a href="book_detail.php?id=<?= $bid ?>">
            <?= htmlspecialchars($b['title']) ?>
        </a>
    </h3>

    <p><?= htmlspecialchars($b['author']) ?></p>
    <p>สถานะ: <?= htmlspecialchars($b['status']) ?></p>

    <div class="book-actions">
        <button type="button"
                class="btn-fav favorite-toggle <?= $isFav ? 'active' : '' ?>"
                data-book-id="<?= $bid ?>">
            ⭐
        </button>
    </div>

</div>
<?php endwhile; ?>
</div>

<!-- ================== PAGINATION ================== -->
<?php if ($totalPages > 1): ?>
<div class="pagination">

<?php if ($page > 1): ?>
<a class="nav" href="?page=<?= $page-1 ?>&q=<?= urlencode($search) ?>">« Prev</a>
<?php else: ?>
<span class="disabled nav">« Prev</span>
<?php endif; ?>

<?php
$start = max(1, $page - 2);
$end   = min($totalPages, $page + 2);
?>

<?php if ($start > 1): ?>
<a href="?page=1&q=<?= urlencode($search) ?>">1</a>
<?php if ($start > 2): ?><span class="dots">...</span><?php endif; ?>
<?php endif; ?>

<?php for ($i=$start; $i<=$end; $i++): ?>
<a class="<?= $i==$page ? 'active' : '' ?>"
   href="?page=<?= $i ?>&q=<?= urlencode($search) ?>">
   <?= $i ?>
</a>
<?php endfor; ?>

<?php if ($end < $totalPages): ?>
<?php if ($end < $totalPages-1): ?><span class="dots">...</span><?php endif; ?>
<a href="?page=<?= $totalPages ?>&q=<?= urlencode($search) ?>"><?= $totalPages ?></a>
<?php endif; ?>

<?php if ($page < $totalPages): ?>
<a class="nav" href="?page=<?= $page+1 ?>&q=<?= urlencode($search) ?>">Next »</a>
<?php else: ?>
<span class="disabled nav">Next »</span>
<?php endif; ?>

</div>
<?php endif; ?>

</div>
</div>

<?php include "includes/footer.php"; ?>

<!-- ================== JS (SCOPED) ================== -->
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ================= ROOT ================= */
    const root = document.querySelector('#elib-index');
    if (!root) return;

    /* ================= NEWS SLIDER ================= */
    const slider = root.querySelector('.news-slides');
    const indicatorsBox = root.querySelector('.indicators');

    if (slider && indicatorsBox) {
        let slides = slider.children;
        let index = 0;
        let animating = false;

        // create indicators
        [...slides].forEach((_, i) => {
            const dot = document.createElement('span');
            if (i === 0) dot.classList.add('active');
            indicatorsBox.appendChild(dot);
        });

        const dots = indicatorsBox.children;

        const updateDots = () => {
            [...dots].forEach(d => d.classList.remove('active'));
            dots[index % dots.length].classList.add('active');
        };

        const next = () => {
            if (animating) return;
            animating = true;

            slider.style.transform = 'translateX(-100%)';

            setTimeout(() => {
                slider.appendChild(slides[0]);
                slider.style.transition = 'none';
                slider.style.transform = 'translateX(0)';
                slider.offsetHeight;
                slider.style.transition = 'transform .8s ease';

                index++;
                updateDots();
                animating = false;
            }, 800);
        };

        const prev = () => {
            if (animating) return;
            animating = true;

            slider.insertBefore(slides[slides.length - 1], slides[0]);
            slider.style.transition = 'none';
            slider.style.transform = 'translateX(-100%)';
            slider.offsetHeight;
            slider.style.transition = 'transform .8s ease';
            slider.style.transform = 'translateX(0)';

            setTimeout(() => {
                index--;
                updateDots();
                animating = false;
            }, 800);
        };

        let timer = setInterval(next, 4000);

        const nextZone = root.querySelector('.next-zone');
        const prevZone = root.querySelector('.prev-zone');

        if (nextZone) {
            nextZone.addEventListener('click', () => {
                clearInterval(timer);
                next();
            });
        }

        if (prevZone) {
            prevZone.addEventListener('click', () => {
                clearInterval(timer);
                prev();
            });
        }
    }

    /* ================= FAVORITE TOGGLE ================= */
    root.querySelectorAll('.favorite-toggle').forEach(btn => {

        btn.addEventListener('click', () => {

            if (btn.dataset.loading === '1') return;
            btn.dataset.loading = '1';
            btn.disabled = true;

            fetch('favorite_toggle.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'book_id=' + encodeURIComponent(btn.dataset.bookId)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.classList.toggle('active', data.isFavorite);
                } else if (data.message) {
                    alert(data.message);
                }
            })
            .catch(err => {
                console.error('Favorite error:', err);
                alert('เกิดข้อผิดพลาด กรุณาลองใหม่');
            })
            .finally(() => {
                btn.disabled = false;
                btn.dataset.loading = '0';
            });
        });

    });

});
</script>


</body>
</html>
