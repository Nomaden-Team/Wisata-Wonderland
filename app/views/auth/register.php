<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$popup_type    = $_SESSION['popup_type'] ?? '';
$popup_message = $_SESSION['popup_message'] ?? '';
unset($_SESSION['popup_type'], $_SESSION['popup_message']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem('wl_theme')||'light';document.documentElement.setAttribute('data-theme',t);document.documentElement.style.colorScheme=t;}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
    <title>Daftar - Wonderland Samarinda</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/enhance.css">
</head>
<body class="auth-body">

<!-- ===================== POPUP MODAL ===================== -->
<div id="wl-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; padding:32px 28px 24px; width:90%; max-width:340px; text-align:center; position:relative; animation:wlPop .2s ease;">
        <button onclick="closePopup()" style="position:absolute;top:12px;right:14px;background:none;border:none;font-size:22px;color:#aaa;cursor:pointer;line-height:1;">&#215;</button>
        <div id="wl-icon" style="width:64px;height:64px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;"></div>
        <p id="wl-title" style="font-size:17px;font-weight:600;margin:0 0 8px;color:#1a1a1a;"></p>
        <p id="wl-desc" style="font-size:14px;color:#666;margin:0 0 24px;line-height:1.6;"></p>
        <button id="wl-btn" onclick="closePopup()" style="width:100%;padding:12px;border:none;border-radius:10px;font-size:15px;font-weight:500;cursor:pointer;color:#fff;"></button>
    </div>
</div>
<style>
    @keyframes wlPop { from { transform:scale(.9); opacity:0; } to { transform:scale(1); opacity:1; } }
</style>

    <div class="auth-top-brand">Wonderland Samarinda</div>
    <div class="auth-top-subtitle">Daftar Akun Pengunjung</div>

    <div class="auth-card">
        <div class="auth-avatar-wrap">
            <div class="auth-avatar-circle"><i class="fas fa-user"></i></div>
        </div>
        <h2 class="auth-card-title">Buat Akun Baru</h2>
        <p class="auth-card-desc">Daftar untuk upload foto & booking tiket</p>

        <form action="index.php?page=register_process" method="POST" onsubmit="return checkForm()">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="auth-form-group">
                <label>Nama Lengkap</label>
                <div class="auth-input-wrap">
                    <i class="fas fa-user auth-input-icon"></i>
                    <input type="text" name="nama" placeholder="Ahmad Hidayat" required autocomplete="name">
                </div>
            </div>

            <div class="auth-form-group">
                <label>Email</label>
                <div class="auth-input-wrap">
                    <i class="fas fa-envelope auth-input-icon"></i>
                    <input type="email" name="email" placeholder="ahmad@email.com" required autocomplete="email">
                </div>
            </div>

            <div class="auth-form-group">
                <label>No. Telepon</label>
                <div class="auth-input-wrap">
                    <i class="fas fa-phone auth-input-icon"></i>
                    <input type="text" name="no_telp" placeholder="081234567890" required autocomplete="tel">
                </div>
            </div>

            <div class="auth-form-group">
                <label>Password <small>(min. 8 karakter)</small></label>
                <div class="auth-input-wrap">
                    <i class="fas fa-lock auth-input-icon"></i>
                    <input type="password" name="password" id="regPassword" placeholder="Min. 8 karakter" class="has-toggle" minlength="8" required autocomplete="new-password">
                    <button type="button" class="auth-toggle-pw" onclick="togglePw('regPassword', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="auth-form-group">
                <label>Konfirmasi Password</label>
                <div class="auth-input-wrap">
                    <i class="fas fa-lock auth-input-icon"></i>
                    <input type="password" name="confirm_password" id="regConfirm" placeholder="Ketik ulang password" class="has-toggle" minlength="8" required autocomplete="new-password">
                    <button type="button" class="auth-toggle-pw" onclick="togglePw('regConfirm', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="auth-form-group">
                <label>Kode Keamanan</label>
                <div class="auth-captcha-row">
                    <div class="auth-captcha-code-wrap">
                        <div class="auth-captcha-meta">Masukkan kode berikut</div>
                        <div class="auth-captcha-code" aria-label="Kode keamanan">
                            <?= htmlspecialchars($_SESSION['register_challenge'] ?? '') ?>
                        </div>
                    </div>
                    <a href="index.php?page=register" class="auth-captcha-refresh" title="Buat kode baru" aria-label="Buat kode baru">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M21 12a9 9 0 1 1-2.64-6.36"/>
                            <polyline points="21 3 21 9 15 9"/>
                        </svg>
                    </a>
                </div>
                <div class="auth-input-wrap">
                    <span class="auth-input-icon" aria-hidden="true">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M12 8v8"/>
                            <path d="M9.5 10.5h5"/>
                        </svg>
                    </span>
                    <input type="text" name="register_challenge" id="regCaptcha" placeholder="Ketik kode di atas" maxlength="5" required autocomplete="off" style="text-transform: uppercase;">
                </div>
                <small class="auth-captcha-help">Tujuan fitur ini untuk memastikan pendaftaran dilakukan oleh manusia.</small>
            </div>

            <button type="submit" class="auth-btn-submit">Daftar Sekarang</button>
        </form>

        <p class="auth-card-footer">
            Sudah punya akun? <a href="index.php?page=login">Masuk di sini</a>
        </p>
    </div>

    <div class="auth-back-link">
        <a href="index.php">← Kembali ke Website Utama</a>
    </div>

