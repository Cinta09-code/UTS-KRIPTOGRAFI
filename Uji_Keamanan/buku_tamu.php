<?php
$file_data = "komentar.txt";

if (isset($_GET['reset'])) {
    file_put_contents($file_data, "");
    header("Location: buku_tamu_aman.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama  = $_POST['nama']  ?? 'Anonim';
    $pesan = $_POST['pesan'] ?? '';
    if (!empty($pesan)) {
        $data_baru = $nama . "|" . $pesan . "\n";
        file_put_contents($file_data, $data_baru, FILE_APPEND);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buku Tamu — Versi Aman Anti-XSS</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0a0f1e 0%, #0d1b3e 100%);
      min-height: 100vh;
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding: 40px 16px;
    }

    .container { width: 100%; max-width: 620px; }

    .header { margin-bottom: 28px; }
    .header-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(34, 197, 94, 0.12);
      border: 1px solid rgba(34, 197, 94, 0.3);
      color: #86efac;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 4px 10px;
      border-radius: 20px;
      margin-bottom: 12px;
    }
    .header h1 {
      font-size: 26px;
      font-weight: 600;
      color: #e2e8f0;
      letter-spacing: -0.02em;
    }
    .header p { margin-top: 6px; font-size: 13px; color: #64748b; }

    .card {
      background: rgba(15, 23, 42, 0.95);
      border: 1px solid #1e3a5f;
      border-radius: 16px;
      padding: 28px;
      margin-bottom: 20px;
      backdrop-filter: blur(10px);
      box-shadow: 0 4px 24px rgba(0,0,0,0.3);
    }
    .card-title {
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0.07em;
      text-transform: uppercase;
      color: #3b82f6;
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .card-title::before {
      content: "";
      width: 3px;
      height: 14px;
      background: linear-gradient(180deg, #3b82f6, #1d4ed8);
      border-radius: 2px;
    }

    .form-group { margin-bottom: 14px; }
    label { display: block; font-size: 12px; font-weight: 500; color: #94a3b8; margin-bottom: 6px; }
    input[type="text"], textarea {
      width: 100%;
      background: #070d1a;
      border: 1px solid #1e3a5f;
      border-radius: 8px;
      padding: 10px 14px;
      font-family: 'Inter', sans-serif;
      font-size: 13px;
      color: #e2e8f0;
      transition: border-color 0.2s, box-shadow 0.2s;
      outline: none;
      resize: none;
    }
    input[type="text"]:focus, textarea:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
    }
    textarea { min-height: 80px; }

    .btn-row { display: flex; align-items: center; gap: 12px; margin-top: 4px; }
    .btn-submit {
      background: linear-gradient(135deg, #2563eb, #1d4ed8);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 9px 22px;
      font-family: 'Inter', sans-serif;
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      transition: opacity 0.2s;
      box-shadow: 0 2px 10px rgba(37,99,235,0.4);
    }
    .btn-submit:hover { opacity: 0.85; }
    .btn-reset { font-size: 12px; color: #ef4444; text-decoration: none; opacity: 0.6; transition: opacity 0.2s; }
    .btn-reset:hover { opacity: 1; }

    .comment-item {
      background: #070d1a;
      border: 1px solid #1e3a5f;
      border-left: 3px solid #22c55e;
      border-radius: 0 8px 8px 0;
      padding: 10px 14px;
      margin-bottom: 10px;
      font-size: 13px;
      color: #94a3b8;
      word-break: break-all;
    }
    .comment-item b { color: #cbd5e1; font-weight: 500; }
    .empty { text-align: center; padding: 30px; color: #334155; font-size: 13px; }

    .notice {
      background: rgba(34, 197, 94, 0.05);
      border: 1px solid rgba(34, 197, 94, 0.2);
      border-radius: 10px;
      padding: 14px 16px;
      font-size: 12px;
      color: #86efac;
      line-height: 1.7;
    }
    .notice strong { color: #4ade80; }
    code {
      background: rgba(34,197,94,0.1);
      border-radius: 4px;
      padding: 1px 5px;
      font-family: monospace;
      font-size: 11.5px;
      color: #86efac;
    }
  </style>
</head>
<body>
<div class="container">

  <div class="header">
    <div class="header-badge">✓ Aman Anti-XSS</div>
    <h1>Buku Tamu Pengunjung</h1>
    <p>Versi terlindungi — input difilter dengan <code style="background:rgba(59,130,246,0.15);color:#93c5fd;border-radius:4px;padding:1px 6px;font-size:12px">htmlspecialchars()</code></p>
  </div>

  <div class="card">
    <div class="card-title">Form Komentar</div>
    <form method="POST">
      <div class="form-group">
        <label>Nama Anda</label>
        <input type="text" name="nama" placeholder="Masukkan nama..." required>
      </div>
      <div class="form-group">
        <label>Pesan</label>
        <textarea name="pesan" placeholder="Tulis pesan Anda..." required></textarea>
      </div>
      <div class="btn-row">
        <button type="submit" class="btn-submit">Kirim Pesan</button>
        <a href="?reset=1" class="btn-reset">[ Hapus Semua Komentar ]</a>
      </div>
    </form>
  </div>

  <div class="card">
    <div class="card-title">Daftar Komentar</div>
    <?php
    if (file_exists($file_data)) {
        $isi_file = file($file_data, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (count($isi_file) > 0) {
            foreach ($isi_file as $baris) {
                list($nama_user, $pesan_user) = explode("|", $baris, 2);

                // ✅ PATCH: htmlspecialchars() mengubah karakter berbahaya menjadi HTML entity
                // ENT_QUOTES = konversi tanda kutip ganda (") dan tunggal (')
                // 'UTF-8'    = encoding agar karakter Indonesia diproses dengan benar
                $nama_aman  = htmlspecialchars($nama_user,  ENT_QUOTES, 'UTF-8');
                $pesan_aman = htmlspecialchars($pesan_user, ENT_QUOTES, 'UTF-8');

                echo "<div class='comment-item'><b>$nama_aman:</b> $pesan_aman</div>";
            }
        } else {
            echo "<div class='empty'>Belum ada komentar.</div>";
        }
    } else {
        echo "<div class='empty'>Belum ada komentar.</div>";
    }
    ?>
  </div>

  <div class="notice">
    <strong>✓ Perlindungan Aktif:</strong><br>
    Semua input diproses melalui <code>htmlspecialchars($input, ENT_QUOTES, 'UTF-8')</code>
    sebelum ditampilkan. Karakter <code>&lt;</code> dan <code>&gt;</code> dikonversi menjadi
    HTML entity <code>&amp;lt;</code> dan <code>&amp;gt;</code> — browser membaca sebagai teks biasa,
    bukan kode JavaScript.
  </div>

</div>
</body>
</html>