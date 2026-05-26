<?php
/**
 * ========================================================
 *  CRYPTO SUPER APP — UTS Kriptografi
 *  Single File Application (PHP + HTML + CSS + JS)
 *  Oleh: [Nama Mahasiswa]
 *  NIM : [NIM]
 *  Pertemuan 1-7 — Semua Tools Kriptografi
 * ========================================================
 *
 *  ROUTING: switch-case berdasarkan $_POST['action']
 *  TOOLS   : Caesar | XOR | SHA-256 | RSA | Digital Signature
 * ========================================================
 */

// ─────────────────────────────────────────────────────────
//  LOGIC LAYER — semua fungsi kriptografi
// ─────────────────────────────────────────────────────────

/* ── 1. CAESAR CIPHER ─────────────────────────────── */
function caesarEncrypt(string $text, int $shift): string
{
    $shift = (($shift % 26) + 26) % 26;
    $result = '';
    foreach (str_split($text) as $ch) {
        if (ctype_upper($ch)) {
            $result .= chr((ord($ch) - 65 + $shift) % 26 + 65);
        } elseif (ctype_lower($ch)) {
            $result .= chr((ord($ch) - 97 + $shift) % 26 + 97);
        } else {
            $result .= $ch;
        }
    }
    return $result;
}

function caesarDecrypt(string $text, int $shift): string
{
    return caesarEncrypt($text, -$shift);
}

/* ── 2. XOR CIPHER ────────────────────────────────── */
function xorCipher(string $text, string $key): string
{
    if ($key === '') return $text;
    $out = '';
    $kLen = strlen($key);
    for ($i = 0; $i < strlen($text); $i++) {
        $out .= chr(ord($text[$i]) ^ ord($key[$i % $kLen]));
    }
    return $out;
}

/* ── 4. RSA KEY GENERATION & ENCRYPT ─────────────── */
function rsaGenerateKeys(): array
{
    $config = [
        'digest_alg'       => 'sha256',
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ];
    $res        = openssl_pkey_new($config);
    openssl_pkey_export($res, $privateKey);
    $pubDetails = openssl_pkey_get_details($res);
    return ['private' => $privateKey, 'public' => $pubDetails['key']];
}

function rsaEncrypt(string $plaintext, string $publicKey): string
{
    $pub = openssl_pkey_get_public($publicKey);
    if (!$pub) return 'ERROR: Public key tidak valid.';
    openssl_public_encrypt($plaintext, $encrypted, $pub);
    return base64_encode($encrypted);
}

function rsaDecrypt(string $ciphertext, string $privateKey): string
{
    $priv = openssl_pkey_get_private($privateKey);
    if (!$priv) return 'ERROR: Private key tidak valid.';
    openssl_private_decrypt(base64_decode($ciphertext), $decrypted, $priv);
    return $decrypted !== false ? $decrypted : 'ERROR: Dekripsi gagal.';
}

/* ── 5. DIGITAL SIGNATURE (Sign & Verify) ─────────── */
function signDocument(string $data, string $privateKey): string
{
    $priv = openssl_pkey_get_private($privateKey);
    if (!$priv) return 'ERROR: Private key tidak valid.';
    openssl_sign($data, $signature, $priv, OPENSSL_ALGO_SHA256);
    return base64_encode($signature);
}

function verifyDocument(string $data, string $signature, string $publicKey): bool
{
    $pub = openssl_pkey_get_public($publicKey);
    if (!$pub) return false;
    return openssl_verify($data, base64_decode($signature), $pub, OPENSSL_ALGO_SHA256) === 1;
}

// ─────────────────────────────────────────────────────────
//  ROUTING LAYER — switch-case
// ─────────────────────────────────────────────────────────

$result = null;
$error  = null;
$action = $_POST['action'] ?? '';

