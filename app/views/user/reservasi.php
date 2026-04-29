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
    <title>Reservasi Saya - Wonderland Samarinda</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/user-dashboard.css">
    <link rel="stylesheet" href="assets/css/enhance.css">
</head>
<body class="ud-body">

<?php
$activePage = 'reservasi';
require __DIR__ . '/partials/sidebar.php';

$jadwalTerjadwal = $jadwalTerjadwal ?? [];
$reservasis = $reservasis ?? [];

$nomorWaAdmin = '+62 812-8346-0325'; 
$namaUserLogin = $_SESSION['nama'] ?? ($nama ?? 'Pengunjung');
$emailUserLogin = $_SESSION['email'] ?? ($email ?? '');

$statusLabel = [
    'pending'    => 'Menunggu Pembayaran / Verifikasi',
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

$borderMap = [
    'pending'    => '#f5a623',
    'terjadwal'  => '#26c6a6',
    'selesai'    => '#3b82f6',
    'dibatalkan' => '#ff6b6b',
];

function wl_format_tanggal(?string $tanggal): string
{
    if (empty($tanggal)) {
        return '-';
    }

    return date('d F Y', strtotime($tanggal));
}

function wl_js_string($value): string
{
    return htmlspecialchars(addslashes((string) $value), ENT_QUOTES, 'UTF-8');
}

function wl_link_wa_reservasi(array $r, string $namaUser, string $emailUser, string $nomorWaAdmin): string
{
    $kode = $r['kode_booking'] ?? ('RES-' . str_pad((string)($r['id'] ?? 0), 3, '0', STR_PAD_LEFT));
    $tanggal = !empty($r['tanggal']) ? date('d/m/Y', strtotime($r['tanggal'])) : '-';

    $pesan =
        "Halo Admin Wonderland Samarinda,\n" .
        "Saya ingin konfirmasi pembayaran reservasi.\n\n" .
        "Kode Reservasi: {$kode}\n" .
        "Nama: {$namaUser}\n" .
        "Email: {$emailUser}\n" .
        "Kegiatan: " . ($r['nama_kegiatan'] ?? '-') . "\n" .
        "Tanggal: {$tanggal}\n" .
        "Jumlah Peserta: " . ($r['jumlah_peserta'] ?? '-') . " orang\n\n" .
        "Saya akan mengirim bukti transfer melalui WhatsApp ini.";

    return 'https://wa.me/' . preg_replace('/[^0-9]/', '', $nomorWaAdmin) . '?text=' . urlencode($pesan);
}

$totalDisetujui = count(array_filter($reservasis, fn($r) => ($r['status'] ?? '') === 'terjadwal'));
$totalMenunggu = count(array_filter($reservasis, fn($r) => ($r['status'] ?? '') === 'pending'));
$totalSelesai = count(array_filter($reservasis, fn($r) => ($r['status'] ?? '') === 'selesai'));
$totalAll = count($reservasis);
?>

<main class="ud-main">
    <div class="ud-topbar">
        <button type="button" class="ud-mobile-toggle" aria-label="Buka menu">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        <div class="ud-topbar-left">
            <div class="ud-page-icon" style="background: linear-gradient(135deg, #ff6b6b, #f5a623);">
                <svg width="22" height="22" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>

            <div>
                <h1 class="ud-page-title">Reservasi</h1>
                <p class="ud-page-sub">Lihat jadwal yang sudah terjadwal dan kelola reservasi Anda</p>
            </div>
        </div>

        <button type="button" class="wl-theme-toggle wl-theme-toggle--user" aria-label="Aktifkan mode gelap" aria-pressed="false">
            <span class="wl-theme-toggle-icon" aria-hidden="true">☾</span>
            <span class="wl-theme-toggle-label">Dark</span>
        </button>

        <button class="ud-btn-red" onclick="openReservasiModal()">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tambah Reservasi
        </button>

        <div class="ud-topbar-line"></div>
    </div>

    <?php if ((($_GET['status'] ?? '') === 'success') && !empty($_GET['kode'])): ?>
        <div class="ud-alert ud-alert-success">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="20,6 9,17 4,12"/>
            </svg>
            Reservasi berhasil dibuat. Silakan lakukan pembayaran lalu kirim bukti transfer ke admin.
            Kode reservasi Anda: <strong><?= htmlspecialchars($_GET['kode']) ?></strong>.
        </div>
    <?php elseif (($_GET['status'] ?? '') === 'error'): ?>
        <div class="ud-alert ud-alert-error">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <?= htmlspecialchars($_GET['msg'] ?? 'Reservasi gagal diproses.') ?>
        </div>
    <?php endif; ?>

    <div class="ud-stats-row ud-stats-4">
        <div class="ud-stat-card ud-stat-border-green">
            <div class="ud-stat-info">
                <div class="ud-stat-label">Disetujui</div>
                <div class="ud-stat-num"><?= $totalDisetujui ?></div>
            </div>
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7); color:#059669;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="9,11 12,14 22,4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
        </div>

        <div class="ud-stat-card ud-stat-border-yellow">
            <div class="ud-stat-info">
                <div class="ud-stat-label">Menunggu</div>
                <div class="ud-stat-num"><?= $totalMenunggu ?></div>
            </div>
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,#fffbf0,#fef0cc); color:#d4881a;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                </svg>
            </div>
        </div>

        <div class="ud-stat-card ud-stat-border-blue">
            <div class="ud-stat-info">
                <div class="ud-stat-label">Selesai</div>
                <div class="ud-stat-num"><?= $totalSelesai ?></div>
            </div>
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,#eff6ff,#dbeafe); color:#2563eb;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                    <polyline points="22,4 12,14.01 9,11.01"/>
                </svg>
            </div>
        </div>

        <div class="ud-stat-card ud-stat-border-red">
            <div class="ud-stat-info">
                <div class="ud-stat-label">Total Saya</div>
                <div class="ud-stat-num"><?= $totalAll ?></div>
            </div>
            <div class="ud-stat-icon" style="background:linear-gradient(135deg,#fff0f0,#ffd6d6); color:#e84545;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
        </div>
    </div>

    <section style="margin-top:24px;">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:14px;margin-bottom:12px;flex-wrap:wrap;">
            <div>
                <h2 style="font-size:1.1rem;margin:0;color:#1f2937;">Reservasi Terjadwal</h2>
                <p style="margin:4px 0 0;color:#6b7280;font-size:.86rem;">
                    Jadwal yang sudah disetujui admin. Gunakan ini sebagai patokan sebelum membuat reservasi baru.
                </p>
            </div>
            <span class="ud-badge-status ud-badge-confirmed"><?= count($jadwalTerjadwal) ?> Terjadwal</span>
        </div>

        <?php if (!empty($jadwalTerjadwal)): ?>
            <?php foreach ($jadwalTerjadwal as $j): ?>
                <div class="ud-card ud-reservasi-card" style="border-left:4px solid #26c6a6;">
                    <div class="ud-reservasi-card-top">
                        <div>
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                <span class="ud-reservasi-name"><?= htmlspecialchars($j['nama_kegiatan'] ?? '-') ?></span>
                                <span class="ud-badge-status ud-badge-confirmed">Terjadwal</span>
                            </div>

                            <?php if (!empty($j['jenis_kegiatan'])): ?>
                                <p style="margin:6px 0 0;color:#6b7280;font-size:.84rem;">
                                    <?= htmlspecialchars($j['jenis_kegiatan']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="ud-reservasi-meta ud-reservasi-meta-detail">
                        <span class="ud-meta-item">
                            <svg width="14" height="14" fill="none" stroke="#e84545" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <div>
                                <div class="ud-meta-label">Tanggal</div>
                                <strong><?= htmlspecialchars(wl_format_tanggal($j['tanggal'] ?? null)) ?></strong>
                            </div>
                        </span>

                        <?php if (!empty($j['jam_mulai'])): ?>
                            <span class="ud-meta-item">
                                <svg width="14" height="14" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12,6 12,12 16,14"/>
                                </svg>
                                <div>
                                    <div class="ud-meta-label">Waktu</div>
                                    <strong>
                                        <?= htmlspecialchars(date('H:i', strtotime($j['jam_mulai']))) ?>
                                        <?php if (!empty($j['jam_selesai'])): ?>
                                            - <?= htmlspecialchars(date('H:i', strtotime($j['jam_selesai']))) ?>
                                        <?php endif; ?>
                                    </strong>
                                </div>
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($j['jumlah_peserta'])): ?>
                            <span class="ud-meta-item">
                                <svg width="14" height="14" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                </svg>
                                <div>
                                    <div class="ud-meta-label">Jumlah Peserta</div>
                                    <strong><?= (int) $j['jumlah_peserta'] ?> Orang</strong>
                                </div>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($j['keterangan'])): ?>
                        <p style="margin:14px 0 0;color:#6b7280;font-size:.86rem;line-height:1.6;">
                            <?= nl2br(htmlspecialchars($j['keterangan'])) ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="ud-card">
                <div class="ud-empty-state">
                    <h3 class="ud-empty-title">Belum Ada Jadwal Terjadwal</h3>
                    <p class="ud-empty-desc">Saat ini belum ada reservasi yang sudah disetujui admin.</p>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <section style="margin-top:28px;">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:14px;margin-bottom:12px;flex-wrap:wrap;">
            <div>
                <h2 style="font-size:1.1rem;margin:0;color:#1f2937;">Reservasi Saya</h2>
                <p style="margin:4px 0 0;color:#6b7280;font-size:.86rem;">
                    Semua reservasi milik Anda tetap tampil di sini, termasuk yang menunggu, disetujui, selesai, dan dibatalkan.
                </p>
            </div>
        </div>

        <?php if (!empty($reservasis)): ?>
            <?php foreach ($reservasis as $r): ?>
                <?php
                $st = $r['status'] ?? 'pending';
                $kodeBooking = $r['kode_booking'] ?? ('RES-' . str_pad((string)($r['id'] ?? 0), 3, '0', STR_PAD_LEFT));
                ?>

                <div class="ud-card ud-reservasi-card" style="border-left:4px solid <?= htmlspecialchars($borderMap[$st] ?? '#e5e7eb') ?>;">
                    <div class="ud-reservasi-card-top">
                        <div>
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                <span class="ud-reservasi-name"><?= htmlspecialchars($r['nama_kegiatan'] ?? '-') ?></span>
                                <span class="ud-badge-status ud-badge-<?= htmlspecialchars($statusClass[$st] ?? 'pending') ?>">
                                    <?= htmlspecialchars($statusLabel[$st] ?? $st) ?>
                                </span>
                            </div>

                            <p style="margin:6px 0 0;color:#6b7280;font-size:.84rem;">
                                Kode Reservasi:
                                <strong><?= htmlspecialchars($kodeBooking) ?></strong>
                            </p>
                        </div>
                    </div>

                    <div class="ud-reservasi-meta ud-reservasi-meta-detail">
                        <span class="ud-meta-item">
                            <svg width="14" height="14" fill="none" stroke="#e84545" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <div>
                                <div class="ud-meta-label">Tanggal Kunjungan</div>
                                <strong><?= htmlspecialchars(wl_format_tanggal($r['tanggal'] ?? null)) ?></strong>
                            </div>
                        </span>

                        <?php if (!empty($r['jam_mulai'])): ?>
                            <span class="ud-meta-item">
                                <svg width="14" height="14" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12,6 12,12 16,14"/>
                                </svg>
                                <div>
                                    <div class="ud-meta-label">Waktu</div>
                                    <strong><?= htmlspecialchars(date('H:i', strtotime($r['jam_mulai']))) ?></strong>
                                </div>
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($r['jumlah_peserta'])): ?>
                            <span class="ud-meta-item">
                                <svg width="14" height="14" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                </svg>
                                <div>
                                    <div class="ud-meta-label">Jumlah Orang</div>
                                    <strong><?= (int) $r['jumlah_peserta'] ?> Orang</strong>
                                </div>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($r['keterangan'])): ?>
                        <p style="margin:14px 0 0;color:#6b7280;font-size:.86rem;line-height:1.6;">
                            <?= nl2br(htmlspecialchars($r['keterangan'])) ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($st === 'pending'): ?>
                        <div class="ud-alert" style="margin-top:14px;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;">
                            Reservasi masih menunggu pembayaran/verifikasi. Kirim bukti transfer ke admin melalui WhatsApp.
                        </div>

                        <div class="ud-reservasi-actions">
                            <a
                                class="ud-btn-red-full"
                                href="<?= htmlspecialchars(wl_link_wa_reservasi($r, $namaUserLogin, $emailUserLogin, $nomorWaAdmin)) ?>"
                                target="_blank"
                                rel="noopener"
                                style="text-decoration:none;text-align:center;justify-content:center;"
                            >
                                Hubungi Admin via WhatsApp
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($st === 'terjadwal'): ?>
                        <div class="ud-reservasi-actions">
                            <button class="ud-btn-red-full" onclick="openETicket(
                                '<?= wl_js_string($kodeBooking) ?>',
                                '<?= wl_js_string($r['nama_kegiatan'] ?? '-') ?>',
                                '<?= wl_js_string(wl_format_tanggal($r['tanggal'] ?? null)) ?>',
                                '<?= (int) ($r['jumlah_peserta'] ?? 1) ?>',
                                '<?= wl_js_string($namaUserLogin) ?>',
                                '<?= wl_js_string($emailUserLogin) ?>'
                            )">
                                Lihat E-Ticket
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="ud-card">
                <div class="ud-empty-state ud-empty-large">
                    <div class="ud-empty-icon-gradient" style="background:linear-gradient(135deg,#ff6b6b,#f5a623);">
                        <svg width="36" height="36" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>

                    <h3 class="ud-empty-title">Belum Ada Reservasi</h3>
                    <p class="ud-empty-desc">Tambahkan reservasi baru untuk kunjungan Anda.</p>

                    <button class="ud-btn-red" onclick="openReservasiModal()">
                        Tambah Reservasi
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>

