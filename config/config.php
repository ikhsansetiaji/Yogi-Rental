<?php
session_start();
define('BASE_URL', 'http://localhost/rental_ps_website');
define('SITE_NAME', 'Royal Game Center');
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'].'/rental_ps_website/uploads/');

// includes/functions.php
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "/login.php");
        exit();
    }
}

function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}