switch ($action) {

    /* ── Caesar ── */
    case 'caesar_encrypt':
        $text  = $_POST['caesar_text']  ?? '';
        $shift = (int)($_POST['caesar_shift'] ?? 3);
        $result = [
            'title'  => 'Caesar Cipher — Enkripsi',
            'output' => caesarEncrypt($text, $shift),
            'info'   => "Shift: $shift | Teks asli: " . strlen($text) . " karakter",
        ];
        break;

    case 'caesar_decrypt':
        $text  = $_POST['caesar_text']  ?? '';
        $shift = (int)($_POST['caesar_shift'] ?? 3);
        $result = [
            'title'  => 'Caesar Cipher — Dekripsi',
            'output' => caesarDecrypt($text, $shift),
            'info'   => "Shift: $shift",
        ];
        break;

    /* ── XOR ── */
    case 'xor_encrypt':
        $text = $_POST['xor_text'] ?? '';
        $key  = $_POST['xor_key']  ?? '';
        if ($key === '') { $error = 'Key tidak boleh kosong.'; break; }
        $encrypted = xorCipher($text, $key);
        $hex       = bin2hex($encrypted);
        $result = [
            'title'  => 'XOR Cipher — Enkripsi',
            'output' => $hex,
            'info'   => 'Format: Hex | Key: "' . htmlspecialchars($key) . '"',
            'sub'    => 'Raw bytes → Hex (bin2hex)',
        ];
        break;

    case 'xor_decrypt':
        $hexText = trim($_POST['xor_text'] ?? '');
        $key     = $_POST['xor_key']  ?? '';
        if ($key === '') { $error = 'Key tidak boleh kosong.'; break; }
        if (!ctype_xdigit($hexText)) { $error = 'Input harus berupa string Hex yang valid.'; break; }
        $raw     = hex2bin($hexText);
        $result  = [
            'title'  => 'XOR Cipher — Dekripsi',
            'output' => xorCipher($raw, $key),
            'info'   => 'Input: Hex → Raw bytes → XOR decrypt',
        ];
        break;

    /* ── SHA-256 ── */
    case 'sha256_hash':
        $text = $_POST['sha_text'] ?? '';
        $result = [
            'title'  => 'SHA-256 Hash Generator',
            'output' => hash('sha256', $text),
            'info'   => 'Algorithm: SHA-256 | Output: 256-bit / 64 hex chars',
            'sub'    => 'One-way hash — tidak bisa di-decrypt',
        ];
        break;

    /* ── RSA ── */
    case 'rsa_generate':
        $keys = rsaGenerateKeys();
        $result = [
            'title'   => 'RSA 2048-bit Key Generator',
            'output'  => $keys['public'],
            'output2' => $keys['private'],
            'info'    => 'Algorithm: RSA-2048 | Digest: SHA-256',
            'sub'     => 'SIMPAN private key dengan aman — jangan bagikan!',
            'dual'    => true,
        ];
        break;

    case 'rsa_encrypt':
        $text   = $_POST['rsa_text']   ?? '';
        $pubKey = $_POST['rsa_pubkey'] ?? '';
        if (empty($pubKey)) { $error = 'Public key tidak boleh kosong.'; break; }
        $enc = rsaEncrypt($text, $pubKey);
        $result = [
            'title'  => 'RSA — Enkripsi',
            'output' => $enc,
            'info'   => 'Encoded: Base64 | Hanya private key yang bisa mendekripsi',
        ];
        break;

    case 'rsa_decrypt':
        $cipher  = $_POST['rsa_cipher']  ?? '';
        $privKey = $_POST['rsa_privkey'] ?? '';
        if (empty($privKey)) { $error = 'Private key tidak boleh kosong.'; break; }
        $dec = rsaDecrypt($cipher, $privKey);
        $result = [
            'title'  => 'RSA — Dekripsi',
            'output' => $dec,
            'info'   => 'Input: Base64 cipher → Plain text',
        ];
        break;

    /* ── Digital Signature ── */
    case 'sig_generate_keys':
        $keys = rsaGenerateKeys();
        $result = [
            'title'   => 'Digital Signature — Generate Key Pair',
            'output'  => $keys['public'],
            'output2' => $keys['private'],
            'info'    => 'Key Pair untuk Sign & Verify dokumen',
            'dual'    => true,
        ];
        break;

    case 'sig_sign':
        $doc     = $_POST['sig_doc']     ?? '';
        $privKey = $_POST['sig_privkey'] ?? '';
        if (empty($privKey)) { $error = 'Private key tidak boleh kosong.'; break; }
        $sig = signDocument($doc, $privKey);
        $result = [
            'title'  => 'Digital Signature — Tanda Tangan',
            'output' => $sig,
            'info'   => 'Algorithm: SHA-256 with RSA | Format: Base64',
            'sub'    => 'Gunakan signature ini + public key untuk verifikasi.',
        ];
        break;

    case 'sig_verify':
        $doc    = $_POST['sig_doc']    ?? '';
        $sig    = $_POST['sig_sig']    ?? '';
        $pubKey = $_POST['sig_pubkey'] ?? '';
        if (empty($pubKey) || empty($sig)) { $error = 'Public key dan signature wajib diisi.'; break; }
        $valid = verifyDocument($doc, $sig, $pubKey);
        $result = [
            'title'  => 'Digital Signature — Verifikasi',
            'output' => $valid ? '✅ VALID — Tanda tangan dokumen terverifikasi!' : '❌ INVALID — Tanda tangan tidak cocok atau dokumen diubah!',
            'info'   => 'Algorithm: SHA-256 with RSA',
            'valid'  => $valid,
        ];
        break;
}

