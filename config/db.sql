-- Create database
CREATE DATABASE IF NOT EXISTS rental_ps_db;
USE rental_ps_db;

-- Table for customers (pelanggan)
CREATE TABLE pelanggan (
    id_pelanggan INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telepon VARCHAR(15) NOT NULL,
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for game consoles/units (unit_ps)
CREATE TABLE unit_ps (
    id_unit INT PRIMARY KEY AUTO_INCREMENT,
    nomor_unit VARCHAR(50) NOT NULL,
    tipe_ps VARCHAR(50) NOT NULL,
    status ENUM('tersedia', 'disewa', 'maintenance') DEFAULT 'tersedia',
    kondisi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for games collection (games)
CREATE TABLE games (
    id_game INT PRIMARY KEY AUTO_INCREMENT,
    judul_game VARCHAR(255) NOT NULL,
    platform VARCHAR(50) NOT NULL,
    genre VARCHAR(100),
    tahun_rilis YEAR,
    status ENUM('tersedia', 'dipinjam', 'rusak') DEFAULT 'tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for rental bookings (peminjaman)
CREATE TABLE peminjaman (
    id_peminjaman INT PRIMARY KEY AUTO_INCREMENT,
    id_pelanggan INT NOT NULL,
    id_unit INT NOT NULL,
    tanggal_mulai DATETIME NOT NULL,
    durasi_jam INT NOT NULL,
    total_biaya DECIMAL(10,2) NOT NULL,
    status_pembayaran ENUM('belum_bayar', 'dp', 'lunas') DEFAULT 'belum_bayar',
    status_peminjaman ENUM('pending', 'aktif', 'selesai', 'dibatalkan') DEFAULT 'pending',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_unit) REFERENCES unit_ps(id_unit) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Indeks untuk query lebih cepat
CREATE INDEX idx_pelanggan ON peminjaman (id_pelanggan);
CREATE INDEX idx_unit ON peminjaman (id_unit);

-- Table for game rentals (peminjaman_game)
CREATE TABLE peminjaman_game (
    id_peminjaman_game INT PRIMARY KEY AUTO_INCREMENT,
    id_peminjaman INT,
    id_game INT,
    FOREIGN KEY (id_peminjaman) REFERENCES peminjaman(id_peminjaman),
    FOREIGN KEY (id_game) REFERENCES games(id_game)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for rental packages (paket_sewa)
CREATE TABLE paket_sewa (
    id_paket INT PRIMARY KEY AUTO_INCREMENT,
    nama_paket VARCHAR(100) NOT NULL,
    durasi_jam INT NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    deskripsi TEXT,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for reviews/ratings (ulasan)
CREATE TABLE ulasan (
    id_ulasan INT PRIMARY KEY AUTO_INCREMENT,
    id_peminjaman INT,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    komentar TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_peminjaman) REFERENCES peminjaman(id_peminjaman)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for payment transactions (pembayaran)
CREATE TABLE pembayaran (
    id_pembayaran INT PRIMARY KEY AUTO_INCREMENT,
    id_peminjaman INT,
    jumlah_pembayaran DECIMAL(10,2) NOT NULL,
    metode_pembayaran VARCHAR(50) NOT NULL,
    status_pembayaran ENUM('pending', 'sukses', 'gagal', 'refund') DEFAULT 'pending',
    tanggal_pembayaran TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_peminjaman) REFERENCES peminjaman(id_peminjaman)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for users
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data into users
INSERT INTO users (username, password)
VALUES ('admin', '$2y$10$LQyT/sW2E154Izoqn.EI5.RsVSjYgXapGyrWSszhMBvUpBnuUcnsa');

-- Add indexes for better performance
ALTER TABLE peminjaman ADD INDEX idx_tanggal_mulai (tanggal_mulai);
ALTER TABLE peminjaman ADD INDEX idx_status_peminjaman (status_peminjaman);
ALTER TABLE pelanggan ADD INDEX idx_email (email);
ALTER TABLE games ADD INDEX idx_judul_game (judul_game);
