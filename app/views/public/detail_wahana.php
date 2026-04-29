<!DOCTYPE html>
<html lang="id" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($wahana['nama']) ?> - Wonderland Samarinda</title>

    <script>
        (function(){
            var t = localStorage.getItem('wl_theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/enhance.css">
    <style>
        /* AOS failsafe: jika CDN gagal load, elemen tetap visible & clickable */
        [data-aos] {
            opacity: 1 !important;
            transform: none !important;
            transition: opacity 0.3s, transform 0.3s !important;
            pointer-events: auto !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- ══════════════════ NAVBAR ══════════════════ -->
<nav class="navbar navbar-expand-lg bg-white custom-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand fs-4 fw-bold" href="index.php">
            <span class="brand-dark">Wonderland</span> <span class="text-brand-gradient">Samarinda</span>
        </a>
        <div class="d-flex align-items-center gap-2 ms-auto me-2">

            <button id="darkModeToggle" class="dark-mode-toggle" title="Ganti mode gelap" aria-label="Ganti mode gelap">
                <i class="bi bi-moon-fill" id="darkModeIcon"></i>
            </button>
        </div>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-lg-3">
                <li class="nav-item"><a class="nav-link" href="index.php#home">Beranda</a></li>
                <li class="nav-item"><a class="nav-link d-flex align-items-center gap-1" href="index.php#find-us"><i class="bi bi-geo-alt"></i> Location</a></li>
                <li class="nav-item"><a class="nav-link active" href="index.php#attraction">Wahana</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#gallery">Galeri</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#contact">Kontak</a></li>
            </ul>
            <div class="d-flex align-items-center gap-3 ms-lg-4 mt-3 mt-lg-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn-user-nav dropdown-toggle d-flex align-items-center gap-2 fw-bold px-3"
                                type="button" data-bs-toggle="dropdown">
                            <div class="user-avatar-sm"><?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?></div>
                            <?= htmlspecialchars($_SESSION['nama']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                            <li><a class="dropdown-item py-2" href="<?= $_SESSION['role'] === 'admin' ? 'index.php?page=admin_dashboard' : 'index.php?page=user_dashboard' ?>">
                                <i class="bi bi-grid me-2 text-primary"></i> Dashboard
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="#" onclick="konfirmasiLogout(event)">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="index.php?page=login" class="btn-login-figma fw-bold px-4">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-3">
    <a href="javascript:history.back()" class="btn-back-detail">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<!-- ══════════════════ HERO ══════════════════ -->
<div class="detail-hero">
    <?php
$fotoWahana = $wahana['foto'] ?? '';
$fotoWahanaUrl = 'assets/wahana/wonderland.jpg';

if ($fotoWahana !== '') {
    $uploadPath = __DIR__ . '/../../../uploads/wahana/' . $fotoWahana;
    $assetPath = __DIR__ . '/../../../assets/wahana/' . $fotoWahana;

    if (file_exists($uploadPath)) {
        $fotoWahanaUrl = 'uploads/wahana/' . $fotoWahana;
    } elseif (file_exists($assetPath)) {
        $fotoWahanaUrl = 'assets/wahana/' . $fotoWahana;
    }
}
?>

<div class="detail-hero-bg" style="background-image: url('<?= htmlspecialchars($fotoWahanaUrl) ?>')"></div>
    <div class="container detail-hero-content">
        <div class="detail-breadcrumb">
            <a href="index.php">Beranda</a>
            <span>›</span>
            <a href="index.php#attraction">Wahana</a>
            <span>›</span>
            <?= htmlspecialchars($wahana['nama']) ?>
        </div>
        <div class="detail-hero-badge"><?= htmlspecialchars($wahana['kategori']) ?></div>
        <h1 class="detail-hero-title"><?= htmlspecialchars($wahana['nama']) ?></h1>
        <p class="detail-hero-desc"><?= htmlspecialchars(substr($wahana['deskripsi'], 0, 160)) ?>...</p>
    </div>
</div>

<!-- ══════════════════ MAIN CONTENT ══════════════════ -->
<section class="detail-content-section">
    <div class="container">
        <div class="row g-5 align-items-start">


            <div class="col-lg-8">
                <h2 class="detail-section-title">Tentang Wahana Ini</h2>
                <p class="detail-body-text"><?= nl2br(htmlspecialchars($wahana['deskripsi'])) ?></p>

                <?php
                $fitur_list = [];
                if (!empty($wahana['fitur'])) {
                    $fitur_list = array_filter(array_map('trim', preg_split('/[\n,]+/', $wahana['fitur'])));
                }
                ?>
                <?php if (!empty($fitur_list)): ?>
                <h3 class="detail-features-title">Fitur &amp; Keunggulan</h3>
                <div class="detail-feature-grid">
                    <?php foreach ($fitur_list as $f): ?>
                    <div class="detail-feature-item"><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($f) ?></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="detail-safety-box">
                    <i class="bi bi-shield-exclamation"></i>
                    <div>
                        <strong>Informasi Keamanan</strong>
                        <p><?= !empty($wahana['info_keamanan']) ? htmlspecialchars($wahana['info_keamanan']) : 'Pastikan mengikuti semua instruksi keselamatan dari petugas. Pengunjung dengan kondisi kesehatan tertentu harap berkonsultasi dengan petugas sebelum menaiki wahana ini.' ?></p>
                    </div>
                </div>


                <div id="ulasan-wahana" class="mt-5">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h3 class="detail-section-title mb-0">Ulasan Pengunjung</h3>
                        <?php if ($total_ulasan_wahana > 0): ?>
                        <div class="d-flex align-items-center gap-2 bg-warning bg-opacity-10 px-3 py-2 rounded-3">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span class="fw-bold"><?= number_format($avg_rating_wahana, 1) ?></span>
                            <span class="text-muted small">dari <?= $total_ulasan_wahana ?> ulasan</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($_GET['ulasan']) && $_GET['ulasan'] === 'limit'): ?>
                    <div class="alert alert-warning rounded-3 d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Anda sudah mengirim 3 ulasan hari ini. Coba lagi besok.
                    </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['ulasan']) && $_GET['ulasan'] === 'kasar'): ?>
                    <div class="alert alert-danger rounded-3 d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-x-circle-fill"></i>
                        Ulasan mengandung kata tidak sopan dan tidak dapat dikirim.
                    </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['ulasan']) && $_GET['ulasan'] === 'ok'): ?>
                    <div class="alert alert-success rounded-3 d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-check-circle-fill"></i>
                        Ulasan kamu berhasil dikirim! Menunggu persetujuan admin.
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($ulasan_wahana)): ?>
<?php
    $ulasan_utama = array_slice($ulasan_wahana, 0, 3);
    $ulasan_lainnya = array_slice($ulasan_wahana, 3);