<script>
    const popupConfig = {
        error:   { icon:'❌', iconBg:'#FCEBEB', title:'Pendaftaran Gagal',      btn:'#C0392B', btnText:'Coba Lagi' },
        csrf:    { icon:'⚠️', iconBg:'#FAEEDA', title:'Permintaan Tidak Valid', btn:'#C0392B', btnText:'Muat Ulang' },
        success: { icon:'🎉', iconBg:'#EAF3DE', title:'Registrasi Berhasil!',   btn:'#27ae60', btnText:'Masuk Sekarang' },
        info:    { icon:'ℹ️', iconBg:'#E6F1FB', title:'Informasi',              btn:'#185FA5', btnText:'OK' },
    };

    function showPopup(type, message, redirect) {
        const cfg = popupConfig[type] || popupConfig.info;
        document.getElementById('wl-icon').innerHTML  = cfg.icon;
        document.getElementById('wl-icon').style.background = cfg.iconBg;
        document.getElementById('wl-title').textContent = cfg.title;
        document.getElementById('wl-desc').textContent  = message;
        document.getElementById('wl-btn').textContent   = cfg.btnText;
        document.getElementById('wl-btn').style.background = cfg.btn;
        document.getElementById('wl-btn').onclick = () => {
            closePopup();
            if (redirect) window.location.href = redirect;
        };
        document.getElementById('wl-overlay').style.display = 'flex';
    }

    function closePopup() {
        document.getElementById('wl-overlay').style.display = 'none';
    }

    document.getElementById('wl-overlay').addEventListener('click', function(e) {
        if (e.target === this) closePopup();
    });

    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }


    function checkForm() {
        const pw      = document.getElementById('regPassword').value;
        const cpw     = document.getElementById('regConfirm').value;
        const captcha = document.getElementById('regCaptcha').value.trim();
        if (pw.length < 8) {
            showPopup('error', 'Password minimal 8 karakter!', null);
            return false;
        }
        if (pw !== cpw) {
            showPopup('error', 'Konfirmasi password tidak cocok!', null);
            return false;
        }
        if (captcha.length === 0) {
            showPopup('error', 'Kode keamanan wajib diisi!', null);
            return false;
        }
        return true;
    }

    <?php if ($popup_type && $popup_message): ?>
    window.addEventListener('DOMContentLoaded', () => {
        showPopup(
            '<?= $popup_type ?>',
            '<?= addslashes($popup_message) ?>',
            <?= $popup_type === 'success' ? "'index.php?page=login'" : 'null' ?>
        );
    });
    <?php endif; ?>
</script>
</body>
</html>
