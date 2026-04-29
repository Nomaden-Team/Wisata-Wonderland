<?php
// config/koneksi.php

$host = "localhost";      // Nama host (biasanya localhost)
$user = "root";           // Username database default XAMPP
$pass = "1";               // Password default XAMPP (kosong)
$db   = "wonderland";  // Nama database yang kamu buat di phpMyAdmin

// Membuat koneksi ke database
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek apakah koneksi berhasil atau gagal
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Opsional: Untuk memastikan karakter unik/simbol tampil dengan benar
mysqli_set_charset($koneksi, "utf8mb4");
?>