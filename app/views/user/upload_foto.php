<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem('wl_theme')||'light';document.documentElement.setAttribute('data-theme',t);document.documentElement.style.colorScheme=t;}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
    <title>Unggah Foto - Wonderland Samarinda</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/user-dashboard.css">

    <link rel="stylesheet" href="assets/css/enhance.css">
</head>
<body class="ud-body">

<?php $activePage = 'upload'; require __DIR__ . '/partials/sidebar.php'; ?>

<main class="ud-main">
    <div class="ud-topbar">
        <button type="button" class="ud-mobile-toggle" aria-label="Buka menu">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <div class="ud-topbar-left">
            <div class="ud-page-icon" style="background: linear-gradient(135deg, #ff6b6b, #f5a623);">
                <svg width="22" height="22" fill="white" viewBox="0 0 24 24"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
            </div>
            <div>
                <h1 class="ud-page-title">Unggah Foto Kunjungan</h1>
                <p class="ud-page-sub">Bagikan momen seru Anda di Wonderland Samarinda!</p>
            </div>
        </div>
        <button type="button" class="wl-theme-toggle wl-theme-toggle--user" aria-label="Aktifkan mode gelap" aria-pressed="false">
            <span class="wl-theme-toggle-icon" aria-hidden="true">☾</span>
            <span class="wl-theme-toggle-label">Dark</span>
        </button>
        <button class="ud-btn-red" onclick="document.getElementById('inputFoto').click()">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17,8 12,3 7,8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Unggah Foto Baru
        </button>
        <div class="ud-topbar-line" ></div>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'success'): ?>
            <div class="ud-alert ud-alert-success"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg> Foto berhasil diupload!</div>
        <?php elseif ($_GET['status'] === 'error'): ?>
            <div class="ud-alert ud-alert-error"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> <?= htmlspecialchars($_GET['msg'] ?? 'Terjadi kesalahan.') ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    $totalDisetujui = count(array_filter($fotos ?? [], fn($f) => ($f['status'] ?? '') === 'approved'));
    $totalMenunggu  = count(array_filter($fotos ?? [], fn($f) => ($f['status'] ?? '') === 'pending'));
    $totalAll      = count($fotos ?? []);
    ?>
    <div class="ud-stats-row ud-stats-3">
        <div class="ud-stat-card">
            <div class="ud-stat-info">
                <div class="ud-stat-label">Disetujui</div>
                <div class="ud-stat-num"><?= $totalDisetujui ?></div>
            </div>
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7); color:#059669;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9,11 12,14 22,4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            </div>
        </div>
        <div class="ud-stat-card">
            <div class="ud-stat-info">
                <div class="ud-stat-label">Menunggu Review</div>
                <div class="ud-stat-num"><?= $totalMenunggu ?></div>
            </div>
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,#fffbf0,#fef0cc); color:#d4881a;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
            </div>
        </div>
        <div class="ud-stat-card">
            <div class="ud-stat-info">
                <div class="ud-stat-label">Total Foto</div>
                <div class="ud-stat-num"><?= $totalAll ?></div>
            </div>
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,#fff0f0,#ffd6d6); color:#e84545;">
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21,15 16,10 5,21"/></svg>
            </div>
        </div>
    </div>

    <div class="ud-card">
        <h2 class="ud-card-title" style="margin-bottom:20px;">Foto Saya</h2>
        <form action="index.php?page=user_upload_foto" method="POST" enctype="multipart/form-data" id="formUpload" style="display:none">
            <input type="hidden" name="from" value="user_upload_foto_page">
            <input type="file" id="inputFoto" name="foto" accept="image/jpeg,image/png,image/webp" onchange="this.form.submit()">
        </form>
        <?php if (!empty($fotos)): ?>
            <div class="ud-foto-grid">
                <?php foreach ($fotos as $foto): ?>
                    <div class="ud-foto-item">
                        <img src="uploads/<?= htmlspecialchars($foto['nama_file']) ?>" alt="Foto" loading="lazy">
                        <?php if (!empty($foto['status'])): $st = $foto['status']; ?>
                            <div class="ud-foto-badge ud-badge-<?= $st ?>"><?= $st === 'approved' ? 'Disetujui' : ($st === 'pending' ? 'Review' : 'Ditolak') ?></div>
                        <?php endif; ?>
                        <form action="index.php?page=user_hapus_foto" method="POST" class="ud-foto-delete-form">
                            <input type="hidden" name="foto_id" value="<?= $foto['id'] ?>">
                            <button type="submit" class="ud-foto-delete-btn" title="Hapus">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3,6 5,6 21,6"/><path d="M19 6l-1 14H6L5 6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="ud-empty-state ud-empty-large">
                <div class="ud-empty-icon-gradient">
                    <svg width="36" height="36" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                </div>
                <h3 class="ud-empty-title">Belum Ada Foto</h3>
                <p class="ud-empty-desc">Unggah foto kunjungan Anda untuk berbagi momen seru.</p>
                <button class="ud-btn-red" onclick="document.getElementById('inputFoto').click()">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17,8 12,3 7,8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Unggah Foto Pertama
                </button>
            </div>
        <?php endif; ?>
    </div>
</main>
<script src="assets/js/enhance.js"></script>
    <script src="assets/js/user.js"></script>
</body>
</html>
