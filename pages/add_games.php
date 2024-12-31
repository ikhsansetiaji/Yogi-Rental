<?php
// File: admin/add_game.php

session_start();

// Jika belum login, redirect ke halaman login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Koneksi ke database
require '../config/database.php';

// Tambahkan game baru
if (isset($_POST['add_game'])) {
    $judul_game = $_POST['judul_game'];
    $platform = $_POST['platform'];
    $genre = $_POST['genre'];
    $tahun_rilis = $_POST['tahun_rilis'];
    $status = $_POST['status'];

    $sql = "INSERT INTO games (judul_game, platform, genre, tahun_rilis, status) VALUES (:judul_game, :platform, :genre, :tahun_rilis, :status)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':judul_game', $judul_game);
    $stmt->bindParam(':platform', $platform);
    $stmt->bindParam(':genre', $genre);
    $stmt->bindParam(':tahun_rilis', $tahun_rilis, PDO::PARAM_INT);
    $stmt->bindParam(':status', $status);

    if ($stmt->execute()) {
        header('Location: games.php');
        exit;
    } else {
        $error = "Gagal menambahkan game. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Tambah Game</title>
</head>
<body>
    <div class="container mt-4">
        <h2>Tambah Game Baru</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="judul_game" class="form-label">Judul Game</label>
                <input type="text" class="form-control" id="judul_game" name="judul_game" required>
            </div>
            <div class="mb-3">
                <label for="platform" class="form-label">Platform</label>
                <input type="text" class="form-control" id="platform" name="platform" required>
            </div>
            <div class="mb-3">
                <label for="genre" class="form-label">Genre</label>
                <input type="text" class="form-control" id="genre" name="genre" required>
            </div>
            <div class="mb-3">
                <label for="tahun_rilis" class="form-label">Tahun Rilis</label>
                <input type="number" class="form-control" id="tahun_rilis" name="tahun_rilis" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="Tersedia">Tersedia</option>
                    <option value="Tidak Tersedia">Tidak Tersedia</option>
                </select>
            </div>
            <button type="submit" name="add_game" class="btn btn-primary">Tambah Game</button>
            <a href="games.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
