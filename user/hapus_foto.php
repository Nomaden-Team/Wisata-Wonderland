<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../koneksi.php';

$user_id = $_SESSION['user_id'];
$foto_id = intval($_POST['foto_id'] ?? 0);

if ($foto_id <= 0) {
    header('Location: dashboard_user.php?status=error&msg=ID foto tidak valid.');
    exit;
}

// Ambil data foto — pastikan milik user yang sedang login
$stmt = $conn->prepare("SELECT nama_file FROM foto_pengunjung WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $foto_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$foto   = $result->fetch_assoc();
$stmt->close();

if (!$foto) {
    header('Location: dashboard_user.php?status=error&msg=Foto tidak ditemukan.');
    exit;
}

// Hapus file fisik dari folder uploads
$path_file = '../uploads/' . $foto['nama_file'];
if (file_exists($path_file)) {
    unlink($path_file);
}

// Hapus record dari database
$stmt = $conn->prepare("DELETE FROM foto_pengunjung WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $foto_id, $user_id);
$stmt->execute();
$stmt->close();

header('Location: dashboard_user.php?status=success');
exit;
