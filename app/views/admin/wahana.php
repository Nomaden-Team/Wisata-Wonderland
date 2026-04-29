<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (function(){
            try {
                var t = localStorage.getItem('wl_theme') || 'light';
                document.documentElement.setAttribute('data-theme', t);
                document.documentElement.style.colorScheme = t;
            } catch(e) {
                document.documentElement.setAttribute('data-theme','light');
            }
        })();
    </script>

    <title>Kelola Wahana - Wonderland Admin</title>
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
                <h1 class="adm-page-title">Kelola Wahana</h1>
                <p class="adm-page-sub">Kelola semua wahana dan atraksi</p>
            </div>

            <button class="adm-btn adm-btn-primary" onclick="openModal('modalTambah')">
                <i class="fas fa-plus"></i> Tambah Wahana
            </button>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="adm-alert adm-alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="adm-alert adm-alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="adm-card" style="margin-bottom:20px;padding:16px 20px;">
            <form method="GET" action="index.php" class="adm-filter-bar" style="margin:0">
                <input type="hidden" name="page" value="admin_wahana">

                <div class="adm-search-wrap" style="max-width:100%">
                    <i class="fas fa-search"></i>
                    <input
                        type="text"
                        name="search"
                        class="adm-search-input"
                        placeholder="Cari wahana..."
                        value="<?= htmlspecialchars($search ?? '') ?>"
                    >
                </div>
            </form>
        </div>

        <div class="adm-wahana-grid">
            <?php if (empty($wahanas)): ?>
                <p class="adm-empty" style="grid-column:1/-1">Belum ada wahana</p>
            <?php else: ?>
                <?php foreach ($wahanas as $w): ?>
                    <?php
                        $fotoSrc = '';

                        if (!empty($w['foto'])) {
                            $uploadPath = 'uploads/wahana/' . $w['foto'];
                            $assetPath  = 'assets/wahana/' . $w['foto'];

                            $fotoSrc = file_exists($uploadPath) ? $uploadPath : $assetPath;
                        }

                        $jsonWahana = htmlspecialchars(
                            json_encode($w, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP),
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>

                    <div class="adm-wahana-card">
                        <div class="adm-wahana-thumb">
                            <?php if (!empty($w['foto'])): ?>
                                <img src="<?= htmlspecialchars($fotoSrc) ?>" alt="<?= htmlspecialchars($w['nama'] ?? 'Wahana') ?>">
                            <?php else: ?>
                                <i class="far fa-image"></i>
                            <?php endif; ?>
                        </div>

                        <div class="adm-wahana-body">
                            <div class="adm-wahana-card-header">
                                <h4 class="adm-wahana-card-name"><?= htmlspecialchars($w['nama'] ?? '-') ?></h4>
                                <span class="adm-badge adm-badge-<?= htmlspecialchars(strtolower($w['status'] ?? 'aktif')) ?>">
                                    <?= htmlspecialchars(ucfirst($w['status'] ?? 'aktif')) ?>
                                </span>
                            </div>

                            <p class="adm-wahana-card-desc"><?= htmlspecialchars($w['deskripsi'] ?? '-') ?></p>

                            <div class="adm-wahana-detail">
                                <span>Kategori:</span>
                                <span class="adm-wahana-detail-val"><?= htmlspecialchars($w['kategori'] ?? '-') ?></span>
                            </div>

                            <div class="adm-wahana-detail">
                                <span>Jam Operasional:</span>
                                <span class="adm-wahana-detail-val"><?= htmlspecialchars($w['jam_operasional'] ?? '-') ?></span>
                            </div>

                            <div class="adm-wahana-actions">
                                <button class="adm-btn adm-btn-edit" onclick='openEditModal(<?= $jsonWahana ?>)'>
                                    <i class="far fa-edit"></i> Edit
                                </button>

                                <button
                                    type="button"
                                    class="adm-btn adm-btn-delete"
                                    onclick="openDeleteModal(<?= (int) $w['id'] ?>, '<?= htmlspecialchars(addslashes($w['nama'] ?? 'Wahana')) ?>')"
                                >
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Modal Tambah -->
<div class="adm-modal-overlay" id="modalTambah">
    <div class="adm-modal">
        <div class="adm-modal-header">
            <h3 class="adm-modal-title">Tambah Wahana</h3>
            <button class="adm-modal-close" onclick="closeModal('modalTambah')">&times;</button>
        </div>

        <form method="POST" action="index.php?page=admin_wahana" enctype="multipart/form-data">
            <input type="hidden" name="action" value="tambah">
            <input type="hidden" name="harga" value="0">

            <div class="adm-form-group">
                <label>Nama Wahana</label>
                <input type="text" name="nama" required placeholder="Contoh: Thunder Coaster">
            </div>

            <div class="adm-form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" placeholder="Deskripsi wahana..."></textarea>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>Kategori</label>
                    <select name="kategori" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Wahana Air">Wahana Air</option>
                        <option value="Wahana Anak">Wahana Anak</option>
                        <option value="Wahana Keluarga">Wahana Keluarga</option>
                        <option value="Transportasi Wahana">Transportasi Wahana</option>
                        <option value="Area Wisata">Area Wisata</option>
                    </select>
                </div>

                <div class="adm-form-group">
                    <label>Status</label>
                    <select name="status" class="adm-search-input" style="padding-left:14px">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>Jam Buka</label>
                    <input type="time" name="jam_buka" required value="09:00">
                </div>

                <div class="adm-form-group">
                    <label>Jam Tutup</label>
                    <input type="time" name="jam_tutup" required value="18:00">
                </div>
            </div>

            <div class="adm-form-group">
                <label>Foto Wahana</label>
                <input type="file" name="foto" accept="image/*">
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
            <h3 class="adm-modal-title">Edit Wahana</h3>
            <button class="adm-modal-close" onclick="closeModal('modalEdit')">&times;</button>
        </div>

        <form method="POST" action="index.php?page=admin_wahana" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="foto_lama" id="edit_foto_lama">
            <input type="hidden" name="harga" id="edit_harga" value="0">

            <div class="adm-form-group">
                <label>Nama Wahana</label>
                <input type="text" name="nama" id="edit_nama" required>
            </div>

            <div class="adm-form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" id="edit_deskripsi"></textarea>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>Kategori</label>
                    <select name="kategori" id="edit_kategori" required>
                        <option value="Wahana Air">Wahana Air</option>
                        <option value="Wahana Anak">Wahana Anak</option>
                        <option value="Wahana Keluarga">Wahana Keluarga</option>
                        <option value="Transportasi Wahana">Transportasi Wahana</option>
                        <option value="Area Wisata">Area Wisata</option>
                    </select>
                </div>

                <div class="adm-form-group">
                    <label>Status</label>
                    <select name="status" id="edit_status" class="adm-search-input" style="padding-left:14px">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="adm-form-row">
                <div class="adm-form-group">
                    <label>Jam Buka</label>
                    <input type="time" name="jam_buka" id="edit_jam_buka" required>
                </div>

                <div class="adm-form-group">
                    <label>Jam Tutup</label>
                    <input type="time" name="jam_tutup" id="edit_jam_tutup" required>
                </div>
            </div>

            <div class="adm-form-group">
                <label>Ganti foto</label>
                <input type="file" name="foto" accept="image/*">
                <small style="color:#6b7280;font-size:12px;">Kosongkan jika foto tidak ingin diganti.</small>
            </div>

            <div class="adm-form-actions">
                <button type="button" class="adm-btn-cancel" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="adm-btn-save">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="adm-modal-overlay" id="modalDelete">
    <div class="adm-modal" style="max-width:420px">
        <div class="adm-modal-header">
            <h3 class="adm-modal-title">Hapus Wahana?</h3>
            <button class="adm-modal-close" onclick="closeModal('modalDelete')">&times;</button>
        </div>

        <div style="padding:20px 0">
            <p id="deleteText" style="font-size:.9rem;color:#6B7280;margin-bottom:20px;">
                Data wahana akan dihapus permanen.
            </p>

            <div class="adm-form-actions">
                <button type="button" class="adm-btn-cancel" onclick="closeModal('modalDelete')">Batal</button>
                <button
                    type="button"
                    class="adm-btn-save"
                    style="background:#DC2626;border-color:#DC2626"
                    onclick="submitDelete()"
                >
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" action="index.php?page=admin_wahana" style="display:none;">
    <input type="hidden" name="action" value="hapus">
    <input type="hidden" name="id" id="deleteId">
</form>

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

function openEditModal(d) {
    document.getElementById('edit_id').value = d.id || '';
    document.getElementById('edit_nama').value = d.nama || '';
    document.getElementById('edit_deskripsi').value = d.deskripsi || '';
    document.getElementById('edit_kategori').value = d.kategori || '';
    document.getElementById('edit_harga').value = '0';
    document.getElementById('edit_status').value = d.status || 'aktif';
    document.getElementById('edit_foto_lama').value = d.foto || '';

    if (d.jam_operasional) {
        const parts = d.jam_operasional.split(' - ');
        document.getElementById('edit_jam_buka').value = parts[0] || '09:00';
        document.getElementById('edit_jam_tutup').value = parts[1] || '18:00';
    } else {
        document.getElementById('edit_jam_buka').value = '09:00';
        document.getElementById('edit_jam_tutup').value = '18:00';
    }

    openModal('modalEdit');
}

function openDeleteModal(id, nama) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteText').textContent =
        'Yakin ingin menghapus wahana "' + nama + '"? Tindakan ini tidak bisa dibatalkan.';

    openModal('modalDelete');
}

function submitDelete() {
    document.getElementById('deleteForm').submit();
}

<?php if (isset($_GET['modal'])): ?>
openModal('modalTambah');
<?php endif; ?>
</script>

<script src="assets/js/enhance.js"></script>
</body>
</html>