?>

<div class="row g-3 mb-4">
    <?php foreach ($ulasan_utama as $idx => $u):


                            $ulasanText = $u['ulasan'];
                            $foreignWords = ['the','is','are','was','were','this','that','great','good','amazing','fun','nice','love','enjoy','very','really','so','and','but','for','with','very','it','we','I','my','our','they','had','has','have','been','not','what','how','can','ride','place','visit','came','went','feel','felt','felt','staff','crowd','queue','ticket','park','theme','family','kids','children','experience','recommend','definitely','worth','time','day','hour','minute','second','wait','line','attraction','enjoyed','fantastic','wonderful','awesome','excellent','super','best','worst','bad','ok','okay'];
                            $words = preg_split('/\s+/', strtolower($ulasanText));
                            $matchCount = 0;
                            foreach ($words as $w) {
                                $clean = preg_replace('/[^a-z]/', '', $w);
                                if (in_array($clean, $foreignWords)) $matchCount++;
                            }
                            $isForeign = ($matchCount >= 2);
                        ?>
                        <div class="col-12">
                            <div class="ulasan-detail-card">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="ulasan-avatar"><?= strtoupper(substr($u['nama_pengunjung'] ?? $u['nama_user'] ?? 'Pengunjung', 0, 1)) ?></div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="fw-bold"><?= htmlspecialchars($u['nama_pengunjung'] ?? $u['nama_user'] ?? 'Pengunjung') ?></div>
                                                    <?php if ($isForeign): ?>
                                                    <span class="ulasan-lang-badge"><i class="bi bi-globe2"></i> Wisatawan</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ulasan-stars mt-1">
                                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                                        <i class="bi bi-star-fill <?= $s <= $u['rating'] ? 'text-warning' : 'text-muted opacity-25' ?>" style="font-size:.8rem;"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column align-items-end gap-1">
                                                <span class="text-muted small"><?= date('d M Y', strtotime($u['created_at'])) ?></span>
                                                <span class="ulasan-verified"><i class="bi bi-patch-check-fill me-1"></i>Terverifikasi</span>
                                            </div>
                                        </div>
                                        <p class="mt-2 mb-1 text-muted ulasan-original-text" style="font-size:.9rem;" data-ulasan-id="<?= $idx ?>"><?= htmlspecialchars($u['ulasan']) ?></p>

                                        <button class="btn-translate-ulasan"
                                                data-ulasan-id="<?= $idx ?>"
                                                data-original="<?= htmlspecialchars($u['ulasan'], ENT_QUOTES) ?>"
                                                onclick="translateUlasan(this)"
                                                title="Terjemahkan ulasan ini ke Bahasa Indonesia">
                                            <i class="bi bi-translate"></i>
                                            <span class="btn-translate-label">Terjemahkan</span>
                                        </button>

                                        <div class="ulasan-translated-box" id="translated-<?= $idx ?>">
                                            <div class="ulasan-translated-label">
                                                <i class="bi bi-translate"></i> Terjemahan Bahasa Indonesia
                                                <span class="ulasan-lang-badge" id="translated-lang-<?= $idx ?>"></span>
                                            </div>
                                            <div class="ulasan-translated-text" id="translated-text-<?= $idx ?>"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <?php endforeach; ?>
