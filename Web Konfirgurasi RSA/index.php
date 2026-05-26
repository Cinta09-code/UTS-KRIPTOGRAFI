<?php
session_start();

$secret_key = "KUNCI_RAHASIA_SAYA_123"; 
$message = "";
$status_class = "";

if (isset($_POST['generate_key'])) {
    $_SESSION['app_key'] = $secret_key;
    $message = "Kunci Keamanan Berhasil Diaktifkan!";
    $status_class = "alert-success";
}

if (isset($_POST['sign_data'])) {
    $data = $_POST['data_to_sign'];
    $sig = hash_hmac('sha256', $data, $secret_key);
    $_SESSION['last_sig'] = $sig;
    $_SESSION['last_data'] = $data;
    $message = "Dokumen Digital Berhasil Ditandatangani!";
    $status_class = "alert-success";
}

if (isset($_POST['verify_data'])) {
    $data_input = $_POST['data_to_verify'];
    $sig_input = $_POST['signature'];
    $expected_sig = hash_hmac('sha256', $data_input, $secret_key);
    
    if ($sig_input === $expected_sig) {
        $message = "✅ VERIFIKASI BERHASIL: Dokumen Masih Asli & Valid!";
        $status_class = "alert-success";
    } else {
        $message = "⚠️ PERINGATAN: Modifikasi Terdeteksi! Dokumen Tidak Valid!";
        $status_class = "alert-danger";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Verifikator Dokumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .main-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            background: #ffffff;
            overflow: hidden;
        }
        .header-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .step-card {
            border: 1px solid #edf2f7;
            border-radius: 15px;
            transition: transform 0.3s ease;
            height: 100%;
        }
        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }
        .btn-custom {
            border-radius: 10px;
            font-weight: 600;
            padding: 10px;
            transition: all 0.3s;
        }
        .alert {
            border-radius: 12px;
            border: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">
            <div class="card main-card">
                <div class="header-gradient">
                    <h2 class="fw-bold mb-1"><i class="fas fa-shield-check"></i> Document Verificator</h2>
                    <p class="mb-0 opacity-75">Sistem Deteksi Modifikasi Data dengan HMAC-SHA256</p>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <?php if($message): ?>
                        <div class="alert <?php echo $status_class; ?> d-flex align-items-center mb-4 shadow-sm" role="alert">
                            <div><?php echo $message; ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card step-card p-4">
                                <div class="icon-box bg-primary bg-opacity-10 text-primary">
                                    <i class="fas fa-key"></i>
                                </div>
                                <h5 class="fw-bold">1. Key Gen</h5>
                                <p class="text-muted small">Aktifkan kunci rahasia untuk memulai proses enkripsi.</p>
                                <form method="post" class="mt-auto">
                                    <button name="generate_key" class="btn btn-primary btn-custom w-100">
                                        Aktifkan Kunci
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card step-card p-4">
                                <div class="icon-box bg-dark bg-opacity-10 text-dark">
                                    <i class="fas fa-pen-nib"></i>
                                </div>
                                <h5 class="fw-bold">2. Digital Sign</h5>
                                <form method="post">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Isi Pesan:</label>
                                        <textarea name="data_to_sign" class="form-control form-control-sm" rows="3">Transfer ke Budi: Rp 100.000</textarea>
                                    </div>
                                    <button name="sign_data" class="btn btn-dark btn-custom w-100">
                                        Tanda Tangani
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card step-card p-4 border-primary border-opacity-25">
                                <div class="icon-box bg-success bg-opacity-10 text-success">
                                    <i class="fas fa-user-secret"></i>
                                </div>
                                <h5 class="fw-bold">3. Verifikasi (MitM)</h5>
                                <form method="post">
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Data Diterima:</label>
                                        <input type="text" name="data_to_verify" class="form-control form-control-sm" value="<?php echo $_SESSION['last_data'] ?? ''; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Signature:</label>
                                        <input type="text" name="signature" class="form-control form-control-sm" value="<?php echo $_SESSION['last_sig'] ?? ''; ?>" readonly>
                                    </div>
                                    <button name="verify_data" class="btn btn-success btn-custom w-100">
                                        Cek Keaslian
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 p-3 bg-light rounded-3">
                        <p class="mb-0 text-center text-muted small">
                            <i class="fas fa-info-circle me-1"></i> 
                            <strong>Simulasi MitM:</strong> Ubah nama <strong>"Budi"</strong> menjadi <strong>"Andi"</strong> pada kolom Verifikasi untuk melihat deteksi modifikasi.
                        </p>
                    </div>
                </div>
            </div>
            <p class="text-center mt-4 text-secondary small">&copy; 2026 Praktikum Keamanan Perangkat Lunak</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>