<?php
session_start();
require '../config/database.php'; // Koneksi database

// Ambil data ulasan dari database
$data_ulasan = [];
try {
    $stmt = $conn->query("SELECT ulasan.*, peminjaman.id_peminjaman 
                          FROM ulasan
                          JOIN peminjaman ON ulasan.id_peminjaman = peminjaman.id_peminjaman
                          ORDER BY ulasan.created_at DESC");
    $data_ulasan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching ulasan data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ulasan</title>
    <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Kelola Ulasan</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID Peminjaman</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data_ulasan)): ?>
                    <?php foreach ($data_ulasan as $index => $ulasan): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($ulasan['id_peminjaman']); ?></td>
                            <td><?= str_repeat('⭐', $ulasan['rating']); // Tampilkan bintang berdasarkan rating ?></td>
                            <td><?= htmlspecialchars($ulasan['komentar']); ?></td>
                            <td><?= htmlspecialchars($ulasan['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada ulasan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
