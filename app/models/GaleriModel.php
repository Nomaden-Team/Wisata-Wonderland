<?php

class GaleriModel
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $result = $this->db->query("SELECT * FROM galeri");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
