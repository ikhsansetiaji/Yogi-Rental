<?php
session_start();

// Redirect ke halaman login jika belum login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require '../config/database.php';

$response = [];

// Handle CRUD actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            // Tambah Pembayaran
            if ($_POST['action'] === 'add') {
                if (empty($_POST['id_peminjaman']) || empty($_POST['jumlah_pembayaran']) || empty($_POST['metode_pembayaran'])) {
                    throw new Exception('Harap isi semua field yang diperlukan.');
                }

                $stmt = $conn->prepare("
                    INSERT INTO pembayaran (id_peminjaman, jumlah_pembayaran, metode_pembayaran, status_pembayaran) 
                    VALUES (:id_peminjaman, :jumlah_pembayaran, :metode_pembayaran, :status_pembayaran)
                ");
                $stmt->execute([
                    ':id_peminjaman' => $_POST['id_peminjaman'],
                    ':jumlah_pembayaran' => $_POST['jumlah_pembayaran'],
                    ':metode_pembayaran' => $_POST['metode_pembayaran'],
                    ':status_pembayaran' => $_POST['status_pembayaran'] ?? 'pending',
                ]);

                if ($stmt->rowCount() > 0) {
                    $response = [
                        'success' => true,
                        'message' => 'Pembayaran berhasil ditambahkan!',
                    ];
                } else {
                    throw new Exception('Gagal menambahkan data.');
                }
                // Validasi id_peminjaman
                $stmt = $conn->prepare("SELECT id_peminjaman FROM peminjaman WHERE id_peminjaman = :id_peminjaman");
                $stmt->execute([':id_peminjaman' => $_POST['id_peminjaman']]);
                if ($stmt->rowCount() === 0) {
                    throw new Exception('ID Peminjaman tidak valid.');
                }

            }

            // Edit Pembayaran
            elseif ($_POST['action'] === 'edit') {
                if (empty($_POST['id_pembayaran']) || empty($_POST['jumlah_pembayaran']) || empty($_POST['metode_pembayaran'])) {
                    throw new Exception('Harap isi semua field yang diperlukan.');
                }

                $stmt = $conn->prepare("
                    UPDATE pembayaran 
                    SET jumlah_pembayaran = :jumlah_pembayaran, metode_pembayaran = :metode_pembayaran, 
                        status_pembayaran = :status_pembayaran 
                    WHERE id_pembayaran = :id_pembayaran
                ");
                $stmt->execute([
                    ':id_pembayaran' => $_POST['id_pembayaran'],
                    ':jumlah_pembayaran' => $_POST['jumlah_pembayaran'],
                    ':metode_pembayaran' => $_POST['metode_pembayaran'],
                    ':status_pembayaran' => $_POST['status_pembayaran'],
                ]);

                if ($stmt->rowCount() > 0) {
                    $response = [
                        'success' => true,
                        'message' => 'Pembayaran berhasil diperbarui!',
                    ];
                } else {
                    throw new Exception('Gagal memperbarui data.');
                }
            }

            // Hapus Pembayaran
            elseif ($_POST['action'] === 'delete') {
                if (empty($_POST['id_pembayaran'])) {
                    throw new Exception('ID pembayaran tidak valid.');
                }

                $stmt = $conn->prepare("DELETE FROM pembayaran WHERE id_pembayaran = :id_pembayaran");
                $stmt->execute([':id_pembayaran' => $_POST['id_pembayaran']]);

                if ($stmt->rowCount() > 0) {
                    $response = [
                        'success' => true,
                        'message' => 'Pembayaran berhasil dihapus!',
                    ];
                } else {
                    throw new Exception('Gagal menghapus data.');
                }
            }
        }
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => $e->getMessage(),
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Ambil data pembayaran untuk ditampilkan
$data_pembayaran = $conn->query("
    SELECT p.*, pem.tanggal_mulai 
    FROM pembayaran p
    JOIN peminjaman pem ON p.id_peminjaman = pem.id_peminjaman
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>Kelola Pembayaran</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#pembayaranModal">Tambah Pembayaran</button>
        <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Peminjaman</th>
                    <th>Jumlah</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_pembayaran as $pembayaran): ?>
                    <tr>
                        <td><?= $pembayaran['id_pembayaran']; ?></td>
                        <td><?= htmlspecialchars($pembayaran['id_peminjaman']); ?></td>
                        <td>Rp <?= number_format($pembayaran['jumlah_pembayaran'], 2, ',', '.'); ?></td>
                        <td><?= htmlspecialchars($pembayaran['metode_pembayaran']); ?></td>
                        <td><?= htmlspecialchars($pembayaran['status_pembayaran']); ?></td>
                        <td><?= $pembayaran['tanggal_pembayaran']; ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-edit" data-id='<?= json_encode($pembayaran); ?>'>Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deletePembayaran(<?= $pembayaran['id_pembayaran']; ?>)">Hapus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah/Edit Pembayaran -->
    <div class="modal fade" id="pembayaranModal" tabindex="-1" aria-labelledby="pembayaranModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="pembayaranForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pembayaranModalLabel">Tambah/Edit Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_pembayaran">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label>ID Peminjaman</label>
                            <select name="id_peminjaman" class="form-select" required>
                                <option value="">Pilih ID Peminjaman</option>
                                <?php
                                $peminjaman = $conn->query("SELECT id_peminjaman, tanggal_mulai FROM peminjaman");
                                foreach ($peminjaman as $row) {
                                    echo "<option value='{$row['id_peminjaman']}'>ID: {$row['id_peminjaman']} - Tanggal: {$row['tanggal_mulai']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Jumlah Pembayaran</label>
                            <input type="number" step="0.01" name="jumlah_pembayaran" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Metode Pembayaran</label>
                            <input type="text" name="metode_pembayaran" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Status Pembayaran</label>
                            <select name="status_pembayaran" class="form-select">
                                <option value="pending">Pending</option>
                                <option value="sukses">Sukses</option>
                                <option value="gagal">Gagal</option>
                                <option value="refund">Refund</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('pembayaranForm');
        const modal = new bootstrap.Modal(document.getElementById('pembayaranModal'));

        // Tambah/Edit Pembayaran
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);

            fetch('', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Gagal!', data.message, 'error');
                    }
                });
        });

        // Edit Pembayaran
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function () {
                const data = JSON.parse(this.getAttribute('data-id'));
                form.action.value = 'edit';
                form.id_pembayaran.value = data.id_pembayaran;
                form.id_peminjaman.value = data.id_peminjaman;
                form.jumlah_pembayaran.value = data.jumlah_pembayaran;
                form.metode_pembayaran.value = data.metode_pembayaran;
                form.status_pembayaran.value = data.status_pembayaran;
                modal.show();
            });
        });

        // Hapus Pembayaran
        function deletePembayaran(id) {
            Swal.fire({
                title: 'Hapus?',
                text: 'Data tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
            }).then(result => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id_pembayaran', id);

                    fetch('', { method: 'POST', body: formData })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Dihapus!', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Gagal!', data.message, 'error');
                            }
                        });
                }
            });
        }
    </script>
</body>
</html>
