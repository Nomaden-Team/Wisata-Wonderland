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
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    <title>Dasbor Admin - Wonderland</title>
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
                <h1 class="adm-page-title">Dasbor</h1>
                <p class="adm-page-sub">Ringkasan data dan aktivitas terbaru Wonderland Samarinda.</p>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="adm-stats-grid">
            <div class="adm-stat-card">
                <div class="adm-stat-top">
                    <div class="adm-stat-icon" style="background:#DC2626">
                        <i class="far fa-calendar-alt"></i>
                    </div>
                    <span class="adm-stat-badge">Semua Data</span>
                </div>
                <p class="adm-stat-label">Total Reservasi</p>
                <p class="adm-stat-value"><?= number_format((int) ($total_reservasi ?? 0), 0, ',', '.') ?></p>
            </div>

            <div class="adm-stat-card">
                <div class="adm-stat-top">
                    <div class="adm-stat-icon" style="background:#F59E0B">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <span class="adm-stat-badge"><?= ((int) ($reservasi_pending ?? 0)) > 0 ? 'Perlu Dicek' : 'Aman' ?></span>
                </div>
                <p class="adm-stat-label">Reservasi Menunggu</p>
                <p class="adm-stat-value"><?= number_format((int) ($reservasi_pending ?? 0), 0, ',', '.') ?></p>
            </div>

            <div class="adm-stat-card">
                <div class="adm-stat-top">
                    <div class="adm-stat-icon" style="background:#10B981">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <span class="adm-stat-badge">
                        <?= number_format((int) ($wahana_aktif ?? 0), 0, ',', '.') ?>/<?= number_format((int) ($wahana_total ?? 0), 0, ',', '.') ?>
                    </span>
                </div>
                <p class="adm-stat-label">Wahana Aktif</p>
                <p class="adm-stat-value"><?= number_format((int) ($wahana_aktif ?? 0), 0, ',', '.') ?></p>
            </div>

            <div class="adm-stat-card">
                <div class="adm-stat-top">
                    <div class="adm-stat-icon" style="background:#8B5CF6">
                        <i class="far fa-star"></i>
                    </div>
                    <span class="adm-stat-badge"><?= ((int) ($ulasan_pending ?? 0)) > 0 ? 'Moderasi' : 'Aman' ?></span>
                </div>
                <p class="adm-stat-label">Ulasan Menunggu</p>
                <p class="adm-stat-value"><?= number_format((int) ($ulasan_pending ?? 0), 0, ',', '.') ?></p>
            </div>
        </div>

        <div class="adm-dash-grid">
            <div class="adm-card">
                <div class="adm-card-header">
                    <h3 class="adm-card-title">Reservasi Terbaru</h3>
                    <i class="fas fa-chart-line adm-card-icon-red"></i>
                </div>

                <?php if (empty($res_terbaru)): ?>
                    <p class="adm-empty">Belum ada reservasi.</p>
                <?php else: ?>
                    <?php foreach ($res_terbaru as $r): ?>
                        <?php
                            $status = $r['status'] ?? 'pending';

                            $statusLabel = [
                                'pending' => 'Menunggu',
                                'terjadwal' => 'Disetujui',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ];

                            $statusClass = [
                                'pending' => 'pending',
                                'terjadwal' => 'confirmed',
                                'selesai' => 'completed',
                                'dibatalkan' => 'cancelled',
                            ];

                            $tanggal = !empty($r['tanggal'])
                                ? date('d M Y', strtotime($r['tanggal']))
                                : '-';
                        ?>

                        <div class="adm-reservasi-item">
                            <div class="adm-res-info">
                                <p class="adm-res-name"><?= htmlspecialchars($r['nama_kegiatan'] ?? '-') ?></p>
                                <p class="adm-res-sub">
                                    <?= htmlspecialchars($r['jenis_kegiatan'] ?? '-') ?> &bull; <?= htmlspecialchars($tanggal) ?>
                                </p>
                            </div>

                            <span class="adm-badge adm-badge-<?= htmlspecialchars($statusClass[$status] ?? 'pending') ?>">
                                <?= htmlspecialchars($statusLabel[$status] ?? $status) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="adm-card">
                <div class="adm-card-header">
                    <h3 class="adm-card-title">Perlu Ditindaklanjuti</h3>
                    <i class="fas fa-list-check adm-card-icon-green"></i>
                </div>

                <?php if (empty($tindak_lanjut)): ?>
                    <p class="adm-empty">Tidak ada item yang perlu ditindaklanjuti.</p>
                <?php else: ?>
                    <?php foreach ($tindak_lanjut as $i => $item): ?>
                        <a href="<?= htmlspecialchars($item['url']) ?>" class="adm-wahana-item" style="text-decoration:none;color:inherit;">
                            <div class="adm-wahana-rank">
                                <i class="<?= htmlspecialchars($item['icon']) ?>" style="font-size:.72rem;"></i>
                            </div>

                            <div class="adm-wahana-info">
                                <p class="adm-wahana-name"><?= htmlspecialchars($item['judul']) ?></p>
                                <p class="adm-wahana-pengunjung">
                                    <?= htmlspecialchars($item['jenis']) ?> &bull; <?= htmlspecialchars($item['meta']) ?>
                                </p>
                            </div>

                            <span class="adm-wahana-revenue">Cek</span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="adm-quick-actions">
            <p class="adm-qa-title">Aksi Cepat</p>

            <div class="adm-qa-grid">
                <a href="index.php?page=admin_reservasi" class="adm-qa-item">
                    <i class="far fa-calendar-alt"></i>
                    <span>Kelola Reservasi</span>
                </a>

                <a href="index.php?page=admin_wahana&modal=tambah" class="adm-qa-item">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Tambah Wahana</span>
                </a>

                <a href="index.php?page=admin_pricelist" class="adm-qa-item">
                    <i class="fas fa-tags"></i>
                    <span>Kelola Harga</span>
                </a>
                </a>
            </div>
        </div>
    </main>
</div>

<button class="adm-help-btn">?</button>
<script src="assets/js/enhance.js"></script>
</body>
</html>