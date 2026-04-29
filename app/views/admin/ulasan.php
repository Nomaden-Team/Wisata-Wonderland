<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem('wl_theme')||'light';document.documentElement.setAttribute('data-theme',t);document.documentElement.style.colorScheme=t;}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
    <title>Kelola Ulasan - Wonderland Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/enhance.css">
</head>

<body class="adm-body">
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<div class="adm-wrapper">
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="adm-main">

        <div class="adm-page-header">
            <div>
                <h1 class="adm-page-title">Kelola Ulasan</h1>
                <p class="adm-page-sub">Kelola ulasan dan masukan pengunjung</p>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="adm-alert adm-alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="adm-alert adm-alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Stat Cards Ulasan -->
        <div class="adm-ulasan-stats">
            <div class="adm-ulasan-stat-card" style="background:#D97706">
                <p class="adm-ulasan-stat-label">Total Ulasan</p>
                <p class="adm-ulasan-stat-value"><?= $total ?></p>
            </div>

            <div class="adm-ulasan-stat-card" style="background:#16A34A">
                <p class="adm-ulasan-stat-label">Dipublikasikan</p>
                <p class="adm-ulasan-stat-value"><?= $pub ?></p>
            </div>

            <div class="adm-ulasan-stat-card" style="background:#2563EB">
                <p class="adm-ulasan-stat-label">Menunggu</p>
                <p class="adm-ulasan-stat-value"><?= $pend ?></p>
            </div>

            <div class="adm-ulasan-stat-card" style="background:#9333EA">
                <p class="adm-ulasan-stat-label">Rata-rata Rating</p>
                <p class="adm-ulasan-stat-value"><?= $avg ?></p>
            </div>
        </div>

        <div class="adm-card" style="margin-bottom:20px;padding:16px 20px;">
            <form method="GET" action="index.php" class="adm-filter-bar" style="margin:0">
                <input type="hidden" name="page" value="admin_ulasan">

                <div class="adm-search-wrap">
                    <i class="fas fa-search"></i>
                    <input
                        type="text"
                        name="search"
                        class="adm-search-input"
                        placeholder="Cari ulasan..."
                        value="<?= htmlspecialchars($search) ?>"
                    >
                </div>

                <select name="status" class="adm-select-filter" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>Dipublikasikan</option>
                    <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Menunggu / Disembunyikan</option>
                </select>
            </form>
        </div>

        <div class="adm-ulasan-list">
            <?php if (empty($ulasans)): ?>
                <div class="adm-card adm-empty">Tidak ada ulasan ditemukan</div>
            <?php else: ?>
                <?php foreach ($ulasans as $ul): ?>
                    <?php
                        $namaUser     = $ul['nama_user']   ?? $ul['nama']        ?? 'Pengunjung';
                        $emailUser    = $ul['email_user']  ?? $ul['email']       ?? '';
                        $komentarText = $ul['ulasan']      ?? $ul['komentar']    ?? '';
                        $wahanaLabel  = $ul['wahana_name'] ?? $ul['nama_wahana'] ?? '-';
                        $statusUlasan = $ul['status']      ?? 'pending';

                        $statusLabelMap = [
                            'approved' => 'Dipublikasikan',
                            'pending'  => 'Menunggu',
                        ];

                        $statusClassMap = [
                            'approved' => 'published',
                            'pending'  => 'pending',
                        ];

                        $statusLabel = $statusLabelMap[$statusUlasan] ?? ucfirst($statusUlasan);
                        $statusClass = $statusClassMap[$statusUlasan] ?? strtolower($statusUlasan);

                        $ratingVal    = (int) ($ul['rating'] ?? 0);
                        $createdAt    = $ul['created_at']  ?? '';

                        $detailData = json_encode([
                            'nama'   => $namaUser,
                            'email'  => $emailUser,
                            'wahana' => $wahanaLabel,
                            'rating' => $ratingVal,
                            'ulasan' => $komentarText,
                            'status' => $statusLabel,
                            'date'   => $createdAt,
                            'suka'  => (int) ($ul['suka'] ?? 0),
                        ], JSON_HEX_QUOT | JSON_HEX_APOS);
                    ?>

                    <div class="adm-ulasan-item">
                        <div class="adm-ulasan-content">
                            <div class="adm-ulasan-top">
                                <div>
                                    <p class="adm-ulasan-name">
                                        <?= htmlspecialchars($namaUser) ?>
                                        <span class="adm-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="<?= $i <= $ratingVal ? 'fas' : 'far' ?> fa-star"></i>
                                            <?php endfor; ?>
                                        </span>
                                    </p>

                                    <?php if ($emailUser !== ''): ?>
                                        <p class="adm-ulasan-email"><?= htmlspecialchars($emailUser) ?></p>
                                    <?php endif; ?>
                                </div>

                                <span class="adm-badge adm-badge-<?= htmlspecialchars($statusClass) ?>">
                                    <?= htmlspecialchars($statusLabel) ?>
                                </span>
                            </div>

                            <span class="adm-ulasan-wahana-tag"><?= htmlspecialchars($wahanaLabel) ?></span>
                            <p class="adm-ulasan-text"><?= htmlspecialchars($komentarText) ?></p>

                            <div class="adm-ulasan-meta">
                                <span><?= htmlspecialchars($createdAt) ?></span>
                                <span><i class="far fa-thumbs-up"></i> <?= (int) ($ul['suka'] ?? 0) ?> suka</span>
                            </div>
                        </div>

                        <div class="adm-ulasan-actions">
                            <button
                                type="button"
                                class="adm-btn-detail"
                                onclick="showDetail(<?= htmlspecialchars($detailData, ENT_QUOTES) ?>)"
                            >
                                <i class="far fa-eye"></i> Detail
                            </button>

                            <?php if ($statusUlasan === 'pending'): ?>
                                <form
                                    method="POST"
                                    action="index.php?page=admin_ulasan"
                                    class="js-confirm-action"
                                    data-confirm-title="Setujui Ulasan?"
                                    data-confirm-message="Ulasan ini akan ditampilkan ke publik."
                                    data-confirm-button="Setujui"
                                    data-confirm-type="success"
                                >
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="id" value="<?= (int) $ul['id'] ?>">
                                    <button type="submit" class="adm-btn-approve" style="width:auto;padding:6px 14px;background:#16A34A">
                                        Approve
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($statusUlasan === 'approved'): ?>
                                <form
                                    method="POST"
                                    action="index.php?page=admin_ulasan"
                                    class="js-confirm-action"
                                    data-confirm-title="Sembunyikan Ulasan?"
                                    data-confirm-message="Ulasan ini akan disembunyikan dari halaman publik."
                                    data-confirm-button="Sembunyikan"
                                    data-confirm-type="danger"
                                >
                                    <input type="hidden" name="action" value="hide">
                                    <input type="hidden" name="id" value="<?= (int) $ul['id'] ?>">
                                    <button type="submit" class="adm-btn-hide">Sembunyikan</button>
                                </form>
                            <?php else: ?>
                                <form
                                    method="POST"
                                    action="index.php?page=admin_ulasan"
                                    class="js-confirm-action"
                                    data-confirm-title="Publish Ulasan?"
                                    data-confirm-message="Ulasan ini akan ditampilkan kembali ke publik."
                                    data-confirm-button="Publish"
                                    data-confirm-type="success"
                                >
                                    <input type="hidden" name="action" value="publish">
                                    <input type="hidden" name="id" value="<?= (int) $ul['id'] ?>">
                                    <button type="submit" class="adm-btn-approve" style="width:auto;padding:6px 14px">
                                        Publish
                                    </button>
                                </form>
                            <?php endif; ?>

                            <form
                                method="POST"
                                action="index.php?page=admin_ulasan"
                                class="js-confirm-action"
                                data-confirm-title="Hapus Ulasan?"
                                data-confirm-message="Ulasan ini akan dihapus permanen."
                                data-confirm-button="Hapus"
                                data-confirm-type="danger"
                            >
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id" value="<?= (int) $ul['id'] ?>">
                                <button type="submit" class="adm-btn-icon del">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<button class="adm-help-btn">?</button>

