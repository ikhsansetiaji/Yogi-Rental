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
            // Tambah Unit PS
            if ($_POST['action'] === 'add') {
                if (empty($_POST['nomor_unit']) || empty($_POST['tipe_ps']) || empty($_POST['status'])) {
                    throw new Exception('Harap isi semua field yang diperlukan.');
                }

                $stmt = $conn->prepare("
                    INSERT INTO unit_ps (nomor_unit, tipe_ps, status, kondisi) 
                    VALUES (:nomor_unit, :tipe_ps, :status, :kondisi)
                ");
                $stmt->execute([
                    ':nomor_unit' => $_POST['nomor_unit'],
                    ':tipe_ps' => $_POST['tipe_ps'],
                    ':status' => $_POST['status'],
                    ':kondisi' => $_POST['kondisi'] ?? null,
                ]);

                if ($stmt->rowCount() > 0) {
                    $response = [
                        'success' => true,
                        'message' => 'Unit PS berhasil ditambahkan!',
                    ];
                } else {
                    throw new Exception('Gagal menambahkan data.');
                }
            }

            // Edit Unit PS
            elseif ($_POST['action'] === 'edit') {
                if (empty($_POST['id_unit']) || empty($_POST['nomor_unit']) || empty($_POST['tipe_ps'])) {
                    throw new Exception('Harap isi semua field yang diperlukan.');
                }

                $stmt = $conn->prepare("
                    UPDATE unit_ps 
                    SET nomor_unit = :nomor_unit, tipe_ps = :tipe_ps, status = :status, kondisi = :kondisi 
                    WHERE id_unit = :id_unit
                ");
                $stmt->execute([
                    ':id_unit' => $_POST['id_unit'],
                    ':nomor_unit' => $_POST['nomor_unit'],
                    ':tipe_ps' => $_POST['tipe_ps'],
                    ':status' => $_POST['status'],
                    ':kondisi' => $_POST['kondisi'] ?? null,
                ]);

                if ($stmt->rowCount() > 0) {
                    $response = [
                        'success' => true,
                        'message' => 'Unit PS berhasil diperbarui!',
                    ];
                } else {
                    throw new Exception('Gagal memperbarui data.');
                }
            }

            // Hapus Unit PS
            elseif ($_POST['action'] === 'delete') {
                if (empty($_POST['id_unit'])) {
                    throw new Exception('ID unit tidak valid.');
                }

                $stmt = $conn->prepare("DELETE FROM unit_ps WHERE id_unit = :id_unit");
                $stmt->execute([':id_unit' => $_POST['id_unit']]);

                if ($stmt->rowCount() > 0) {
                    $response = [
                        'success' => true,
                        'message' => 'Unit PS berhasil dihapus!',
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

// Ambil data unit PS untuk ditampilkan
$data_units = $conn->query("SELECT * FROM unit_ps")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Unit PS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>Kelola Unit PS</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#unitModal">Tambah Unit</button>
        <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nomor Unit</th>
                    <th>Tipe PS</th>
                    <th>Status</th>
                    <th>Kondisi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_units as $unit): ?>
                    <tr>
                        <td><?= $unit['id_unit']; ?></td>
                        <td><?= htmlspecialchars($unit['nomor_unit']); ?></td>
                        <td><?= htmlspecialchars($unit['tipe_ps']); ?></td>
                        <td><?= htmlspecialchars($unit['status']); ?></td>
                        <td><?= htmlspecialchars($unit['kondisi']); ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm btn-edit" data-id='<?= json_encode($unit); ?>'>Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteUnit(<?= $unit['id_unit']; ?>)">Hapus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah/Edit Unit -->
    <div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="unitForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="unitModalLabel">Tambah/Edit Unit PS</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_unit">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label>Nomor Unit</label>
                            <input type="text" name="nomor_unit" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Tipe PS</label>
                            <input type="text" name="tipe_ps" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-select" required>
                                <option value="tersedia">Tersedia</option>
                                <option value="disewa">Disewa</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Kondisi</label>
                            <textarea name="kondisi" class="form-control"></textarea>
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
        const form = document.getElementById('unitForm');
        const modal = new bootstrap.Modal(document.getElementById('unitModal'));

        // Tambah/Edit Unit
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

        // Edit Unit
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function () {
                const data = JSON.parse(this.getAttribute('data-id'));
                form.action.value = 'edit';
                form.id_unit.value = data.id_unit;
                form.nomor_unit.value = data.nomor_unit;
                form.tipe_ps.value = data.tipe_ps;
                form.status.value = data.status;
                form.kondisi.value = data.kondisi;
                modal.show();
            });
        });

        // Hapus Unit
        function deleteUnit(id) {
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
                    formData.append('id_unit', id);

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
