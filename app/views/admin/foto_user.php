<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>(function(){try{var t=localStorage.getItem('wl_theme')||'light';document.documentElement.setAttribute('data-theme',t);document.documentElement.style.colorScheme=t;}catch(e){document.documentElement.setAttribute('data-theme','light');}})();</script>
    <title>Foto User - Wonderland Admin</title>
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
                <h1 class="adm-page-title">Kelola Foto User</h1>
                <p class="adm-page-sub">Review & approve foto yang diupload pengunjung</p>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="adm-foto-stats">
            <div class="adm-foto-stat" style="background:#2563EB">
                <p class="adm-foto-stat-label">Total Foto</p>
                <p class="adm-foto-stat-value"><?= $total ?></p>
            </div>

            <div class="adm-foto-stat" style="background:#D97706">
                <p class="adm-foto-stat-label">Menunggu</p>
                <p class="adm-foto-stat-value"><?= $pending ?></p>
            </div>

            <div class="adm-foto-stat" style="background:#16A34A">
                <p class="adm-foto-stat-label">Disetujui</p>
                <p class="adm-foto-stat-value"><?= $approved ?></p>
            </div>

            <div class="adm-foto-stat" style="background:#DC2626">
                <p class="adm-foto-stat-label">Ditolak</p>
                <p class="adm-foto-stat-value"><?= $rejected ?></p>
            </div>
        </div>

        <div class="adm-card" style="margin-bottom:20px;padding:16px 20px;">
            <form method="GET" action="index.php" class="adm-filter-bar" style="margin:0">
                <input type="hidden" name="page" value="admin_foto_user">

                <div class="adm-search-wrap">
                    <i class="fas fa-search"></i>
                    <input
                        type="text"
                        name="search"
                        class="adm-search-input"
                        placeholder="Cari foto berdasarkan nama, email, atau caption..."
                        value="<?= htmlspecialchars($search) ?>"
                    >
                </div>

                <select name="status" class="adm-select-filter" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending"  <?= $filter === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                    <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                    <option value="rejected" <?= $filter === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </form>
        </div>

        <?php if (empty($fotos)): ?>
            <div class="adm-card adm-empty">Tidak ada foto yang ditemukan</div>
        <?php else: ?>
            <div class="adm-foto-grid-admin">
                <?php foreach ($fotos as $foto): ?>
                    <div class="adm-foto-card-admin">
                        <div class="adm-foto-thumb">
                            <img
                                src="uploads/<?= htmlspecialchars($foto['nama_file']) ?>"
                                alt="Foto"
                                loading="lazy"
                                onerror="this.parentElement.innerHTML='<div style=\'display:flex;align-items:center;justify-content:center;height:100%;color:#9CA3AF;font-size:2rem\'><i class=\'far fa-image\'></i></div>'"
                            >
                        </div>

                        <div class="adm-foto-info">
                            <p class="adm-foto-user-name"><?= htmlspecialchars($foto['nama']) ?></p>
                            <p class="adm-foto-user-email"><?= htmlspecialchars($foto['email']) ?></p>
                            <p style="font-size:.75rem;color:#9CA3AF;margin-top:4px"><?= htmlspecialchars($foto['created_at']) ?></p>

                            <div style="margin-top:8px">
                                <span class="adm-badge adm-badge-<?= strtolower($foto['status'] ?? 'pending') ?>">
                                    <?= ucfirst($foto['status'] ?? 'pending') ?>
                                </span>
                            </div>

                            <div class="adm-foto-actions">
                                <?php if (($foto['status'] ?? 'pending') !== 'approved'): ?>
                                    <form
                                        method="POST"
                                        action="index.php?page=admin_foto_user"
                                        class="js-confirm-action"
                                        data-confirm-title="Setujui Foto?"
                                        data-confirm-message="Foto ini akan ditampilkan ke galeri publik."
                                        data-confirm-button="Setujui"
                                        data-confirm-type="success"
                                    >
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="id" value="<?= (int) $foto['id'] ?>">
                                        <button type="submit" class="adm-btn-approve">✓ Setujui</button>
                                    </form>
                                <?php endif; ?>

                                <?php if (($foto['status'] ?? 'pending') !== 'rejected'): ?>
                                    <form
                                        method="POST"
                                        action="index.php?page=admin_foto_user"
                                        class="js-confirm-action"
                                        data-confirm-title="Reject Foto?"
                                        data-confirm-message="Foto ini akan ditolak dan tidak ditampilkan ke publik."
                                        data-confirm-button="Reject"
                                        data-confirm-type="danger"
                                    >
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="id" value="<?= (int) $foto['id'] ?>">
                                        <button type="submit" class="adm-btn-reject">✕ Reject</button>
                                    </form>
                                <?php endif; ?>

                                <form
                                    method="POST"
                                    action="index.php?page=admin_foto_user"
                                    class="js-confirm-action"
                                    data-confirm-title="Hapus Foto?"
                                    data-confirm-message="Foto ini akan dihapus permanen."
                                    data-confirm-button="Hapus"
                                    data-confirm-type="danger"
                                    style="width:100%;margin-top:6px"
                                >
                                    <input type="hidden" name="action" value="hapus">
                                    <input type="hidden" name="id" value="<?= (int) $foto['id'] ?>">
                                    <button type="submit" class="adm-btn-reject" style="width:100%">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>
</div>

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

<button class="adm-help-btn">?</button>

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
</script>

<script src="assets/js/enhance.js"></script>
</body>
</html>