</div>

<?php if (!empty($ulasan_lainnya)): ?>
    <div class="collapse" id="ulasanLainnya">
        <div class="row g-3 mb-4">
            <?php foreach ($ulasan_lainnya as $idxLain => $u): ?>
                <?php
                    $idx = $idxLain + 3;

                    $ulasanText = $u['ulasan'];
                    $foreignWords = ['the','is','are','was','were','this','that','great','good','amazing','fun','nice','love','enjoy','very','really','so','and','but','for','with','very','it','we','I','my','our','they','had','has','have','been','not','what','how','can','ride','place','visit','came','went','feel','felt','felt','staff','crowd','queue','ticket','park','theme','family','kids','children','experience','recommend','definitely','worth','time','day','hour','minute','second','wait','line','attraction','enjoyed','fantastic','wonderful','awesome','excellent','super','best','worst','bad','ok','okay'];
                    $words = preg_split('/\s+/', strtolower($ulasanText));
                    $matchCount = 0;

                    foreach ($words as $w) {
                        $clean = preg_replace('/[^a-z]/', '', $w);
                        if (in_array($clean, $foreignWords)) {
                            $matchCount++;
                        }
                    }

                    $isForeign = ($matchCount >= 2);
                ?>

                <div class="col-12">
                    <div class="ulasan-detail-card">
                        <div class="d-flex align-items-start gap-3">
                            <div class="ulasan-avatar"><?= strtoupper(substr($u['nama_pengunjung'] ?? $u['nama_user'] ?? 'Pengunjung', 0, 1)) ?></div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="fw-bold"><?= htmlspecialchars($u['nama_pengunjung'] ?? $u['nama_user'] ?? 'Pengunjung') ?></div>
                                            <?php if ($isForeign): ?>
                                                <span class="ulasan-lang-badge"><i class="bi bi-globe2"></i> Wisatawan</span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="ulasan-stars mt-1">
                                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                                <i class="bi bi-star-fill <?= $s <= $u['rating'] ? 'text-warning' : 'text-muted opacity-25' ?>" style="font-size:.8rem;"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span class="text-muted small"><?= date('d M Y', strtotime($u['created_at'])) ?></span>
                                        <span class="ulasan-verified"><i class="bi bi-patch-check-fill me-1"></i>Terverifikasi</span>
                                    </div>
                                </div>

                                <p class="mt-2 mb-1 text-muted ulasan-original-text" style="font-size:.9rem;" data-ulasan-id="<?= $idx ?>">
                                    <?= htmlspecialchars($u['ulasan']) ?>
                                </p>

                                <button class="btn-translate-ulasan"
                                        data-ulasan-id="<?= $idx ?>"
                                        data-original="<?= htmlspecialchars($u['ulasan'], ENT_QUOTES) ?>"
                                        onclick="translateUlasan(this)"
                                        title="Terjemahkan ulasan ini ke Bahasa Indonesia">
                                    <i class="bi bi-translate"></i>
                                    <span class="btn-translate-label">Terjemahkan</span>
                                </button>

                                <div class="ulasan-translated-box" id="translated-<?= $idx ?>">
                                    <div class="ulasan-translated-label">
                                        <i class="bi bi-translate"></i> Terjemahan Bahasa Indonesia
                                        <span class="ulasan-lang-badge" id="translated-lang-<?= $idx ?>"></span>
                                    </div>
                                    <div class="ulasan-translated-text" id="translated-text-<?= $idx ?>"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="text-center mb-4">
        <button
            class="btn btn-outline-danger rounded-3 px-4 fw-bold"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#ulasanLainnya"
            aria-expanded="false"
            aria-controls="ulasanLainnya"
            id="btnToggleUlasan"
            onclick="toggleTextUlasan()"
        >
            Lihat Selengkapnya
        </button>
    </div>
