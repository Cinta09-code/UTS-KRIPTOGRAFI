<?php
$host    = "localhost";
$user_db = "root";
$pass_db = "";
$db_name = "keamanan_db";

$conn = new mysqli($host, $user_db, $pass_db, $db_name);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$pesan      = "";
$tipe_pesan = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = $_POST['username'];
    $pass_input = $_POST['password'];

    $sql_aman = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql_aman);
    $stmt->bind_param("ss", $user_input, $pass_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row        = $result->fetch_assoc();
        $pesan      = "Berhasil Login! Selamat datang, Role: " . $row['role'];
        $tipe_pesan = "sukses";
    } else {
        $pesan      = "Gagal Login! Sistem memblokir injeksi.";
        $tipe_pesan = "error";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SQLi Lab — Secure Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      background: linear-gradient(135deg, #1a4fa0 0%, #0d2d6b 50%, #051a4a 100%);
      display: flex; align-items: center; justify-content: center;
      padding: 2rem;
    }

    .wrap { width: 100%; max-width: 420px; }

    .brand {
      text-align: center; margin-bottom: 2rem;
    }
    .brand-icon {
      width: 64px; height: 64px;
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.25);
      border-radius: 16px;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 1rem;
    }
    .brand-icon i { font-size: 28px; color: #fff; }
    .brand h1 { color: #fff; font-size: 22px; font-weight: 600; margin-bottom: 4px; }
    .brand p  { color: rgba(255,255,255,0.6); font-size: 14px; }

    .card {
      background: #fff; border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .pesan {
      display: none; padding: 12px 16px;
      border-radius: 8px; margin-bottom: 1.25rem;
      font-size: 14px; font-weight: 500;
    }
    .pesan.sukses { display:block; background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
    .pesan.error  { display:block; background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

    .form-group { margin-bottom: 1.25rem; }
    .form-group label {
      display: block; font-size: 12px; font-weight: 500;
      color: #1a4fa0; letter-spacing: 0.5px;
      text-transform: uppercase; margin-bottom: 8px;
    }
    .input-wrap { position: relative; }
    .input-wrap .icon-left {
      position: absolute; left: 12px; top: 50%;
      transform: translateY(-50%);
      font-size: 18px; color: #94a3b8; pointer-events: none;
    }
    .input-wrap input {
      width: 100%;
      padding: 11px 12px 11px 40px;
      border: 1.5px solid #e2e8f0; border-radius: 8px;
      font-size: 14px; font-family: 'Inter', sans-serif;
      color: #1e293b; outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .input-wrap input:focus {
      border-color: #1a4fa0;
      box-shadow: 0 0 0 3px rgba(26,79,160,0.12);
    }
    .input-wrap input::placeholder { color: #cbd5e1; }
    .toggle-pass {
      position: absolute; right: 12px; top: 50%;
      transform: translateY(-50%);
      background: none; border: none;
      cursor: pointer; color: #94a3b8; font-size: 18px; padding: 2px;
    }

    .btn-submit {
      width: 100%; height: 46px;
      background: linear-gradient(135deg, #1a4fa0, #0d2d6b);
      border: none; border-radius: 8px;
      color: #fff; font-size: 15px; font-weight: 500;
      font-family: 'Inter', sans-serif; cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      transition: opacity 0.2s, transform 0.15s;
      margin-top: 0.25rem;
    }
    .btn-submit:hover  { opacity: 0.9; transform: translateY(-1px); }
    .btn-submit:active { transform: translateY(0); }

    .card-footer {
      margin-top: 1.5rem; padding-top: 1.25rem;
      border-top: 1px solid #f1f5f9;
      display: flex; align-items: center; gap: 8px;
    }
    .dot { width: 8px; height: 8px; background: #10b981; border-radius: 50%; flex-shrink: 0; }
    .card-footer span { font-size: 12px; color: #64748b; }

    .copyright { text-align: center; margin-top: 1.25rem; font-size: 12px; color: rgba(255,255,255,0.4); }
  </style>
</head>
<body>
  <div class="wrap">

    <div class="brand">
      <div class="brand-icon">
        <i class="ti ti-building-skyscraper"></i>
      </div>
      <h1>Sistem Login Perusahaan</h1>
      <p>Masuk ke portal karyawan</p>
    </div>

    <div class="card">

      <?php if (!empty($pesan)) : ?>
        <div class="pesan <?= $tipe_pesan ?>">
          <i class="ti <?= $tipe_pesan === 'sukses' ? 'ti-circle-check' : 'ti-alert-circle' ?>"
            style="font-size:16px;vertical-align:-2px;margin-right:6px"></i>
          <?= htmlspecialchars($pesan) ?>
        </div>
      <?php endif; ?>

      <form method="POST">
        <div class="form-group">
          <label for="username">Username</label>
          <div class="input-wrap">
            <i class="ti ti-user icon-left"></i>
            <input type="text" name="username" id="username"
              placeholder="Masukkan username" required />
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrap">
            <i class="ti ti-lock icon-left"></i>
            <input type="password" name="password" id="password"
              placeholder="Masukkan password" required />
            <button type="button" class="toggle-pass" onclick="togglePassword()" aria-label="Tampilkan password">
              <i class="ti ti-eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-submit">
          <i class="ti ti-login" style="font-size:18px"></i>
          Masuk
        </button>
      </form>

      <div class="card-footer">
        <div class="dot"></div>
        <span>Dilindungi Prepared Statements · SQL Injection Safe</span>
      </div>
    </div>

    <p class="copyright">&copy; 2026 Sistem Keamanan Perusahaan</p>
  </div>

  <script>
    function togglePassword() {
      const inp  = document.getElementById('password');
      const icon = document.getElementById('eyeIcon');
      const show = inp.type === 'password';
      inp.type       = show ? 'text' : 'password';
      icon.className = show ? 'ti ti-eye-off' : 'ti ti-eye';
    }
  </script>
</body>
</html>