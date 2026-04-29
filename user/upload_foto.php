<?php
session_start();

// Proteksi: harus sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/koneksi.php';

$user_id = $_SESSION['user_id'];

// Cek apakah ada file yang diupload
if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
    header('Location: dashboard_user.php?status=error&msg=Tidak ada file dipilih.');
    exit;
}

$file      = $_FILES['foto'];
$error     = $file['error'];
$tmp_name  = $file['tmp_name'];
$size      = $file['size'];
$orig_name = $file['name'];

// Validasi error upload
if ($error !== UPLOAD_ERR_OK) {
    header('Location: dashboard_user.php?status=error&msg=Upload gagal (kode error: ' . $error . ')');
    exit;
}

// Validasi ukuran file (maks 5MB)
$maks_size = 5 * 1024 * 1024; // 5 MB
if ($size > $maks_size) {
    header('Location: dashboard_user.php?status=error&msg=Ukuran file maksimal 5MB.');
    exit;
}

// Validasi tipe file (hanya jpg, png, webp)
$allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $tmp_name);
finfo_close($finfo);

if (!in_array($mime, $allowed_types)) {
    header('Location: dashboard_user.php?status=error&msg=Format file harus JPG, PNG, atau WEBP.');
    exit;
}

// Generate nama file unik agar tidak bentrok
$ext       = pathinfo($orig_name, PATHINFO_EXTENSION);
$nama_file = 'foto_' . $user_id . '_' . time() . '.' . strtolower($ext);

// Folder tujuan (relatif dari file ini: user/ → naik ke root → uploads/)
$folder_upload = '../uploads/';

// Buat folder uploads jika belum ada
if (!is_dir($folder_upload)) {
    mkdir($folder_upload, 0755, true);
}

$tujuan = $folder_upload . $nama_file;

// Pindahkan file dari tmp ke folder uploads
if (!move_uploaded_file($tmp_name, $tujuan)) {
    header('Location: dashboard_user.php?status=error&msg=Gagal menyimpan file.');
    exit;
}

// Simpan data ke database
$stmt = $koneksi->prepare("INSERT INTO foto_pengunjung (user_id, nama_file, caption, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iss", $user_id, $nama_file, $caption);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: dashboard_user.php?status=success&msg=Foto terupload! Menunggu persetujuan admin.');
} else {
    unlink($tujuan);
    $stmt->close();
    header('Location: dashboard_user.php?status=error&msg=Gagal menyimpan ke database.');
}
exit;