<div class="ud-modal-overlay" id="reservasiModal" onclick="closeReservasiModal(event)">
    <div class="ud-modal" style="max-width:460px;width:94%;">
        <div class="ud-modal-icon" style="background:linear-gradient(135deg,#ff6b6b,#f5a623);">
            <svg width="32" height="32" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        </div>

        <h2 class="ud-modal-title">Buat Reservasi</h2>
        <p class="ud-modal-desc">
            Isi data kunjungan Anda. Setelah reservasi dibuat, hubungi admin via WhatsApp dan kirim bukti transfer.
            E-ticket aktif setelah admin menyetujui reservasi.
        </p>

        <form action="index.php?page=user_submit_reservasi" method="POST" style="text-align:left;">
            <div style="margin-bottom:14px;">
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Nama Kegiatan / Acara</label>
                <input
                    type="text"
                    name="nama_kegiatan"
                    required
                    placeholder="cth: Kunjungan Keluarga"
                    style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-family:Poppins,sans-serif;font-size:.85rem;outline:none;"
                >
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Tanggal Kunjungan</label>
                <input
                    type="date"
                    name="tanggal"
                    required
                    min="<?= date('Y-m-d') ?>"
                    style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-family:Poppins,sans-serif;font-size:.85rem;outline:none;"
                >
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Jumlah Peserta</label>
                <input
                    type="number"
                    name="jumlah_peserta"
                    required
                    min="1"
                    max="500"
                    value="2"
                    style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-family:Poppins,sans-serif;font-size:.85rem;outline:none;"
                >
            </div>

            <div style="margin-bottom:20px;">
                <label style="font-size:.8rem;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Keterangan (opsional)</label>
                <textarea
                    name="keterangan"
                    rows="3"
                    placeholder="Informasi tambahan..."
                    style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-family:Poppins,sans-serif;font-size:.85rem;outline:none;resize:vertical;"
                ></textarea>
            </div>

            <div class="eticket-actions" style="display:flex;gap:10px;">
                <button type="button" onclick="closeReservasiModal()" class="ud-btn-outline-sm" style="flex:1;">Batal</button>
                <button type="submit" class="ud-btn-red" style="flex:1;justify-content:center;">Kirim Reservasi</button>
            </div>
        </form>
    </div>
