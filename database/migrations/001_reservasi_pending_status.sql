ALTER TABLE reservasi
  MODIFY status ENUM('pending','terjadwal','selesai','dibatalkan')
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
  DEFAULT 'pending';
