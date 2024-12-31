<?php
// Include konfigurasi database
require_once '../config/database.php';

// Periksa apakah form telah di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $id_peminjaman = $_POST['id_peminjaman'];
    $id_game = $_POST['id_game'];

    // Validasi input
    if (empty($id_peminjaman) || empty($id_game)) {
        echo json_encode(["status" => "error", "message" => "Semua field wajib diisi."]);
        exit;
    }

    // Buat koneksi ke database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Periksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Query untuk menambahkan data ke tabel peminjaman_game
    $stmt = $conn->prepare("INSERT INTO peminjaman_game (id_peminjaman, id_game) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_peminjaman, $id_game);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Peminjaman berhasil ditambahkan."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menambahkan peminjaman."]);
    }

    // Tutup koneksi
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Metode tidak valid."]);
}
