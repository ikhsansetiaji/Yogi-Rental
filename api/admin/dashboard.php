<?php
// File: admin/dashboard.php

session_start();

// Jika belum login, redirect ke halaman login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Admin Dashboard</title>
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .sidebar {
            height: 100vh;
            background: #343a40;
            color: #fff;
            position: fixed;
            width: 250px;
            padding-top: 20px;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background: #495057;
            color: #fff;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .navbar {
            background: #343a40;
            color: #fff;
        }
        .navbar .btn {
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3 class="text-center">Admin Panel</h3>
        <a href="#"><i class="fas fa-home"></i> Dashboard</a>
        <a href="./games.php"><i class="fas fa-gamepad"></i> Games</a>
        <a href="./pelanggan.php"><i class="fas fa-users"></i> Pelanggan</a>
        <a href="./peminjaman.php"><i class="fas fa-shopping-cart"></i> Peminjaman</a>
        <a href="./pembayaran.php"><i class="fas fa-receipt"></i> Pembayaran</a>
        <a href="./unit_ps.php"><i class="fas fa-receipt"></i> Unit PS</a>
        <!-- <a href="./promo.php"><i class="fas fa-tags"></i> Promo</a> -->
        <a href="./ulasan.php"><i class="fas fa-star"></i> Ulasan</a>
        <a href="?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
        <nav class="navbar navbar-dark">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Dashboard</span>
                <button class="btn btn-outline-light" onclick="location.href='?logout=true'"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </nav>

        <div class="container mt-4">
            <h2>Welcome to Admin Dashboard</h2>
            <p>Manage your PlayStation rental system efficiently using this admin panel.</p>

            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Games</h5>
                            <p class="card-text">Manage game inventory.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Pelanggan</h5>
                            <p class="card-text">View and manage customer data.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Peminjaman</h5>
                            <p class="card-text">Track rental orders.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Pembayaran</h5>
                            <p class="card-text">Process payments securely.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Promo</h5>
                            <p class="card-text">Manage promotions and offers.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-secondary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Ulasan</h5>
                            <p class="card-text">Read customer feedback.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
