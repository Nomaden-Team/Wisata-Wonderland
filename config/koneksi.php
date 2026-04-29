
<?php
$host = "sql304.infinityfree.com";
$user = "if0_41787408";
$pass = "yP10rIBMqMK3V";
$db   = "if0_41787408_wonderland";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");
?>