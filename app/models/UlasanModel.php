<?php

class UlasanModel
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $result = $this->db->query("SELECT * FROM ulasan ORDER BY created_at DESC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getApproved(int $limit = 6): array
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM ulasan 
            WHERE status = 'approved' 
            ORDER BY created_at DESC 
            LIMIT ?
        ");

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $limit);
        $stmt->execute();

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    public function getAvgRating(): float
    {
        $res = $this->db->query("
            SELECT AVG(rating) AS avg_rating 
            FROM ulasan 
            WHERE status = 'approved'
        ");

        $row = $res ? $res->fetch_assoc() : null;

        return $row ? round((float) $row['avg_rating'], 1) : 0.0;
    }

    public function countApproved(): int
    {
        $res = $this->db->query("
            SELECT COUNT(*) AS total 
            FROM ulasan 
            WHERE status = 'approved'
        ");

        $row = $res ? $res->fetch_assoc() : null;

        return $row ? (int) $row['total'] : 0;
    }

    public function getByWahana(string $wahanaName, int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM ulasan 
            WHERE status = 'approved' 
              AND wahana_name = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("si", $wahanaName, $limit);
        $stmt->execute();

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    public function getByWahanaId(int $wahanaId, int $limit = 10): array
{
    if (!$this->hasColumn('ulasan', 'wahana_id')) {
        return [];
    }

    $stmt = $this->db->prepare("
        SELECT * 
        FROM ulasan 
        WHERE status = 'approved' 
          AND wahana_id = ? 
        ORDER BY rating DESC, created_at DESC 
        LIMIT ?
    ");

    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("ii", $wahanaId, $limit);
    $stmt->execute();

    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $rows;
}
    public function getAvgRatingByWahana(string $wahanaName): float
    {
        $stmt = $this->db->prepare("
            SELECT AVG(rating) AS avg_rating 
            FROM ulasan 
            WHERE status = 'approved' 
              AND wahana_name = ?
        ");

        if (!$stmt) {
            return 0.0;
        }

        $stmt->bind_param("s", $wahanaName);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ? round((float) $row['avg_rating'], 1) : 0.0;
    }

    public function getAvgRatingByWahanaId(int $wahanaId): float
    {
        if (!$this->hasColumn('ulasan', 'wahana_id')) {
            return 0.0;
        }

        $stmt = $this->db->prepare("
            SELECT AVG(rating) AS avg_rating 
            FROM ulasan 
            WHERE status = 'approved' 
              AND wahana_id = ?
        ");

        if (!$stmt) {
            return 0.0;
        }

        $stmt->bind_param("i", $wahanaId);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ? round((float) $row['avg_rating'], 1) : 0.0;
    }

    public function countByWahanaId(int $wahanaId): int
    {
        if (!$this->hasColumn('ulasan', 'wahana_id')) {
            return 0;
        }

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total 
            FROM ulasan 
            WHERE status = 'approved' 
              AND wahana_id = ?
        ");

        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param("i", $wahanaId);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int) ($row['total'] ?? 0);
    }

    public function create(string $namaPengunjung, string $ulasan, int $rating): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO ulasan (nama_user, ulasan, rating, status) 
            VALUES (?, ?, ?, 'pending')
        ");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ssi", $namaPengunjung, $ulasan, $rating);

        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function createWithWahana(
        string $namaPengunjung,
        string $ulasan,
        int $rating,
        string $wahanaName,
        ?int $userId = null
    ): bool {
        if (!$this->hasColumn('ulasan', 'wahana_name')) {
            return false;
        }

        if ($this->hasColumn('ulasan', 'user_id')) {
            $stmt = $this->db->prepare("
                INSERT INTO ulasan 
                    (nama_user, ulasan, rating, status, wahana_name, user_id) 
                VALUES 
                    (?, ?, ?, 'pending', ?, ?)
            ");

            if (!$stmt) {
                return false;
            }

            $stmt->bind_param(
                "ssisi",
                $namaPengunjung,
                $ulasan,
                $rating,
                $wahanaName,
                $userId
            );
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO ulasan 
                    (nama_user, ulasan, rating, status, wahana_name) 
                VALUES 
                    (?, ?, ?, 'pending', ?)
            ");

            if (!$stmt) {
                return false;
            }

            $stmt->bind_param(
                "ssis",
                $namaPengunjung,
                $ulasan,
                $rating,
                $wahanaName
            );
        }

        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function createWithWahanaId(
        string $namaPengunjung,
        string $ulasan,
        int $rating,
        int $wahanaId,
        ?int $userId = null
    ): bool {
        if (!$this->hasColumn('ulasan', 'wahana_id')) {
            return false;
        }

        if ($this->hasColumn('ulasan', 'user_id')) {
            $stmt = $this->db->prepare("
                INSERT INTO ulasan 
                    (nama_user, ulasan, rating, status, wahana_id, user_id) 
                VALUES 
                    (?, ?, ?, 'pending', ?, ?)
            ");

            if (!$stmt) {
                return false;
            }

            $stmt->bind_param(
                "ssiii",
                $namaPengunjung,
                $ulasan,
                $rating,
                $wahanaId,
                $userId
            );
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO ulasan 
                    (nama_user, ulasan, rating, status, wahana_id) 
                VALUES 
                    (?, ?, ?, 'pending', ?)
            ");

            if (!$stmt) {
                return false;
            }

            $stmt->bind_param(
                "ssii",
                $namaPengunjung,
                $ulasan,
                $rating,
                $wahanaId
            );
        }

        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function updateStatus(int $id, string $status): bool
    {
        /*
         * Status yang valid mengikuti enum/kolom database saat ini.
         * UI boleh memakai istilah "hidden", tapi disimpan sebagai "rejected"
         * supaya tidak terjadi error Data truncated for column status.
         */
        $statusMap = [
            'published' => 'approved',
            'hidden'    => 'pending',
            'rejected'  => 'pending',
        ];

        $status = $statusMap[$status] ?? $status;

        /*
         * Kolom status database user saat ini tidak menerima "hidden" maupun "rejected".
         * Status aman yang sudah dipakai project: pending dan approved.
         */
        $allowedStatus = ['pending', 'approved'];

        if ($id <= 0 || !in_array($status, $allowedStatus, true)) {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE ulasan 
            SET status = ? 
            WHERE id = ?
        ");

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
        $stmt = $this->db->prepare("
            DELETE FROM ulasan 
            WHERE id = ?
        ");

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $id);

        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }

    public function countTodayByUserId(int $userId): int
    {
        if (!$this->hasColumn('ulasan', 'user_id')) {
            return 0;
        }

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total 
            FROM ulasan 
            WHERE user_id = ? 
              AND DATE(created_at) = CURDATE()
        ");

        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int) ($row['total'] ?? 0);
    }

    public function countTodayByUser(string $namaUser): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total 
            FROM ulasan 
            WHERE nama_user = ? 
              AND DATE(created_at) = CURDATE()
        ");

        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param("s", $namaUser);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return (int) ($row['total'] ?? 0);
    }
private function hasColumn(string $table, string $column): bool
{
    $allowedTables = ['ulasan'];

    if (!in_array($table, $allowedTables, true)) {
        return false;
    }

    $table = $this->db->real_escape_string($table);
    $column = $this->db->real_escape_string($column);

    $result = $this->db->query("
        SHOW COLUMNS FROM `$table` LIKE '$column'
    ");

    return $result && $result->num_rows > 0;
}
}