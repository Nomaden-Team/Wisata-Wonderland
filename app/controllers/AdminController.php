<?php

require_once __DIR__ . '/../models/ReservasiModel.php';
require_once __DIR__ . '/../models/UlasanModel.php';

class AdminController
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    private function requireAdmin(): void
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header("Location: index.php?page=login");
            exit;
        }
    }

    private function fetchAllAssoc(string $sql, string $types = '', array $params = []): array
    {
        if ($types === '' || empty($params)) {
            $result = $this->db->query($sql);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return $rows;
    }

   public function dashboard(): void
{
    $this->requireAdmin();
    $active_page = 'dashboard';

    $getInt = function (string $sql): int {
        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_row();

        return (int) ($row[0] ?? 0);
    };

    $getRows = function (string $sql): array {
        $result = $this->db->query($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    };

    $total_reservasi = $getInt("SELECT COUNT(*) FROM reservasi");
    $reservasi_pending = $getInt("SELECT COUNT(*) FROM reservasi WHERE status = 'pending'");
    $wahana_aktif = $getInt("SELECT COUNT(*) FROM wahana WHERE status = 'aktif'");
    $wahana_total = $getInt("SELECT COUNT(*) FROM wahana");
    $ulasan_pending = $getInt("SELECT COUNT(*) FROM ulasan WHERE status = 'pending'");

    $res_terbaru = $getRows(
        "SELECT *
         FROM reservasi
         ORDER BY created_at DESC
         LIMIT 5"
    );

    $reservasi_perlu_dicek = $getRows(
        "SELECT id, nama_kegiatan, jenis_kegiatan, tanggal, status, created_at
         FROM reservasi
         WHERE status = 'pending'
         ORDER BY created_at DESC
         LIMIT 3"
    );

    $ulasan_perlu_dicek = $getRows(
        "SELECT id, nama_user, wahana_name, rating, status, created_at
         FROM ulasan
         WHERE status = 'pending'
         ORDER BY created_at DESC
         LIMIT 3"
    );

    $tindak_lanjut = [];

    foreach ($reservasi_perlu_dicek as $item) {
        $tindak_lanjut[] = [
            'jenis' => 'Reservasi',
            'judul' => $item['nama_kegiatan'] ?? 'Reservasi baru',
            'meta' => trim(($item['jenis_kegiatan'] ?? '-') . ' • ' . ($item['tanggal'] ?? '-')),
            'url' => 'index.php?page=admin_reservasi',
            'icon' => 'far fa-calendar-alt',
        ];
    }

    foreach ($ulasan_perlu_dicek as $item) {
        $rating = (int) ($item['rating'] ?? 0);

        $tindak_lanjut[] = [
            'jenis' => 'Ulasan',
            'judul' => $item['nama_user'] ?? 'Ulasan baru',
            'meta' => 'Rating ' . $rating . '/5' . (!empty($item['wahana_name']) ? ' • ' . $item['wahana_name'] : ''),
            'url' => 'index.php?page=admin_ulasan',
            'icon' => 'far fa-star',
        ];
    }

    require __DIR__ . '/../views/admin/dashboard.php';
}

   public function wahana(): void
{
    $this->requireAdmin();
    $active_page = 'wahana';

    $kategoriList = [
        'Wahana Air',
        'Wahana Anak',
        'Wahana Keluarga',
        'Transportasi Wahana',
        'Area Wisata'
    ];

    $statusList = ['aktif', 'nonaktif'];

    $redirectAdminWahana = function (): void {
        header("Location: index.php?page=admin_wahana");
        exit;
    };

    $isValidTime = function (string $time): bool {
        return (bool) preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time);
    };

    $uploadFoto = function (string $fotoLama = '') use ($redirectAdminWahana): string {
        if (empty($_FILES['foto']['name'])) {
            return $fotoLama;
        }

        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Foto gagal diunggah.';
            $redirectAdminWahana();
        }

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif'
        ];

        $tmpName = $_FILES['foto']['tmp_name'];
        $mimeType = mime_content_type($tmpName);

        if (!array_key_exists($mimeType, $allowedMimeTypes)) {
            $_SESSION['error'] = 'Format foto tidak valid. Gunakan JPG, PNG, WEBP, atau GIF.';
            $redirectAdminWahana();
        }

        if ((int) $_FILES['foto']['size'] > 2 * 1024 * 1024) {
            $_SESSION['error'] = 'Ukuran foto maksimal 2MB.';
            $redirectAdminWahana();
        }

        $folder = __DIR__ . '/../../uploads/wahana/';

        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $extension = $allowedMimeTypes[$mimeType];
        $fileName = 'wahana_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $targetPath = $folder . $fileName;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            $_SESSION['error'] = 'Foto gagal disimpan.';
            $redirectAdminWahana();
        }

        return $fileName;
    };

    $action = $_POST['action'] ?? '';

    if ($action === 'tambah') {
        $nama = trim($_POST['nama'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $jamBuka = trim($_POST['jam_buka'] ?? '');
        $jamTutup = trim($_POST['jam_tutup'] ?? '');
        $status = trim($_POST['status'] ?? 'aktif');

        /*
         * Harga wahana tidak dipakai di konsep saat ini.
         * Kolom harga tetap dipertahankan di database sebagai cadangan pengembangan.
         */
        $harga = 0;

        if ($nama === '') {
            $_SESSION['error'] = 'Nama wahana wajib diisi.';
            $redirectAdminWahana();
        }

        if (!in_array($kategori, $kategoriList, true)) {
            $_SESSION['error'] = 'Kategori wahana tidak valid.';
            $redirectAdminWahana();
        }

        if (!$isValidTime($jamBuka) || !$isValidTime($jamTutup)) {
            $_SESSION['error'] = 'Jam operasional tidak valid.';
            $redirectAdminWahana();
        }

        if ($jamBuka >= $jamTutup) {
            $_SESSION['error'] = 'Jam tutup harus lebih besar dari jam buka.';
            $redirectAdminWahana();
        }

        if (!in_array($status, $statusList, true)) {
            $_SESSION['error'] = 'Status wahana tidak valid.';
            $redirectAdminWahana();
        }

        $jamOperasional = $jamBuka . ' - ' . $jamTutup;
        $gambar = $uploadFoto('');

        $stmt = $this->db->prepare("
            INSERT INTO wahana 
                (nama, deskripsi, kategori, harga, jam_operasional, status, foto)
            VALUES 
                (?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            $_SESSION['error'] = 'Gagal menyiapkan query tambah wahana.';
            $redirectAdminWahana();
        }

        $stmt->bind_param(
            "sssisss",
            $nama,
            $deskripsi,
            $kategori,
            $harga,
            $jamOperasional,
            $status,
            $gambar
        );

        if (!$stmt->execute()) {
            $stmt->close();
            $_SESSION['error'] = 'Wahana gagal ditambahkan.';
            $redirectAdminWahana();
        }

        $stmt->close();

        $_SESSION['success'] = 'Wahana berhasil ditambahkan.';
        $redirectAdminWahana();
    }

    if ($action === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $nama = trim($_POST['nama'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $jamBuka = trim($_POST['jam_buka'] ?? '');
        $jamTutup = trim($_POST['jam_tutup'] ?? '');
        $status = trim($_POST['status'] ?? 'aktif');
        $fotoLama = trim($_POST['foto_lama'] ?? '');

        /*
         * Harga wahana tidak dipakai di konsep saat ini.
         * Kolom harga tetap dipertahankan di database sebagai cadangan pengembangan.
         */
        $harga = 0;

        if ($id <= 0) {
            $_SESSION['error'] = 'Data wahana tidak valid.';
            $redirectAdminWahana();
        }

        if ($nama === '') {
            $_SESSION['error'] = 'Nama wahana wajib diisi.';
            $redirectAdminWahana();
        }

        if (!in_array($kategori, $kategoriList, true)) {
            $_SESSION['error'] = 'Kategori wahana tidak valid.';
            $redirectAdminWahana();
        }

        if (!$isValidTime($jamBuka) || !$isValidTime($jamTutup)) {
            $_SESSION['error'] = 'Jam operasional tidak valid.';
            $redirectAdminWahana();
        }

        if ($jamBuka >= $jamTutup) {
            $_SESSION['error'] = 'Jam tutup harus lebih besar dari jam buka.';
            $redirectAdminWahana();
        }

        if (!in_array($status, $statusList, true)) {
            $_SESSION['error'] = 'Status wahana tidak valid.';
            $redirectAdminWahana();
        }

        $jamOperasional = $jamBuka . ' - ' . $jamTutup;
        $gambar = $uploadFoto($fotoLama);

        $stmt = $this->db->prepare("
            UPDATE wahana 
            SET 
                nama = ?, 
                deskripsi = ?, 
                kategori = ?, 
                harga = ?, 
                jam_operasional = ?, 
                status = ?, 
                foto = ?
            WHERE id = ?
        ");

        if (!$stmt) {
            $_SESSION['error'] = 'Gagal menyiapkan query edit wahana.';
            $redirectAdminWahana();
        }

        $stmt->bind_param(
            "sssisssi",
            $nama,
            $deskripsi,
            $kategori,
            $harga,
            $jamOperasional,
            $status,
            $gambar,
            $id
        );

        if (!$stmt->execute()) {
            $stmt->close();
            $_SESSION['error'] = 'Wahana gagal diperbarui.';
            $redirectAdminWahana();
        }

        $stmt->close();

        $_SESSION['success'] = 'Wahana berhasil diperbarui.';
        $redirectAdminWahana();
    }

    if ($action === 'hapus') {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'Data wahana tidak valid.';
            $redirectAdminWahana();
        }

        $stmt = $this->db->prepare("DELETE FROM wahana WHERE id = ?");

        if (!$stmt) {
            $_SESSION['error'] = 'Gagal menyiapkan query hapus wahana.';
            $redirectAdminWahana();
        }

        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            $stmt->close();
            $_SESSION['error'] = 'Wahana gagal dihapus.';
            $redirectAdminWahana();
        }

        $stmt->close();

        $_SESSION['success'] = 'Wahana berhasil dihapus.';
        $redirectAdminWahana();
    }

    $search = trim($_GET['search'] ?? '');
    $sql = "SELECT * FROM wahana";
    $types = '';
    $params = [];

    if ($search !== '') {
        $sql .= " WHERE nama LIKE ? OR kategori LIKE ?";
        $types = 'ss';
        $like = "%{$search}%";
        $params[] = $like;
        $params[] = $like;
    }

    $sql .= " ORDER BY nama";

    $wahanas = $this->fetchAllAssoc($sql, $types, $params);

    require __DIR__ . '/../views/admin/wahana.php';
}

    public function fasilitas(): void
    {
        $this->requireAdmin();
        $active_page = 'fasilitas';

        $action = $_POST['action'] ?? '';

        if ($action === 'tambah') {
            $stmt = $this->db->prepare("INSERT INTO fasilitas (nama, ikon, deskripsi, status) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $_POST['nama'], $_POST['ikon'], $_POST['deskripsi'], $_POST['status']);
            $stmt->execute();
            $stmt->close();

            $_SESSION['success'] = 'Fasilitas berhasil ditambahkan.';
            header("Location: index.php?page=admin_fasilitas");
            exit;
        }

        if ($action === 'edit') {
            $stmt = $this->db->prepare("UPDATE fasilitas SET nama=?, ikon=?, deskripsi=?, status=? WHERE id=?");
            $stmt->bind_param("ssssi", $_POST['nama'], $_POST['ikon'], $_POST['deskripsi'], $_POST['status'], $_POST['id']);
            $stmt->execute();
            $stmt->close();

            $_SESSION['success'] = 'Fasilitas berhasil diperbarui.';
            header("Location: index.php?page=admin_fasilitas");
            exit;
        }

        if ($action === 'hapus') {
            $stmt = $this->db->prepare("DELETE FROM fasilitas WHERE id=?");
            $stmt->bind_param("i", $_POST['id']);
            $stmt->execute();
            $stmt->close();

            header('Location: index.php?page=admin_fasilitas&ok=hapus');
            exit;
        }

        $search = trim($_GET['search'] ?? '');
        $sql = "SELECT * FROM fasilitas";
        $types = '';
        $params = [];

        if ($search !== '') {
            $sql .= " WHERE nama LIKE ?";
            $types = 's';
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY id";

        $fasilitas_list = $this->fetchAllAssoc($sql, $types, $params);

        require __DIR__ . '/../views/admin/fasilitas.php';
    }

    public function reservasi(): void
    {
        $this->requireAdmin();
        $active_page = 'reservasi';

        $model = new ReservasiModel($this->db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $redirectOk = '';

            if ($action === 'tambah') {
                $userId = !empty($_POST['user_id']) ? (int) $_POST['user_id'] : null;
                $status = $_POST['status'] ?? ReservasiModel::STATUS_TERJADWAL;

                if (!ReservasiModel::isValidStatus($status)) {
                    $status = ReservasiModel::STATUS_TERJADWAL;
                }

                $kodeBooking = strtoupper(substr(md5(uniqid(($userId ?? 0) . time(), true)), 0, 10));

                $stmt = $this->db->prepare(
                    "INSERT INTO reservasi 
                        (user_id, nama_kegiatan, jenis_kegiatan, tanggal, jam_mulai, jam_selesai, jumlah_peserta, keterangan, status, kode_booking, created_at)
                     VALUES 
                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
                );

                $stmt->bind_param(
                    "isssssisss",
                    $userId,
                    $_POST['nama_kegiatan'],
                    $_POST['jenis_kegiatan'],
                    $_POST['tanggal'],
                    $_POST['jam_mulai'],
                    $_POST['jam_selesai'],
                    $_POST['jumlah_peserta'],
                    $_POST['keterangan'],
                    $status,
                    $kodeBooking
                );

                $stmt->execute();
                $stmt->close();

                $redirectOk = 'tambah';
            }

            if ($action === 'edit') {
                $userId = !empty($_POST['user_id']) ? (int) $_POST['user_id'] : null;
                $status = $_POST['status'] ?? ReservasiModel::STATUS_PENDING;

                if (!ReservasiModel::isValidStatus($status)) {
                    $status = ReservasiModel::STATUS_PENDING;
                }

                $stmt = $this->db->prepare(
                    "UPDATE reservasi 
                     SET user_id=?, nama_kegiatan=?, jenis_kegiatan=?, tanggal=?, jam_mulai=?, jam_selesai=?, jumlah_peserta=?, keterangan=?, status=? 
                     WHERE id=?"
                );

                $stmt->bind_param(
                    "isssssissi",
                    $userId,
                    $_POST['nama_kegiatan'],
                    $_POST['jenis_kegiatan'],
                    $_POST['tanggal'],
                    $_POST['jam_mulai'],
                    $_POST['jam_selesai'],
                    $_POST['jumlah_peserta'],
                    $_POST['keterangan'],
                    $status,
                    $_POST['id']
                );

                $stmt->execute();
                $stmt->close();

                $redirectOk = 'edit';
            }

            if ($action === 'hapus') {
                $model->delete((int) $_POST['id']);
                $redirectOk = 'hapus';
            }

            if ($action === 'update_status') {
                $status = $_POST['status'] ?? '';

                if ($model->updateStatus((int) $_POST['id'], $status)) {
                    $redirectOk = $status === ReservasiModel::STATUS_TERJADWAL ? 'approve' : 'status';
                }
            }

            $location = 'index.php?page=admin_reservasi';

            if ($redirectOk !== '') {
                $location .= '&ok=' . urlencode($redirectOk);
            }

            header('Location: ' . $location);
            exit;
        }

        $search = trim($_GET['search'] ?? '');
        $filter_status = trim($_GET['status'] ?? '');

        if ($filter_status !== '' && !ReservasiModel::isValidStatus($filter_status)) {
            $filter_status = '';
        }

        $reservasis = $model->getAll($search, $filter_status);
        $wahana_list = $this->db->query("SELECT id, nama FROM wahana WHERE status='aktif' ORDER BY nama")->fetch_all(MYSQLI_ASSOC);
        $user_list = $this->db->query("SELECT id, nama, email FROM users ORDER BY nama")->fetch_all(MYSQLI_ASSOC);

        require __DIR__ . '/../views/admin/reservasi.php';
    }

    public function ulasan(): void
    {
        $this->requireAdmin();
        $active_page = 'ulasan';

        $model = new UlasanModel($this->db);

        $redirectUlasan = function (): void {
            header('Location: index.php?page=admin_ulasan');
            exit;
        };

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $id = (int) ($_POST['id'] ?? 0);

            if ($id <= 0 && $action !== '') {
                $_SESSION['error'] = 'Data ulasan tidak valid.';
                $redirectUlasan();
            }

            if ($action === 'hapus') {
                if ($model->delete($id)) {
                    $_SESSION['success'] = 'Ulasan berhasil dihapus.';
                } else {
                    $_SESSION['error'] = 'Ulasan gagal dihapus.';
                }

                $redirectUlasan();
            }

            if ($action === 'approve' || $action === 'publish') {
                if ($model->updateStatus($id, 'approved')) {
                    $_SESSION['success'] = 'Ulasan berhasil dipublikasikan.';
                } else {
                    $_SESSION['error'] = 'Status ulasan gagal diperbarui.';
                }

                $redirectUlasan();
            }

            if ($action === 'hide') {
                /*
                 * Database ulasan saat ini hanya aman memakai status "pending" dan "approved".
                 * Hide di UI dipetakan ke "pending" agar ulasan tidak tampil di publik,
                 * karena landing page hanya menampilkan status "approved".
                 */
                if ($model->updateStatus($id, 'pending')) {
                    $_SESSION['success'] = 'Ulasan berhasil disembunyikan.';
                } else {
                    $_SESSION['error'] = 'Status ulasan gagal diperbarui.';
                }

                $redirectUlasan();
            }

            if ($action === 'update_status') {
                $status = $_POST['status'] ?? '';

                if ($model->updateStatus($id, $status)) {
                    $_SESSION['success'] = 'Status ulasan berhasil diperbarui.';
                } else {
                    $_SESSION['error'] = 'Status ulasan tidak valid.';
                }

                $redirectUlasan();
            }

            $redirectUlasan();
        }

        $search = trim($_GET['search'] ?? '');
        $filter = trim($_GET['status'] ?? '');

        $allowedFilters = ['pending', 'approved'];

        if (!in_array($filter, $allowedFilters, true)) {
            $filter = '';
        }

        $allUlasans = $model->getAll();

        $total = count($allUlasans);
        $pub = count(array_filter($allUlasans, fn($u) => ($u['status'] ?? '') === 'approved'));
        $pend = count(array_filter($allUlasans, fn($u) => ($u['status'] ?? '') === 'pending'));

        $approvedRatings = array_column(
            array_filter($allUlasans, fn($u) => ($u['status'] ?? '') === 'approved'),
            'rating'
        );

        $avg = count($approvedRatings) > 0
            ? round(array_sum($approvedRatings) / count($approvedRatings), 1)
            : 0;

        $ulasans = array_values(array_filter($allUlasans, function ($u) use ($search, $filter) {
            $status = $u['status'] ?? 'pending';

            if ($filter !== '' && $status !== $filter) {
                return false;
            }

            if ($search === '') {
                return true;
            }

            $haystack = strtolower(
                ($u['nama_user'] ?? '') . ' ' .
                ($u['email_user'] ?? '') . ' ' .
                ($u['wahana_name'] ?? '') . ' ' .
                ($u['ulasan'] ?? '')
            );

            return str_contains($haystack, strtolower($search));
        }));

        require __DIR__ . '/../views/admin/ulasan.php';
    }

    public function pricelist(): void
    {
        $this->requireAdmin();
        $active_page = 'pricelist';

        $redirectPricelist = function (): void {
            header('Location: index.php?page=admin_pricelist');
            exit;
        };

        $kategoriList = ['Tiket Masuk', 'Tiket Terusan', 'Promo', 'Parking', 'Lainnya'];
        $statusList = ['aktif', 'nonaktif'];

        $normalizePrice = function ($value): int {
            if ($value === '' || $value === null) {
                return 0;
            }

            return max(0, min(1000000, (int) $value));
        };

        $action = $_POST['action'] ?? '';

        if ($action === 'tambah') {
            $nama = trim($_POST['nama'] ?? '');
            $deskripsi = trim($_POST['deskripsi'] ?? '');
            $benefit = trim($_POST['benefit'] ?? '');
            $kategori = trim($_POST['kategori'] ?? 'Tiket Masuk');
            $status = trim($_POST['status'] ?? 'aktif');
            $harga_normal = $normalizePrice($_POST['harga_normal'] ?? 0);
            $harga_promo = $normalizePrice($_POST['harga_promo'] ?? 0);

            if ($nama === '') {
                $_SESSION['error'] = 'Nama item harga wajib diisi.';
                $redirectPricelist();
            }

            if (!in_array($kategori, $kategoriList, true)) {
                $_SESSION['error'] = 'Kategori item harga tidak valid.';
                $redirectPricelist();
            }

            if (!in_array($status, $statusList, true)) {
                $_SESSION['error'] = 'Status item harga tidak valid.';
                $redirectPricelist();
            }

            $stmt = $this->db->prepare(
                "INSERT INTO pricelist (nama, deskripsi, benefit, kategori, harga_normal, harga_promo, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            if (!$stmt) {
                $_SESSION['error'] = 'Gagal menyiapkan query tambah harga.';
                $redirectPricelist();
            }

            $stmt->bind_param(
                "ssssiis",
                $nama,
                $deskripsi,
                $benefit,
                $kategori,
                $harga_normal,
                $harga_promo,
                $status
            );

            if (!$stmt->execute()) {
                $stmt->close();
                $_SESSION['error'] = 'Item harga gagal ditambahkan.';
                $redirectPricelist();
            }

            $stmt->close();

            $_SESSION['success'] = 'Item harga berhasil ditambahkan.';
            $redirectPricelist();
        }

        if ($action === 'edit') {
            $id = (int) ($_POST['id'] ?? 0);
            $nama = trim($_POST['nama'] ?? '');
            $deskripsi = trim($_POST['deskripsi'] ?? '');
            $benefit = trim($_POST['benefit'] ?? '');
            $kategori = trim($_POST['kategori'] ?? 'Tiket Masuk');
            $status = trim($_POST['status'] ?? 'aktif');
            $harga_normal = $normalizePrice($_POST['harga_normal'] ?? 0);
            $harga_promo = $normalizePrice($_POST['harga_promo'] ?? 0);

            if ($id <= 0) {
                $_SESSION['error'] = 'Data item harga tidak valid.';
                $redirectPricelist();
            }

            if ($nama === '') {
                $_SESSION['error'] = 'Nama item harga wajib diisi.';
                $redirectPricelist();
            }

            if (!in_array($kategori, $kategoriList, true)) {
                $_SESSION['error'] = 'Kategori item harga tidak valid.';
                $redirectPricelist();
            }

            if (!in_array($status, $statusList, true)) {
                $_SESSION['error'] = 'Status item harga tidak valid.';
                $redirectPricelist();
            }

            $stmt = $this->db->prepare(
                "UPDATE pricelist
                 SET nama = ?, deskripsi = ?, benefit = ?, kategori = ?, harga_normal = ?, harga_promo = ?, status = ?
                 WHERE id = ?"
            );

            if (!$stmt) {
                $_SESSION['error'] = 'Gagal menyiapkan query edit harga.';
                $redirectPricelist();
            }

            $stmt->bind_param(
                "ssssiisi",
                $nama,
                $deskripsi,
                $benefit,
                $kategori,
                $harga_normal,
                $harga_promo,
                $status,
                $id
            );

            if (!$stmt->execute()) {
                $stmt->close();
                $_SESSION['error'] = 'Item harga gagal diperbarui.';
                $redirectPricelist();
            }

            $stmt->close();

            $_SESSION['success'] = 'Item harga berhasil diperbarui.';
            $redirectPricelist();
        }

        if ($action === 'hapus') {
            $id = (int) ($_POST['id'] ?? 0);

            if ($id <= 0) {
                $_SESSION['error'] = 'Data item harga tidak valid.';
                $redirectPricelist();
            }

            $stmt = $this->db->prepare("DELETE FROM pricelist WHERE id = ?");

            if (!$stmt) {
                $_SESSION['error'] = 'Gagal menyiapkan query hapus harga.';
                $redirectPricelist();
            }

            $stmt->bind_param("i", $id);

            if (!$stmt->execute()) {
                $stmt->close();
                $_SESSION['error'] = 'Item harga gagal dihapus.';
                $redirectPricelist();
            }

            $stmt->close();

            $_SESSION['success'] = 'Item harga berhasil dihapus.';
            $redirectPricelist();
        }

        $search = trim($_GET['search'] ?? '');
        $sql = "SELECT * FROM pricelist";
        $types = '';
        $params = [];

        if ($search !== '') {
            $sql .= " WHERE nama LIKE ? OR deskripsi LIKE ? OR benefit LIKE ? OR kategori LIKE ?";
            $like = "%{$search}%";
            $types = 'ssss';
            $params = [$like, $like, $like, $like];
        }

        $sql .= " ORDER BY kategori, nama";

        $price_items = $this->fetchAllAssoc($sql, $types, $params);

        require __DIR__ . '/../views/admin/pricelist.php';
    }

    public function fotoUser(): void
    {
        $this->requireAdmin();
        $active_page = 'foto_user';

        $action = $_POST['action'] ?? '';

        if ($action === 'approve') {
            $stmt = $this->db->prepare("UPDATE foto_pengunjung SET status='approved' WHERE id=?");
            $stmt->bind_param("i", $_POST['id']);
            $stmt->execute();
            $stmt->close();

            header('Location: index.php?page=admin_foto_user');
            exit;
        }

        if ($action === 'reject') {
            $stmt = $this->db->prepare("UPDATE foto_pengunjung SET status='rejected' WHERE id=?");
            $stmt->bind_param("i", $_POST['id']);
            $stmt->execute();
            $stmt->close();

            header('Location: index.php?page=admin_foto_user');
            exit;
        }

        if ($action === 'hapus') {
            $stmt = $this->db->prepare("SELECT nama_file FROM foto_pengunjung WHERE id=?");
            $stmt->bind_param("i", $_POST['id']);
            $stmt->execute();
            $f = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($f) {
                $path = __DIR__ . '/../../uploads/' . $f['nama_file'];

                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $stmt = $this->db->prepare("DELETE FROM foto_pengunjung WHERE id=?");
            $stmt->bind_param("i", $_POST['id']);
            $stmt->execute();
            $stmt->close();

            header('Location: index.php?page=admin_foto_user');
            exit;
        }

        $search = trim($_GET['search'] ?? '');
        $filter = trim($_GET['status'] ?? '');

        $sql = "SELECT fp.*, u.nama, u.email
                FROM foto_pengunjung fp
                LEFT JOIN users u ON fp.user_id = u.id
                WHERE 1=1";

        $types = '';
        $params = [];

        if ($search !== '') {
            $sql .= " AND (u.nama LIKE ? OR u.email LIKE ?)";
            $like = "%{$search}%";
            $types .= 'ss';
            $params[] = $like;
            $params[] = $like;
        }

        if ($filter !== '') {
            $sql .= " AND fp.status = ?";
            $types .= 's';
            $params[] = $filter;
        }

        $sql .= " ORDER BY fp.created_at DESC";

        $fotos = $this->fetchAllAssoc($sql, $types, $params);

        $total = $this->db->query("SELECT COUNT(*) FROM foto_pengunjung")->fetch_row()[0] ?? 0;
        $pending = $this->db->query("SELECT COUNT(*) FROM foto_pengunjung WHERE status='pending'")->fetch_row()[0] ?? 0;
        $approved = $this->db->query("SELECT COUNT(*) FROM foto_pengunjung WHERE status='approved'")->fetch_row()[0] ?? 0;
        $rejected = $this->db->query("SELECT COUNT(*) FROM foto_pengunjung WHERE status='rejected'")->fetch_row()[0] ?? 0;

        require __DIR__ . '/../views/admin/foto_user.php';
    }
}