// ─────────────────────────────────────────────────────────
//  VIEW LAYER — HTML + CSS + JS
// ─────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CryptoVault — Super App Kriptografi</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;600;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════════
   DESIGN SYSTEM — Terminal Noir Aesthetic
   ═══════════════════════════════════════════════════════ */
:root {
  --bg-void:    #080b10;
  --bg-panel:   #0d1117;
  --bg-card:    #111820;
  --bg-input:   #0a0f16;
  --border:     #1e2d3d;
  --border-glow:#2a4a6b;
  --accent:     #00d4ff;
  --accent-dim: #0099bb;
  --accent2:    #7c3aed;
  --accent3:    #10b981;
  --danger:     #ef4444;
  --warning:    #f59e0b;
  --text-hi:    #e2e8f0;
  --text-mid:   #94a3b8;
  --text-lo:    #475569;
  --font-mono:  'JetBrains Mono', monospace;
  --font-ui:    'Syne', sans-serif;
  --glow:       0 0 20px rgba(0,212,255,0.15);
  --glow-strong:0 0 40px rgba(0,212,255,0.3);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html { scroll-behavior: smooth; }

body {
  background: var(--bg-void);
  color: var(--text-hi);
  font-family: var(--font-ui);
  min-height: 100vh;
  overflow-x: hidden;
}

/* Animated grid background */
body::before {
  content: '';
  position: fixed; inset: 0; z-index: -1;
  background-image:
    linear-gradient(rgba(0,212,255,0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0,212,255,0.03) 1px, transparent 1px);
  background-size: 40px 40px;
  animation: gridMove 20s linear infinite;
}
@keyframes gridMove {
  0%   { transform: translateY(0); }
  100% { transform: translateY(40px); }
}

/* ── HEADER ─────────────────────────────────────── */
.site-header {
  padding: 2rem 2rem 1.5rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: linear-gradient(180deg, rgba(0,212,255,0.04) 0%, transparent 100%);
  position: relative;
}

.logo {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo-icon {
  width: 48px; height: 48px;
  background: linear-gradient(135deg, var(--accent), var(--accent2));
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.5rem;
  box-shadow: var(--glow-strong);
}

.logo-text h1 {
  font-size: 1.6rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: var(--text-hi);
}

.logo-text span {
  font-family: var(--font-mono);
  font-size: 0.7rem;
  color: var(--accent);
  letter-spacing: 0.15em;
  text-transform: uppercase;
}

.header-badge {
  font-family: var(--font-mono);
  font-size: 0.65rem;
  color: var(--text-lo);
  text-align: right;
  line-height: 1.8;
}

.header-badge strong { color: var(--accent); }

/* ── TOOL NAVIGATION ─────────────────────────────── */
.tool-nav {
  padding: 1.5rem 2rem;
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  border-bottom: 1px solid var(--border);
  background: var(--bg-panel);
}

.tool-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.1rem;
  border: 1px solid var(--border);
  border-radius: 8px;
  background: var(--bg-card);
  color: var(--text-mid);
  font-family: var(--font-mono);
  font-size: 0.78rem;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.tool-btn:hover, .tool-btn.active {
  border-color: var(--accent);
  color: var(--accent);
  background: rgba(0,212,255,0.05);
  box-shadow: var(--glow);
}

.tool-btn .icon { font-size: 1rem; }

.tool-btn.active {
  background: rgba(0,212,255,0.1);
}

.plus-badge {
  font-size: 0.55rem;
  background: var(--accent3);
  color: #000;
  padding: 2px 5px;
  border-radius: 4px;
  font-weight: 700;
}

/* ── MAIN LAYOUT ─────────────────────────────────── */
.main {
  display: grid;
  grid-template-columns: 1fr;
  max-width: 1100px;
  margin: 0 auto;
  padding: 2rem;
  gap: 1.5rem;
}

/* ── TOOL PANELS ─────────────────────────────────── */
.tool-panel {
  display: none;
  animation: fadeIn 0.3s ease;
}

.tool-panel.active { display: block; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ── CARD ────────────────────────────────────────── */
.card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 1.5rem;
}

.card-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 0.75rem;
  background: linear-gradient(90deg, rgba(0,212,255,0.05) 0%, transparent 100%);
}

.card-header-icon {
  width: 36px; height: 36px;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem;
  flex-shrink: 0;
}

.card-header-icon.cyan  { background: rgba(0,212,255,0.15); }
.card-header-icon.purple{ background: rgba(124,58,237,0.15); }
.card-header-icon.green { background: rgba(16,185,129,0.15); }
.card-header-icon.orange{ background: rgba(245,158,11,0.15); }

.card-header h2 {
  font-size: 1rem;
  font-weight: 700;
  color: var(--text-hi);
}

.card-header p {
  font-family: var(--font-mono);
  font-size: 0.68rem;
  color: var(--text-lo);
  margin-top: 2px;
}

.card-body { padding: 1.5rem; }

/* ── FORM ELEMENTS ───────────────────────────────── */
.form-group {
  margin-bottom: 1.25rem;
}

label {
  display: block;
  font-family: var(--font-mono);
  font-size: 0.72rem;
  color: var(--accent);
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 0.5rem;
}

input[type="text"],
input[type="number"],
textarea,
select {
  width: 100%;
  background: var(--bg-input);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text-hi);
  font-family: var(--font-mono);
  font-size: 0.85rem;
  padding: 0.75rem 1rem;
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
  resize: vertical;
}