</div>

<div class="ud-modal-overlay" id="eticketModal" onclick="closeETicket(event)">
    <div class="ud-modal" id="eticketBox" style="max-width:480px;width:94%;padding:0;overflow:hidden;border-radius:20px;">
        <div style="background:linear-gradient(135deg,#ff6b6b,#e84545,#f5a623);padding:24px 28px 20px;position:relative;overflow:hidden;">
            <div style="font-size:.7rem;color:rgba(255,255,255,.7);letter-spacing:1px;text-transform:uppercase;font-weight:600;">
                Wonderland Samarinda
            </div>

            <div style="font-size:1.15rem;font-weight:800;color:white;line-height:1.2;">
                E-Ticket Reservasi
            </div>

            <div style="margin-top:14px;">
                <span style="background:rgba(255,255,255,.2);color:white;font-size:.7rem;font-weight:700;padding:4px 12px;border-radius:20px;border:1px solid rgba(255,255,255,.3);">
                    DISETUJUI
                </span>
            </div>
        </div>

        <div style="background:var(--bg, #faf9f7);padding:20px 28px 24px;">
            <div style="text-align:center;margin-bottom:18px;">
                <div style="font-size:1.1rem;font-weight:800;color:#1f2937;" id="et-nama"></div>
                <div style="font-size:.75rem;color:#6b7280;margin-top:3px;">Kunjungan ke Wonderland Samarinda</div>
            </div>

            <div style="display:flex;justify-content:center;margin-bottom:18px;">
                <div style="background:white;border-radius:14px;padding:14px;box-shadow:0 2px 12px rgba(0,0,0,.08);text-align:center;">
                    <div id="et-qrcode" style="width:130px;height:130px;margin:0 auto;"></div>
                    <div id="et-kode" style="font-size:.85rem;font-weight:800;color:#e84545;letter-spacing:2px;margin-top:8px;font-family:monospace;"></div>
                    <div style="font-size:.62rem;color:#9ca3af;margin-top:2px;">Kode Booking</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:18px;">
                <div style="background:white;border-radius:10px;padding:12px;">
                    <div style="font-size:.62rem;color:#9ca3af;text-transform:uppercase;font-weight:600;">Nama Pemesan</div>
                    <div style="font-size:.82rem;font-weight:700;color:#1f2937;margin-top:3px;" id="et-user"></div>
                </div>

                <div style="background:white;border-radius:10px;padding:12px;">
                    <div style="font-size:.62rem;color:#9ca3af;text-transform:uppercase;font-weight:600;">Tanggal</div>
                    <div style="font-size:.82rem;font-weight:700;color:#1f2937;margin-top:3px;" id="et-tanggal"></div>
                </div>

                <div style="background:white;border-radius:10px;padding:12px;">
                    <div style="font-size:.62rem;color:#9ca3af;text-transform:uppercase;font-weight:600;">Jumlah Peserta</div>
                    <div style="font-size:.82rem;font-weight:700;color:#1f2937;margin-top:3px;" id="et-peserta"></div>
                </div>

                <div style="background:white;border-radius:10px;padding:12px;">
                    <div style="font-size:.62rem;color:#9ca3af;text-transform:uppercase;font-weight:600;">Lokasi</div>
                    <div style="font-size:.82rem;font-weight:700;color:#1f2937;margin-top:3px;">Wonderland SMD</div>
                </div>
            </div>

            <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:10px;padding:10px 14px;margin-bottom:18px;">
                <div style="font-size:.75rem;color:#92400e;line-height:1.5;">
                    Tunjukkan e-ticket ini kepada petugas saat tiba di lokasi.
                </div>
            </div>

            <div style="display:flex;gap:10px;">
                <div class="eticket-actions" style="display:flex;gap:10px;">
    <button type="button" onclick="printETicket()" class="ud-btn-red" style="flex:1;justify-content:center;">Cetak</button>
    <button type="button" onclick="closeETicket()" class="ud-btn-outline-sm" style="flex:1;">Tutup</button>
