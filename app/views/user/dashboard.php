<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem('wl_theme')||'light';document.documentElement.setAttribute('data-theme',t);document.documentElement.style.colorScheme=t;}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
    <title>Dasbor Pengunjung - Wonderland Samarinda</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/enhance.css">
    <link rel="stylesheet" href="assets/css/user-dashboard.css">
</head>
<body class="ud-body">

<?php $activePage = 'dashboard'; require __DIR__ . '/partials/sidebar.php'; ?>

<main class="ud-main">
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'success'): ?>
            <div class="ud-alert ud-alert-success" style="margin-top:20px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                <?= htmlspecialchars($_GET['msg'] ?? 'Foto berhasil diupload! Menunggu persetujuan admin.') ?>
            </div>
        <?php elseif ($_GET['status'] === 'error'): ?>
            <div class="ud-alert ud-alert-error" style="margin-top:20px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <?= htmlspecialchars($_GET['msg'] ?? 'Terjadi kesalahan.') ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="ud-topbar">
        <button type="button" class="ud-mobile-toggle" aria-label="Buka menu">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>

        <div class="ud-topbar-left">
            <div class="ud-page-icon" style="background: linear-gradient(135deg, #ff6b6b, #f5a623);">
                <svg width="22" height="22" fill="white" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </div>
            <div>
                <h1 class="ud-page-title">Dasbor Pengunjung</h1>
                <p class="ud-page-sub">Selamat datang kembali, <strong><?= htmlspecialchars($nama ?? 'Pengunjung') ?>!</strong></p>
            </div>
        </div>

        <button type="button" class="wl-theme-toggle wl-theme-toggle--user" aria-label="Aktifkan mode gelap" aria-pressed="false">
            <span class="wl-theme-toggle-icon" aria-hidden="true">☾</span>
            <span class="wl-theme-toggle-label">Dark</span>
        </button>

        <div class="ud-topbar-line"></div>
    </div>

    <!-- Banner Selamat Datang -->
    <div class="ud-welcome-banner">
        <div class="ud-welcome-text">
            <div class="ud-welcome-greeting">Halo, <?= htmlspecialchars($nama ?? 'Pengunjung') ?>! 👋</div>
            <div class="ud-welcome-desc">Selamat datang kembali di portal pengunjung Wonderland Samarinda. Kelola foto dan reservasi kamu di sini.</div>
        </div>
        <div class="ud-welcome-emoji">🎡</div>
    </div>

    <div class="ud-stats-row">
        <div class="ud-stat-card">
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,#fff0f0,#ffe0e0); color:#e84545;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21,15 16,10 5,21"/></svg>
            </div>
            <div class="ud-stat-info">
                <div class="ud-stat-num"><?= count($fotos ?? []) ?></div>
                <div class="ud-stat-label">Total Foto</div>
            </div>
        </div>

        <div class="ud-stat-card">
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,#fffbf0,#fef0cc); color:#d4881a;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="ud-stat-info">
                <div class="ud-stat-num"><?= count($reservasis ?? []) ?></div>
                <div class="ud-stat-label">Total Reservasi</div>
            </div>
        </div>
    </div>

    <div class="ud-card">
        <div class="ud-card-header">
            <div class="ud-card-title-row">
                <div class="ud-card-icon" style="background:linear-gradient(135deg,#fff0f0,#ffe0e0); color:#e84545;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                </div>
                <h2 class="ud-card-title">Foto Saya</h2>
            </div>

            <button class="ud-btn-red" onclick="document.getElementById('inputFoto').click()">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17,8 12,3 7,8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Unggah Foto
            </button>
        </div>

        <form action="index.php?page=user_upload_foto" method="POST" enctype="multipart/form-data" id="formUpload" style="display:none">
            <input type="hidden" name="from" value="user_dashboard">
            <input type="file" id="inputFoto" name="foto" accept="image/jpeg,image/png,image/webp" onchange="this.form.submit()">
        </form>

        <?php if (!empty($fotos)): ?>
            <div class="ud-foto-grid">
                <?php foreach ($fotos as $foto): ?>
                    <div class="ud-foto-item">
                        <img src="uploads/<?= htmlspecialchars($foto['nama_file']) ?>" alt="Foto Kunjungan" loading="lazy">

                        <?php if (!empty($foto['status'])): ?>
                            <?php $st = $foto['status']; ?>
                            <div class="ud-foto-badge ud-badge-<?= htmlspecialchars($st) ?>">
                                <?= $st === 'approved' ? 'Disetujui' : ($st === 'pending' ? 'Review' : 'Ditolak') ?>
                            </div>
                        <?php endif; ?>

                        <form action="index.php?page=user_hapus_foto" method="POST" class="ud-foto-delete-form js-user-confirm-action">
                            <input type="hidden" name="foto_id" value="<?= (int) $foto['id'] ?>">
                            <button type="submit" class="ud-foto-delete-btn" title="Hapus">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3,6 5,6 21,6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="ud-empty-state">
                <div class="ud-empty-bg"><svg width="40" height="40" fill="none" stroke="#aaa" stroke-width="1.5" viewBox="0 0 24 24"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg></div>
                <p class="ud-empty-text">Belum ada foto</p>
                <button class="ud-btn-red" onclick="document.getElementById('inputFoto').click()">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17,8 12,3 7,8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Unggah Foto Pertama
                </button>
            </div>
        <?php endif; ?>
    </div>

    <div class="ud-card">
        <div class="ud-card-header">
            <div class="ud-card-title-row">
                <div class="ud-card-icon" style="background:linear-gradient(135deg,#fffbf0,#fef0cc); color:#d4881a;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <h2 class="ud-card-title">Kunjungan Mendatang</h2>
            </div>

            <a href="index.php?page=user_reservasi" class="ud-btn-yellow">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Reservasi
            </a>
        </div>

        <?php if (!empty($reservasis ?? [])): ?>
            <div class="ud-reservasi-list">
                <?php foreach ($reservasis as $r): ?>
                    <?php
                    $st = $r['status'] ?? 'terjadwal';
                    $stLabel = [
                        'terjadwal' => 'Disetujui',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        'pending' => 'Menunggu'
                    ];
                    $stClass = [
                        'terjadwal' => 'confirmed',
                        'selesai' => 'completed',
                        'dibatalkan' => 'cancelled',
                        'pending' => 'pending'
                    ];
                    ?>

                    <div class="ud-reservasi-item">
                        <div class="ud-reservasi-top">
                            <div class="ud-reservasi-left">
                                <span class="ud-reservasi-name"><?= htmlspecialchars($r['nama_kegiatan']) ?></span>
                                <span class="ud-badge-status ud-badge-<?= $stClass[$st] ?? 'confirmed' ?>">
                                    <?= $stLabel[$st] ?? $st ?>
                                </span>
                            </div>
                        </div>

                        <div class="ud-reservasi-meta">
                            <span class="ud-meta-item">
                                <svg width="14" height="14" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <span class="ud-meta-label">Tanggal</span> <?= date('j/n/Y', strtotime($r['tanggal'])) ?>
                            </span>

                            <?php if (!empty($r['jam_mulai'])): ?>
                                <span class="ud-meta-item">
                                    <svg width="14" height="14" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                                    <span class="ud-meta-label">Waktu</span> <?= date('H:i', strtotime($r['jam_mulai'])) ?> WIB
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($r['jumlah_peserta'])): ?>
                                <span class="ud-meta-item">
                                    <svg width="14" height="14" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    <span class="ud-meta-label">Jumlah</span> <?= (int) $r['jumlah_peserta'] ?> Orang
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="ud-empty-state">
                <div class="ud-empty-bg"><svg width="40" height="40" fill="none" stroke="#aaa" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                <p class="ud-empty-text">Belum ada reservasi</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<div class="ud-modal-overlay" id="comingSoonModal" onclick="hideComingSoon(event)">
    <div class="ud-modal">
        <div class="ud-modal-icon"><svg width="32" height="32" fill="white" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></div>
        <h2 class="ud-modal-title">Coming Soon!</h2>
        <p class="ud-modal-desc">Fitur <strong>Tambah Reservasi</strong> sedang dalam pengembangan. Anda akan dapat membuat reservasi online dalam waktu dekat!</p>
        <div class="ud-modal-info">
            <p class="ud-modal-info-title">Untuk sementara, Anda dapat:</p>
            <ul><li>✓ Hubungi kami via WhatsApp</li><li>✓ Kunjungi langsung loket tiket</li><li>✓ Telepon ke customer service</li></ul>
        </div>
        <button class="ud-btn-red ud-btn-full" onclick="hideComingSoon()">Mengerti</button>
    </div>