<!-- Modal Konfirmasi Aksi -->
<div class="adm-modal-overlay" id="modalConfirmAction">
    <div class="adm-modal" style="max-width:420px">
        <div class="adm-modal-header">
            <h3 class="adm-modal-title" id="confirmActionTitle">Konfirmasi Aksi</h3>
            <button class="adm-modal-close" type="button" onclick="closeConfirmAction()">&times;</button>
        </div>

        <div style="padding:20px 0">
            <p id="confirmActionMessage" style="font-size:.9rem;color:#6B7280;margin-bottom:20px;">
                Apakah kamu yakin ingin melanjutkan aksi ini?
            </p>

            <div class="adm-form-actions">
                <button type="button" class="adm-btn-cancel" onclick="closeConfirmAction()">Batal</button>
                <button
                    type="button"
                    class="adm-btn-save"
                    id="confirmActionButton"
                    onclick="submitConfirmAction()"
                >
                    Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Ulasan -->
<div id="modalDetail" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:var(--adm-card,#1e293b);border-radius:12px;padding:28px 32px;max-width:520px;width:90%;box-shadow:0 8px 32px rgba(0,0,0,.4);position:relative;color:var(--adm-text,#e2e8f0);">
        <button onclick="closeDetail()" style="position:absolute;top:14px;right:18px;background:none;border:none;font-size:22px;cursor:pointer;color:inherit;">&times;</button>

        <h2 style="margin:0 0 16px;font-size:1.2rem;">Detail Ulasan</h2>

        <table style="width:100%;border-collapse:collapse;font-size:.93rem;">
            <tr>
                <td style="padding:6px 0;color:#94a3b8;width:110px">Nama</td>
                <td id="dNama" style="padding:6px 0;font-weight:600"></td>
            </tr>
            <tr>
                <td style="padding:6px 0;color:#94a3b8">Email</td>
                <td id="dEmail" style="padding:6px 0"></td>
            </tr>
            <tr>
                <td style="padding:6px 0;color:#94a3b8">Wahana</td>
                <td id="dWahana" style="padding:6px 0"></td>
            </tr>
            <tr>
                <td style="padding:6px 0;color:#94a3b8">Rating</td>
                <td id="dRating" style="padding:6px 0"></td>
            </tr>
            <tr>
                <td style="padding:6px 0;color:#94a3b8">Status</td>
                <td id="dStatus" style="padding:6px 0"></td>
            </tr>
            <tr>
                <td style="padding:6px 0;color:#94a3b8">Tanggal</td>
                <td id="dDate" style="padding:6px 0"></td>
            </tr>
            <tr>
                <td style="padding:6px 0;color:#94a3b8">Suka</td>
                <td id="dSuka" style="padding:6px 0"></td>
            </tr>
        </table>

        <div style="margin-top:14px">
            <p style="color:#94a3b8;margin:0 0 6px;font-size:.85rem;">Komentar</p>
            <div id="dUlasan" style="background:rgba(0,0,0,.2);border-radius:8px;padding:12px;line-height:1.6;white-space:pre-wrap;"></div>
        </div>
    </div>
</div>

<script>
let pendingConfirmForm = null;

function openConfirmAction(form) {
    pendingConfirmForm = form;

    const title = form.dataset.confirmTitle || 'Konfirmasi Aksi';
    const message = form.dataset.confirmMessage || 'Apakah kamu yakin ingin melanjutkan aksi ini?';
    const buttonText = form.dataset.confirmButton || 'Lanjutkan';
    const buttonType = form.dataset.confirmType || 'danger';

    document.getElementById('confirmActionTitle').textContent = title;
    document.getElementById('confirmActionMessage').textContent = message;
    document.getElementById('confirmActionButton').textContent = buttonText;

    const button = document.getElementById('confirmActionButton');

    if (buttonType === 'danger') {
        button.style.background = '#DC2626';
        button.style.borderColor = '#DC2626';
    } else if (buttonType === 'success') {
        button.style.background = '#16A34A';
        button.style.borderColor = '#16A34A';
    } else {
        button.style.background = '';
        button.style.borderColor = '';
    }

    document.getElementById('modalConfirmAction').classList.add('show');
}

function closeConfirmAction() {
    document.getElementById('modalConfirmAction').classList.remove('show');
    pendingConfirmForm = null;
}

function submitConfirmAction() {
    if (pendingConfirmForm) {
        pendingConfirmForm.submit();
    }
}

document.querySelectorAll('.js-confirm-action').forEach(function (form) {
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        openConfirmAction(form);
    });
});

function showDetail(d) {
    document.getElementById('dNama').textContent   = d.nama   || '-';
    document.getElementById('dEmail').textContent  = d.email  || '-';
    document.getElementById('dWahana').textContent = d.wahana || '-';

    var stars = '';

    for (var i = 1; i <= 5; i++) {
        stars += (i <= d.rating ? '\u2605' : '\u2606');
    }

    document.getElementById('dRating').textContent = stars + ' (' + d.rating + ')';
    document.getElementById('dStatus').textContent = d.status || '-';
    document.getElementById('dDate').textContent   = d.date   || '-';
    document.getElementById('dSuka').textContent  = d.suka  + ' suka';
    document.getElementById('dUlasan').textContent = d.ulasan || '-';

    var modal = document.getElementById('modalDetail');
    modal.style.display = 'flex';
}

function closeDetail() {
    document.getElementById('modalDetail').style.display = 'none';
}

document.getElementById('modalDetail').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDetail();
    }
});
</script>

<script src="assets/js/enhance.js"></script>
</body>
</html>