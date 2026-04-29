<?php
session_start();
include 'config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.php");
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
    session_unset();
    session_destroy();
    echo "<script>alert('Permintaan tidak valid (CSRF). Silakan coba lagi.'); window.location='register.php';</script>";
    exit;
}
unset($_SESSION['csrf_token']);

$nama            = trim($_POST['nama'] ?? '');
$email           = trim($_POST['email'] ?? '');
$no_telp         = trim($_POST['no_telp'] ?? '');
$password        = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($nama) || empty($email) || empty($no_telp) || empty($password)) {
    echo "<script>alert('Semua field wajib diisi!'); window.location='register.php';</script>";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Format email tidak valid!'); window.location='register.php';</script>";
    exit;
}

if (strlen($password) < 8) {
    echo "<script>alert('Password minimal 8 karakter!'); window.location='register.php';</script>";
    exit;
}

if ($password !== $confirm_password) {
    echo "<script>alert('Konfirmasi password tidak cocok!'); window.location='register.php';</script>";
    exit;
}

$stmt = $koneksi->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    echo "<script>alert('Email sudah digunakan! Silakan gunakan email lain.'); window.location='register.php';</script>";
    exit;
}
$stmt->close();

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$stmt = $koneksi->prepare("INSERT INTO users (nama, email, no_telp, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nama, $email, $no_telp, $hashed_password);

if ($stmt->execute()) {
    $stmt->close();
    echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='login.php';</script>";
} else {
    $stmt->close();
    echo "<script>alert('Terjadi kesalahan saat mendaftar. Coba lagi.'); window.location='register.php';</script>";
}
