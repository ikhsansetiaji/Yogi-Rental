<?php
// File: admin/games.php

session_start();

// Jika belum login, redirect ke halaman login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require '../config/database.php';

$message = ""; // Variabel untuk pesan SweetAlert

// Tambahkan game baru
if (isset($_POST['add_game'])) {
    $judul_game = $_POST['judul_game'];
    $platform = $_POST['platform'];
    $genre = $_POST['genre'];
    $tahun_rilis = $_POST['tahun_rilis'];
    $status = $_POST['status'];

    $sql = "INSERT INTO games (judul_game, platform, genre, tahun_rilis, status) VALUES (:judul_game, :platform, :genre, :tahun_rilis, :status)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':judul_game' => $judul_game,
        ':platform' => $platform,
        ':genre' => $genre,
        ':tahun_rilis' => $tahun_rilis,
        ':status' => $status
    ]);
    $message = "Game berhasil ditambahkan!";
}

// Edit game
if (isset($_POST['edit_game'])) {
    $id_game = $_POST['id_game'];
    $judul_game = $_POST['judul_game'];
    $platform = $_POST['platform'];
    $genre = $_POST['genre'];
    $tahun_rilis = $_POST['tahun_rilis'];
    $status = $_POST['status'];

    $sql = "UPDATE games 
            SET judul_game = :judul_game, platform = :platform, genre = :genre, tahun_rilis = :tahun_rilis, status = :status 
            WHERE id_game = :id_game";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':judul_game' => $judul_game,
        ':platform' => $platform,
        ':genre' => $genre,
        ':tahun_rilis' => $tahun_rilis,
        ':status' => $status,
        ':id_game' => $id_game
    ]);
    $message = "Game berhasil diperbarui!";
}

// Hapus game
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM games WHERE id_game = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    $message = "Game berhasil dihapus!";
}

// Ambil data game
$sql = "SELECT * FROM games";
$stmt = $conn->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Manage Games</title>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Games</h2>
            <!-- Tombol Kembali ke Dashboard -->
            <a href="../admin/dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>

        <!-- Form Tambah Game -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addGameModal">Tambah Game</button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul Game</th>
                    <th>Platform</th>
                    <th>Genre</th>
                    <th>Tahun Rilis</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <td><?= $row['id_game'] ?></td>
                        <td><?= htmlspecialchars($row['judul_game']) ?></td>
                        <td><?= htmlspecialchars($row['platform']) ?></td>
                        <td><?= htmlspecialchars($row['genre']) ?></td>
                        <td><?= htmlspecialchars($row['tahun_rilis']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editGameModal"
                                    onclick='loadEditData(<?= json_encode($row) ?>)'>
                                Edit
                            </button>
                            <a href="?delete=<?= $row['id_game'] ?>" class="btn btn-danger btn-sm delete-btn" data-id="<?= $row['id_game'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah Game -->
    <div class="modal fade" id="addGameModal" tabindex="-1" aria-labelledby="addGameModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGameModalLabel">Tambah Game Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_game" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function loadEditData(game) {
            document.getElementById('edit_id_game').value = game.id_game;
            document.getElementById('edit_judul_game').value = game.judul_game;
            document.getElementById('edit_platform').value = game.platform;
            document.getElementById('edit_genre').value = game.genre;
            document.getElementById('edit_tahun_rilis').value = game.tahun_rilis;
            document.getElementById('edit_status').value = game.status;
        }

        // SweetAlert2 - Konfirmasi Penghapusan
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                const deleteUrl = this.href;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = deleteUrl;
                    }
                });
            });
        });

        // SweetAlert2 - Pesan berhasil
        <?php if ($message): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?= $message ?>'
        });
        <?php endif; ?>
    </script>
</body>
</html>
