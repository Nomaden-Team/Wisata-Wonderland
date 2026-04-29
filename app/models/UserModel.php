<?php

class UserModel
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }


    public function findAdminByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT id, username, email, password FROM admin WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }


    public function findUserByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT id, nama, email, password FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function getDb(): mysqli
    {
        return $this->db;
    }

    public function createUser(string $nama, string $email, string $noTelp, string $hashedPassword): bool
    {
        $stmt = $this->db->prepare("INSERT INTO users (nama, email, no_telp, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $email, $noTelp, $hashedPassword);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getUserPhotos(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM foto_pengunjung WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public function addUserPhoto(int $userId, string $namaFile, string $caption = ''): bool
    {
        $stmt = $this->db->prepare("INSERT INTO foto_pengunjung (user_id, nama_file, caption, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $userId, $namaFile, $caption);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getUserPhoto(int $fotoId, int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT nama_file FROM foto_pengunjung WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $fotoId, $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function deleteUserPhoto(int $fotoId, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM foto_pengunjung WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $fotoId, $userId);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