<?php endif; ?>

<?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-chat-dots fs-3 d-block mb-2 opacity-50"></i>
                        Belum ada ulasan untuk wahana ini. Jadilah yang pertama!
                    </div>
                    <?php endif; ?>

                    <!-- Form tulis ulasan -->
                    <div id="tulis-ulasan" class="ulasan-form-card mt-4">
                        <?php if (!isset($_SESSION['user_id'])): ?>

                        <div class="ulasan-login-prompt">
                            <div class="d-flex align-items-start gap-3">
                                <div class="ulasan-prompt-icon">
                                    <i class="bi bi-chat-square-text-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-bold mb-1">Bagikan Pengalaman Anda</div>
                                    <div class="text-muted small mb-3">Silakan login terlebih dahulu untuk menulis ulasan wahana ini.</div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button type="button" onclick="mintaLogin()" class="btn btn-danger px-4 fw-bold rounded-3">Tulis Ulasan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>

                        <?php
                            $sisa_kuota  = 3 - $ulasan_hari_ini;
                            $limit_habis = ($ulasan_hari_ini >= 3);
                        ?>

                        <!-- Header form + badge kuota -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-pencil-square me-2 text-danger"></i>Tulis Ulasan Kamu
                            </h5>

                            <div class="ulasan-kuota-badge <?= $limit_habis ? 'kuota-habis' : ($ulasan_hari_ini >= 2 ? 'kuota-hampir' : 'kuota-aman') ?>">
                                <?php if ($limit_habis): ?>
                                    <i class="bi bi-slash-circle me-1"></i> Kuota hari ini habis
                                <?php else: ?>
                                    <i class="bi bi-journal-check me-1"></i>
                                    <?= $ulasan_hari_ini ?>/3 ulasan hari ini
                                    &nbsp;·&nbsp; sisa <strong><?= $sisa_kuota ?></strong>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($limit_habis): ?>
                        <!-- Kuota habis: tampilkan pesan informatif -->
                        <div class="ulasan-limit-box">
                            <div class="ulasan-limit-icon">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="ulasan-limit-body">
                                <div class="ulasan-limit-title">Batas Ulasan Hari Ini Tercapai</div>
                                <p class="ulasan-limit-desc">
                                    Kamu sudah mengirim <strong>3 ulasan</strong> hari ini.
                                    Batas ini diterapkan untuk menjaga kualitas ulasan.
                                    Kuota akan direset otomatis mulai <strong>besok</strong>. Sampai jumpa! 🌟
                                </p>
                                <div class="ulasan-limit-progress">
                                    <div class="ulasan-limit-bar"></div>
                                </div>
                                <small class="text-muted">Reset dalam: <span id="resetCountdown" class="fw-semibold text-danger"></span></small>
                            </div>
                        </div>

                        <?php else: ?>
                        <!-- Form normal -->
                        <form method="POST" action="index.php?page=detail_wahana&id=<?= $wahana['id'] ?>#tulis-ulasan">
                            <input type="hidden" name="submit_ulasan" value="1">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Nama Kamu</label>
                                <input type="text" name="nama_pengunjung" class="form-control rounded-3"
                                       placeholder="Masukkan nama kamu..."
                                       value="<?= htmlspecialchars($_SESSION['nama']) ?>"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Rating</label>
                                <div class="star-rating-input" id="starInput">
                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                        <i class="bi bi-star-fill star-inp" data-val="<?= $s ?>" style="font-size:1.8rem; cursor:pointer; color:#ddd;"></i>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="rating" id="ratingVal" value="5">
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold small">Ceritakan Pengalamanmu</label>
                                <textarea name="ulasan" class="form-control rounded-3" rows="4"
                                          placeholder="Bagikan pengalaman seru kamu di <?= htmlspecialchars($wahana['nama']) ?>..."
                                          required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger px-5 py-2 fw-bold rounded-3 w-100">
                                <i class="bi bi-send me-2"></i> Kirim Ulasan
                            </button>
                            <?php if ($sisa_kuota === 1): ?>
                            <p class="text-center text-warning small mt-2 mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>Ini adalah ulasan terakhir kamu hari ini!
                            </p>
                            <?php endif; ?>
                        </form>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- ═══ KOLOM KANAN: SIDEBAR ═══ -->
            <div class="col-lg-4">
                <div style="position:sticky; top:90px; z-index:10;">
                    
                    <div class="detail-info-card">
                        <h6>Detail Wahana</h6>
                        <?php if (!empty($wahana['durasi'])): ?>
                        <div class="detail-info-row">
                            <div class="detail-info-icon icon-red"><i class="bi bi-clock"></i></div>
                            <div class="detail-info-meta"><small>Durasi</small><strong><?= htmlspecialchars($wahana['durasi']) ?></strong></div>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($wahana['kapasitas'])): ?>
                        <div class="detail-info-row">
                            <div class="detail-info-icon icon-blue"><i class="bi bi-people-fill"></i></div>
                            <div class="detail-info-meta"><small>Kapasitas</small><strong><?= htmlspecialchars($wahana['kapasitas']) ?></strong></div>
                        </div>
                        <?php endif; ?>
                        <div class="detail-info-row">
                            <div class="detail-info-icon icon-red"><i class="bi bi-tag"></i></div>
                            <div class="detail-info-meta"><small>Kategori</small><strong><?= htmlspecialchars($wahana['kategori']) ?></strong></div>
                        </div>
                        <div class="detail-info-row">
                            <div class="detail-info-icon icon-green"><i class="bi bi-geo-alt-fill"></i></div>
                            <div class="detail-info-meta"><small>Lokasi</small><strong>Wonderland Samarinda</strong></div>
                        </div>
                        <button class="detail-jam-btn" onclick="openJamModal()">
                            <i class="bi bi-clock me-2"></i> Lihat Jam Operasional
                        </button>
                    </div>
                    <a href="https://maps.google.com/?q=Wonderland+Samarinda,+Jl.+Untung+Suropati,+Loa+Bakung,+Samarinda"
                       target="_blank" rel="noopener"
                       class="btn btn-directions w-100 py-3 fw-bold shadow-sm mt-3">
                        <i class="bi bi-cursor-fill me-2"></i> Get Directions
                    </a>
                </div><!-- end sticky wrapper -->
            </div>

        </div>
    </div>
