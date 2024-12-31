<?php
// Koneksi ke database
$servername = "localhost"; // Ganti dengan host database Anda
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$dbname = "rental_ps_db"; // Ganti dengan nama database Anda

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Proses penyimpanan ulasan jika form dikirim
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_peminjaman = intval($_POST['id_peminjaman']);
    $rating = intval($_POST['rating']);
    $komentar = htmlspecialchars($_POST['komentar']);

    // Validasi data
    if ($rating >= 1 && $rating <= 5) {
        $sql = "INSERT INTO ulasan (id_peminjaman, rating, komentar) VALUES ('$id_peminjaman', '$rating', '$komentar')";
        if ($conn->query($sql) === TRUE) {
            $message = "Review submitted successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "Invalid rating value!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelajah Rental</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bc.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">Jelajah Rental</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#games">Games</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pricing">Pricing</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="#booking">Book Now</a></li> -->
                    <!-- <li class="nav-item"><a href="admin/login.php" class="btn btn-outline-light ms-3">Login</a></li> -->
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container hero-content">
            <h1 class="hero-title">Ultimate Gaming Experience</h1>
            <p class="hero-subtitle">Nikmati pengalaman bermain game premium dengan peralatan terbaik dan game terbaru</p>
            <!-- <button class="cta-button">Book Your Session</button> -->
        </div>
    </section>


    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title">Why Choose Us</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-gamepad feature-icon"></i>
                        <h3>Latest Games</h3>
                        <p>Akses ke judul game terbaru dan terpopuler</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-wifi feature-icon"></i>
                        <h3>High-Speed Internet</h3>
                        <p>Koneksi sangat cepat untuk bermain game online tanpa hambatan</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-couch feature-icon"></i>
                        <h3>Comfort Gaming</h3>
                        <p>Kursi gaming premium dan lingkungan yang nyaman</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Games Section -->
    <section class="featured-games" id="games">
        <div class="container">
            <h2 class="section-title">Featured Games</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="game-card">
                        <img src="./assets/img/fifa24.jpg" alt="Game 1" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">FIFA 24</h5>
                            <p class="card-text">FIFA 24 adalah game simulasi sepak bola.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="game-card">
                        <img src="./assets/img/ggta5.jpg" alt="Game 2" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">GTA V</h5>
                            <p class="card-text">GTA V adalah game aksi petualangan dunia terbuka.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="game-card">
                        <img src="./assets/img/gowjpg.jpg" alt="Game 3" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">God of War</h5>
                            <p class="card-text">God of War adalah aksi petualangan mitologi Norse.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="pages/view_game.php" class="btn btn-primary btn-lg">Lihat Semua Game</a>
        </div>
    </section>

    <!-- Pricing Section -->
<section class="py-5" id="pricing">
    <div class="container">
        <h2 class="section-title">Our Packages</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="price-card">
                    <h3>Basic</h3>
                    <div class="price">Rp 50k</div>
                    <p>per jam</p>
                    <ul class="list-unstyled">
                        <li>Konsol Standar</li>
                        <li>2 Controller</li>
                        <li>Game Dasar</li>
                    </ul>
                    <!-- <button class="btn btn-outline-primary mt-3">Pilih Paket</button> -->
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="price-card featured">
                    <h3>Premium</h3>
                    <div class="price">Rp 75k</div>
                    <p>per jam</p>
                    <ul class="list-unstyled">
                        <li>Konsol Premium</li>
                        <li>4 Controller</li>
                        <li>Semua Game</li>
                        <li>Termasuk Camilan</li>
                    </ul>
                    <!-- <button class="btn btn-light mt-3">Pilih Paket</button> -->
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="price-card">
                    <h3>Party</h3>
                    <div class="price">Rp 100k</div>
                    <p>per jam</p>
                    <ul class="list-unstyled">
                        <li>Konsol Premium</li>
                        <li>6 Controller</li>
                        <li>Semua Game</li>
                        <li>Ruang Pribadi</li>
                        <li>Makanan & Minuman</li>
                    </ul>
                    <!-- <button class="btn btn-outline-primary mt-3">Pilih Paket</button> -->
                </div>
            </div>
        </div>
    </div>
</section>


    <!-- <section id="booking" class="booking-section">
        <div class="container">
            <h2 class="text-center">Book Your PS Rental Now!</h2>
            <form action="api/peminjaman.php" method="post">
                <div class="form-group">
                    <label for="id_peminjaman">Rental ID</label>
                    <input type="text" name="id_peminjaman" id="id_peminjaman" class="form-control" placeholder="Enter Rental ID" required>
                </div>
                <div class="form-group">
                    <label for="id_game">Game ID</label>
                    <input type="text" name="id_game" id="id_game" class="form-control" placeholder="Enter Game ID" required>
                </div>
                <button type="submit" class="btn btn-primary">Book Now</button>
            </form>
        </div>
    </section> -->

    <section class="recommended-menu py-5">
        <div class="container">
            <h2 class="section-title">Recommended for You</h2>
            <div class="row">
                <!-- Menu Item 1 -->
                <div class="col-md-4">
                    <div class="menu-item-card">
                        <img src="assets/img/rdr2jpg.jpg" alt="Menu Item 1" class="img-fluid">
                        <div class="menu-item-info">
                            <h3 class="menu-item-title">RDR II</h3>
                            <p class="menu-item-description">Red Dead Redemption 2 adalah game aksi petualangan dunia terbuka yang mengikuti kisah Arthur Morgan dalam geng Van der Linde. Fokus pada cerita mendalam dan eksplorasi.</p>
                            <!-- <button class="cta-button">Learn More</button> -->
                        </div>
                    </div>
                </div>
                <!-- Menu Item 2 -->
                <div class="col-md-4">
                    <div class="menu-item-card">
                        <img src="assets/img/wukong.jpg" alt="Menu Item 2" class="img-fluid">
                        <div class="menu-item-info">
                            <h3 class="menu-item-title">Black Myth Wukong</h3>
                            <p class="menu-item-description">Black Myth: Wukong adalah game aksi petualangan yang mengikuti perjalanan Sun Wukong, Raja Kera, melawan musuh-musuh mitologi dengan pertarungan dinamis dan grafis realistis.</p>
                            <!-- <button class="cta-button">Learn More</button> -->
                        </div>
                    </div>
                </div>
                <!-- Menu Item 3 -->
                <div class="col-md-4">
                    <div class="menu-item-card">
                        <img src="assets/img/teken8.jpg" alt="Menu Item 3" class="img-fluid">
                        <div class="menu-item-info">
                            <h3 class="menu-item-title">Tekken 8</h3>
                            <p class="menu-item-description">Tekken 8 adalah game pertarungan yang melanjutkan cerita keluarga Mishima dengan grafis baru dan pertarungan yang lebih dinamis. Fokus pada rivalitas antara Kazuya dan Heihachi.</p>
                            <!-- <button class="cta-button">Learn More</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>




    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h4>Jelajah Rental</h4>
                    <p>Premium gaming experience for everyone</p>
                </div>
                <div class="col-md-4">
                    <h4>Quick Links</h4>
                    <ul class="list-unstyled">
                        <li><a href="#games" class="text-white">Games</a></li>
                        <li><a href="#pricing" class="text-white">Pricing</a></li>
                        <!-- <li><a href="#booking" class="text-white">Book Now</a></li> -->
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4>Connect With Us</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p>&copy; 2024 Jelajah Rental. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>