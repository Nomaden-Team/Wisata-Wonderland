<?php $active_page = $active_page ?? 'dashboard'; ?>
<!-- Backdrop gelap saat sidebar terbuka di mobile -->
<div class="adm-sidebar-backdrop" id="admSidebarBackdrop"></div>

<aside class="adm-sidebar" id="admSidebar">
    <div class="adm-sidebar-brand" style="padding: 2rem 1.5rem;">
        <span class="adm-sidebar-logo">Wonder<span>land</span></span>
    </div>

    <nav class="adm-nav">
        <p class="adm-sidebar-section-title">Main Menu</p>

        <a href="index.php?page=admin_dashboard" class="adm-nav-link <?= $active_page === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i> <span>Dasbor</span>
        </a>
        <a href="index.php?page=admin_reservasi" class="adm-nav-link <?= $active_page === 'reservasi' ? 'active' : '' ?>">
            <i class="bi bi-calendar-check"></i> <span>Reservasi</span>
        </a>
        <a href="index.php?page=admin_wahana" class="adm-nav-link <?= $active_page === 'wahana' ? 'active' : '' ?>">
            <i class="bi bi-ticket-perforated"></i> <span>Wahana</span>
        </a>
        <a href="index.php?page=admin_fasilitas" class="adm-nav-link <?= $active_page === 'fasilitas' ? 'active' : '' ?>">
            <i class="bi bi-building-check"></i> <span>Fasilitas</span>
        </a>

        <p class="adm-sidebar-section-title" style="margin-top: 25px;">Secondary</p>

        <a href="index.php?page=admin_ulasan" class="adm-nav-link <?= $active_page === 'ulasan' ? 'active' : '' ?>">
            <i class="bi bi-chat-left-text"></i> <span>Ulasan</span>
        </a>
        <a href="index.php?page=admin_foto_user" class="adm-nav-link <?= $active_page === 'foto_user' ? 'active' : '' ?>">
            <i class="bi bi-images"></i> <span>Foto User</span>
        </a>
        <a href="index.php?page=admin_pricelist" class="adm-nav-link <?= $active_page === 'pricelist' ? 'active' : '' ?>">
            <i class="bi bi-tags"></i> <span>Price List</span>
        </a>
    </nav>
</aside>