</section>

<?php if (!empty($wahana_lainnya)): ?>
<section class="other-wahana-section">
    <div class="container">
        <h2 class="other-section-title">Wahana Lainnya</h2>
        <div class="row g-4">
            <?php foreach ($wahana_lainnya as $lain): ?>
                <?php
                    $fotoLain = trim($lain['foto'] ?? '');
                    $fotoLainUrl = 'assets/wahana/wonderland.jpg';

                    if ($fotoLain !== '') {
                        $uploadPath = __DIR__ . '/../../../uploads/wahana/' . $fotoLain;
                        $assetPath = __DIR__ . '/../../../assets/wahana/' . $fotoLain;

                        if (file_exists($uploadPath)) {
                            $fotoLainUrl = 'uploads/wahana/' . $fotoLain;
                        } elseif (file_exists($assetPath)) {
                            $fotoLainUrl = 'assets/wahana/' . $fotoLain;
                        }
                    }
                ?>
                <div class="col-md-4">
                    <a href="index.php?page=detail_wahana&id=<?= (int) $lain['id'] ?>" class="other-wahana-card">
                        <div class="card-img-wrap">
                            <img
                                src="<?= htmlspecialchars($fotoLainUrl) ?>"
                                alt="<?= htmlspecialchars($lain['nama'] ?? 'Wahana Wonderland') ?>"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='assets/wahana/wonderland.jpg';"
                            >
                        </div>
                        <div class="other-wahana-body">
                            <div class="other-wahana-name"><?= htmlspecialchars($lain['nama'] ?? '-') ?></div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FOOTER -->
