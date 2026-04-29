<?php

class UlasanController {
    private $db;


    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function getHomeData() {
        $queryStats = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_ulasan FROM ulasan";
        $resultStats = mysqli_query($this->db, $queryStats);
        $stats = mysqli_fetch_assoc($resultStats);

        $avg_rating = $stats['avg_rating'] ?? 0;
        $total_ulasan = $stats['total_ulasan'] ?? 0;

        $queryUlasan = "SELECT u.*, w.nama as wahana_name
                        FROM ulasan u
                        LEFT JOIN wahana w ON u.wahana_id = w.id
                        ORDER BY u.rating DESC, u.created_at DESC
                        LIMIT 6";
        $resultUlasan = mysqli_query($this->db, $queryUlasan);

        $ulasan_home = [];
        if ($resultUlasan) {
            while ($row = mysqli_fetch_assoc($resultUlasan)) {
                $ulasan_home[] = $row;
            }
        }


        return [
            'avg_rating' => $avg_rating,
            'total_ulasan' => $total_ulasan,
            'ulasan_home' => $ulasan_home
        ];
    }
}