</div>

<div class="ud-modal-overlay" id="deleteFotoModal" onclick="closeDeleteFotoModal(event)">
    <div class="ud-modal">
        <div class="ud-modal-icon">
            <svg width="32" height="32" fill="white" viewBox="0 0 24 24"><path d="M9 3h6l1 2h5v2H3V5h5l1-2zm1 6h2v9h-2V9zm4 0h2v9h-2V9z"/></svg>
        </div>
        <h2 class="ud-modal-title">Hapus Foto?</h2>
        <p class="ud-modal-desc">Foto ini akan dihapus dari dashboard kamu. Tindakan ini tidak bisa dibatalkan.</p>
        <div style="display:flex;gap:10px;">
            <button type="button" class="ud-btn-yellow ud-btn-full" onclick="closeDeleteFotoModal()">Batal</button>
            <button type="button" class="ud-btn-red ud-btn-full" onclick="submitDeleteFoto()">Hapus</button>
        </div>
    </div>
</div>

<script>
let pendingDeleteFotoForm = null;

function showComingSoon() {
    document.getElementById('comingSoonModal').classList.add('active');
}

function hideComingSoon(e) {
    if (!e || e.target === document.getElementById('comingSoonModal')) {
        document.getElementById('comingSoonModal').classList.remove('active');
    }
}

function openDeleteFotoModal(form) {
    pendingDeleteFotoForm = form;
    document.getElementById('deleteFotoModal').classList.add('active');
}

function closeDeleteFotoModal(e) {
    if (!e || e.target === document.getElementById('deleteFotoModal')) {
        document.getElementById('deleteFotoModal').classList.remove('active');
        pendingDeleteFotoForm = null;
    }
}

function submitDeleteFoto() {
    if (pendingDeleteFotoForm) {
        pendingDeleteFotoForm.submit();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    var alerts = document.querySelectorAll('.ud-alert');

    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity .5s';
            alert.style.opacity = '0';
            setTimeout(function() { alert.style.display = 'none'; }, 500);
        }, 5000);
    });

    document.querySelectorAll('.js-user-confirm-action').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            openDeleteFotoModal(form);
        });
    });
});
</script>

<script src="assets/js/enhance.js"></script>
<script src="assets/js/user.js"></script>
</body>
</html>