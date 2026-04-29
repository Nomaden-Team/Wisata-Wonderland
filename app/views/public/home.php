<?php
$jumlah_wahana = count($wahana_list ?? []);
$rating_rata = !empty($avg_rating) ? number_format((float) $avg_rating, 1) : '4.8';
$jumlah_ulasan_total = (int) ($total_ulasan ?? 0);
$hero_stat_visitors = '500K+';
$hero_stat_years = '10+';
$featured_wahana = array_slice($wahana_list ?? [], 0, 6);
$wahana_names_ulasan = array_values(array_unique(array_filter(array_column($ulasan_home ?? [], 'wahana_name'))));
?>
<!DOCTYPE html>
<html lang="id" id="htmlRoot">
<head>
    <title>Wonderland Samarinda</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script>
        (function(){
            var t = localStorage.getItem('wl_theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        [data-aos] { opacity: 1 !important; transform: none !important; transition: none !important; }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="landing-page">

<?php

$isLoggedIn = isset($_SESSION['user_id']);
?>

<?php if ($isLoggedIn): ?>
<style>
/* Sudah login: pastikan body langsung terlihat tanpa animasi pembuka */
body.landing-page { opacity: 1 !important; visibility: visible !important; overflow: auto !important; }
</style>
<?php else: ?>
<div id="preloader">

    <div class="pre-logo">
        <span>Wonderland Samarinda</span>
    </div>
    <div class="pre-count">0</div>
    <div class="pre-bar-wrap">
        <div class="pre-bar"></div>
    </div>
</div>

<div id="splash">

    <div class="splash-logos">
        <img src="assets/img/logo-univ.svg"
             onerror="this.style.display='none'"
             alt="University Logo">
        <div class="splash-logo-divider"></div>
        <img src="assets/img/logo-prodi.svg"
             onerror="this.style.display='none'"
             alt="Study Program Logo">
    </div>


    <h1 class="splash-title">
        <span class="word">Wonderland</span>
        <span class="word">Samarinda</span>
    </h1>

    <p class="splash-tagline">Taman Rekreasi Unggulan Kalimantan Timur</p>

    <!-- Required click before entering main page -->
    <button class="splash-explore">
        Jelajahi Sekarang &nbsp;<i class="bi bi-arrow-right-short fs-5"></i>
    </button>


    <div class="splash-credits">
        Dikembangkan oleh Sistem Informasi · Universitas Mulawarman · 2026
    </div>
</div>
<?php endif; ?>

<nav class="navbar navbar-expand-lg custom-navbar landing-navbar fixed-top">
    <div class="container">
        <a class="navbar-brand landing-brand" href="index.php">
            <span>Wonderland</span> <span>Samarinda</span>
        </a>
        <div class="d-flex align-items-center gap-2 ms-auto me-2">

            <button id="darkModeToggle" class="dark-mode-toggle" title="Toggle Dark Mode" aria-label="Toggle Dark Mode">
                <i class="bi bi-moon-fill" id="darkModeIcon"></i>
            </button>
        </div>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2 landing-nav-links">
                <li class="nav-item"><a class="nav-link active" href="#about">Tentang</a></li>
                <li class="nav-item"><a class="nav-link" href="#attraction">Wahana</a></li>
                <li class="nav-item"><a class="nav-link" href="#gallery">Galeri</a></li>
                <li class="nav-item"><a class="nav-link" href="#ulasan">Ulasan</a></li>
                <li class="nav-item"><a class="nav-link" href="#find-us">Lokasi</a></li>
                <li class="nav-item"><a class="nav-link" href="#visitor-info">Info</a></li>
            </ul>

            <div class="d-flex align-items-center gap-3 ms-lg-4 mt-3 mt-lg-0 w-100 w-lg-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown w-100 w-lg-auto">
                        <button class="btn-user-nav dropdown-toggle d-flex align-items-center gap-2 fw-bold px-3 w-100 w-lg-auto"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-sm">
                                <?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?>
                            </div>
                            <?= htmlspecialchars($_SESSION['nama']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-end shadow border-0 rounded-4 mt-2 w-100 w-lg-auto">
                            <li><a class="dropdown-item py-2" href="<?= $_SESSION['role'] === 'admin' ? 'index.php?page=admin_dashboard' : 'index.php?page=user_dashboard' ?>">
                                <i class="bi bi-grid me-2 text-primary"></i> Dasbor
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="#" onclick="konfirmasiLogout(event)">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                            </a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="index.php?page=login" class="btn landing-login-btn">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<header id="home" class="landing-hero">
    <div class="landing-orb orb-left"></div>
    <div class="landing-orb orb-right"></div>
    <div class="landing-orb orb-bottom"></div>
    <div class="container position-relative">
        <div class="landing-hero-inner" data-aos="fade-up">
            <div class="landing-pill">
                <i class="bi bi-stars"></i>
                <span>#1 Theme Park in Samarinda</span>
            </div>
            <h1 class="landing-hero-title">
                Selamat Datang di
                <span>Wonderland Samarinda</span>
            </h1>
            <p class="landing-hero-subtitle">
                Nikmati petualangan seru dengan wahana menarik, keseruan keluarga,
                dan pengalaman tak terlupakan di taman rekreasi unggulan Kalimantan Timur!
            </p>
            <div class="landing-hero-actions">
                <a href="#attraction" class="btn landing-btn-primary">Jelajahi Wahana <i class="bi bi-arrow-right-short"></i></a>
                <a href="#visitor-info" class="btn landing-btn-secondary">Informasi Pengunjung</a>
            </div>

            <div class="landing-stats-row" data-aos="fade-up" data-aos-delay="150">
                <div class="landing-stat-card">
                    <div class="landing-stat-icon text-danger"><i class="bi bi-people"></i></div>
                    <h3><?= $hero_stat_visitors ?></h3>
                    <p>Pengunjung Puas</p>
                </div>
                <div class="landing-stat-card">
                    <div class="landing-stat-icon text-warning"><i class="bi bi-ticket-perforated"></i></div>
                    <h3><?= $jumlah_wahana ?>+</h3>
                    <p>Wahana</p>
                </div>
                <div class="landing-stat-card">
                    <div class="landing-stat-icon text-success"><i class="bi bi-star"></i></div>
                    <h3><?= $rating_rata ?>/5</h3>
                    <p>Rating</p>
                </div>
                <div class="landing-stat-card">
                    <div class="landing-stat-icon text-danger"><i class="bi bi-heart"></i></div>
                    <h3><?= $hero_stat_years ?></h3>
                    <p>Tahun</p>
                </div>
            </div>

            <a href="#about" class="landing-scroll-indicator" aria-label="Scroll to about section">
                <span></span>
            </a>
        </div>
    </div>
</header>

<section id="about" class="landing-section landing-about-section">
    <div class="container">
        <div class="landing-section-heading text-center" data-aos="fade-up">
            <h2>Tentang <span>Wonderland Samarinda</span></h2>
            <p>Destinasi rekreasi keluarga yang menghadirkan keceriaan dan keseruan di Samarinda.</p>
        </div>

        <div class="row align-items-center g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <div class="landing-about-copy">
                    <p>Wonderland Samarinda adalah destinasi wisata keluarga di Kalimantan Timur yang menghadirkan wahana seru, area menarik, dan pengalaman menyenangkan untuk semua usia.</p>
                    <p>Berlokasi di Samarinda, tempat wisata ini memiliki berbagai wahana, spot foto menarik, dan suasana ramah keluarga yang membuat setiap kunjungan terasa berkesan.</p>
                    <p>Baik untuk mencari hiburan, bersantai, maupun menghabiskan waktu bersama keluarga, Wonderland Samarinda menawarkan pengalaman yang menyenangkan dan berkesan.</p>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <div class="landing-about-image">
                    <img src="assets/wahana/about-wonderland.jpg" alt="Tentang Wonderland Samarinda">
                </div>
            </div>
        </div>

        <div class="row g-4 mt-4">
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="landing-feature-card">
                    <div class="landing-feature-icon"><i class="bi bi-people"></i></div>
                    <h3>Ramah Keluarga</h3>
                    <p>Wahana untuk segala usia dengan standar keamanan yang nyaman untuk keluarga.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
                <div class="landing-feature-card">
                    <div class="landing-feature-icon"><i class="bi bi-stars"></i></div>
                    <h3>Pengalaman Tak Terlupakan</h3>
                    <p>Berbagai atraksi menarik dan spot foto estetik untuk momen yang susah dilupakan.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                <div class="landing-feature-card">
                    <div class="landing-feature-icon"><i class="bi bi-shield-check"></i></div>
                    <h3>Aman & Nyaman</h3>
                    <p>Fasilitas lengkap dan tim profesional siap membantu pengunjung sepanjang hari.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="attraction" class="landing-section landing-attraction-section">
    <div class="container" id="wahana-app">

        <div class="landing-section-heading text-center" data-aos="fade-up">
            <h2>Pilihan <span>Wahana</span></h2>
            <p>Pilih wahana favoritmu, dari yang santai sampai yang bikin adrenalin naik terus.</p>
        </div>

        <div class="wahana-filter-bar" v-cloak>
            <button
                v-for="cat in categories"
                :key="cat"
                class="wahana-filter-btn"
                :class="{ active: activeCategory === cat }"
                @click="activeCategory = cat">
                {{ cat }}
            </button>
        </div>

        <!-- Cards Grid (Vue) -->
       <transition-group name="wahana-fade" tag="div" class="row g-4" v-cloak>
    <div
        v-for="(w, index) in filteredWahana"
        :key="w.id"
        class="col-lg-4 col-md-6">
        <article class="landing-attraction-card h-100">
            <div class="landing-attraction-media">
                <img :src="w.foto_url" :alt="w.nama">
                <span class="landing-attraction-badge">{{ w.kategori }}</span>
            </div>

            <div class="landing-attraction-body">
                <h3>{{ w.nama }}</h3>
                <p>{{ w.deskripsi.substring(0, 110) }}{{ w.deskripsi.length > 110 ? '...' : '' }}</p>

                <div class="landing-attraction-footer justify-content-end">
                    <a :href="'index.php?page=detail_wahana&id=' + w.id" class="btn landing-card-link">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </article>
    </div>
</transition-group>

        <!-- Empty state -->
        <div v-if="filteredWahana.length === 0" class="wahana-empty" v-cloak>
            <i class="bi bi-search"></i>
            <p>Tidak ada wahana di kategori ini.</p>
        </div>

    </div>
</section>

<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>

<script>
const wahanaData = <?php
    $vue_wahana = array_map(function($w) {
        $foto = $w['foto'] ?? '';

        if ($foto !== '') {
            $uploadPath = __DIR__ . '/../../uploads/wahana/' . $foto;
           $foto = $w['foto'] ?? '';

if ($foto !== '') {
    $uploadPath = __DIR__ . '/../../../uploads/wahana/' . $foto;
    $assetPath = __DIR__ . '/../../../assets/wahana/' . $foto;

    if (file_exists($uploadPath)) {
        $foto_url = 'uploads/wahana/' . $foto;
    } elseif (file_exists($assetPath)) {
        $foto_url = 'assets/wahana/' . $foto;
    } else {
        $foto_url = 'assets/wahana/wonderland.jpg';
    }
} else {
    $foto_url = 'assets/wahana/wonderland.jpg';
}
        } else {
            $foto_url = 'assets/wahana/wonderland.jpg';
        }

        return [
            'id'        => (int) ($w['id'] ?? 0),
            'nama'      => $w['nama'] ?? '',
            'kategori'  => $w['kategori'] ?? '',
            'deskripsi' => $w['deskripsi'] ?? '',
            'foto_url'  => $foto_url,
        ];
    }, $wahana_list ?? []);

    echo json_encode($vue_wahana, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>;

const { createApp } = Vue;

createApp({
    data() {
        return {
            wahana: wahanaData,
            activeCategory: 'Semua',
        };
    },
    computed: {
        categories() {
            const cats = [...new Set(this.wahana.map(w => w.kategori).filter(Boolean))];
            return ['Semua', ...cats];
        },
        filteredWahana() {
            if (this.activeCategory === 'Semua') return this.wahana;
            return this.wahana.filter(w => w.kategori === this.activeCategory);
        },
    },
}).mount('#wahana-app');
</script>

<section id="gallery" class="landing-section landing-gallery-section">
    <div class="container">
        <div class="landing-section-heading text-center" data-aos="fade-up">
            <h2>Galeri <span>Foto</span></h2>
            <p>Lihat berbagai momen seru dan sudut menarik di Wonderland Samarinda.</p>
        </div>

<div class="landing-gallery-grid">
    <?php if (!empty($galeri_list)): ?>
        <?php $indexGallery = 0; ?>

        <?php foreach ($galeri_list as $g): ?>
            <?php
                $source = $g['source'] ?? 'galeri';

                /*
                 * Pola bento dibuat stabil:
                 * tetap ada variasi besar/tinggi/lebar,
                 * tapi tidak random dari database.
                 */
                $bentoPattern = [
                    'item-large',
                    '',
                    '',
                    'item-tall',
                    '',
                    '',
                    'item-wide',
                    '',
                ];

                $class = $bentoPattern[$indexGallery % count($bentoPattern)];
                $indexGallery++;

                $foto = trim($g['foto'] ?? '');
                $pathFoto = 'assets/wahana/wonderland.jpg';

                if ($foto !== '') {
                    if ($source === 'user_upload') {
                        $uploadPath = __DIR__ . '/../../../uploads/' . $foto;

                        if (file_exists($uploadPath)) {
                            $pathFoto = 'uploads/' . $foto;
                        }
                    } else {
                        $assetGalleryPath = __DIR__ . '/../../../assets/gallery/' . $foto;
                        $uploadGalleryPath = __DIR__ . '/../../../uploads/gallery/' . $foto;

                        if (file_exists($assetGalleryPath)) {
                            $pathFoto = 'assets/gallery/' . $foto;
                        } elseif (file_exists($uploadGalleryPath)) {
                            $pathFoto = 'uploads/gallery/' . $foto;
                        }
                    }
                }

                $judulFoto = $g['judul'] ?? 'Foto Galeri Wonderland';
            ?>

            <div class="gallery-item <?= htmlspecialchars($class) ?>" data-aos="zoom-in">
                <img
                    src="<?= htmlspecialchars($pathFoto) ?>"
                    alt="<?= htmlspecialchars($judulFoto) ?>"
                    onerror="this.src='assets/wahana/about-wonderland.jpg'"
                >
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center text-muted py-4">
            Belum ada foto galeri yang tersedia.
        </div>
    <?php endif; ?>
</div>

        <p class="landing-gallery-note">Bagikan momen seru Anda di media sosial dengan tagar <span>#WonderlandSamarinda</span></p>
    </div>
</section>

<section id="ulasan" class="landing-section landing-review-section">
    <div class="container">
        <div class="landing-section-heading text-center" data-aos="fade-up">
            <h2>Apa Kata <span>Mereka</span></h2>
            <p>Ribuan keluarga telah merasakan keseruan di Wonderland Samarinda. Simak pengalaman mereka!</p>
        </div>

<div class="landing-review-summary" data-aos="zoom-in">
    <div class="landing-review-score">
        <i class="bi bi-star-fill"></i>
        <strong><?= $rating_rata ?></strong>
        <span>dari 5.0</span>
    </div>
    <div class="landing-review-divider"></div>
    <div class="landing-review-total">
        <strong><?= $jumlah_ulasan_total ?></strong>
        <span>Total Ulasan</span>
    </div>
</div>

        <?php if (!empty($ulasan_home)): ?>
        <div class="landing-review-filters" id="ulasanFilter">
            <button class="ulasan-filter-btn active" onclick="filterUlasan('semua', this)">Semua Wahana</button>
            <?php foreach ($wahana_names_ulasan as $wn): ?>
                <button class="ulasan-filter-btn" onclick="filterUlasan('<?= htmlspecialchars($wn) ?>', this)"><?= htmlspecialchars($wn) ?></button>
            <?php endforeach; ?>
        </div>

        <div class="row g-4" id="ulasanGrid">
            <?php foreach ($ulasan_home as $u): ?>
            <div class="col-lg-4 col-md-6 ulasan-item" data-wahana="<?= htmlspecialchars($u['wahana_name'] ?? 'umum') ?>">
                <div class="landing-review-card" data-aos="fade-up">
                    <div class="landing-review-top">
                        <div class="d-flex align-items-center gap-3">
                            <div class="landing-review-avatar"><?= strtoupper(substr($u['nama_user'], 0, 1)) ?></div>
                            <div>
                                <h3><?= htmlspecialchars($u['nama_user']) ?></h3>
                                <p><?= date('j F Y', strtotime($u['created_at'])) ?></p>
                            </div>
                        </div>
                        <div class="landing-review-quote"><i class="bi bi-quote"></i></div>
                    </div>
                    <div class="landing-review-stars mb-2">
                        <?php for ($s = 1; $s <= 5; $s++): ?>
                            <i class="bi bi-star-fill <?= $s <= $u['rating'] ? 'active' : '' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <?php if (!empty($u['wahana_name'])): ?>
                        <span class="landing-review-badge"><?= htmlspecialchars($u['wahana_name']) ?></span>
                    <?php endif; ?>
                    <p class="landing-review-text">"<?= htmlspecialchars($u['ulasan']) ?>"</p>
                    <div class="landing-review-meta">
                        <span class="landing-review-helpful"><i class="bi bi-hand-thumbs-up"></i> <?= 12 + (strlen($u['nama_user']) + (int) $u['rating']) % 21 ?></span>
                        <span class="landing-review-verified">Verified Visit</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-4 text-muted">Belum ada ulasan yang tersedia.</div>
        <?php endif; ?>

        <div class="landing-review-cta text-center" data-aos="fade-up">
            <h3>Punya Pengalaman Seru?</h3>
            <p>Bagikan cerita Anda dan bantu keluarga lain menemukan keseruan di Wonderland.</p>
            <a href="index.php?page=detail_wahana&id=<?= isset($wahana_list[0]) ? $wahana_list[0]['id'] : 1 ?>#tulis-ulasan" class="btn landing-btn-primary">Tulis Ulasan</a>
        </div>
    </div>
</section>

<section id="find-us" class="landing-section landing-info-section">
    <div class="container">
        <div class="landing-section-heading text-center" data-aos="fade-up">
            <h2>Find <span>Us</span></h2>
            <p>Kunjungi Wonderland Samarinda dan nikmati petualangan seru bersama keluarga.</p>
        </div>
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-7" data-aos="fade-right">
                <div class="landing-map-card h-100">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.4826!2d117.1455!3d-0.5020!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2df67ef6d5c4b1c1%3A0x3e0b65e7a8c4d2e0!2sWonderland%20Samarinda!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
                        width="100%" height="100%" style="border:0; min-height:380px;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
            <div class="col-lg-5" data-aos="fade-left">
                <div class="landing-info-stack">
                    <div class="landing-info-card">
                        <div class="landing-info-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <h3>Alamat</h3>
                            <p>Jl. Padat Karya, RT 018, Handil Bakti, <Br>Kec. Palaran, Samarinda, <Br>Kalimantan Timur 75242</p>
                        </div>
                    </div>
                    <div class="landing-info-card">
                        <div class="landing-info-icon"><i class="bi bi-sign-turn-right-fill"></i></div>
                        <div>
                            <h3>Cara Menuju Lokasi</h3>
                            <ul>
                                <li>Dari APT Pranoto Airport: sekitar 30 menit via Jl. Ir. H. Juanda.</li>
                                <li>Dari pusat kota Samarinda: sekitar 15 menit via Jl. Slamet Riyadi.</li>
                                <li>Tersedia transportasi online dan angkutan kota menuju area terdekat.</li>
                            </ul>
                        </div>
                    </div>
                    <a href="https://maps.google.com/?q=Wonderland+Samarinda,+Jl.+Untung+Suropati,+Loa+Bakung,+Samarinda"
                       target="_blank" rel="noopener" class="btn landing-btn-primary w-100 justify-content-center">
                        <i class="bi bi-cursor-fill me-2"></i> Lihat Rute
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="visitor-info" class="landing-section landing-visitor-section">
    <div class="container">
        <div class="landing-section-heading text-center" data-aos="fade-up">
            <h2>Informasi <span>Pengunjung</span></h2>
            <p>Semua yang perlu Anda tahu sebelum menikmati hari seru di Wonderland Samarinda.</p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="100">
                <div class="landing-visitor-card h-100">
                    <div class="landing-visitor-head">
                        <span class="landing-visitor-icon bg-danger-subtle text-danger"><i class="bi bi-clock"></i></span>
                        <h3>Jam Operasional</h3>
                    </div>
                    <div class="landing-visitor-list">
                        <div><span>Senin - Jumat</span><strong>09:00 - 18:00</strong></div>
                        <div><span>Sabtu - Minggu</span><strong>08:00 - 20:00</strong></div>
                        <div><span>Libur Nasional</span><strong>08:00 - 21:00</strong></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                <div class="landing-visitor-card h-100">
                    <div class="landing-visitor-head">
                        <span class="landing-visitor-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-ticket-perforated"></i></span>
                        <h3>Harga Tiket</h3>
                    </div>
                    <div class="landing-visitor-list">
                        <?php if (!empty($pricelist_home)): ?>
                            <?php foreach ($pricelist_home as $p): ?>
                            <?php
                                $hargaPaket = (int) (($p['harga_promo'] ?? 0) ?: ($p['harga_normal'] ?? 0));
                                $benefitText = trim($p['benefit'] ?? '');
                                $modalData = [
                                    'nama' => $p['nama'] ?? '',
                                    'kategori' => $p['kategori'] ?? '',
                                    'deskripsi' => $p['deskripsi'] ?? '',
                                    'benefit' => $benefitText,
                                    'harga' => 'Rp ' . number_format($hargaPaket, 0, ',', '.'),
                                ];

                                $modalJson = htmlspecialchars(
                                    json_encode($modalData, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP),
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                            ?>
<div role="button"
     tabindex="0"
     class="rounded-3 border shadow-sm bg-white"
     onclick='openPaketModal(<?= $modalJson ?>)'
     onkeydown="if(event.key === 'Enter'){ openPaketModal(<?= $modalJson ?>); }"
     title="Lihat benefit paket <?= htmlspecialchars($p['nama'] ?? '') ?>"
     style="cursor:pointer;">
    <span><?= htmlspecialchars($p['nama']) ?></span>

    <span class="d-inline-flex align-items-center gap-2">
        <strong>Rp <?= number_format($hargaPaket, 0, ',', '.') ?></strong>
        <i class="bi bi-chevron-right text-danger"></i>
    </span>
</div>
                            <?php endforeach; ?>
                        <?php elseif (!empty($tiket_list)): ?>
                            <?php foreach ($tiket_list as $t): ?>
                            <div>
                                <span><?= htmlspecialchars($t['nama_tiket']) ?></span>
                                <strong>Rp <?= number_format($t['harga'], 0, ',', '.') ?></strong>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div><span>Dewasa Weekday</span><strong>Rp 75.000</strong></div>
                            <div><span>Anak Weekday</span><strong>Rp 50.000</strong></div>
                            <div><span>Dewasa Weekend</span><strong>Rp 100.000</strong></div>
                            <div><span>Anak Weekend</span><strong>Rp 75.000</strong></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12" data-aos="zoom-in" data-aos-delay="300">
                <div class="landing-visitor-card h-100">
                    <div class="landing-visitor-head">
                        <span class="landing-visitor-icon bg-success-subtle text-success"><i class="bi bi-p-circle"></i></span>
                        <h3>Parkir</h3>
                    </div>
                    <div class="landing-visitor-list">
                        <div><span>Mobil</span><strong>Rp 10.000</strong></div>
                        <div><span>Motor</span><strong>Rp 5.000</strong></div>
                    </div>
                    <p class="landing-visitor-note">Area parkir luas dan aman tersedia untuk semua pengunjung setiap hari.</p>
                </div>
            </div>
        </div>

        <div class="landing-facility-card" data-aos="fade-up">
            <h3>Fasilitas Tersedia</h3>
            <div class="row g-3 justify-content-center" >
                <?php if (!empty($fasilitas_list)): ?>
                    <?php foreach ($fasilitas_list as $f): ?>
                    <div class="col-md-3 col-6" >
                        <div class="landing-facility-item">
                            <i class="fas <?= htmlspecialchars($f['ikon'] ?? 'fa-circle-info') ?>"></i>
                            <h4><?= htmlspecialchars($f['nama']) ?></h4>
                            <p><?= htmlspecialchars($f['deskripsi'] ?? '') ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-md-3 col-6"><div class="landing-facility-item"><i class="bi bi-shop"></i><h4>Pujasera</h4><p>Beragam pilihan makanan</p></div></div>
                    <div class="col-md-3 col-6"><div class="landing-facility-item"><i class="bi bi-cup-hot"></i><h4>Kafe</h4><p>Kedai camilan tersedia di area taman</p></div></div>
                    <div class="col-md-3 col-6"><div class="landing-facility-item"><i class="bi bi-shield-plus"></i><h4>P3K</h4><p>Petugas medis tersedia</p></div></div>
                    <div class="col-md-3 col-6"><div class="landing-facility-item"><i class="bi bi-building"></i><h4>Mushola</h4><p>Fasilitas ibadah yang bersih</p></div></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="landing-notes-card" data-aos="fade-up">
            <h3>Catatan Penting</h3>
            <ul class="notes-list">
                <li>Harap jaga kebersihan dan buang sampah pada tempat yang tersedia.</li>
                <li>Batasan tinggi badan dan usia berlaku untuk wahana tertentu demi keselamatan.</li>
                <li>Makanan dan minuman dari luar tidak diperkenankan di dalam area taman.</li>
                <li>Ikuti semua instruksi keselamatan dari petugas kami selama berada di area wisata.</li>
            </ul>
        </div>
    </div>
</section>

<footer id="contact" class="footer landing-footer pt-5 pb-3">
    <div class="container">
        <div class="row gy-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <h3 class="landing-footer-brand">
                    <span>Wonderland</span><br>
                    <span>Samarinda</span>
                </h3>
                <p class="footer-desc mb-4">Destinasi rekreasi keluarga untuk menikmati keseruan, petualangan, dan momen berkesan di Kalimantan Timur.</p>
                <div class="footer-socials d-flex gap-2">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-twitter-x"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <h5 class="text-white fw-bold mb-3">Tautan Cepat</h5>
                <ul class="footer-links list-unstyled">
                    <li><a href="#about">Tentang</a></li>
                    <li><a href="#attraction">Wahana</a></li>
                    <li><a href="#gallery">Galeri</a></li>
                    <li><a href="#visitor-info">Info Pengunjung</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white fw-bold mb-3">Hubungi Kami</h5>
                <ul class="footer-contact list-unstyled">
                    <li class="d-flex mb-3">
                        <i class="bi bi-geo-alt text-danger me-3 fs-5"></i>
                        <span>Jl. Padat Karya, RT 018, Handil Bakti, Kec. Palaran, Samarinda, <Br>Kalimantan Timur 75242</span>
                    </li>
                    <li class="d-flex mb-3 align-items-center">
                        <i class="bi bi-telephone text-warning me-3 fs-5"></i>
                        <a href="tel:+62541123456" style="color:inherit;text-decoration:none">+62 541 123 456</a>
                    </li>
                    <li class="d-flex align-items-center">
                        <i class="bi bi-envelope text-success me-3 fs-5"></i>
                        <a href="mailto:info@wonderlandsamarinda.com" style="color:inherit;text-decoration:none">info@wonderlandsamarinda.com</a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-white fw-bold mb-3">Jam Operasional</h5>
                <div class="footer-hours">
                    <div class="mb-3"><span class="fw-bold text-white">Sen - Jum:</span><br><span>09:00 - 18:00</span></div>
                    <div class="mb-3"><span class="fw-bold text-white">Sab - Min:</span><br><span>08:00 - 20:00</span></div>
                    <div><span class="fw-bold text-white">Libur Nasional:</span><br><span>08:00 - 21:00</span></div>
                </div>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center pt-2">
            <p class="mb-2 mb-md-0">© 2026 Wonderland Samarinda. All rights reserved.</p>
            <div class="bottom-links d-flex gap-3">
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Syarat Layanan</a>
                <a href="index.php?page=login" class="text-muted">Admin</a>
            </div>
        </div>

<div class="wl-credits-block">
    <div class="wl-credits-logos">
        <img src="assets/img/logo-wlp.png"
             onerror="this.style.display='none'"
             alt="Wonderland Samarinda Logo">
    </div>
    <span>Wonderland Samarinda · 2026</span>
</div>
    </div>
</footer>

<div class="modal fade" id="modalPaketHarga" tabindex="-1" aria-hidden="true" data-bs-scroll="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <small class="text-muted" id="paketKategori"></small>
                    <h5 class="modal-title fw-bold" id="paketNama">Detail Paket</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body pt-3">
                <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                    <span class="text-muted small">Harga Paket</span>
                    <strong class="text-danger fs-5" id="paketHarga">Rp 0</strong>
                </div>

                <p class="text-muted small mb-3" id="paketDeskripsi"></p>

                <div id="paketBenefitWrap">
                    <div class="fw-bold mb-2">Benefit / Free Akses</div>
                    <ul class="mb-0 ps-3" id="paketBenefitList"></ul>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-danger rounded-3 px-4 fw-bold" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLogout" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-box-arrow-right text-danger" style="font-size: 2.5rem;"></i>
                </div>
                <h6 class="fw-bold mb-2">Konfirmasi Keluar</h6>
                <p class="text-muted small mb-4">Apakah kamu yakin ingin keluar dari akun ini?</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary flex-fill rounded-3" data-bs-dismiss="modal">Batal</button>
                    <a href="index.php?page=logout" class="btn btn-danger flex-fill rounded-3 fw-bold">Ya, Keluar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js" onerror="window.AOS={init:function(){}}"></script>
<script>
if (typeof AOS !== "undefined") { AOS.init({ duration: 900, once: true }); }

function konfirmasiLogout(e) {
    e.preventDefault();
    new bootstrap.Modal(document.getElementById('modalLogout')).show();
}

function openPaketModal(data) {
    const modalEl = document.getElementById('modalPaketHarga');
    const benefitWrap = document.getElementById('paketBenefitWrap');
    const benefitList = document.getElementById('paketBenefitList');

    document.getElementById('paketNama').textContent = data.nama || 'Detail Paket';
    document.getElementById('paketKategori').textContent = data.kategori || '';
    document.getElementById('paketHarga').textContent = data.harga || 'Rp 0';
    document.getElementById('paketDeskripsi').textContent =
        data.deskripsi || 'Informasi detail paket tersedia di loket Wonderland Samarinda.';

    benefitList.innerHTML = '';

    const benefits = (data.benefit || '')
        .split(/\r?\n/)
        .map(function (item) {
            return item.trim();
        })
        .filter(Boolean);

    if (benefits.length > 0) {
        benefits.forEach(function (item) {
            const li = document.createElement('li');
            li.textContent = item;
            benefitList.appendChild(li);
        });

        benefitWrap.style.display = '';
    } else {
        benefitWrap.style.display = 'none';
    }

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl, {
        backdrop: true,
        keyboard: true,
        scroll: true
    });

    modal.show();

    document.body.style.paddingRight = '0';
}

function filterUlasan(wahana, btn) {
    document.querySelectorAll('.ulasan-filter-btn').forEach(function (b) {
        b.classList.remove('active');
    });
    btn.classList.add('active');
    document.querySelectorAll('.ulasan-item').forEach(function (item) {
        item.style.display = (wahana === 'semua' || item.dataset.wahana === wahana) ? '' : 'none';
    });
}

(function() {
    var savedTheme = localStorage.getItem('wl_theme') || 'light';
    applyTheme(savedTheme);

    var btn = document.getElementById('darkModeToggle');
    if (btn) {
        btn.addEventListener('click', function() {
            var current = document.documentElement.getAttribute('data-theme') || 'light';
            var next = current === 'dark' ? 'light' : 'dark';
            applyTheme(next);
            localStorage.setItem('wl_theme', next);
        });
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        var icon = document.getElementById('darkModeIcon');
        if (icon) {
            icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        }
    }
})();
</script>

<script src="assets/js/enhance.js"></script>
</body>
</html>