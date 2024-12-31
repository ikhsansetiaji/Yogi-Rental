<?php
// File: admin/view_games.php

session_start();


// Koneksi ke database
require '../config/database.php';

// Ambil semua data game
$sql = "SELECT * FROM games";
$stmt = $conn->prepare($sql);
$stmt->execute();
$games = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>View All Games</title>
</head>
<body>
    <div class="container mt-4">
        <h2>All Games</h2>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul Game</th>
                    <th>Platform</th>
                    <th>Genre</th>
                    <th>Tahun Rilis</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($games) > 0): ?>
                    <?php foreach ($games as $game): ?>
                        <tr>
                            <td><?= htmlspecialchars($game['id_game']) ?></td>
                            <td><?= htmlspecialchars($game['judul_game']) ?></td>
                            <td><?= htmlspecialchars($game['platform']) ?></td>
                            <td><?= htmlspecialchars($game['genre']) ?></td>
                            <td><?= htmlspecialchars($game['tahun_rilis']) ?></td>
                            <td><?= htmlspecialchars($game['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No games available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
