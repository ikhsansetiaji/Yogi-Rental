<?php
session_start();
require '../config/database.php'; // Koneksi database

$response = [];

// Ambil data promo untuk ditampilkan di tabel
$data_promo = [];
try {
    $stmt = $conn->query("SELECT * FROM promo");
    $data_promo = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching promo data: " . $e->getMessage());
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Tambah Data Promo
        if (isset($_POST['action']) && $_POST['action'] === 'add') {
            if (
                empty($_POST['kode_promo']) || empty($_POST['nama_promo']) || empty($_POST['tanggal_mulai']) ||
                empty($_POST['tanggal_selesai']) || (empty($_POST['potongan_persen']) && empty($_POST['potongan_nominal']))
            ) {
                throw new Exception('Harap isi semua field yang diperlukan.');
            }

            // Insert Data
            $stmt = $conn->prepare("INSERT INTO promo (kode_promo, nama_promo, deskripsi, potongan_persen, potongan_nominal, tanggal_mulai, tanggal_selesai, status) 
            VALUES (:kode_promo, :nama_promo, :deskripsi, :potongan_persen, :potongan_nominal, :tanggal_mulai, :tanggal_selesai, :status)");
            $stmt->execute([
                ':kode_promo' => $_POST['kode_promo'],
                ':nama_promo' => $_POST['nama_promo'],
                ':deskripsi' => $_POST['deskripsi'],
                ':potongan_persen' => $_POST['potongan_persen'],
                ':potongan_nominal' => $_POST['potongan_nominal'],
                ':tanggal_mulai' => $_POST['tanggal_mulai'],
                ':tanggal_selesai' => $_POST['tanggal_selesai'],
                ':status' => $_POST['status'],
            ]);

            $response = $stmt->rowCount() > 0
                ? ['success' => true, 'message' => 'Promo berhasil ditambahkan.']
                : ['success' => false, 'message' => 'Gagal menambahkan promo.'];
        }

        // Edit Data Promo
        if (isset($_POST['action']) && $_POST['action'] === 'edit') {
            if (
                empty($_POST['id_promo']) || empty($_POST['kode_promo']) || empty($_POST['nama_promo']) || 
                empty($_POST['tanggal_mulai']) || empty($_POST['tanggal_selesai'])
            ) {
                throw new Exception('Harap isi semua field yang diperlukan.');
            }

            // Update Data
            $stmt = $conn->prepare("UPDATE promo SET kode_promo = :kode_promo, nama_promo = :nama_promo, deskripsi = :deskripsi, 
                potongan_persen = :potongan_persen, potongan_nominal = :potongan_nominal, tanggal_mulai = :tanggal_mulai, 
                tanggal_selesai = :tanggal_selesai, status = :status WHERE id_promo = :id_promo");
            $stmt->execute([
                ':id_promo' => $_POST['id_promo'],
                ':kode_promo' => $_POST['kode_promo'],
                ':nama_promo' => $_POST['nama_promo'],
                ':deskripsi' => $_POST['deskripsi'],
                ':potongan_persen' => $_POST['potongan_persen'],
                ':potongan_nominal' => $_POST['potongan_nominal'],
                ':tanggal_mulai' => $_POST['tanggal_mulai'],
                ':tanggal_selesai' => $_POST['tanggal_selesai'],
                ':status' => $_POST['status'],
            ]);

            $response = $stmt->rowCount() > 0
                ? ['success' => true, 'message' => 'Promo berhasil diperbarui.']
                : ['success' => false, 'message' => 'Gagal memperbarui promo.'];
        }

        // Hapus Data Promo
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $stmt = $conn->prepare("DELETE FROM promo WHERE id_promo = :id_promo");
            $stmt->execute([':id_promo' => $_POST['id_promo']]);

            $response = $stmt->rowCount() > 0
                ? ['success' => true, 'message' => 'Promo berhasil dihapus.']
                : ['success' => false, 'message' => 'Gagal menghapus promo.'];
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }

    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Promo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>Kelola Promo</h2>
        <button class="btn btn-primary mb-3" onclick="openAddModal()">Tambah Promo</button>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode Promo</th>
                    <th>Nama Promo</th>
                    <th>Potongan</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_promo as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= htmlspecialchars($row['kode_promo']); ?></td>
                        <td><?= htmlspecialchars($row['nama_promo']); ?></td>
                        <td>
                            <?= $row['potongan_persen'] ? $row['potongan_persen'] . '%' : 'Rp ' . number_format($row['potongan_nominal'], 0, ',', '.'); ?>
                        </td>
                        <td><?= htmlspecialchars($row['tanggal_mulai'] . ' - ' . $row['tanggal_selesai']); ?></td>
                        <td><?= htmlspecialchars($row['status']); ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deletePromo(<?= $row['id_promo']; ?>)">Hapus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="promoModal" tabindex="-1" aria-labelledby="promoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="promoModalLabel">Tambah/Edit Promo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="promoForm">
                        <input type="hidden" name="action" value="">
                        <input type="hidden" name="id_promo" value="">

                        <div class="mb-3">
                            <label for="kode_promo" class="form-label">Kode Promo</label>
                            <input type="text" id="kode_promo" name="kode_promo" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="nama_promo" class="form-label">Nama Promo</label>
                            <input type="text" id="nama_promo" name="nama_promo" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="potongan_persen" class="form-label">Potongan Persen (%)</label>
                            <input type="number" id="potongan_persen" name="potongan_persen" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="potongan_nominal" class="form-label">Potongan Nominal (Rp)</label>
                            <input type="number" id="potongan_nominal" name="potongan_nominal" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const modal = new bootstrap.Modal(document.getElementById('promoModal'));
        const form = document.getElementById('promoForm');

        function openAddModal() {
            form.reset();
            form.action.value = 'add';
            modal.show();
        }

        function openEditModal(data) {
            form.reset();
            form.action.value = 'edit';
            form.id_promo.value = data.id_promo;
            form.kode_promo.value = data.kode_promo;
            form.nama_promo.value = data.nama_promo;
            form.deskripsi.value = data.deskripsi;
            form.potongan_persen.value = data.potongan_persen;
            form.potongan_nominal.value = data.potongan_nominal;
            form.tanggal_mulai.value = data.tanggal_mulai;
            form.tanggal_selesai.value = data.tanggal_selesai;
            form.status.value = data.status;
            modal.show();
        }

        function deletePromo(id) {
            Swal.fire({
                title: 'Hapus?',
                text: 'Data promo tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            }).then(result => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id_promo', id);

                    fetch('', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Dihapus!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
                    });
                }
            });
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
            });
        });
    </script>
</body>
</html>