<footer id="contact" class="footer pt-5 pb-3">
    <div class="container">
        <div class="row gy-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <h3 class="fw-bold mb-3 footer-brand"><span class="text-brand-gradient">Wonderland</span><br><span class="text-brand-gradient">Samarinda</span></h3>
                <p class="footer-desc mb-4">Your ultimate destination for family fun in East Kalimantan.</p>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white fw-bold mb-3">Quick Links</h5>
                <ul class="footer-links list-unstyled">
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="index.php#attraction">Wahana</a></li>
                    <li><a href="index.php#gallery">Galeri</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white fw-bold mb-3">Contact Us</h5>
                <ul class="footer-contact list-unstyled">
                    <li class="d-flex mb-3"><i class="bi bi-geo-alt text-danger me-3 fs-5"></i><span>Jl. Untung Suropati, Loa Bakung<br>Samarinda, Kaltim 75131</span></li>
                    <li class="d-flex align-items-center"><i class="bi bi-telephone text-warning me-3 fs-5"></i><span>+62 541 123 456</span></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white fw-bold mb-3">Opening Hours</h5>
                <div class="footer-hours">
                    <div class="mb-2"><span class="fw-bold text-white">Sen-Jum:</span><br><span>09:00 - 18:00</span></div>
                    <div><span class="fw-bold text-white">Sab-Min:</span><br><span>08:00 - 20:00</span></div>
                </div>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center pt-2">
            <p class="mb-2 mb-md-0">© 2026 Wonderland Samarinda. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Modal Jam Operasional -->
