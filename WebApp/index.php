<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enkripsi & Dekripsi — Shinta Aulia Pasha</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:        #0b0c10;
            --surface:   #13141a;
            --surface2:  #1c1d26;
            --border:    rgba(255,255,255,0.08);
            --border2:   rgba(255,255,255,0.14);
            --accent:    #5b6af0;
            --accent2:   #3de6c0;
            --text:      #e4e5f0;
            --muted:     rgba(228,229,240,0.42);
            --danger:    #f05b7a;
            --success:   #3de6c0;
            --radius:    14px;
            --mono:      'JetBrains Mono', monospace;
            --sans:      'DM Sans', sans-serif;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--sans);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
        }

        /* ── background grid ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(91,106,240,0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(91,106,240,0.07) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        /* ── glow orbs ── */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
        }
        .orb-1 { width: 420px; height: 420px; background: rgba(91,106,240,0.18); top: -120px; left: -120px; }
        .orb-2 { width: 350px; height: 350px; background: rgba(61,230,192,0.12); bottom: -100px; right: -80px; }

        /* ── wrapper ── */
        .wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 560px;
        }

        /* ── top badge ── */
        .top-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.6rem;
        }
        .badge-pill {
            font-family: var(--mono);
            font-size: 10px;
            letter-spacing: 0.12em;
            padding: 4px 12px;
            border-radius: 30px;
            border: 1px solid rgba(91,106,240,0.5);
            background: rgba(91,106,240,0.12);
            color: #a0aaff;
        }
        .badge-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--accent2);
            animation: blink 2s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.2} }

        /* ── card ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: 20px;
            padding: 2rem;
            animation: fadeUp 0.5s ease both;
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(16px); }
            to   { opacity:1; transform:translateY(0); }
        }

        .card-title {
            font-family: var(--mono);
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            background: linear-gradient(120deg, #fff 30%, #a0aaff 65%, var(--accent2) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.25rem;
        }
        .card-sub {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 1.8rem;
        }

        /* ── divider ── */
        .divider {
            height: 1px;
            background: var(--border);
            margin: 1.4rem 0;
        }

        /* ── label ── */
        label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px;
        }

        /* ── inputs ── */
        textarea, input[type="number"], input[type="text"] {
            width: 100%;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 13.5px;
            font-family: var(--mono);
            color: var(--text);
            transition: border-color 0.2s, box-shadow 0.2s;
            resize: vertical;
        }
        textarea { min-height: 100px; }
        textarea:focus, input[type="number"]:focus, input[type="text"]:focus {
            outline: none;
            border-color: rgba(91,106,240,0.65);
            box-shadow: 0 0 0 3px rgba(91,106,240,0.15);
        }

        /* ── algo selector ── */
        .algo-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 1.2rem;
        }
        .algo-option { display: none; }
        .algo-label {
            display: flex;
            flex-direction: column;
            gap: 3px;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            background: var(--surface2);
            transition: all 0.2s;
        }
        .algo-label:hover { border-color: var(--border2); }
        .algo-option:checked + .algo-label {
            border-color: rgba(91,106,240,0.7);
            background: rgba(91,106,240,0.12);
        }
        .algo-name { font-size: 13px; font-weight: 600; color: var(--text); }
        .algo-desc { font-size: 10px; color: var(--muted); }

        /* ── input row ── */
        .input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 1.2rem;
        }
        .field { margin-bottom: 1.2rem; }

        /* ── buttons ── */
        .btn-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 1.4rem;
        }
        .btn {
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            font-family: var(--sans);
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, filter 0.15s;
        }
        .btn:hover  { transform: translateY(-1px); filter: brightness(1.1); }
        .btn:active { transform: translateY(0); filter: brightness(0.95); }
        .btn-enc {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 4px 18px rgba(91,106,240,0.35);
        }
        .btn-enc:hover { box-shadow: 0 8px 24px rgba(91,106,240,0.5); }
        .btn-dec {
            background: transparent;
            color: var(--accent2);
            border: 1px solid rgba(61,230,192,0.45);
        }
        .btn-dec:hover { background: rgba(61,230,192,0.08); }

        /* ── result ── */
        .result-wrap {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1rem 1.1rem;
            margin-bottom: 1.4rem;
        }
        .result-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .result-tag {
            font-family: var(--mono);
            font-size: 9.5px;
            letter-spacing: 0.12em;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .tag-enc { background: rgba(91,106,240,0.18); color: #a0aaff; }
        .tag-dec { background: rgba(61,230,192,0.15); color: var(--accent2); }
        .result-text {
            font-family: var(--mono);
            font-size: 14px;
            line-height: 1.65;
            word-break: break-all;
            color: var(--text);
        }

        /* ── error ── */
        .error-box {
            background: rgba(240,91,122,0.1);
            border: 1px solid rgba(240,91,122,0.3);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            color: var(--danger);
            margin-bottom: 1.2rem;
        }

        /* ── identity bar ── */
        .identity-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.9rem 1rem;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 12px;
        }
        .avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            display: flex; align-items: center; justify-content: center;
            font-family: var(--mono);
            font-size: 13px; font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }
        .id-name  { font-size: 13px; font-weight: 600; color: var(--text); }
        .id-meta  { font-size: 11px; color: var(--muted); font-family: var(--mono); }

        /* ── footer ── */
        .footer {
            text-align: center;
            font-size: 10.5px;
            font-family: var(--mono);
            color: rgba(228,229,240,0.2);
            margin-top: 1.4rem;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<div class="wrapper">

    <!-- top badge -->
    <div class="top-badge">
        <span class="badge-dot"></span>
        <span class="badge-pill">CIPHER TOOL v2.0</span>
    </div>

    <!-- card -->
    <div class="card">
        <div class="card-title">Enkripsi &amp; Dekripsi</div>
        <p class="card-sub">Caesar Cipher &amp; Vigenère Cipher · Kriptografi</p>

        <form method="POST" action="">

            <!-- Algoritma -->
            <label>Algoritma</label>
            <div class="algo-grid">
                <input type="radio" name="method" id="caesar" value="caesar" class="algo-option"
                    <?php echo (!isset($_POST['method']) || $_POST['method'] === 'caesar') ? 'checked' : ''; ?>>
                <label for="caesar" class="algo-label">
                    <span class="algo-name">Caesar Cipher</span>
                    <span class="algo-desc">Pergeseran karakter (A–Z)</span>
                </label>

                <input type="radio" name="method" id="vigenere" value="vigenere" class="algo-option"
                    <?php echo (isset($_POST['method']) && $_POST['method'] === 'vigenere') ? 'checked' : ''; ?>>
                <label for="vigenere" class="algo-label">
                    <span class="algo-name">Vigenère Cipher</span>
                    <span class="algo-desc">Kunci kata, multi-shift</span>
                </label>
            </div>

            <!-- Pesan -->
            <div class="field">
                <label for="text">Pesan (Plaintext / Ciphertext)</label>
                <textarea name="text" id="text" placeholder="Ketik pesan di sini..."><?php echo isset($_POST['text']) ? htmlspecialchars($_POST['text']) : ''; ?></textarea>
            </div>

            <!-- Key -->
            <div class="input-row">
                <!-- Caesar key -->
                <div id="key-caesar-wrap">
                    <label for="key_caesar">Key — Pergeseran (Caesar)</label>
                    <input type="number" name="key_caesar" id="key_caesar" min="1" max="25"
                        value="<?php echo isset($_POST['key_caesar']) ? (int)$_POST['key_caesar'] : 3; ?>"
                        placeholder="1–25">
                </div>
                <!-- Vigenere key -->
                <div id="key-vigenere-wrap">
                    <label for="key_vigenere">Key — Kata Kunci (Vigenère)</label>
                    <input type="text" name="key_vigenere" id="key_vigenere"
                        value="<?php echo isset($_POST['key_vigenere']) ? htmlspecialchars($_POST['key_vigenere']) : ''; ?>"
                        placeholder="Contoh: KUNCI">
                </div>
            </div>

            <!-- Buttons -->
            <div class="btn-row">
                <button type="submit" name="action" value="encrypt" class="btn btn-enc">🔒 Enkripsi</button>
                <button type="submit" name="action" value="decrypt" class="btn btn-dec">🔓 Dekripsi</button>
            </div>

            <!-- Identity bar (di bawah tombol) -->
            <div class="identity-bar">
                <div class="avatar">SA</div>
                <div>
                    <div class="id-name">Shinta Aulia Pasha</div>
                    <div class="id-meta">231220030 &nbsp;·&nbsp; Universitas Muhammadiyah Pontianak</div>
                </div>
            </div>

        </form>

        <?php
        function caesar_cipher($text, $key, $is_encrypt = true) {
            $result = "";
            $key    = (int)$key % 26;
            if (!$is_encrypt) $key = -$key;

            for ($i = 0; $i < strlen($text); $i++) {
                $char = $text[$i];

                if (ctype_alpha($char)) {
                    // hanya geser huruf A-Z / a-z
                    $base   = ctype_upper($char) ? ord('A') : ord('a');
                    $result .= chr((ord($char) - $base + $key + 26) % 26 + $base);
                } else {
                    // spasi, angka, simbol → abaikan (lewatkan apa adanya)
                    $result .= $char;
                }
            }
            return $result;
        }

        function vigenere_cipher($text, $key, $is_encrypt = true) {
            $result   = "";
            $key      = strtolower(preg_replace('/[^a-zA-Z]/', '', $key)); // hanya huruf
            $keyLen   = strlen($key);
            $keyIndex = 0;

            if ($keyLen === 0) return $text; // key kosong → kembalikan apa adanya

            for ($i = 0; $i < strlen($text); $i++) {
                $char = $text[$i];

                if (ctype_alpha($char)) {
                    $shift = ord($key[$keyIndex % $keyLen]) - ord('a');
                    if (!$is_encrypt) $shift = -$shift;

                    $base     = ctype_upper($char) ? ord('A') : ord('a');
                    $result   .= chr((ord($char) - $base + $shift + 26) % 26 + $base);
                    $keyIndex++;
                } else {
                    $result .= $char;
                }
            }
            return $result;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
            $text      = trim($_POST['text'] ?? '');
            $method    = $_POST['method'] ?? 'caesar';
            $action    = $_POST['action']; // 'encrypt' atau 'decrypt'
            $is_enc    = ($action === 'encrypt');
            $error     = '';
            $output    = '';

            // Validasi pesan tidak kosong
            if ($text === '') {
                $error = 'Pesan tidak boleh kosong.';
            }

            if (!$error) {
                if ($method === 'caesar') {
                    $key_c = (int)($_POST['key_caesar'] ?? 3);
                    if ($key_c < 1 || $key_c > 25) {
                        $error = 'Key Caesar harus antara 1 sampai 25.';
                    } else {
                        $output = caesar_cipher($text, $key_c, $is_enc);
                    }
                } else {
                    $key_v = trim($_POST['key_vigenere'] ?? '');
                    if ($key_v === '' || !preg_match('/[a-zA-Z]/', $key_v)) {
                        $error = 'Key Vigenère harus mengandung minimal satu huruf.';
                    } else {
                        $output = vigenere_cipher($text, $key_v, $is_enc);
                    }
                }
            }

            // Tampilkan error
            if ($error) {
                echo '<div class="error-box">⚠ ' . htmlspecialchars($error) . '</div>';
            }

            // Tampilkan hasil
            if ($output !== '') {
                $tag_class = $is_enc ? 'tag-enc' : 'tag-dec';
                $tag_label = $is_enc ? 'HASIL ENKRIPSI' : 'HASIL DEKRIPSI';
                $algo_name = ($method === 'caesar') ? 'Caesar' : 'Vigenère';
                echo '<div class="divider"></div>';
                echo '<div class="result-wrap">';
                echo '  <div class="result-meta">';
                echo '    <span class="result-tag ' . $tag_class . '">' . $tag_label . '</span>';
                echo '    <span style="font-size:10px;color:var(--muted);font-family:var(--mono);">' . $algo_name . '</span>';
                echo '  </div>';
                echo '  <div class="result-text">' . htmlspecialchars($output) . '</div>';
                echo '</div>';
            }
        }
        ?>

    </div>

    <div class="footer">FAKULTAS TEKNIK DAN ILMU KOMPUTER · TEKNIK INFORMATIKA · 2026</div>

</div>

<script>
    // Tampilkan / sembunyikan field key sesuai algoritma yang dipilih
    const radios   = document.querySelectorAll('input[name="method"]');
    const wrapC    = document.getElementById('key-caesar-wrap');
    const wrapV    = document.getElementById('key-vigenere-wrap');

    function toggleKeys() {
        const val = document.querySelector('input[name="method"]:checked')?.value;
        wrapC.style.display = (val === 'caesar')   ? '' : 'none';
        wrapV.style.display = (val === 'vigenere') ? '' : 'none';
    }

    radios.forEach(r => r.addEventListener('change', toggleKeys));
    toggleKeys(); 
</script>

</body>
</html>