</div>
            </div>
        </div>
    </div>
</div>

<script>
var etState = {};

function openETicket(kode, nama, tanggal, peserta, namaUser, email) {
    etState = { kode, nama, tanggal, peserta, namaUser, email };

    document.getElementById('et-kode').textContent = kode;
    document.getElementById('et-nama').textContent = nama;
    document.getElementById('et-tanggal').textContent = tanggal;
    document.getElementById('et-peserta').textContent = peserta + ' Orang';
    document.getElementById('et-user').textContent = namaUser;

    var qrDiv = document.getElementById('et-qrcode');
    qrDiv.innerHTML =
        '<img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=' +
        encodeURIComponent('WONDERLAND-SMD|' + kode + '|' + nama + '|' + tanggal) +
        '" width="130" height="130" style="border-radius:6px;" alt="QR Code">';

    document.getElementById('eticketModal').classList.add('active');
}

function closeETicket(e) {
    if (!e || e.target === document.getElementById('eticketModal')) {
        document.getElementById('eticketModal').classList.remove('active');
    }
}
function printETicket() {
    const modal = document.getElementById('eticketModal');
    modal.classList.add('active');

    setTimeout(() => {
        window.print();
    }, 200);
}
function openReservasiModal() {
    document.getElementById('reservasiModal').classList.add('active');
}

function closeReservasiModal(e) {
    if (!e || e.target === document.getElementById('reservasiModal')) {
        document.getElementById('reservasiModal').classList.remove('active');
    }
}
</script>

<script src="assets/js/enhance.js"></script>
<script src="assets/js/user.js"></script>
</body>
</html>