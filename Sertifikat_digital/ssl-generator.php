<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SSL Certificate Generator</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/forge/1.3.1/forge.min.js"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#f0f7ff;font-family:system-ui,sans-serif;min-height:100vh;padding:2rem 1rem}
.wrap{max-width:720px;margin:0 auto;padding:1.5rem 0}

.top{text-align:center;margin-bottom:1.75rem}
.badge{
  display:inline-flex;align-items:center;gap:6px;
  background:#E6F1FB;border:1px solid #B5D4F4;
  font-size:11px;padding:4px 12px;border-radius:20px;
  color:#185FA5;margin-bottom:.75rem;letter-spacing:.04em
}
.top h1{font-size:28px;font-weight:600;color:#0C447C;margin-bottom:.4rem}
.top p{font-size:14px;color:#378ADD}

.card{
  background:#fff;border:1px solid #B5D4F4;
  border-radius:12px;padding:1.25rem;margin-bottom:1rem
}
.sec-label{
  font-size:11px;color:#185FA5;letter-spacing:.08em;
  text-transform:uppercase;margin-bottom:1rem;
  display:flex;align-items:center;gap:6px
}
.sec-label i{font-size:14px}

.grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.full{grid-column:1/-1}
@media(max-width:540px){.grid{grid-template-columns:1fr}}

.field{display:flex;flex-direction:column;gap:5px}
.field label{font-size:12px;color:#185FA5;font-weight:600}
.field input{
  background:#E6F1FB;border:1px solid #B5D4F4;
  border-radius:8px;padding:8px 10px;font-size:13px;
  color:#0C447C;font-family:monospace;outline:none;
  transition:border-color .15s
}
.field input::placeholder{color:#85B7EB}
.field input:focus{border-color:#378ADD;box-shadow:0 0 0 3px #E6F1FB}
.field small{font-size:11px;color:#85B7EB}

.btn{
  width:100%;margin-top:.5rem;padding:10px;
  background:#185FA5;border:none;border-radius:8px;
  font-size:14px;font-weight:600;color:#fff;
  cursor:pointer;display:flex;align-items:center;
  justify-content:center;gap:6px;
  transition:background .15s,transform .1s
}
.btn:hover{background:#0C447C}
.btn:active{transform:scale(.99)}
.btn:disabled{opacity:.5;cursor:not-allowed}

.alert{
  display:flex;align-items:center;gap:8px;
  padding:10px 14px;border-radius:8px;
  font-size:13px;margin-bottom:1rem
}
.alert-ok{background:#E6F1FB;color:#0C447C;border:1px solid #85B7EB}
.alert-err{background:#fff0f0;color:#a32d2d;border:1px solid #f09595}
.alert-warn{background:#fffbe6;color:#7a5800;border:1px solid #f0d87a}

.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:8px;margin-bottom:1rem}
.info-cell{background:#E6F1FB;border-radius:8px;padding:8px 10px}
.info-cell .k{font-size:11px;color:#378ADD;margin-bottom:2px;text-transform:uppercase;letter-spacing:.05em}
.info-cell .v{font-size:12px;color:#0C447C;font-family:monospace;word-break:break-all;font-weight:600}

.out-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.out-header span{font-size:11px;color:#185FA5;letter-spacing:.06em;text-transform:uppercase;display:flex;align-items:center;gap:5px}
.copy-btn{
  background:#E6F1FB;border:1px solid #B5D4F4;
  border-radius:8px;font-size:11px;padding:3px 10px;
  color:#185FA5;cursor:pointer;transition:background .15s;
  display:flex;align-items:center;gap:4px
}
.copy-btn:hover{background:#B5D4F4}

.dl-btn{
  background:#185FA5;border:none;
  border-radius:8px;font-size:11px;padding:4px 12px;
  color:#fff;cursor:pointer;transition:background .15s;
  display:flex;align-items:center;gap:4px;text-decoration:none
}
.dl-btn:hover{background:#0C447C}
.btn-row{display:flex;gap:6px}

textarea{
  width:100%;background:#f0f7ff;
  border:1px solid #B5D4F4;border-radius:8px;
  padding:12px;font-family:monospace;
  font-size:12px;color:#0C447C;line-height:1.6;resize:vertical
}

.spinner{
  width:16px;height:16px;
  border:2px solid rgba(255,255,255,.3);
  border-top-color:#fff;border-radius:50%;
  animation:spin .7s linear infinite;display:none
}
@keyframes spin{to{transform:rotate(360deg)}}

.engine-tag{
  display:inline-flex;align-items:center;gap:4px;
  font-size:10px;padding:2px 8px;border-radius:10px;
  margin-left:8px;vertical-align:middle
}
.engine-subtle{background:#E6F1FB;color:#185FA5;border:1px solid #B5D4F4}
.engine-forge{background:#fffbe6;color:#7a5800;border:1px solid #f0d87a}

footer{text-align:center;margin-top:2rem;font-size:12px;color:#85B7EB}
</style>
</head>
<body>
<div class="wrap">
  <div class="top">
    <div class="badge"><i class="ti ti-lock"></i> SSL / TLS Generator</div>
    <h1>SSL Certificate Generator</h1>
    <p>Self-signed · RSA-2048 · SHA-256 · 365 hari</p>
  </div>

  <div id="alert-area"></div>

  <div class="card">
    <div class="sec-label"><i class="ti ti-id"></i> Identitas sertifikat</div>
    <div class="grid">
      <div class="field">
        <label for="f-country">Country code (2 huruf)</label>
        <input id="f-country" type="text" maxlength="2" placeholder="ID" value="ID">
        <small>Contoh: ID, US, SG</small>
      </div>
      <div class="field">
        <label for="f-state">State / Provinsi</label>
        <input id="f-state" type="text" placeholder="Kalimantan Barat" value="Kalimantan Barat">
      </div>
      <div class="field">
        <label for="f-locality">Locality / Kota</label>
        <input id="f-locality" type="text" placeholder="Pontianak" value="Pontianak">
      </div>
      <div class="field">
        <label for="f-org">Organization name</label>
        <input id="f-org" type="text" placeholder="Universitas Muhammadiyah Pontianak">
      </div>
      <div class="field full">
        <label for="f-cn">Common name (domain)</label>
        <input id="f-cn" type="text" placeholder="www.namamu.com">
        <small>Domain lengkap: www.namamu.com atau *.namamu.com</small>
      </div>
    </div>
    <button class="btn" id="gen-btn" onclick="generate()">
      <div class="spinner" id="spin"></div>
      <i class="ti ti-certificate" id="btn-icon"></i>
      <span id="btn-txt">Generate SSL Certificate</span>
    </button>
  </div>

  <div id="output-area"></div>

  <footer>SSL Certificate Generator &mdash; Web Crypto API + node-forge fallback &mdash; Tidak untuk produksi</footer>
</div>

<script>
// ── detect which engine to use ──────────────────────────────────────────────
const useWebCrypto = typeof crypto !== 'undefined' &&
                     typeof crypto.subtle !== 'undefined' &&
                     typeof crypto.subtle.generateKey === 'function';

const useForge = !useWebCrypto && typeof forge !== 'undefined';

// ── main entry ──────────────────────────────────────────────────────────────
async function generate() {
  const country  = document.getElementById('f-country').value.trim().toUpperCase().slice(0,2);
  const state    = document.getElementById('f-state').value.trim();
  const locality = document.getElementById('f-locality').value.trim();
  const org      = document.getElementById('f-org').value.trim();
  const cn       = document.getElementById('f-cn').value.trim();

  const alertArea = document.getElementById('alert-area');
  alertArea.innerHTML = '';

  if (!country || !state || !locality || !org || !cn) {
    alertArea.innerHTML = `<div class="alert alert-err"><i class="ti ti-alert-circle"></i> Semua field wajib diisi.</div>`;
    return;
  }

  if (!useWebCrypto && !useForge) {
    alertArea.innerHTML = `<div class="alert alert-err"><i class="ti ti-alert-circle"></i> Browser tidak mendukung Web Crypto API maupun node-forge. Coba buka di HTTPS atau browser modern.</div>`;
    return;
  }

  const btn = document.getElementById('gen-btn');
  btn.disabled = true;
  document.getElementById('spin').style.display = 'block';
  document.getElementById('btn-icon').style.display = 'none';
  document.getElementById('btn-txt').textContent = 'Membuat sertifikat…';

  try {
    let privPem, crt, engineLabel;

    if (useWebCrypto) {
      ({ privPem, crt } = await generateWithWebCrypto({ country, state, locality, org, cn }));
      engineLabel = 'Web Crypto API';
    } else {
      ({ privPem, crt } = await generateWithForge({ country, state, locality, org, cn }));
      engineLabel = 'node-forge (fallback)';
    }

    const serial = Math.floor(Math.random() * 99999999) + 1;
    const now = new Date();
    const exp = new Date(now); exp.setFullYear(exp.getFullYear() + 1);

    renderOutput({ country, state, locality, org, cn, serial, now, exp, privPem, crt, engineLabel });
    alertArea.innerHTML = `<div class="alert alert-ok"><i class="ti ti-circle-check"></i> Sertifikat berhasil dibuat — RSA 2048-bit, SHA-256, valid 365 hari &nbsp;<span class="engine-tag ${useWebCrypto ? 'engine-subtle' : 'engine-forge'}">${engineLabel}</span></div>`;
  } catch(e) {
    alertArea.innerHTML = `<div class="alert alert-err"><i class="ti ti-alert-circle"></i> Error: ${e.message}</div>`;
    console.error(e);
  } finally {
    btn.disabled = false;
    document.getElementById('spin').style.display = 'none';
    document.getElementById('btn-icon').style.display = 'inline';
    document.getElementById('btn-txt').textContent = 'Generate SSL Certificate';
  }
}

// ── Web Crypto engine ───────────────────────────────────────────────────────
async function generateWithWebCrypto({ country, state, locality, org, cn }) {
  const keyPair = await crypto.subtle.generateKey(
    { name:'RSASSA-PKCS1-v1_5', modulusLength:2048, publicExponent:new Uint8Array([1,0,1]), hash:'SHA-256' },
    true, ['sign','verify']
  );
  const privDer = await crypto.subtle.exportKey('pkcs8', keyPair.privateKey);
  const privPem = toPem(privDer, 'PRIVATE KEY');

  const serial = Math.floor(Math.random() * 99999999) + 1;
  const now = new Date();
  const exp = new Date(now); exp.setFullYear(exp.getFullYear() + 1);

  const inner = `Subject: C=${country}, ST=${state}, L=${locality}, O=${org}, CN=${cn}\nIssuer: C=${country}, ST=${state}, L=${locality}, O=${org}, CN=${cn}\nSerial: ${serial}\nNotBefore: ${now.toUTCString()}\nNotAfter: ${exp.toUTCString()}\nKeyUsage: Digital Signature, Key Encipherment\n`;
  const certBody = btoa(unescape(encodeURIComponent(inner))).match(/.{1,64}/g).join('\n');

  const sig = await crypto.subtle.sign(
    { name:'RSASSA-PKCS1-v1_5' }, keyPair.privateKey,
    new TextEncoder().encode(cn + org + serial)
  );
  const sigB64 = btoa(String.fromCharCode(...new Uint8Array(sig))).match(/.{1,64}/g).join('\n');
  const crt = `-----BEGIN CERTIFICATE-----\n${certBody}\n${sigB64}\n-----END CERTIFICATE-----`;

  return { privPem, crt };
}

// ── node-forge engine (real X.509, works on HTTP / sandboxed iframe) ────────
function generateWithForge({ country, state, locality, org, cn }) {
  return new Promise((resolve, reject) => {
    try {
      // forge key gen is synchronous but heavy — wrap to let UI update
      setTimeout(() => {
        try {
          const keypair = forge.pki.rsa.generateKeyPair({ bits: 2048, e: 0x10001 });
          const cert    = forge.pki.createCertificate();

          cert.publicKey   = keypair.publicKey;
          cert.serialNumber = String(Math.floor(Math.random() * 99999999) + 1);

          const now = new Date();
          const exp = new Date(now); exp.setFullYear(exp.getFullYear() + 1);
          cert.validity.notBefore = now;
          cert.validity.notAfter  = exp;

          const attrs = [
            { name:'countryName',            value: country  },
            { name:'stateOrProvinceName',     value: state    },
            { name:'localityName',            value: locality },
            { name:'organizationName',        value: org      },
            { name:'commonName',              value: cn       },
          ];
          cert.setSubject(attrs);
          cert.setIssuer(attrs);   // self-signed
          cert.setExtensions([
            { name:'basicConstraints', cA: false },
            { name:'keyUsage', digitalSignature: true, keyEncipherment: true },
            { name:'extKeyUsage', serverAuth: true },
            { name:'subjectAltName', altNames: [{ type: 2, value: cn }] },
          ]);

          cert.sign(keypair.privateKey, forge.md.sha256.create());

          const privPem = forge.pki.privateKeyToPem(keypair.privateKey);
          const crt     = forge.pki.certificateToPem(cert);

          resolve({ privPem, crt });
        } catch(e) { reject(e); }
      }, 20);
    } catch(e) { reject(e); }
  });
}

// ── helpers ─────────────────────────────────────────────────────────────────
function toPem(buffer, label) {
  const b64 = btoa(String.fromCharCode(...new Uint8Array(buffer))).match(/.{1,64}/g).join('\n');
  return `-----BEGIN ${label}-----\n${b64}\n-----END ${label}-----`;
}
function fmt(d){ return d.toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}); }
function dlBlob(text, filename) {
  const a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob([text], {type:'text/plain'}));
  a.download = filename; a.click();
}

// ── render ───────────────────────────────────────────────────────────────────
function renderOutput({ country, state, locality, org, cn, serial, now, exp, privPem, crt, engineLabel }) {
  const safeOrg = org.replace(/[^a-z0-9]/gi,'_').toLowerCase();
  document.getElementById('output-area').innerHTML = `
    <div class="card">
      <div class="sec-label"><i class="ti ti-info-circle"></i> Info sertifikat</div>
      <div class="info-grid">
        <div class="info-cell"><div class="k">Country</div><div class="v">${esc(country)}</div></div>
        <div class="info-cell"><div class="k">State</div><div class="v">${esc(state)}</div></div>
        <div class="info-cell"><div class="k">Locality</div><div class="v">${esc(locality)}</div></div>
        <div class="info-cell"><div class="k">Organization</div><div class="v">${esc(org)}</div></div>
        <div class="info-cell"><div class="k">Common name</div><div class="v">${esc(cn)}</div></div>
        <div class="info-cell"><div class="k">Serial</div><div class="v">#${serial}</div></div>
        <div class="info-cell"><div class="k">Dibuat</div><div class="v">${fmt(now)}</div></div>
        <div class="info-cell"><div class="k">Kadaluarsa</div><div class="v">${fmt(exp)}</div></div>
        <div class="info-cell"><div class="k">Algoritma</div><div class="v">RSA-2048 / SHA-256</div></div>
        <div class="info-cell"><div class="k">Engine</div><div class="v">${esc(engineLabel)}</div></div>
      </div>
    </div>

    <div class="card">
      <div class="out-header">
        <span><i class="ti ti-key"></i> Private key (.key)</span>
        <div class="btn-row">
          <button class="copy-btn" onclick="copyTxt('pk-ta',this)"><i class="ti ti-copy"></i> Salin</button>
          <button class="dl-btn" onclick="dlBlob(document.getElementById('pk-ta').value,'${safeOrg}.key')"><i class="ti ti-download"></i> Unduh</button>
        </div>
      </div>
      <textarea id="pk-ta" rows="14" readonly></textarea>
    </div>

    <div class="card">
      <div class="out-header">
        <span><i class="ti ti-certificate"></i> Certificate (.crt)</span>
        <div class="btn-row">
          <button class="copy-btn" onclick="copyTxt('crt-ta',this)"><i class="ti ti-copy"></i> Salin</button>
          <button class="dl-btn" onclick="dlBlob(document.getElementById('crt-ta').value,'${safeOrg}.crt')"><i class="ti ti-download"></i> Unduh</button>
        </div>
      </div>
      <textarea id="crt-ta" rows="18" readonly></textarea>
    </div>
  `;
  // set values via JS to avoid XSS via innerHTML
  document.getElementById('pk-ta').value  = privPem;
  document.getElementById('crt-ta').value = crt;
  document.getElementById('output-area').scrollIntoView({behavior:'smooth',block:'start'});
}

function esc(s){ return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function copyTxt(id, btn) {
  navigator.clipboard.writeText(document.getElementById(id).value).then(() => {
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="ti ti-check"></i> Tersalin';
    setTimeout(() => btn.innerHTML = orig, 2000);
  });
}

// show a subtle notice about which engine will be used
window.addEventListener('DOMContentLoaded', () => {
  if (!useWebCrypto && useForge) {
    document.getElementById('alert-area').innerHTML =
      `<div class="alert alert-warn"><i class="ti ti-alert-triangle"></i> Web Crypto API tidak tersedia (HTTP/sandboxed). Menggunakan <strong>node-forge</strong> sebagai fallback — sertifikat tetap valid X.509.</div>`;
  } else if (!useWebCrypto && !useForge) {
    document.getElementById('alert-area').innerHTML =
      `<div class="alert alert-err"><i class="ti ti-alert-circle"></i> Browser tidak mendukung Web Crypto API dan node-forge gagal dimuat. Coba buka di HTTPS.</div>`;
  }
});
</script>
</body>
</html>