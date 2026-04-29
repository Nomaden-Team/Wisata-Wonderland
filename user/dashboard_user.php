<?php
session_start();

// Proteksi: kalau belum login, redirect ke login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// 1. Perbaikan: Path koneksi ditambah ../ karena file ada di dalam folder 'user'
require_once '../config/koneksi.php';

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$nama    = $_SESSION['nama'] ?? 'Pengunjung';
$email   = $_SESSION['email'] ?? '';

// 2. Perbaikan: Ganti $conn menjadi $koneksi (sesuai file koneksi.php kamu)
$stmt = $koneksi->prepare("SELECT * FROM foto_pengunjung WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$fotos  = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengunjung - Wonderland Samarinda</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="dashboard-body">

    <header class="dashboard-navbar">
        <div class="dashboard-navbar-left">
            <h1 class="dashboard-navbar-title">Dashboard Pengunjung</h1>
            <p class="dashboard-navbar-sub">Selamat datang, <?= htmlspecialchars($nama) ?>!</p>
        </div>
        <div class="dashboard-navbar-right">
            <a href="../index.php" class="dashboard-btn-outline">
                <i class="fas fa-home"></i> Kembali ke Home
            </a>
            <a href="../logout.php" class="dashboard-btn-outline">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </header>

    <main class="dashboard-main">

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'success'): ?>
                <div class="dashboard-alert dashboard-alert-success">
                    <i class="fas fa-check-circle"></i> Foto berhasil diupload!
                </div>
            <?php elseif ($_GET['status'] === 'error'): ?>
                <div class="dashboard-alert dashboard-alert-error">
                    <i class="fas fa-times-circle"></i> Gagal upload foto. <?= htmlspecialchars($_GET['msg'] ?? '') ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="dashboard-card dashboard-profile-card">
            <div class="dashboard-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="dashboard-profile-info">
                <h2 class="dashboard-profile-name"><?= htmlspecialchars($nama) ?></h2>
                <p class="dashboard-profile-email"><?= htmlspecialchars($email) ?></p>
            </div>
        </div>

        <div class="dashboard-upload-section">
            <div class="dashboard-upload-icon">
                <i class="fas fa-camera"></i>
            </div>
            <h3 class="dashboard-upload-title">Upload Foto Kunjungan Anda</h3>
            <p class="dashboard-upload-desc">Bagikan momen seru Anda di Wonderland Samarinda!</p>

            <button class="dashboard-btn-upload" onclick="document.getElementById('inputFoto').click()">
                <i class="fas fa-upload"></i> Pilih Foto
            </button>

            <form action="upload_foto.php" method="POST" enctype="multipart/form-data" id="formUpload" style="display:none">
                <input
                    type="file"
                    id="inputFoto"
                    name="foto"
                    accept="image/jpeg, image/png, image/webp"
                    onchange="document.getElementById('formUpload').submit()"
                >
            </form>
        </div>

        <div class="dashboard-card dashboard-foto-card">
            <h3 class="dashboard-foto-title">Foto Saya</h3>

            <?php if (!empty($fotos)): ?>
                <div class="dashboard-foto-grid">
                    <?php foreach ($fotos as $foto): ?>
                        <div class="dashboard-foto-item">
                            <img
                                src="../uploads/<?= htmlspecialchars($foto['nama_file']) ?>"
                                alt="Foto Kunjungan"
                                loading="lazy"
                            >
                            <form action="hapus_foto.php" method="POST" class="dashboard-foto-delete-form"
                                  onsubmit="return confirm('Hapus foto ini?')">
                                <input type="hidden" name="foto_id" value="<?= $foto['id'] ?>">
                                <button type="submit" class="dashboard-foto-delete-btn" title="Hapus foto">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="dashboard-empty-state">
                    <i class="far fa-image dashboard-empty-icon"></i>
                    <p class="dashboard-empty-text">Belum ada foto yang diupload</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <button class="dashboard-help-btn" title="Bantuan" onclick="alert('Hubungi kami di WhatsApp: 0812-XXXX-XXXX')">?</button>

</body>
</html>