<div class="jam-modal-overlay" id="jamModal" onclick="closeJamModal(event)" style="display: none; opacity: 0; pointer-events: none; transition: opacity 0.3s ease;">
    <div class="jam-modal-box">
        <button class="jam-close-btn" onclick="closeJamModal()">&times;</button>
        <h5 class="jam-modal-title"><i class="bi bi-clock-history me-2 text-danger"></i>Jam Operasional</h5>
        <div class="jam-row"><span class="jam-row-day">Senin - Jumat</span><span class="jam-row-time">09:00 - 18:00</span></div>
        <div class="jam-row"><span class="jam-row-day">Sabtu - Minggu</span><span class="jam-row-time">08:00 - 20:00</span></div>
        <div class="jam-row"><span class="jam-row-day">Hari Libur Nasional</span><span class="jam-row-time">08:00 - 21:00</span></div>
        <div class="jam-row" style="color: var(--secondary); border-bottom:none; font-size:0.82rem;">
            <i class="bi bi-info-circle me-1"></i> Jam bisa berubah di hari spesial
        </div>
    </div>
</div>

<!-- Modal konfirmasi logout -->
<div class="modal fade" id="modalLogout" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="mb-3"><i class="bi bi-box-arrow-right text-danger" style="font-size:2.5rem;"></i></div>
                <h6 class="fw-bold mb-2">Konfirmasi Logout</h6>
                <p class="text-muted small mb-4">Apakah kamu yakin ingin keluar dari akun ini?</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary flex-fill rounded-3" data-bs-dismiss="modal">Batal</button>
                    <a href="index.php?page=logout" class="btn btn-danger flex-fill rounded-3 fw-bold">Ya, Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js" onerror="window.AOS={init:function(){}}"></script>
<script>
if (typeof AOS !== "undefined") { AOS.init({ duration: 900, once: true }); }

function openJamModal() {
    const modal = document.getElementById('jamModal');
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.style.opacity = '1';
        modal.style.pointerEvents = 'auto';
    }, 10);
}
function closeJamModal(e) {
    if (!e || e.target === document.getElementById('jamModal') || e.target.classList.contains('jam-close-btn')) {
        const modal = document.getElementById('jamModal');
        modal.style.opacity = '0';
        modal.style.pointerEvents = 'none';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeJamModal(); });

function konfirmasiLogout(e) {
    e.preventDefault();
    new bootstrap.Modal(document.getElementById('modalLogout')).show();
}

const stars = document.querySelectorAll('.star-inp');
const ratingVal = document.getElementById('ratingVal');
let currentRating = 5;

function paintStars(n) {
    stars.forEach((s, i) => {
        s.style.color = i < n ? '#f59e0b' : '#ddd';
    });
}
paintStars(5);

stars.forEach((star, idx) => {
    star.addEventListener('mouseover', () => paintStars(idx + 1));
    star.addEventListener('click', () => {
        currentRating = idx + 1;
        ratingVal.value = currentRating;
        paintStars(currentRating);
    });
});
document.getElementById('starInput')?.addEventListener('mouseleave', () => paintStars(currentRating));

function mintaLogin() {
    Swal.fire({
        title: 'Mau kasih ulasan?',
        text: "Kamu harus login atau daftar akun dulu ya!",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Login Sekarang',
        cancelButtonText: 'Daftar Akun',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'index.php?page=login';
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            window.location.href = 'index.php?page=register';
        }
    })
}

(function() {
    var savedTheme = localStorage.getItem('wl_theme') || 'light';
    applyTheme(savedTheme);

    document.getElementById('darkModeToggle').addEventListener('click', function() {
        var current = document.documentElement.getAttribute('data-theme') || 'light';
        var next = current === 'dark' ? 'light' : 'dark';
        applyTheme(next);
        localStorage.setItem('wl_theme', next);
    });

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        var icon = document.getElementById('darkModeIcon');
        if (icon) {
            if (theme === 'dark') {
                icon.className = 'bi bi-sun-fill';
            } else {
                icon.className = 'bi bi-moon-fill';
            }
        }
    }
})();

var translateCache = {};

