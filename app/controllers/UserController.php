

<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/ReservasiModel.php';

class UserController
{
    private UserModel $userModel;
    private ReservasiModel $reservasiModel;
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
        $this->userModel = new UserModel($db);
        $this->reservasiModel = new ReservasiModel($db);
    }

    private function requireLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        if (isset($_SESSION['role']) && $_SESSION['role'] !== 'user') {
            header("Location: index.php?page=admin_dashboard");
            exit;
        }
    }

    public function uploadFotoPage(): void
    {
        $this->requireLogin();

        $userId = (int) $_SESSION['user_id'];
        $nama = $_SESSION['nama'] ?? 'Pengunjung';
        $email = $_SESSION['email'] ?? '';
        $fotos = $this->userModel->getUserPhotos($userId);

        require __DIR__ . '/../views/user/upload_foto.php';
    }

    public function reservasiPage(): void
    {
        $this->requireLogin();

        $userId = (int) $_SESSION['user_id'];
        $nama = $_SESSION['nama'] ?? 'Pengunjung';
        $email = $_SESSION['email'] ?? '';
        $fotos = $this->userModel->getUserPhotos($userId);

        $jadwalTerjadwal = $this->reservasiModel->getJadwalTerjadwal();
        $reservasis = $this->reservasiModel->getByUserId($userId);

        require __DIR__ . '/../views/user/reservasi.php';
    }

    public function dashboard(): void
    {
        $this->requireLogin();

        $userId = (int) $_SESSION['user_id'];
        $nama = $_SESSION['nama'] ?? 'Pengunjung';
        $email = $_SESSION['email'] ?? '';
        $fotos = $this->userModel->getUserPhotos($userId);
        $reservasis = $this->reservasiModel->getByUserId($userId);

        require __DIR__ . '/../views/user/dashboard.php';
    }

    private function resolveRedirectAfterFoto(): string
    {
        $from = $_POST['from'] ?? $_GET['from'] ?? '';

        if ($from === 'upload_foto_page' || $from === 'user_upload_foto_page') {
            return 'index.php?page=user_upload_foto_page';
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        if ($referer && strpos($referer, 'user_upload_foto_page') !== false) {
            return 'index.php?page=user_upload_foto_page';
        }

        return 'index.php?page=user_dashboard';
    }

    public function uploadFoto(): void
    {
        $this->requireLogin();

        $redirectBase = $this->resolveRedirectAfterFoto();

        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            header('Location: ' . $redirectBase . '&status=error&msg=' . urlencode('Tidak ada file dipilih.'));
            exit;
        }

        $file = $_FILES['foto'];
        $error = $file['error'];
        $tmpName = $file['tmp_name'];
        $size = $file['size'];
        $origName = $file['name'];

        if ($error !== UPLOAD_ERR_OK) {
            header('Location: ' . $redirectBase . '&status=error&msg=' . urlencode('Upload gagal.'));
            exit;
        }

        $maksSize = 5 * 1024 * 1024;

        if ($size > $maksSize) {
            header('Location: ' . $redirectBase . '&status=error&msg=' . urlencode('Ukuran file maksimal 5MB.'));
            exit;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmpName);
        finfo_close($finfo);

        if (!in_array($mime, $allowedTypes, true)) {
            header('Location: ' . $redirectBase . '&status=error&msg=' . urlencode('Format file harus JPG, PNG, atau WEBP.'));
            exit;
        }

        $userId = (int) $_SESSION['user_id'];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $namaFile = 'foto_' . $userId . '_' . time() . '.' . $ext;

        $folderUpload = __DIR__ . '/../../uploads/';

        if (!is_dir($folderUpload)) {
            mkdir($folderUpload, 0755, true);
        }

        $tujuan = $folderUpload . $namaFile;

        if (!move_uploaded_file($tmpName, $tujuan)) {
            header('Location: ' . $redirectBase . '&status=error&msg=' . urlencode('Gagal menyimpan file.'));
            exit;
        }

        $caption = trim($_POST['caption'] ?? '');

        if ($this->userModel->addUserPhoto($userId, $namaFile, $caption)) {
            header('Location: ' . $redirectBase . '&status=success&msg=' . urlencode('Foto berhasil diunggah. Menunggu persetujuan admin.'));
            exit;
        }

        @unlink($tujuan);

        header('Location: ' . $redirectBase . '&status=error&msg=' . urlencode('Gagal menyimpan ke database.'));
        exit;
    }

    public function submitReservasi(): void
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=user_reservasi');
            exit;
        }

        $userId = (int) $_SESSION['user_id'];
        $namaKegiatan = trim($_POST['nama_kegiatan'] ?? '');
        $tanggal = $_POST['tanggal'] ?? '';
        $jumlah = (int) ($_POST['jumlah_peserta'] ?? 1);
        $keterangan = trim($_POST['keterangan'] ?? '');

        if ($namaKegiatan === '' || $tanggal === '' || $jumlah < 1 || $jumlah > 500) {
            header('Location: index.php?page=user_reservasi&status=error&msg=' . urlencode('Data reservasi tidak valid.'));
            exit;
        }

        $today = date('Y-m-d');

        if ($tanggal < $today) {
            header('Location: index.php?page=user_reservasi&status=error&msg=' . urlencode('Tanggal kunjungan tidak boleh lebih lama dari hari ini.'));
            exit;
        }

        $kode = $this->reservasiModel->createByUser(
            $userId,
            $namaKegiatan,
            $tanggal,
            $jumlah,
            $keterangan
        );

        if ($kode === null) {
            header('Location: index.php?page=user_reservasi&status=error&msg=' . urlencode('Reservasi gagal disimpan. Coba lagi.'));
            exit;
        }

        header("Location: index.php?page=user_reservasi&status=success&kode=" . urlencode($kode));
        exit;
    }

    public function hapusFoto(): void
    {
        $this->requireLogin();

        $redirectBase = $this->resolveRedirectAfterFoto();

        $userId = (int) $_SESSION['user_id'];
        $fotoId = (int) ($_POST['foto_id'] ?? 0);

        if ($fotoId <= 0) {
            header('Location: ' . $redirectBase . '&status=error&msg=' . urlencode('ID foto tidak valid.'));
            exit;
        }

        $foto = $this->userModel->getUserPhoto($fotoId, $userId);

        if (!$foto) {
            header('Location: ' . $redirectBase . '&status=error&msg=' . urlencode('Foto tidak ditemukan.'));
            exit;
        }

        $pathFile = __DIR__ . '/../../uploads/' . $foto['nama_file'];

        if (file_exists($pathFile)) {
            @unlink($pathFile);
        }

        $this->userModel->deleteUserPhoto($fotoId, $userId);

        header('Location: ' . $redirectBase . '&status=success&msg=' . urlencode('Foto berhasil dihapus.'));
        exit;
    }
}