<?php

class ReservasiModel
{
    private mysqli $db;

    public const STATUS_PENDING    = 'pending';
    public const STATUS_TERJADWAL  = 'terjadwal';
    public const STATUS_SELESAI    = 'selesai';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const ALLOWED_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_TERJADWAL,
        self::STATUS_SELESAI,
        self::STATUS_DIBATALKAN,
    ];

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public static function isValidStatus(string $status): bool
    {
        return in_array($status, self::ALLOWED_STATUSES, true);
    }

    public function getAll(string $search = '', string $filterStatus = ''): array
    {
        $where  = "WHERE 1=1";
        $params = [];
        $types  = '';

        if ($search !== '') {
            $where .= " AND (nama_kegiatan LIKE ? OR jenis_kegiatan LIKE ? OR kode_booking LIKE ?)";
            $s = "%{$search}%";
            $params[] = $s;
            $params[] = $s;
            $params[] = $s;
            $types .= 'sss';
        }

        if ($filterStatus !== '' && self::isValidStatus($filterStatus)) {
            $where .= " AND status = ?";
            $params[] = $filterStatus;
            $types .= 's';
        }

        $sql = "SELECT * FROM reservasi {$where} ORDER BY created_at DESC";

        if (empty($params)) {
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

    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM reservasi
             WHERE user_id = ?
             ORDER BY created_at DESC"
        );

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param('i', $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        $stmt->close();

        return $rows;
    }

    public function getJadwalTerjadwal(): array
    {
        $stmt = $this->db->prepare(
            "SELECT *
             FROM reservasi
             WHERE status = ?
             ORDER BY tanggal ASC, jam_mulai ASC, created_at ASC"
        );

        if (!$stmt) {
            return [];
        }

        $status = self::STATUS_TERJADWAL;
        $stmt->bind_param('s', $status);
        $stmt->execute();

        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

        $stmt->close();

        return $rows;
    }

    private function generateKodeBooking(int $userId): string
    {
        for ($i = 0; $i < 5; $i++) {
            $random = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
            $kode = 'WDR-' . date('Ymd') . '-' . $random;

            $stmt = $this->db->prepare("SELECT id FROM reservasi WHERE kode_booking = ? LIMIT 1");

            if (!$stmt) {
                continue;
            }

            $stmt->bind_param('s', $kode);
            $stmt->execute();

            $exists = $stmt->get_result()->num_rows > 0;

            $stmt->close();

            if (!$exists) {
                return $kode;
            }
        }

        return 'WDR-' . date('Ymd') . '-' . strtoupper(substr(md5($userId . microtime(true)), 0, 4));
    }

    public function createByUser(
        int $userId,
        string $namaKegiatan,
        string $tanggal,
        int $jumlahPeserta,
        string $keterangan = ''
    ): ?string {
        $kodeBooking = $this->generateKodeBooking($userId);
        $status = self::STATUS_PENDING;

        $stmt = $this->db->prepare(
            "INSERT INTO reservasi
             (user_id, nama_kegiatan, tanggal, jumlah_peserta, keterangan, status, kode_booking, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
        );

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param(
            "ississs",
            $userId,
            $namaKegiatan,
            $tanggal,
            $jumlahPeserta,
            $keterangan,
            $status,
            $kodeBooking
        );

        $ok = $stmt->execute();

        $stmt->close();

        return $ok ? $kodeBooking : null;
    }

    public function updateStatus(int $id, string $status): bool
    {
        if (!self::isValidStatus($status)) {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE reservasi
             SET status = ?
             WHERE id = ?"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("si", $status, $id);

        $ok = $stmt->execute();

        $stmt->close();

        return $ok;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM reservasi WHERE id = ?");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $id);

        $ok = $stmt->execute();

        $stmt->close();

        return $ok;
    }
}