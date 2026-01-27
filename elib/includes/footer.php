<style>
  * {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: "Segoe UI", Tahoma, sans-serif;
}

body {
  background-color: #f5f7fa;
  color: #333;
}

.section-title {
  font-size: 24px;
  margin-bottom: 20px;
  color: #1e3a8a;
}

footer {
  position: relative;
  margin-top: 60px;
  padding: 30px 20px;
  overflow: hidden;

  background:
    radial-gradient(
      circle at var(--x, 50%) var(--y, 50%),
      rgba(255,255,255,0.15),
      transparent 30%
    ),
    linear-gradient(135deg, #2563eb, #2563eb, #38bdf8);

  color: #e0f2fe;
  backdrop-filter: blur(10px);
  box-shadow:
    0 -10px 30px rgba(0,0,0,0.25),
    inset 0 1px 0 rgba(255,255,255,0.25);
}

footer::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 1px;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255,255,255,0.4),
    transparent
  );
}

.footer-content span {
  opacity: 0.85;
}

.footer-content {
  font-family: "Prompt", sans-serif;
  font-style: normal;
  max-width: 1200px;
  margin: auto;
  text-align: center;
  font-size: 14px;
  line-height: 1.8;
  position: relative;
  z-index: 1;
}

.footer-content p {
  font-family: "Prompt", sans-serif;
  font-style: normal;
  max-width: 1200px;
  margin: auto;
  text-align: center;
  font-size: 14px;
  line-height: 1.8;
  position: relative;
  z-index: 1;
}



@import url('https://fonts.googleapis.com/css2?family=Itim&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

</style>

<footer>
  <div class="footer-content">
    <p>© 2025 E-Library ICC | ห้องสมุดดิจิทัลเพื่อการเรียนรู้</p>
    <p>present by DBTech</p>
  </div>
</footer>
