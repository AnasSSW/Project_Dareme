<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;

// รับค่าค้นหาจากหน้าเรียกใช้
$search = $search ?? '';
$searchValue = htmlspecialchars($search);

// ดึงหมวดหมู่
include "db.php";
$catResult = $conn->query("SELECT id, name FROM categories ORDER BY name");
?>

<style>
/* ================= NAVBAR ================= */
.navbar {
  position: relative;
  padding: 16px 0;
  z-index: 1000;
  background:
    radial-gradient(circle at var(--x, 50%) var(--y, 50%), rgba(255,255,255,0.2), transparent 30%),
    linear-gradient(135deg, #1e40af, #2563eb, #3b82f6, #38bdf8);
  backdrop-filter: blur(12px);
  box-shadow: 0 12px 40px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.2), inset 0 -1px 0 rgba(255,255,255,0.15);
}

.navbar-container {
  max-width: 1200px;
  margin: auto;
  padding: 0 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.navbar-top {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  grid-template-rows: auto auto;
  align-items: center;
  gap: 24px;
}

/* ---------- BRAND ---------- */
.brand {
  grid-column: 1;
  grid-row: 1 / 3;  /* ขยายเต็มสองแถว */
  display: flex;
  align-items: center;
  gap: 14px;
  color: #fff;
}

.brand img {
  width: 64px;  /* เพิ่มขนาดรูป */
  height: 64px;
  border-radius: 50%;
  border: 2px solid rgba(255,255,255,0.3);
  box-shadow: 0 0 20px rgba(255,255,255,0.5);
}

.brand-text { 
  display: flex; 
  flex-direction: column; 
  gap: 4px;
}

.brand-title { 
  font-size: 24px;  /* เพิ่มขนาดตัวอักษร */
  font-weight: 800; 
  line-height: 1.2;
}

.brand-subtitle { 
  font-size: 14px;  /* เพิ่มขนาดตัวอักษร */
  opacity: 0.9; 
  line-height: 1.3;
}

/* ---------- SEARCH ---------- */
.search-box {
  grid-column: 2;
  grid-row: 1 / 3;  /* ขยายเต็มสองแถว */
}

.search-box form {
  display: flex; 
  gap: 8px;
}

.search-box input {
  width: 320px;
  padding: 10px 14px;
  border-radius: 12px;
  border: none;
  background: rgba(255,255,255,0.2);
  color: #fff;
}

.search-box input::placeholder { color: rgba(255,255,255,0.8); }

.search-box button {
  padding: 10px 18px;
  border-radius: 12px;
  border: none;
  font-weight: 700;
  cursor: pointer;
  color: black;
  background: linear-gradient(135deg, #fbbf24, #fde047);
}

/* ---------- USER MENU ---------- */
.user-menu {
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
  justify-self: end;
  grid-column: 3;
  grid-row: 1 / 3;  /* ขยายเต็มสองแถว */
}

.username-display {
  padding: 8px 14px;
  background: rgba(255,255,255,0.15);
  border-radius: 10px;
  color: white;
  font-weight: 600;
}

.hamburger {
  font-size: 26px;
  cursor: pointer;
  padding: 6px 12px;
  border-radius: 10px;
  background: rgba(255,255,255,0.15);
  color: white;
}

/* ---------- DROPDOWN ---------- */
.dropdown {
  position: absolute;
  top: 55px;
  right: 0;
  background: white;
  border-radius: 14px;
  min-width: 220px;
  overflow: hidden;
  display: none;
  z-index: 9999;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.dropdown.show { display: block; }

.dropdown a {
  display: flex;
  gap: 10px;
  padding: 14px 18px;
  text-decoration: none;
  color: #1e293b;
  border-bottom: 1px solid #f1f5f9;
}

.dropdown a:last-child { border-bottom: none; }
.dropdown a:hover { background: #e0f2fe; }
.dropdown a:nth-child(1)::before { content: "👤"; }
.dropdown a:nth-child(2)::before { content: "📚"; }
.dropdown a:nth-child(3)::before { content: "🚪"; }

/* ---------- LOGIN ---------- */
.login-btn {
  padding: 10px 22px;
  background: linear-gradient(135deg, #fbbf24, #fde047);
  border-radius: 12px;
  font-weight: 700;
  text-decoration: none;
  color: #1e3a8a;
}

/* ---------- MAIN MENU ---------- */
.menu {
  display: flex;
  justify-content: center;
  gap: 12px;
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu a {
  padding: 10px 18px;
  color: #e0f2fe;
  text-decoration: none;
  border-radius: 12px;
  background: rgba(255,255,255,0.1);
}

.menu a:hover { background: rgba(255,255,255,0.25); }

/* ---------- CATEGORY SLIDE ---------- */
.category-wrapper { position: relative; }

.category-slide {
  position: absolute;
  top: 55px;
  left: 50%;
  transform: translateX(-50%);
  min-width: 220px;
  background: white;
  border-radius: 14px;
  overflow: hidden;
  max-height: 0;
  opacity: 0;
  pointer-events: none;
  transition: all .35s ease;
  box-shadow: 0 20px 50px rgba(0,0,0,.25);
  z-index: 9999;
}

.category-slide.show { max-height: 500px; opacity: 1; pointer-events: auto; }

.category-slide a {
  display: block;
  padding: 14px 18px;
  color: #1e293b;
  text-decoration: none;
  border-bottom: 1px solid #f1f5f9;
  font-weight: 500;
}

.category-slide a:last-child { border-bottom: none; }
.category-slide a:hover { background: #e0f2fe; }

/* ---------- RESPONSIVE ---------- */
@media (max-width: 768px) {
  .navbar-top { grid-template-columns: 1fr; }
  .brand { grid-row: auto; }
  .search-box { grid-row: auto; }
  .user-menu { grid-row: auto; }
  .search-box input { width: 100%; }
  .menu { flex-direction: column; }
}
</style>

<nav class="navbar">
  <div class="navbar-container">
    <div class="navbar-top">
      <div class="brand">
        <img src="https://lh5.googleusercontent.com/proxy/pg2Dj_tPgZ0xpH_qYAbrUWtmZnsvHuR_Q_qtOyb2Ji2h7vJqK5OrRSafK282WorIEbaFcDve_jog5W_6ggF5geIPmRGivygAFBTlMIkQ-DpgJUcklu4APw">
        <div class="brand-text">
          <span class="brand-title">E-Library</span>
          <small class="brand-subtitle">Intrachai Commercial College</small>
        </div>
      </div>

      <div class="search-box">
        <form method="get" action="index.php">
          <input type="text" name="q" placeholder="ค้นหาหนังสือ..." value="<?= $searchValue ?>">
          <button class="button1">ค้นหา</button>
        </form>
      </div>

      <div class="user-menu">
        <?php if ($user): ?>
          <div class="username-display"><?= htmlspecialchars($user['fullname']) ?></div>
          <div class="hamburger" onclick="toggleMenu()">☰</div>

          <div class="dropdown" id="dropdown">
            <a href="profile.php" class="profile">ข้อมูลส่วนตัว</a>
            <a href="borrow_history.php" class="user-history">ประวัติการยืม</a>
            <?php if ($user['role'] === 'admin'): ?>
              <a href="admin_borrow_history.php" class="admin-history">ประวัติการยืม (Admin)</a>
            <?php endif; ?>
            <a href="logout.php" class="logout">ออกจากระบบ</a>
          </div>
        <?php else: ?>
          <a href="login.php" class="login-btn">Login</a>
        <?php endif; ?>
      </div>
    </div>

    <ul class="menu">
      <li><a href="index.php">หน้าแรก</a></li>
      <li><a href="favorites.php">รายการโปรด</a></li>
      <li class="category-wrapper">
        <a href="javascript:void(0)" onclick="toggleCategory()">หมวดหมู่ ▾</a>
        <div class="category-slide" id="categorySlide">
          <?php while($cat = $catResult->fetch_assoc()): ?>
            <a href="category.php?cat=<?= $cat['id'] ?>">📚 <?= htmlspecialchars($cat['name']) ?></a>
          <?php endwhile; ?>
        </div>
      </li>
      <li><a href="return.php">คืนหนังสือ</a></li>
      <?php if ($user && $user['role'] === 'admin'): ?>
        <li><a href="admin_add.php">เพิ่มหนังสือ</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<script>
function toggleMenu() {
  document.getElementById("dropdown").classList.toggle("show");
}
document.addEventListener("click", function(e){
  if (!e.target.closest(".user-menu")) {
    const d = document.getElementById("dropdown");
    if (d) d.classList.remove("show");
  }
});

function toggleCategory() {
  document.getElementById("categorySlide").classList.toggle("show");
}
document.addEventListener("click", function(e){
  if (!e.target.closest(".category-wrapper")) {
    const cat = document.getElementById("categorySlide");
    if (cat) cat.classList.remove("show");
  }
});
</script>
