<?php
// File: config/database.php

// Definisi konstanta untuk koneksi database
define('DB_HOST', 'localhost'); // Host database (default: localhost)
define('DB_USER', 'root');      // Username database
define('DB_PASS', '');          // Password database (kosong untuk default pada XAMPP)
define('DB_NAME', 'rental_ps_db'); // Nama database

try {
    // Membuat koneksi menggunakan PDO
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    
    // Set atribut PDO untuk mode error
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set atribut PDO untuk memastikan data di-fetch sebagai associative array
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Menangkap dan menampilkan error koneksi (hanya untuk development)
    die("Connection failed: " . htmlspecialchars($e->getMessage()));
}
