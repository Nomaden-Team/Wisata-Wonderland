<?php

$admin_nama = $_SESSION['nama'] ?? 'Admin';
?>
<header class="adm-topbar">

    <button type="button" class="adm-mobile-toggle" id="admMenuToggle" aria-label="Buka menu">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <line x1="3" y1="6"  x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
    </button>

    <div class="adm-topbar-left">
        <p class="adm-welcome-text">Selamat datang kembali,</p>
        <p class="adm-welcome-name"><?= htmlspecialchars($admin_nama) ?></p>
    </div>
    <div class="adm-topbar-right">
        <button type="button" class="wl-theme-toggle wl-theme-toggle--admin" aria-label="Aktifkan mode gelap" aria-pressed="false">
            <span class="wl-theme-toggle-icon" aria-hidden="true">☾</span>
            <span class="wl-theme-toggle-label">Dark</span>
        </button>
        <a href="index.php" class="adm-view-website">View Website</a>
        <button type="button"
           class="adm-logout-btn"
           id="admLogoutBtn"
           title="Keluar">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                <polyline points="16,17 21,12 16,7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            <span>Keluar</span>
        </button>
    </div>
</header>

<!-- ── Custom Logout Modal ─────────────────────────────── -->
<div class="adm-logout-overlay" id="admLogoutOverlay" aria-hidden="true">
    <div class="adm-logout-modal" role="dialog" aria-modal="true">


        <div class="alm-icon-wrap">
            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                <polyline points="16,17 21,12 16,7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
        </div>

        <h3 class="alm-title">Keluar dari Dasbor?</h3>
        <p class="alm-desc">Apakah Anda yakin ingin keluar dari dashboard?</p>

        <div class="alm-actions">
            <button type="button" class="alm-btn-cancel" id="admLogoutCancel">Batal</button>
            <a href="index.php?page=logout" class="alm-btn-confirm">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <polyline points="16,17 21,12 16,7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Ya, Keluar
            </a>
        </div>
    </div>
</div>

<style>
/* ── Topbar right ── */
.adm-topbar-right { display: flex; align-items: center; gap: 12px; }

@media (max-width: 640px) {
    .adm-topbar-right {
        gap: 8px;
    }
    .adm-topbar-right .wl-theme-toggle-label {
        display: none;
    }
    .adm-topbar-right .wl-theme-toggle {
        padding-inline: .85rem;
    }
}

.adm-logout-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 16px;
    background: #fee2e2;
    color: #b91c1c;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    border: 1px solid #fecaca;
    cursor: pointer;
    transition: all .18s ease;
    font-family: inherit;
}
.adm-logout-btn:hover {
    background: #dc2626;
    color: #fff;
    border-color: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(220,38,38,.25);
}
@media (max-width: 640px) {
    .adm-logout-btn span { display: none; }
    .adm-logout-btn { padding: 9px 11px; }
}

/* ── Logout Modal Overlay ── */
.adm-logout-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    opacity: 0;
    visibility: hidden;
    transition: opacity .25s ease, visibility .25s ease;
}
.adm-logout-overlay.show {
    opacity: 1;
    visibility: visible;
}

/* ── Modal Card ── */
.adm-logout-modal {
    background: #fff;
    border-radius: 20px;
    padding: 2rem 1.75rem 1.75rem;
    max-width: 360px;
    width: 100%;
    text-align: center;
    box-shadow: 0 24px 64px rgba(0,0,0,.18);
    transform: scale(0.88) translateY(16px);
    transition: transform .28s cubic-bezier(0.34,1.56,0.64,1), opacity .25s ease;
    opacity: 0;
}
.adm-logout-overlay.show .adm-logout-modal {
    transform: scale(1) translateY(0);
    opacity: 1;
}

/* ── Icon ── */
.alm-icon-wrap {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #dc2626;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.1rem;
    box-shadow: 0 8px 20px rgba(220,38,38,.18);
}

/* ── Text ── */
.alm-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 0.5rem;
}
.alm-desc {
    font-size: 0.84rem;
    color: #64748b;
    line-height: 1.6;
    margin: 0 0 1.5rem;
}

/* ── Buttons ── */
.alm-actions {
    display: flex;
    gap: 10px;
}
.alm-btn-cancel {
    flex: 1;
    padding: 11px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    font-size: 0.88rem;
    cursor: pointer;
    transition: background .15s;
    font-family: inherit;
}
.alm-btn-cancel:hover { background: #e2e8f0; }

.alm-btn-confirm {
    flex: 1;
    padding: 11px;
    border-radius: 10px;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    font-weight: 700;
    font-size: 0.88rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all .18s ease;
    box-shadow: 0 6px 16px rgba(220,38,38,.3);
}
.alm-btn-confirm:hover {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(220,38,38,.4);
}

[data-theme="dark"] .adm-view-website { color: #cbd5e1; }
[data-theme="dark"] .adm-view-website:hover { color: #f8fafc; }

[data-theme="dark"] .adm-logout-overlay {
    background: rgba(2, 6, 23, 0.72);
}
</style>

<script>
(function () {
    var btn     = document.getElementById('admLogoutBtn');
    var overlay = document.getElementById('admLogoutOverlay');
    var cancel  = document.getElementById('admLogoutCancel');

    if (!btn || !overlay) return;

    btn.addEventListener('click', function () {
        overlay.classList.add('show');
        overlay.setAttribute('aria-hidden', 'false');
    });

    function closeModal() {
        overlay.classList.remove('show');
        overlay.setAttribute('aria-hidden', 'true');
    }

    cancel.addEventListener('click', closeModal);


    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeModal();
    });


    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });
})();
</script>
