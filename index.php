<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/koneksi.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/WahanaController.php';
require_once __DIR__ . '/app/controllers/UserController.php';
require_once __DIR__ . '/app/controllers/AdminController.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        (new WahanaController($koneksi))->home();
        break;
    case 'detail_wahana':
        (new WahanaController($koneksi))->detail();
        break;
    case 'login':
        (new AuthController($koneksi))->showLogin();
        break;
    case 'login_process':
        (new AuthController($koneksi))->login();
        break;
    case 'register':
        (new AuthController($koneksi))->showRegister();
        break;
    case 'register_process':
        (new AuthController($koneksi))->register();
        break;
    case 'logout':
        (new AuthController($koneksi))->logout();
        break;
    case 'user_dashboard':
        (new UserController($koneksi))->dashboard();
        break;
    case 'user_upload_foto':
        (new UserController($koneksi))->uploadFoto();
        break;
    case 'user_hapus_foto':
        (new UserController($koneksi))->hapusFoto();
        break;
    case 'user_upload_foto_page':
        (new UserController($koneksi))->uploadFotoPage();
        break;
    case 'user_submit_reservasi':
        (new UserController($koneksi))->submitReservasi();
        break;
    case 'user_reservasi':
        (new UserController($koneksi))->reservasiPage();
        break;
    case 'admin_dashboard':
        (new AdminController($koneksi))->dashboard();
        break;
    case 'admin_wahana':
        (new AdminController($koneksi))->wahana();
        break;
    case 'admin_fasilitas':
        (new AdminController($koneksi))->fasilitas();
        break;
    case 'admin_reservasi':
        (new AdminController($koneksi))->reservasi();
        break;
    case 'admin_ulasan':
        (new AdminController($koneksi))->ulasan();
        break;
    case 'admin_foto_user':
        (new AdminController($koneksi))->fotoUser();
        break;
    case 'admin_pricelist':
        (new AdminController($koneksi))->pricelist();
        break;
    case 'pricelist':
        (new WahanaController($koneksi))->pricelist();
        break;
    default:
        http_response_code(404);
        echo "404 - Halaman tidak ditemukan";
        break;
}