async function translateUlasan(btn) {
    var id = btn.getAttribute('data-ulasan-id');
    var originalText = btn.getAttribute('data-original');
    var translatedBox = document.getElementById('translated-' + id);
    var translatedTextEl = document.getElementById('translated-text-' + id);
    var translatedLangEl = document.getElementById('translated-lang-' + id);
    var labelEl = btn.querySelector('.btn-translate-label');


    if (translatedBox.classList.contains('visible')) {
        translatedBox.classList.remove('visible');
        labelEl.textContent = 'Terjemahkan';
        btn.querySelector('i').className = 'bi bi-translate';
        return;
    }


    if (translateCache[id]) {
        showTranslation(translatedBox, translatedTextEl, translatedLangEl, btn, labelEl, translateCache[id]);
        return;
    }


    btn.classList.add('translating');
    btn.querySelector('i').className = '';
    var spinner = document.createElement('span');
    spinner.className = 'spinner-translate';
    btn.querySelector('i').replaceWith(spinner);
    labelEl.textContent = 'Menerjemahkan...';

    try {

        var apiUrl = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=id&dt=t&q=" + encodeURIComponent(originalText);

        var resp = await fetch(apiUrl);
        var data = await resp.json();

        var translatedText = "";
        var detectedLang = "??";

        if (data && data[0] && data[0][0] && data[0][0][0]) {


            translatedText = data[0].map(function(line){ return line[0]; }).join("");


            if (data[2]) detectedLang = data[2].toUpperCase();
        } else {
            translatedText = "⚠️ Terjemahan tidak tersedia saat ini.";
        }

        var result = { text: translatedText, lang: detectedLang };
        translateCache[id] = result;
        showTranslation(translatedBox, translatedTextEl, translatedLangEl, btn, labelEl, result);

    } catch (err) {
        console.error("Translation Error:", err);
        var sp = btn.querySelector('.spinner-translate') || btn.querySelector('span');
        if (sp && sp.classList.contains('spinner-translate')) {
            var newIcon = document.createElement('i');
            newIcon.className = 'bi bi-translate';
            sp.replaceWith(newIcon);
        }
        btn.classList.remove('translating');
        labelEl.textContent = 'Terjemahkan';
        translatedTextEl.textContent = '❌ Gagal menerjemahkan. Silakan coba lagi nanti.';
        translatedLangEl.textContent = '';
        translatedBox.classList.add('visible');
    }
}

function showTranslation(box, textEl, langEl, btn, labelEl, result) {

    var sp = btn.querySelector('.spinner-translate');
    if (sp) {
        var newIcon = document.createElement('i');
        newIcon.className = 'bi bi-translate';
        sp.replaceWith(newIcon);
    }
    btn.classList.remove('translating');

    textEl.textContent = result.text;
    langEl.textContent = result.lang ? result.lang.toUpperCase() : '';
    box.classList.add('visible');
    labelEl.textContent = 'Sembunyikan Terjemahan';
    btn.querySelector('i').className = 'bi bi-eye-slash';
}

(function() {
    var el = document.getElementById('resetCountdown');
    if (!el) return;

    function updateCountdown() {
        var now      = new Date();
        var midnight = new Date();
        midnight.setHours(24, 0, 0, 0);

        var diff = midnight - now;
        if (diff <= 0) {
            el.textContent = 'Segera...';
            return;
        }

        var jam    = Math.floor(diff / 3600000);
        var menit  = Math.floor((diff % 3600000) / 60000);
        var detik  = Math.floor((diff % 60000) / 1000);

        el.textContent =
            String(jam).padStart(2,'0') + ' jam ' +
            String(menit).padStart(2,'0') + ' menit ' +
            String(detik).padStart(2,'0') + ' detik';
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
})();

function toggleTextUlasan() {
    const btn = document.getElementById('btnToggleUlasan');

    if (!btn) {
        return;
    }

    setTimeout(function () {
        const target = document.getElementById('ulasanLainnya');

        if (!target) {
            return;
        }

        btn.textContent = target.classList.contains('show')
            ? 'Sembunyikan'
            : 'Lihat Selengkapnya';
    }, 350);
}
</script>
</body>
</html>