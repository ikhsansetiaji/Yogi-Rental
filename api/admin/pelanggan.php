<?php
// File: admin/customers.php

session_start();

// Jika belum login, redirect ke halaman login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require '../config/database.php';

// Tambahkan pelanggan baru
if (isset($_POST['add_customer'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $telepon = trim($_POST['telepon']);

    if (!empty($nama) && !empty($email) && !empty($telepon)) {
        $sql = "INSERT INTO pelanggan (nama, email, telepon) VALUES (:nama, :email, :telepon)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nama' => $nama,
            ':email' => $email,
            ':telepon' => $telepon
        ]);
        $_SESSION['message'] = "Pelanggan berhasil ditambahkan!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Harap isi semua data pelanggan.";
        $_SESSION['message_type'] = "warning";
    }
    header('Location: pelanggan.php');
    exit;
}

// Edit pelanggan
if (isset($_POST['edit_customer'])) {
    $id_pelanggan = $_POST['id_customer'];
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $telepon = trim($_POST['telepon']);

    if (!empty($id_pelanggan) && !empty($nama) && !empty($email) && !empty($telepon)) {
        $sql = "UPDATE pelanggan 
                SET nama = :nama, email = :email, telepon = :telepon 
                WHERE id_pelanggan = :id_pelanggan";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nama' => $nama,
            ':email' => $email,
            ':telepon' => $telepon,
            ':id_pelanggan' => $id_pelanggan
        ]);
        $_SESSION['message'] = "Pelanggan berhasil diperbarui!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Harap isi semua data pelanggan.";
        $_SESSION['message_type'] = "warning";
    }
    header('Location: pelanggan.php');
    exit;
}

// Hapus pelanggan
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Periksa apakah pelanggan memiliki peminjaman aktif
    $sqlCheck = "SELECT COUNT(*) FROM peminjaman WHERE id_pelanggan = :id AND status_peminjaman = 'aktif'";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->execute([':id' => $id]);
    $isBorrowing = $stmtCheck->fetchColumn();

    if ($isBorrowing > 0) {
        $_SESSION['message'] = "Pelanggan tidak dapat dihapus karena masih memiliki peminjaman aktif.";
        $_SESSION['message_type'] = "warning";
    } else {
        $sql = "DELETE FROM pelanggan WHERE id_pelanggan = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $_SESSION['message'] = "Pelanggan berhasil dihapus!";
        $_SESSION['message_type'] = "success";
    }

    header('Location: pelanggan.php');
    exit;
}

// Ambil data pelanggan beserta status peminjaman
$sql = "
    SELECT p.*, 
           COALESCE(pm.status_peminjaman, 'Tidak ada peminjaman') AS status_peminjaman
    FROM pelanggan p
    LEFT JOIN peminjaman pm ON p.id_pelanggan = pm.id_pelanggan AND pm.status_peminjaman = 'aktif'
";
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
    <title>Manage Customers</title>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Customers</h2>
            <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>

        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCustomerModal">Tambah Pelanggan</button>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Status Peminjaman</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <td><?= $row['id_pelanggan'] ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['telepon']) ?></td>
                        <td><?= htmlspecialchars($row['status_peminjaman']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editCustomerModal"
                                    onclick='loadEditData(<?= json_encode($row) ?>)'>
                                Edit
                            </button>
                            <a href="?delete=<?= $row['id_pelanggan'] ?>" class="btn btn-danger btn-sm delete-btn">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah Pelanggan -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">Tambah Pelanggan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="telepon" class="form-label">Telepon</label>
                            <input type="text" class="form-control" id="telepon" name="telepon" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_customer" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pelanggan -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_customer" name="id_customer">
                        <div class="mb-3">
                            <label for="edit_nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_telepon" class="form-label">Telepon</label>
                            <input type="text" class="form-control" id="edit_telepon" name="telepon" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status Peminjaman</label>
                            <input type="text" class="form-control" id="edit_status" name="status_peminjaman" readonly>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="edit_customer" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function loadEditData(customer) {
            document.getElementById('edit_id_customer').value = customer.id_pelanggan;
            document.getElementById('edit_nama').value = customer.nama;
            document.getElementById('edit_email').value = customer.email;
            document.getElementById('edit_telepon').value = customer.telepon;
            document.getElementById('edit_status').value = customer.status_peminjaman || 'Tidak ada peminjaman';
        }

        // SweetAlert2 - Pesan berhasil
        <?php if (isset($_SESSION['message'])): ?>
            Swal.fire({
                icon: '<?= $_SESSION['message_type'] ?>',
                title: '<?= ucfirst($_SESSION['message_type']) ?>',
                text: '<?= $_SESSION['message'] ?>'
            });
            <?php 
                unset($_SESSION['message']); 
                unset($_SESSION['message_type']); 
            endif; ?>

        // SweetAlert2 - Konfirmasi sebelum hapus
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function(event) {
                event.preventDefault();
                const href = this.getAttribute('href');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data pelanggan akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            });
        });
    </script>
</body>
</html>
