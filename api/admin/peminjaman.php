<?php
session_start();
require '../config/database.php'; // Koneksi database

$response = [];

// Handle CRUD actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debugging: Tambahkan log ini untuk memeriksa data POST
    error_log("Data POST: " . print_r($_POST, true));

    try {
        // Tambah Data
        if (isset($_POST['action']) && $_POST['action'] === 'add') {
            if (
                empty($_POST['id_pelanggan']) || empty($_POST['id_unit']) || empty($_POST['tanggal_mulai']) ||
                empty($_POST['durasi_jam']) || empty($_POST['total_biaya']) || empty($_POST['status_pembayaran']) || 
                empty($_POST['status_peminjaman'])
            ) {
                throw new Exception('Harap isi semua field yang diperlukan.');
            }

            $tanggal_mulai = date('Y-m-d H:i:s', strtotime($_POST['tanggal_mulai']));

            $stmt = $conn->prepare("
                INSERT INTO peminjaman (id_pelanggan, id_unit, tanggal_mulai, durasi_jam, total_biaya, status_pembayaran, status_peminjaman) 
                VALUES (:id_pelanggan, :id_unit, :tanggal_mulai, :durasi_jam, :total_biaya, :status_pembayaran, :status_peminjaman)
            ");
            $stmt->execute([
                ':id_pelanggan' => $_POST['id_pelanggan'],
                ':id_unit' => $_POST['id_unit'],
                ':tanggal_mulai' => $tanggal_mulai,
                ':durasi_jam' => $_POST['durasi_jam'],
                ':total_biaya' => $_POST['total_biaya'],
                ':status_pembayaran' => $_POST['status_pembayaran'],
                ':status_peminjaman' => $_POST['status_peminjaman'],
            ]);

            $response = $stmt->rowCount() > 0
                ? ['success' => true, 'message' => 'Data peminjaman berhasil ditambahkan.']
                : ['success' => false, 'message' => 'Gagal menambahkan data.'];
        }

        // Edit Data
        elseif (isset($_POST['action']) && $_POST['action'] === 'edit') {
            if (
                empty($_POST['id_peminjaman']) || empty($_POST['id_pelanggan']) || empty($_POST['id_unit']) ||
                empty($_POST['tanggal_mulai']) || empty($_POST['durasi_jam']) || empty($_POST['total_biaya']) || 
                empty($_POST['status_pembayaran']) || empty($_POST['status_peminjaman'])
            ) {
                throw new Exception('Harap isi semua field yang diperlukan.');
            }

            $tanggal_mulai = date('Y-m-d H:i:s', strtotime($_POST['tanggal_mulai']));

            $stmt = $conn->prepare("
                UPDATE peminjaman 
                SET id_pelanggan = :id_pelanggan, id_unit = :id_unit, tanggal_mulai = :tanggal_mulai, 
                    durasi_jam = :durasi_jam, total_biaya = :total_biaya, status_pembayaran = :status_pembayaran, status_peminjaman = :status_peminjaman
                WHERE id_peminjaman = :id_peminjaman
            ");
            $stmt->execute([
                ':id_peminjaman' => $_POST['id_peminjaman'],
                ':id_pelanggan' => $_POST['id_pelanggan'],
                ':id_unit' => $_POST['id_unit'],
                ':tanggal_mulai' => $tanggal_mulai,
                ':durasi_jam' => $_POST['durasi_jam'],
                ':total_biaya' => $_POST['total_biaya'],
                ':status_pembayaran' => $_POST['status_pembayaran'],
                ':status_peminjaman' => $_POST['status_peminjaman'],
            ]);

            $response = $stmt->rowCount() > 0
                ? ['success' => true, 'message' => 'Data peminjaman berhasil diperbarui.']
                : ['success' => false, 'message' => 'Gagal memperbarui data.'];
        }

        // Hapus Data
        elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $stmt = $conn->prepare("DELETE FROM peminjaman WHERE id_peminjaman = :id_peminjaman");
            $stmt->execute([':id_peminjaman' => $_POST['id_peminjaman']]);

            $response = $stmt->rowCount() > 0
                ? ['success' => true, 'message' => 'Data peminjaman berhasil dihapus.']
                : ['success' => false, 'message' => 'Gagal menghapus data.'];
        }
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Handle GET request to fetch pelanggan and unit
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'fetch_data') {
    try {
        // Ambil data pelanggan
        $stmtPelanggan = $conn->query("SELECT id_pelanggan, nama FROM pelanggan");
        $pelanggan = $stmtPelanggan->fetchAll(PDO::FETCH_ASSOC);

        // Ambil data unit
        $stmtUnit = $conn->query("SELECT id_unit, nomor_unit FROM unit_ps");
        $unit = $stmtUnit->fetchAll(PDO::FETCH_ASSOC);

        $response = [
            'success' => true,
            'data' => [
                'pelanggan' => $pelanggan,
                'unit' => $unit
            ]
        ];
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Ambil data peminjaman untuk ditampilkan di halaman utama
$data_peminjaman = $conn->query("
    SELECT p.*, pel.nama AS nama_pelanggan, u.nomor_unit AS nomor_unit, u.tipe_ps AS tipe_ps
    FROM peminjaman p
    JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
    JOIN unit_ps u ON p.id_unit = u.id_unit
")->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>Kelola Peminjaman</h2>
        <button class="btn btn-primary mb-3" onclick="openAddModal()">Tambah Peminjaman</button>
        <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pelanggan</th>
                    <th>Unit</th>
                    <th>Tanggal Mulai</th>
                    <th>Durasi</th>
                    <th>Total Biaya</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data_peminjaman as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                        <td><?= htmlspecialchars($row['nomor_unit']); ?></td>
                        <td><?= htmlspecialchars($row['tanggal_mulai']); ?></td>
                        <td><?= htmlspecialchars($row['durasi_jam']); ?> jam</td>
                        <td>Rp <?= number_format($row['total_biaya'], 0, ',', '.'); ?></td>
                        <td><?= htmlspecialchars($row['status_peminjaman']); ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deletePeminjaman(<?= $row['id_peminjaman']; ?>)">Hapus</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal untuk tambah/edit data -->
    <div class="modal fade" id="peminjamanModal" tabindex="-1" aria-labelledby="peminjamanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="peminjamanModalLabel">Tambah/Edit Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form id="peminjamanForm">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id_peminjaman" value="">
                    
                    <div class="mb-3">
                        <label for="id_pelanggan" class="form-label">Pelanggan</label>
                        <select id="id_pelanggan" name="id_pelanggan" class="form-select" required>
                            <!-- Data pelanggan akan dimuat di sini -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_unit" class="form-label">Unit</label>
                        <select id="id_unit" name="id_unit" class="form-select" required>
                            <!-- Data unit akan dimuat di sini -->
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="datetime-local" id="tanggal_mulai" name="tanggal_mulai" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="durasi_jam" class="form-label">Durasi (Jam)</label>
                        <input type="number" id="durasi_jam" name="durasi_jam" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="total_biaya" class="form-label">Total Biaya</label>
                        <input type="number" id="total_biaya" name="total_biaya" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                        <select id="status_pembayaran" name="status_pembayaran" class="form-select" required>
                            <option value="belum_bayar">Belum Bayar</option>
                            <option value="dp">DP</option>
                            <option value="lunas">Lunas</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status_peminjaman" class="form-label">Status Peminjaman</label>
                        <select id="status_peminjaman" name="status_peminjaman" class="form-select" required>
                            <option value="pending">Pending</option>
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        const modal = new bootstrap.Modal(document.getElementById('peminjamanModal'));
        const form = document.getElementById('peminjamanForm');
        const pelangganSelect = document.getElementById('id_pelanggan');
        const unitSelect = document.getElementById('id_unit');

        // Fungsi untuk membuka modal tambah data
        function openAddModal() {
            form.reset();
            fetchData(); // Ambil data pelanggan dan unit
            form.action.value = 'add'; // Set action ke 'add'
            modal.show();
        }

        // Fungsi untuk membuka modal edit data
        function openEditModal(data) {
            form.reset();
            form.action.value = 'edit'; // Set action ke 'edit'
            form.id_peminjaman.value = data.id_peminjaman;
            form.id_pelanggan.value = data.id_pelanggan;
            form.id_unit.value = data.id_unit;

            // Format tanggal_mulai ke datetime-local
            const tanggalMulai = new Date(data.tanggal_mulai);
            const formattedTanggalMulai = tanggalMulai.toISOString().slice(0, 16); // Ambil format YYYY-MM-DDTHH:mm
            form.tanggal_mulai.value = formattedTanggalMulai;

            form.durasi_jam.value = data.durasi_jam;
            form.total_biaya.value = data.total_biaya;
            form.status_pembayaran.value = data.status_pembayaran;
            form.status_peminjaman.value = data.status_peminjaman;
            modal.show();
        }


        // Fungsi untuk mengambil data pelanggan dan unit
        function fetchData() {
            fetch('?action=fetch_data') // Mengambil data dari server
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Isi dropdown pelanggan
                        pelangganSelect.innerHTML = '<option value="">Pilih Pelanggan</option>';
                        data.data.pelanggan.forEach(pelanggan => {
                            const option = document.createElement('option');
                            option.value = pelanggan.id_pelanggan;
                            option.textContent = pelanggan.nama;
                            pelangganSelect.appendChild(option);
                        });

                        // Isi dropdown unit
                        unitSelect.innerHTML = '<option value="">Pilih Unit</option>';
                        data.data.unit.forEach(unit => {
                            const option = document.createElement('option');
                            option.value = unit.id_unit;
                            option.textContent = unit.nomor_unit;
                            unitSelect.appendChild(option);
                        });
                    } else {
                        alert('Gagal memuat data.');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Fungsi untuk submit form
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            console.log("Data yang dikirim:", Object.fromEntries(formData.entries())); // Debug data sebelum dikirim
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
                Swal.fire('Error!', 'Terjadi kesalahan saat mengirim data.', 'error');
            });
        });

        // Fungsi untuk menghapus peminjaman
        function deletePeminjaman(id) {
            Swal.fire({
                title: 'Hapus?',
                text: 'Data tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
            }).then(result => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id_peminjaman', id);

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
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data.', 'error');
                    });
                }
            });
        }
    </script>

</body>
</html>
