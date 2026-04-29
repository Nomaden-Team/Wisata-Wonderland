<?php

$activePage = $activePage ?? 'dashboard';
?>
<!-- SIDEBAR -->
<aside class="ud-sidebar" id="udSidebar">
    <div class="ud-sidebar-brand">
        <span class="ud-brand-icon">🎢</span>
        <div>
            <div class="ud-brand-name">Wonderland</div>
            <div class="ud-brand-sub">Pengunjung Portal</div>
        </div>
    </div>

    <div class="ud-sidebar-user">
        <div class="ud-user-avatar">
            <?= strtoupper(substr($nama ?? 'U', 0, 1)) ?>
        </div>
        <div class="ud-user-info">
            <div class="ud-user-name"><?= htmlspecialchars($nama ?? 'Pengunjung') ?></div>
            <div class="ud-user-email"><?= htmlspecialchars($email ?? '') ?></div>
        </div>
    </div>

    <div class="ud-sidebar-divider"></div>

    <nav class="ud-sidebar-nav">
        <a href="index.php?page=user_dashboard" class="ud-nav-item <?= $activePage === 'dashboard' ? 'active' : '' ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="index.php?page=user_upload_foto_page" class="ud-nav-item <?= $activePage === 'upload' ? 'active' : '' ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17,8 12,3 7,8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Upload Foto
        </a>
        <a href="index.php?page=user_reservasi" class="ud-nav-item <?= $activePage === 'reservasi' ? 'active' : '' ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Reservasi
        </a>
    </nav>

    <div class="ud-sidebar-bottom">
        <a href="index.php" class="ud-nav-item">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
            Kembali ke Home
        </a>
        <a href="index.php?page=logout" class="ud-nav-item ud-nav-logout">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Logout
        </a>
    </div>
</aside>
