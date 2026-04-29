<?php
session_start();
include 'config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
    session_unset();
    session_destroy();
    echo "<script>alert('Permintaan tidak valid (CSRF). Silakan coba lagi.'); window.location='login.php';</script>";
    exit;
}

unset($_SESSION['csrf_token']);

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo "<script>alert('Email dan password wajib diisi!'); window.location='login.php';</script>";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Format email tidak valid!'); window.location='login.php';</script>";
    exit;
}

$stmt = $koneksi->prepare("SELECT id, nama, email, password FROM admin WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res_admin = $stmt->get_result();

if ($res_admin && $res_admin->num_rows > 0) {
    $admin = $res_admin->fetch_assoc();
    $stmt->close();

    if (password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['nama']    = $admin['nama'];
        $_SESSION['email']   = $admin['email'];
        $_SESSION['role']    = 'admin';
        header("Location: admin/dashboard.php");
        exit;
    }
} else {
    $stmt->close();
}

$stmt = $koneksi->prepare("SELECT id, nama, email, password FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res_user = $stmt->get_result();

if ($res_user && $res_user->num_rows > 0) {
    $user = $res_user->fetch_assoc();
    $stmt->close();

    if (password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['email']   = $user['email'];
        $_SESSION['role']    = 'user';
        header("Location: user/dashboard_user.php");
        exit;
    }
} else {
    $stmt->close();
}

echo "<script>alert('Login Gagal! Email atau Password salah.'); window.location='login.php';</script>";
