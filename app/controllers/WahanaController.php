<?php

require_once __DIR__ . '/../models/WahanaModel.php';
require_once __DIR__ . '/../models/GaleriModel.php';
require_once __DIR__ . '/../models/UlasanModel.php';
require_once __DIR__ . '/../helpers/profanity_filter.php';

class WahanaController
{
    private WahanaModel $wahanaModel;
    private GaleriModel $galeriModel;
    private UlasanModel $ulasanModel;
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
        $this->wahanaModel = new WahanaModel($db);
        $this->galeriModel = new GaleriModel($db);
        $this->ulasanModel = new UlasanModel($db);
    }

    
    public function home(): void
    {
$wahana_list = $this->wahanaModel->getAll();
$galeri_list = $this->galeriModel->getAll();

$foto_user_approved = [];

$res_foto_user = $this->db->query(
    "SELECT *
     FROM foto_pengunjung
     WHERE status = 'approved'
     ORDER BY created_at DESC"
);

if ($res_foto_user) {
    $rows_foto_user = $res_foto_user->fetch_all(MYSQLI_ASSOC);

    foreach ($rows_foto_user as $foto_user) {
        $namaFile = trim($foto_user['nama_file'] ?? '');

        if ($namaFile === '') {
            continue;
        }

        $foto_user_approved[] = [
            'id' => 'user_' . (int) ($foto_user['id'] ?? 0),
            'foto' => $namaFile,
            'judul' => $foto_user['caption'] ?? 'Foto Pengunjung Wonderland',
            'ukuran' => 'normal',
            'source' => 'user_upload',
        ];
    }
}

if (!empty($foto_user_approved)) {
    $galeri_list = array_merge($foto_user_approved, $galeri_list);
}



$galeri_list = array_slice($galeri_list, 0, 8);     
$tiket_list = [];
        $res = $this->db->query("SELECT * FROM tiket WHERE status='aktif' ORDER BY kategori, segmen");

        if ($res) {
            $tiket_list = $res->fetch_all(MYSQLI_ASSOC);
        }

        $pricelist_home = [];
        $res_pl = $this->db->query(
            "SELECT * FROM pricelist 
             WHERE status='aktif' 
             ORDER BY kategori, nama 
             LIMIT 8"
        );

        if ($res_pl) {
            $pricelist_home = $res_pl->fetch_all(MYSQLI_ASSOC);
        }

        $pricelist_all = [];
        $res_all = $this->db->query(
            "SELECT * FROM pricelist 
             WHERE status='aktif' 
             ORDER BY kategori, nama"
        );

        if ($res_all) {
            $rows = $res_all->fetch_all(MYSQLI_ASSOC);

            foreach ($rows as $row) {
                $pricelist_all[$row['kategori']][] = $row;
            }
        }

        $fasilitas_list = [];
        $res2 = $this->db->query("SELECT * FROM fasilitas WHERE status='tersedia' ORDER BY nama");

        if ($res2) {
            $fasilitas_list = $res2->fetch_all(MYSQLI_ASSOC);
        }

        $ulasan_home = $this->ulasanModel->getApproved(6);
        $avg_rating = $this->ulasanModel->getAvgRating();
        $total_ulasan = $this->ulasanModel->countApproved();

        require __DIR__ . '/../views/public/home.php';
    }

    public function detail(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $wahana = $this->wahanaModel->getById($id);

        if (!$wahana) {
            header("Location: index.php");
            exit;
        }

        $wahana_lainnya = $this->wahanaModel->getOthers($id, 3);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ulasan'])) {
            if (!isset($_SESSION['user_id'])) {
                header("Location: index.php?page=login");
                exit;
            }

            $userId = (int) $_SESSION['user_id'];
            $nama = trim($_POST['nama_pengunjung'] ?? '');
            $teks = trim($_POST['ulasan'] ?? '');
            $rating = (int) ($_POST['rating'] ?? 5);
            $wahanaId = (int) $wahana['id'];

            if (containsProfanity($teks) || containsProfanity($nama)) {
                header("Location: index.php?page=detail_wahana&id={$id}&ulasan=kasar#tulis-ulasan");
                exit;
            }

            if ($this->ulasanModel->countTodayByUserId($userId) >= 3) {
                header("Location: index.php?page=detail_wahana&id={$id}&ulasan=limit#tulis-ulasan");
                exit;
            }

            if ($nama !== '' && $teks !== '' && $rating >= 1 && $rating <= 5) {
                $this->ulasanModel->createWithWahanaId($nama, $teks, $rating, $wahanaId, $userId);

                header("Location: index.php?page=detail_wahana&id={$id}&ulasan=ok#ulasan-wahana");
                exit;
            }
        }

        $ulasan_wahana = $this->ulasanModel->getByWahanaId($id, 30);
        $avg_rating_wahana = $this->ulasanModel->getAvgRatingByWahanaId($id);
        $total_ulasan_wahana = count($ulasan_wahana);

        $ulasan_hari_ini = 0;

        if (isset($_SESSION['user_id'])) {
            $ulasan_hari_ini = $this->ulasanModel->countTodayByUserId((int) $_SESSION['user_id']);
        }

        require __DIR__ . '/../views/public/detail_wahana.php';
    }

    public function pricelist(): void
    {
        $rows = [];
        $res = $this->db->query(
            "SELECT * FROM pricelist 
             WHERE status='aktif' 
             ORDER BY kategori, nama"
        );

        if ($res) {
            $rows = $res->fetch_all(MYSQLI_ASSOC);
        }

        $pricelist_grouped = [];

        foreach ($rows as $row) {
            $pricelist_grouped[$row['kategori']][] = $row;
        }

        require __DIR__ . '/../views/public/pricelist.php';
    }
}