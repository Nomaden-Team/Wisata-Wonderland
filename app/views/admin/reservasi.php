<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (function () {
            try {
                var t = localStorage.getItem('wl_theme') || 'light';
                document.documentElement.setAttribute('data-theme', t);
                document.documentElement.style.colorScheme = t;
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    <title>Kelola Reservasi - Wonderland Admin</title>
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
                <h1 class="adm-page-title">Kelola Reservasi</h1>
                <p class="adm-page-sub">Kelola reservasi dan persetujuan pengunjung</p>
            </div>

            <button class="adm-btn adm-btn-primary" onclick="openModal('modalTambah')">
                <i class="fas fa-plus"></i> Tambah Reservasi
            </button>
        </div>

        <?php if (isset($_GET['ok'])): ?>
            <?php
            $okMessages = [
                'tambah'  => 'Reservasi berhasil ditambahkan!',
                'edit'    => 'Reservasi berhasil diupdate!',
                'hapus'   => 'Reservasi berhasil dihapus!',
                'approve' => 'Reservasi berhasil disetujui!',
                'status'  => 'Status reservasi berhasil diubah!',
            ];
            ?>
            <div style="background:#D1FAE5;color:#065F46;padding:12px 18px;border-radius:10px;margin-bottom:18px;font-size:.85rem;font-weight:600;">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($okMessages[$_GET['ok']] ?? 'Aksi reservasi berhasil diproses!') ?>
            </div>
        <?php endif; ?>

        <div class="adm-card" style="margin-bottom:16px;padding:16px 20px;">
            <form method="GET" action="index.php" class="adm-filter-bar" style="margin:0">
                <input type="hidden" name="page" value="admin_reservasi">

                <div class="adm-search-wrap">
                    <i class="fas fa-search"></i>
                    <input
                        type="text"
                        name="search"
                        class="adm-search-input"
                        placeholder="Cari berdasarkan nama kegiatan, jenis, atau kode booking..."
                        value="<?= htmlspecialchars($search ?? '') ?>"
                    >
                </div>

                <select name="status" class="adm-select-filter" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= (($filter_status ?? '') === 'pending') ? 'selected' : '' ?>>Menunggu Pembayaran/Verifikasi</option>
                    <option value="terjadwal" <?= (($filter_status ?? '') === 'terjadwal') ? 'selected' : '' ?>>Disetujui</option>
                    <option value="selesai" <?= (($filter_status ?? '') === 'selesai') ? 'selected' : '' ?>>Selesai</option>
                    <option value="dibatalkan" <?= (($filter_status ?? '') === 'dibatalkan') ? 'selected' : '' ?>>Dibatalkan</option>
                </select>
            </form>
        </div>

        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Nama Kegiatan</th>
                        <th>Tanggal Kunjungan</th>
                        <th>Pengunjung</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (empty($reservasis)): ?>
                    <tr>
                        <td colspan="7" class="adm-empty">Tidak ada data reservasi</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservasis as $r): ?>
                        <?php
                        $status = $r['status'] ?? 'pending';

                        $statusLabel = [
                            'pending'    => 'Menunggu Pembayaran/Verifikasi',
                            'terjadwal'  => 'Disetujui',
                            'selesai'    => 'Selesai',
                            'dibatalkan' => 'Dibatalkan',
                        ];

                        $statusClass = [
                            'pending'    => 'pending',
                            'terjadwal'  => 'confirmed',
                            'selesai'    => 'completed',
                            'dibatalkan' => 'cancelled',
                        ];

                        $namaUser = '—';

                        if (!empty($r['user_id']) && !empty($user_list)) {
                            foreach ($user_list as $u) {
                                if ((int) $u['id'] === (int) $r['user_id']) {
                                    $namaUser =
                                        htmlspecialchars($u['nama']) .
                                        '<br><small style="color:#6b7280">' .
                                        htmlspecialchars($u['email']) .
                                        '</small>';
                                    break;
                                }
                            }
                        }

                        $jsonReservasi = htmlspecialchars(
                            json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                        <tr>
                            <td>
                                <strong>
                                    <?= !empty($r['kode_booking'])
                                        ? htmlspecialchars($r['kode_booking'])
                                        : 'RES-' . str_pad((string) $r['id'], 3, '0', STR_PAD_LEFT)
                                    ?>
                                </strong>
                            </td>

                            <td><?= $namaUser ?></td>

                            <td>
                                <p class="adm-td-name"><?= htmlspecialchars($r['nama_kegiatan'] ?? '-') ?></p>
                                <p class="adm-td-sub"><?= htmlspecialchars($r['jenis_kegiatan'] ?? '') ?></p>

                                <?php if (!empty($r['kode_booking'])): ?>
                                    <small style="color:#6b7280">Kode: <?= htmlspecialchars($r['kode_booking']) ?></small>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($r['tanggal'] ?? '-') ?></td>

                            <td><?= (int) ($r['jumlah_peserta'] ?? 0) ?> orang</td>

                            <td>
                                <span class="adm-badge adm-badge-<?= htmlspecialchars($statusClass[$status] ?? 'pending') ?>">
                                    <?= htmlspecialchars($statusLabel[$status] ?? $status) ?>
                                </span>
                            </td>

                            <td>
                                <div class="adm-td-actions">
                                    <?php if ($status === 'pending'): ?>
                                        <form
                                            method="POST"
                                            action="index.php?page=admin_reservasi"
                                            class="js-confirm-action"
                                            data-confirm-title="Setujui Reservasi?"
                                            data-confirm-message="Pastikan bukti transfer sudah diterima dan dicek sebelum menyetujui reservasi ini."
                                            data-confirm-button="Setujui"
                                            data-confirm-type="success"
                                            style="display:inline"
                                        >
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                            <input type="hidden" name="status" value="terjadwal">
                                            <button type="submit" class="adm-btn-icon" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($status !== 'dibatalkan'): ?>
                                        <form
                                            method="POST"
                                            action="index.php?page=admin_reservasi"
                                            class="js-confirm-action"
                                            data-confirm-title="Batalkan Reservasi?"
                                            data-confirm-message="Reservasi ini akan diubah menjadi dibatalkan."
                                            data-confirm-button="Batalkan"
                                            data-confirm-type="danger"
                                            style="display:inline"
                                        >
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                            <input type="hidden" name="status" value="dibatalkan">
                                            <button type="submit" class="adm-btn-icon del" title="Batalkan">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <button class="adm-btn-icon edit" onclick='openEditModal(<?= $jsonReservasi ?>)' title="Edit">
                                        <i class="far fa-edit"></i>
                                    </button>

                                    <form
                                        method="POST"
                                        action="index.php?page=admin_reservasi"
                                        class="js-confirm-action"
                                        data-confirm-title="Hapus Reservasi?"
                                        data-confirm-message="Data reservasi ini akan dihapus permanen."
                                        data-confirm-button="Hapus"
                                        data-confirm-type="danger"
                                        style="display:inline"
                                    >
                                        <input type="hidden" name="action" value="hapus">
                                        <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                                        <button type="submit" class="adm-btn-icon del" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- Modal Tambah -->
<div class="adm-modal-overlay" id="modalTambah">
    <div class="adm-modal">
        <div class="adm-modal-header">
            <h3 class="adm-modal-title">Tambah Reservasi</h3>
            <button class="adm-modal-close" onclick="closeModal('modalTambah')">&times;</button>
        </div>

        <form method="POST" action="index.php?page=admin_reservasi">
            <input type="hidden" name="action" value="tambah">

            <div class="adm-form-group">
                <label>Pilih User (Pengunjung)</label>
                <select name="user_id" class="adm-search-input" style="padding-left:14px">
                    <option value="">— Tanpa User (reservasi umum) —</option>
                    <?php foreach (($user_list ?? []) as $u): ?>
                        <option value="<?= (int) $u['id'] ?>">
                            <?= htmlspecialchars($u['nama']) ?> (<?= htmlspecialchars($u['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="adm-form-group">
                <label>Nama Kegiatan</label>
                <input type="text" name="nama_kegiatan" required placeholder="Contoh: Kunjungan Sekolah">
            </div>

            <div class="adm-form-group">
                <label>Jenis Kegiatan</label>
                <input type="text" name="jenis_kegiatan" placeholder="Contoh: Wisata Edukasi">
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" required>
                </div>

                <div class="adm-form-group">
                    <label>Jumlah Peserta</label>
                    <input type="number" name="jumlah_peserta" min="1" required placeholder="10">
                </div>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>Jam Mulai</label>
                    <input type="text" name="jam_mulai" placeholder="09:00">
                </div>

                <div class="adm-form-group">
                    <label>Jam Selesai</label>
                    <input type="text" name="jam_selesai" placeholder="17:00">
                </div>
            </div>

            <div class="adm-form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" placeholder="Keterangan tambahan..." rows="3" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px"></textarea>
            </div>

            <div class="adm-form-group">
                <label>Status</label>
                <select name="status" class="adm-search-input" style="padding-left:14px">
                    <option value="pending">Menunggu Pembayaran/Verifikasi</option>
                    <option value="terjadwal">Disetujui</option>
                    <option value="selesai">Selesai</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>

            <div class="adm-form-actions">
                <button type="button" class="adm-btn-cancel" onclick="closeModal('modalTambah')">Batal</button>
                <button type="submit" class="adm-btn-save">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="adm-modal-overlay" id="modalEdit">
    <div class="adm-modal">
        <div class="adm-modal-header">
            <h3 class="adm-modal-title">Edit Reservasi</h3>
            <button class="adm-modal-close" onclick="closeModal('modalEdit')">&times;</button>
        </div>

        <form method="POST" action="index.php?page=admin_reservasi">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">

            <div class="adm-form-group">
                <label>Pilih User (Pengunjung)</label>
                <select name="user_id" id="edit_user_id" class="adm-search-input" style="padding-left:14px">
                    <option value="">— Tanpa User (reservasi umum) —</option>
                    <?php foreach (($user_list ?? []) as $u): ?>
                        <option value="<?= (int) $u['id'] ?>">
                            <?= htmlspecialchars($u['nama']) ?> (<?= htmlspecialchars($u['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="adm-form-group">
                <label>Nama Kegiatan</label>
                <input type="text" name="nama_kegiatan" id="edit_nama_kegiatan" required>
            </div>

            <div class="adm-form-group">
                <label>Jenis Kegiatan</label>
                <input type="text" name="jenis_kegiatan" id="edit_jenis_kegiatan">
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" id="edit_tanggal" required>
                </div>

                <div class="adm-form-group">
                    <label>Jumlah Peserta</label>
                    <input type="number" name="jumlah_peserta" id="edit_jumlah_peserta" min="1" required>
                </div>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>Jam Mulai</label>
                    <input type="text" name="jam_mulai" id="edit_jam_mulai" placeholder="09:00">
                </div>

                <div class="adm-form-group">
                    <label>Jam Selesai</label>
                    <input type="text" name="jam_selesai" id="edit_jam_selesai" placeholder="17:00">
                </div>
            </div>

            <div class="adm-form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" id="edit_keterangan" rows="3" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px"></textarea>
            </div>

            <div class="adm-form-group">
                <label>Status</label>
                <select name="status" id="edit_status" class="adm-search-input" style="padding-left:14px">
                    <option value="pending">Menunggu Pembayaran/Verifikasi</option>
                    <option value="terjadwal">Disetujui</option>
                    <option value="selesai">Selesai</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>

            <div class="adm-form-actions">
                <button type="button" class="adm-btn-cancel" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="adm-btn-save">Update</button>
            </div>
        </form>
    </div>
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
function openModal(id) {
    const modal = document.getElementById(id);

    if (modal) {
        modal.classList.add('show');
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);

    if (modal) {
        modal.classList.remove('show');
    }
}

function openEditModal(r) {
    document.getElementById('edit_id').value = r.id || '';
    document.getElementById('edit_user_id').value = r.user_id || '';
    document.getElementById('edit_nama_kegiatan').value = r.nama_kegiatan || '';
    document.getElementById('edit_jenis_kegiatan').value = r.jenis_kegiatan || '';
    document.getElementById('edit_tanggal').value = r.tanggal || '';
    document.getElementById('edit_jumlah_peserta').value = r.jumlah_peserta || '';
    document.getElementById('edit_jam_mulai').value = r.jam_mulai || '';
    document.getElementById('edit_jam_selesai').value = r.jam_selesai || '';
    document.getElementById('edit_keterangan').value = r.keterangan || '';
    document.getElementById('edit_status').value = r.status || 'pending';

    openModal('modalEdit');
}

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

<?php if (isset($_GET['modal']) && $_GET['modal'] === 'tambah'): ?>
openModal('modalTambah');
<?php endif; ?>
</script>

<script src="assets/js/enhance.js"></script>
</body>
</html>