input:focus, textarea:focus, select:focus {
  border-color: var(--accent-dim);
  box-shadow: 0 0 0 3px rgba(0,212,255,0.1);
}

textarea { min-height: 100px; }
textarea.tall { min-height: 150px; }
textarea.taller { min-height: 120px; }

select option { background: var(--bg-card); }

/* ── BUTTONS ─────────────────────────────────────── */
.btn-group {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.btn {
  padding: 0.65rem 1.4rem;
  border: none;
  border-radius: 8px;
  font-family: var(--font-mono);
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  letter-spacing: 0.05em;
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.btn-primary {
  background: linear-gradient(135deg, var(--accent), var(--accent-dim));
  color: #000;
  box-shadow: 0 4px 15px rgba(0,212,255,0.3);
}
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 25px rgba(0,212,255,0.4);
}

.btn-secondary {
  background: transparent;
  border: 1px solid var(--border-glow);
  color: var(--text-mid);
}
.btn-secondary:hover {
  border-color: var(--accent);
  color: var(--accent);
  background: rgba(0,212,255,0.05);
}

.btn-purple {
  background: linear-gradient(135deg, var(--accent2), #5b21b6);
  color: #fff;
  box-shadow: 0 4px 15px rgba(124,58,237,0.3);
}
.btn-purple:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 25px rgba(124,58,237,0.4);
}

.btn-green {
  background: linear-gradient(135deg, var(--accent3), #059669);
  color: #fff;
  box-shadow: 0 4px 15px rgba(16,185,129,0.3);
}
.btn-green:hover {
  transform: translateY(-2px);
}

.btn-danger {
  background: linear-gradient(135deg, var(--danger), #b91c1c);
  color: #fff;
}

/* ── RESULT BOX ──────────────────────────────────── */
.result-box {
  background: var(--bg-input);
  border: 1px solid var(--border-glow);
  border-radius: 12px;
  overflow: hidden;
  margin-top: 1.5rem;
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}

.result-header {
  padding: 0.75rem 1.25rem;
  background: rgba(0,212,255,0.06);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.result-title {
  font-family: var(--font-mono);
  font-size: 0.72rem;
  color: var(--accent);
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.copy-btn {
  background: none;
  border: 1px solid var(--border);
  color: var(--text-lo);
  font-family: var(--font-mono);
  font-size: 0.65rem;
  padding: 3px 10px;
  border-radius: 5px;
  cursor: pointer;
  transition: all 0.2s;
}
.copy-btn:hover { border-color: var(--accent); color: var(--accent); }

.result-content {
  padding: 1.25rem;
}

.result-output {
  font-family: var(--font-mono);
  font-size: 0.82rem;
  color: var(--accent3);
  word-break: break-all;
  line-height: 1.7;
  white-space: pre-wrap;
}

.result-output.dual {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.key-block {
  background: var(--bg-panel);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 0.75rem;
}

.key-block-label {
  font-size: 0.62rem;
  color: var(--text-lo);
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 0.5rem;
}

.key-block-value {
  font-family: var(--font-mono);
  font-size: 0.65rem;
  color: var(--text-mid);
  word-break: break-all;
  line-height: 1.6;
  max-height: 180px;
  overflow-y: auto;
}

.result-info {
  margin-top: 0.75rem;
  font-family: var(--font-mono);
  font-size: 0.68rem;
  color: var(--text-lo);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.info-badge {
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 0.62rem;
}
.info-badge.cyan   { background: rgba(0,212,255,0.1); color: var(--accent); }
.info-badge.green  { background: rgba(16,185,129,0.1); color: var(--accent3); }
.info-badge.orange { background: rgba(245,158,11,0.1); color: var(--warning); }

.result-valid   { color: var(--accent3); font-size: 1rem; font-weight: 700; }
.result-invalid { color: var(--danger);  font-size: 1rem; font-weight: 700; }

/* ── ERROR BOX ───────────────────────────────────── */
.error-box {
  padding: 1rem 1.25rem;
  background: rgba(239,68,68,0.08);
  border: 1px solid rgba(239,68,68,0.3);
  border-radius: 10px;
  color: var(--danger);
  font-family: var(--font-mono);
  font-size: 0.8rem;
  margin-top: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

/* ── DIVIDER ─────────────────────────────────────── */
.divider {
  border: none;
  border-top: 1px solid var(--border);
  margin: 1.5rem 0;
}

/* ── INFO CALLOUT ────────────────────────────────── */
.callout {
  padding: 0.85rem 1.1rem;
  border-radius: 8px;
  font-family: var(--font-mono);
  font-size: 0.73rem;
  line-height: 1.6;
  margin-bottom: 1.25rem;
  display: flex;
  gap: 0.6rem;
}
.callout-info    { background: rgba(0,212,255,0.05);  border: 1px solid rgba(0,212,255,0.15);  color: var(--accent-dim); }
.callout-warning { background: rgba(245,158,11,0.05); border: 1px solid rgba(245,158,11,0.15); color: var(--warning); }

/* ── GRID 2-COL ──────────────────────────────────── */
.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

/* ── FOOTER ──────────────────────────────────────── */
footer {
  text-align: center;
  padding: 2rem;
  border-top: 1px solid var(--border);
  font-family: var(--font-mono);
  font-size: 0.68rem;
  color: var(--text-lo);
  line-height: 2;
}

footer strong { color: var(--accent); }

/* ── RESPONSIVE ──────────────────────────────────── */
@media (max-width: 700px) {
  .main { padding: 1rem; }
  .tool-nav { padding: 1rem; }
  .site-header { flex-direction: column; gap: 1rem; text-align: center; }
  .grid-2 { grid-template-columns: 1fr; }
  .result-output.dual { grid-template-columns: 1fr; }
}

/* ── SCROLLBAR ───────────────────────────────────── */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: var(--bg-void); }
::-webkit-scrollbar-thumb { background: var(--border-glow); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: var(--accent-dim); }

/* ── TYPING CURSOR ───────────────────────────────── */
.cursor::after {
  content: '█';
  animation: blink 1s step-end infinite;
  color: var(--accent);
}
@keyframes blink {
  50% { opacity: 0; }
}
</style>
</head>
<body>

<!-- ── HEADER ───────────────────────────────────────── -->
<header class="site-header">
  <div class="logo">
    <div class="logo-icon">🔐</div>
    <div class="logo-text">
      <h1>CryptoVault</h1>
      <span>Super App Kriptografi // UTS Pertemuan 1–7</span>
    </div>
  </div>
  <div class="header-badge">
    <strong>PHP Single-File Application</strong><br>
    UTS Kriptografi — 5 Tools Terintegrasi<br>
    PHP <?= phpversion() ?> · OpenSSL <?= OPENSSL_VERSION_TEXT ?>
  </div>
</header>

<!-- ── TOOL NAVIGATION ───────────────────────────────── -->
<nav class="tool-nav">
  <button class="tool-btn active" onclick="switchTool('caesar', this)">
    <span class="icon">🔤</span> Caesar Cipher
  </button>
  <button class="tool-btn" onclick="switchTool('xor', this)">
    <span class="icon">⊕</span> XOR Cipher
  </button>
  <button class="tool-btn" onclick="switchTool('sha', this)">
    <span class="icon">#</span> SHA-256 Hash
  </button>
  <button class="tool-btn" onclick="switchTool('rsa', this)">
    <span class="icon">🔑</span> RSA Generator
  </button>
  <button class="tool-btn" onclick="switchTool('sig', this)">
    <span class="icon">✍️</span> Digital Signature
    <span class="plus-badge">+10</span>
  </button>
</nav>

<!-- ── MAIN ─────────────────────────────────────────── -->
<main class="main">

  <?php if ($error): ?>
  <div class="error-box">⚠️ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($result): ?>
  <div class="result-box" id="global-result">
    <div class="result-header">
      <span class="result-title">▶ <?= htmlspecialchars($result['title']) ?></span>
      <button class="copy-btn" onclick="copyResult()">⎘ Salin</button>
    </div>
    <div class="result-content">
      <?php if (!empty($result['dual'])): ?>
        <div class="result-output dual">
          <div class="key-block">
            <div class="key-block-label">🔓 Public Key</div>
            <div class="key-block-value" id="pub-key-val"><?= htmlspecialchars($result['output']) ?></div>
          </div>
          <div class="key-block">
            <div class="key-block-label">🔒 Private Key (RAHASIA)</div>
            <div class="key-block-value" id="priv-key-val"><?= htmlspecialchars($result['output2']) ?></div>
          </div>
        </div>
        <div style="margin-top:0.75rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
          <button class="copy-btn" onclick="copyText(document.getElementById('pub-key-val').textContent)">⎘ Salin Public Key</button>
          <button class="copy-btn" onclick="copyText(document.getElementById('priv-key-val').textContent)">⎘ Salin Private Key</button>
        </div>
      <?php elseif (isset($result['valid'])): ?>
        <div class="<?= $result['valid'] ? 'result-valid' : 'result-invalid' ?>">
          <?= htmlspecialchars($result['output']) ?>
        </div>
      <?php else: ?>
        <div class="result-output" id="main-output"><?= htmlspecialchars($result['output']) ?></div>
      <?php endif; ?>

      <div class="result-info">
        <span class="info-badge cyan"><?= htmlspecialchars($result['info'] ?? '') ?></span>
        <?php if (!empty($result['sub'])): ?>
          <span class="info-badge orange"><?= htmlspecialchars($result['sub']) ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
       TOOL 1: CAESAR CIPHER
  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
  <div class="tool-panel active" id="panel-caesar">
    <div class="card">
      <div class="card-header">
        <div class="card-header-icon cyan">🔤</div>
        <div>
          <h2>Caesar Cipher</h2>
          <p>Enkripsi substitusi klasik — geser karakter sejumlah nilai shift</p>
        </div>
      </div>
      <div class="card-body">
        <div class="callout callout-info">
          ℹ️ Caesar Cipher menggeser setiap huruf dalam alfabet sebanyak nilai shift.
          Contoh: shift=3 → A→D, B→E, Z→C. Angka & simbol tidak berubah.
        </div>
        <form method="POST">
          <input type="hidden" name="tab" value="caesar">
          <div class="form-group">
            <label>Teks Input</label>
            <textarea name="caesar_text" placeholder="Masukkan teks di sini..." class="tall"><?= htmlspecialchars($_POST['caesar_text'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label>Nilai Shift (1–25)</label>
            <input type="number" name="caesar_shift" min="1" max="25" value="<?= (int)($_POST['caesar_shift'] ?? 3) ?>">
          </div>
          <div class="btn-group">
            <button type="submit" name="action" value="caesar_encrypt" class="btn btn-primary">🔒 Enkripsi</button>
            <button type="submit" name="action" value="caesar_decrypt" class="btn btn-secondary">🔓 Dekripsi</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
       TOOL 2: XOR CIPHER
  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
  <div class="tool-panel" id="panel-xor">
    <div class="card">
      <div class="card-header">
        <div class="card-header-icon purple">⊕</div>
        <div>
          <h2>XOR Cipher</h2>
          <p>Enkripsi XOR bitwise + output Hex (bin2hex)</p>
        </div>
      </div>
      <div class="card-body">
        <div class="callout callout-info">
          ℹ️ XOR Cipher menggunakan operasi XOR bitwise antara setiap byte teks dan key.
          Output enkripsi dalam format Hexadecimal. Untuk dekripsi, masukkan string Hex.
        </div>
        <form method="POST">
          <input type="hidden" name="tab" value="xor">
          <div class="form-group">
            <label>Teks / Hex Input</label>
            <textarea name="xor_text" placeholder="Enkripsi: teks biasa | Dekripsi: string hex" class="tall"><?= htmlspecialchars($_POST['xor_text'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label>Secret Key</label>
            <input type="text" name="xor_key" placeholder="Masukkan key rahasia..." value="<?= htmlspecialchars($_POST['xor_key'] ?? '') ?>">
          </div>
          <div class="btn-group">
            <button type="submit" name="action" value="xor_encrypt" class="btn btn-purple">🔒 Enkripsi → Hex</button>
            <button type="submit" name="action" value="xor_decrypt" class="btn btn-secondary">🔓 Dekripsi ← Hex</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
       TOOL 3: SHA-256 HASH
  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
  <div class="tool-panel" id="panel-sha">
    <div class="card">
      <div class="card-header">
        <div class="card-header-icon green">#</div>
        <div>
          <h2>SHA-256 Hash Generator</h2>
          <p>Fungsi hash satu arah — 256-bit / 64 karakter hex</p>
        </div>
      </div>
      <div class="card-body">
        <div class="callout callout-warning">
          ⚠️ SHA-256 adalah fungsi hash SATU ARAH. Output tidak bisa di-decrypt kembali.
          Digunakan untuk verifikasi integritas data, password storage, dan digital signature.
        </div>
        <form method="POST">
          <input type="hidden" name="tab" value="sha">
          <div class="form-group">
            <label>Teks / Data Input</label>
            <textarea name="sha_text" placeholder="Masukkan teks yang akan di-hash..."><?= htmlspecialchars($_POST['sha_text'] ?? '') ?></textarea>
          </div>
          <div class="btn-group">
            <button type="submit" name="action" value="sha256_hash" class="btn btn-green"># Generate Hash SHA-256</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
       TOOL 4: RSA GENERATOR & ENCRYPT
  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
  <div class="tool-panel" id="panel-rsa">
    <!-- Generate Keys -->
    <div class="card">
      <div class="card-header">
        <div class="card-header-icon orange">🗝️</div>
        <div>
          <h2>RSA Key Generator</h2>
          <p>Buat pasangan kunci RSA-2048 (Public + Private)</p>
        </div>
      </div>
      <div class="card-body">
        <div class="callout callout-warning">
          ⚠️ Private key bersifat RAHASIA. Jangan pernah membagikannya ke orang lain!
          Setelah generate, salin dan simpan kedua kunci di tempat yang aman.
        </div>
        <form method="POST">
          <input type="hidden" name="tab" value="rsa">
          <div class="btn-group">
            <button type="submit" name="action" value="rsa_generate" class="btn btn-primary">🗝️ Generate RSA-2048 Key Pair</button>
          </div>
        </form>
      </div>
    </div>

    <hr class="divider">

    <!-- Encrypt -->
    <div class="card">
      <div class="card-header">
        <div class="card-header-icon cyan">🔒</div>
        <div>
          <h2>RSA Enkripsi</h2>
          <p>Enkripsi teks menggunakan Public Key → output Base64</p>
        </div>
      </div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="tab" value="rsa">
          <div class="form-group">
            <label>Teks yang Akan Dienkripsi</label>
            <textarea name="rsa_text" placeholder="Masukkan pesan rahasia..."><?= htmlspecialchars($_POST['rsa_text'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label>Public Key (PEM Format)</label>
            <textarea name="rsa_pubkey" class="taller" placeholder="-----BEGIN PUBLIC KEY-----&#10;...&#10;-----END PUBLIC KEY-----"><?= htmlspecialchars($_POST['rsa_pubkey'] ?? '') ?></textarea>
          </div>
          <button type="submit" name="action" value="rsa_encrypt" class="btn btn-primary">🔒 Enkripsi</button>
        </form>
      </div>
    </div>

    <hr class="divider">

    <!-- Decrypt -->
    <div class="card">
      <div class="card-header">
        <div class="card-header-icon green">🔓</div>
        <div>
          <h2>RSA Dekripsi</h2>
          <p>Dekripsi Base64 cipher menggunakan Private Key</p>
        </div>
      </div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="tab" value="rsa">
          <div class="form-group">
            <label>Cipher Text (Base64)</label>
            <textarea name="rsa_cipher" placeholder="Paste cipher text Base64 di sini..."><?= htmlspecialchars($_POST['rsa_cipher'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label>Private Key (PEM Format)</label>
            <textarea name="rsa_privkey" class="taller" placeholder="-----BEGIN PRIVATE KEY-----&#10;...&#10;-----END PRIVATE KEY-----"><?= htmlspecialchars($_POST['rsa_privkey'] ?? '') ?></textarea>
          </div>
          <button type="submit" name="action" value="rsa_decrypt" class="btn btn-green">🔓 Dekripsi</button>
        </form>
      </div>
    </div>
  </div>

  <!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
       TOOL 5: DIGITAL SIGNATURE (+10)
  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
  <div class="tool-panel" id="panel-sig">

    <!-- Generate Keys -->
    <div class="card">
      <div class="card-header">
        <div class="card-header-icon purple">✍️</div>
        <div>
          <h2>Digital Signature — Generate Key Pair</h2>
          <p>Buat key pair khusus untuk tanda tangan digital dokumen</p>
        </div>
      </div>
      <div class="card-body">
        <div class="callout callout-info">
          ℹ️ Digital Signature membuktikan keaslian & integritas dokumen.
          Pengirim menandatangani dengan Private Key → Penerima verifikasi dengan Public Key.
        </div>
        <form method="POST">
          <input type="hidden" name="tab" value="sig">
          <div class="btn-group">
            <button type="submit" name="action" value="sig_generate_keys" class="btn btn-purple">🗝️ Generate Key Pair</button>
          </div>
        </form>
      </div>
    </div>

    <hr class="divider">

    <!-- Sign -->
    <div class="card">
      <div class="card-header">
        <div class="card-header-icon cyan">✍️</div>
        <div>
          <h2>Tanda Tangani Dokumen</h2>
          <p>Sign dokumen dengan Private Key → output signature Base64</p>
        </div>
      </div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="tab" value="sig">
          <div class="form-group">
            <label>Isi Dokumen</label>
            <textarea name="sig_doc" class="tall" placeholder="Masukkan isi dokumen yang akan ditandatangani..."><?= htmlspecialchars($_POST['sig_doc'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label>Private Key (PEM Format)</label>
            <textarea name="sig_privkey" class="taller" placeholder="-----BEGIN PRIVATE KEY-----&#10;...&#10;-----END PRIVATE KEY-----"><?= htmlspecialchars($_POST['sig_privkey'] ?? '') ?></textarea>
          </div>
          <button type="submit" name="action" value="sig_sign" class="btn btn-purple">✍️ Tanda Tangani Dokumen</button>
        </form>
      </div>
    </div>

    <hr class="divider">

    <!-- Verify -->
    <div class="card">
      <div class="card-header">
        <div class="card-header-icon green">✅</div>
        <div>
          <h2>Verifikasi Tanda Tangan</h2>
          <p>Cek keaslian dokumen menggunakan Public Key + Signature</p>
        </div>
      </div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="tab" value="sig">
          <div class="form-group">
            <label>Isi Dokumen (sama persis dengan saat signing)</label>
            <textarea name="sig_doc" class="tall" placeholder="Masukkan isi dokumen yang akan diverifikasi..."><?= htmlspecialchars($_POST['sig_doc'] ?? '') ?></textarea>
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label>Signature (Base64)</label>
              <textarea name="sig_sig" placeholder="Paste signature Base64..."><?= htmlspecialchars($_POST['sig_sig'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
              <label>Public Key (PEM Format)</label>
              <textarea name="sig_pubkey" placeholder="-----BEGIN PUBLIC KEY-----&#10;...&#10;-----END PUBLIC KEY-----"><?= htmlspecialchars($_POST['sig_pubkey'] ?? '') ?></textarea>
            </div>
          </div>
          <button type="submit" name="action" value="sig_verify" class="btn btn-green">✅ Verifikasi Tanda Tangan</button>
        </form>
      </div>
    </div>
  </div><!-- /panel-sig -->

</main>

<!-- ── FOOTER ─────────────────────────────────────────── -->
<footer>
  <strong>CryptoVault</strong> — Super App Kriptografi UTS<br>
  PHP Single-File Application · Caesar · XOR · SHA-256 · RSA · Digital Signature<br>
  Implementasi: PHP <?= phpversion() ?> + OpenSSL Extension
</footer>

<!-- ── JAVASCRIPT ─────────────────────────────────────── -->
<script>
/* ── Tab Switcher ── */
function switchTool(id, btn) {
  // hide all panels
  document.querySelectorAll('.tool-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
  // show selected
  document.getElementById('panel-' + id).classList.add('active');
  btn.classList.add('active');
  // scroll result into view if present
  const res = document.getElementById('global-result');
  if (res) res.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/* ── Auto-activate tab based on last submission ── */
document.addEventListener('DOMContentLoaded', () => {
  const tab = '<?= htmlspecialchars($_POST['tab'] ?? '') ?>';
  if (tab) {
    const btn = document.querySelector(`.tool-btn[onclick*="'${tab}'"]`);
    if (btn) switchTool(tab, btn);
  }
  // Scroll to result
  const res = document.getElementById('global-result');
  if (res) setTimeout(() => res.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
});

/* ── Copy helpers ── */
function copyText(text) {
  navigator.clipboard.writeText(text.trim()).then(() => {
    showToast('✅ Disalin ke clipboard!');
  });
}

function copyResult() {
  const el = document.getElementById('main-output');
  if (el) {
    copyText(el.textContent);
  } else {
    const pub = document.getElementById('pub-key-val');
    const priv = document.getElementById('priv-key-val');
    if (pub && priv) {
      copyText('PUBLIC KEY:\n' + pub.textContent + '\n\nPRIVATE KEY:\n' + priv.textContent);
    }
  }
}

/* ── Toast notification ── */
function showToast(msg) {
  const toast = document.createElement('div');
  toast.textContent = msg;
  toast.style.cssText = `
    position:fixed; bottom:2rem; right:2rem;
    background:#00d4ff; color:#000;
    padding:0.75rem 1.25rem; border-radius:10px;
    font-family:'JetBrains Mono',monospace; font-size:0.8rem;
    z-index:9999; animation:slideUp 0.3s ease;
    box-shadow:0 4px 20px rgba(0,212,255,0.4);
  `;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 2000);
}
</script>

</body>
</html>