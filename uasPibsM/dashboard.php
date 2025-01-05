<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];


// Koneksi ke database
include 'config/db.php';

// Ambil data footer dan slogan dari database
$footerQuery = "SELECT * FROM users LIMIT 1";
$sloganQuery = "SELECT * FROM footer_header LIMIT 1";

$footerResult = $conn->query($footerQuery);
$footerSloganResult = $conn->query($sloganQuery);

$footerData = $footerResult->num_rows > 0 ? $footerResult->fetch_assoc() : ['nama_lengkap' => 'N/A'];
$footerSlogan = $footerSloganResult->num_rows > 0 ? $footerSloganResult->fetch_assoc() : ['website_name' => 'N/A', 'slogan' => 'N/A', 'alamat' => 'N/A'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f4f4; /* Background lebih cerah */
        }

        /* Style header */
/* Style header */
header {
    display: flex;
    align-items: center;
    justify-content: space-between; /* Konten di kiri dan tombol Logout di kanan */
    background-color: #34495e; /* Warna navbar */
    color: #ecf0f1; /* Warna teks lebih terang agar terlihat */
    padding: 15px 20px;
    border-bottom: 1px solid #2c3e50; /* Garis bawah lebih gelap untuk pemisah */
}

/* Bagian logo */
.header-logo img {
    width: 60px;
    height: 60px;
    object-fit: cover;
}

/* Bagian teks (Nama Web, Slogan, Alamat) */
.header-text h3 {
    margin: 0;
    font-size: 18px;
    color: #ecf0f1; /* Warna teks putih */
}

.header-text .slogan {
    margin: 0;
    font-size: 14px;
    color: #bdc3c7; /* Warna abu-abu terang */
}

.header-text .alamat {
    margin: 0;
    font-size: 12px;
    color: #95a5a6; /* Warna lebih redup untuk detail alamat */
}

/* Tombol Logout */
.btn-logout {
    background-color: #ecf0f1; /* Latar belakang putih terang */
    color: #34495e; /* Warna teks mengikuti warna navbar */
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 14px;
    transition: background-color 0.3s ease;
}

.btn-logout:hover {
    background-color: #bdc3c7; /* Warna hover */
    color: #fff; /* Warna teks putih saat hover */
}

/* Bagian header-content */
.header-content {
    display: flex;
    align-items: center;
    gap: 15px; /* Jarak antara logo dan teks */
}




        .container {
            display: flex;
            flex: 1;
            box-sizing: border-box;
        }

        nav {
            width: 20%;
            background-color: #34495e; /* Warna nav lebih gelap */
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        nav ul {
            list-style: none;
        }

        nav ul li {
            margin: 15px 0;
        }

        nav ul li a {
            text-decoration: none;
            color: #ecf0f1;
            font-size: 16px;
            display: block;
            padding: 10px;
            transition: background-color 0.3s ease, padding-left 0.3s ease;
        }

        nav ul li a:hover {
            background-color: #2980b9; /* Warna hover biru */
            padding-left: 15px;
        }

        section {
            width: 60%;
            background-color: #fff;
            padding: 20px;
        }

        aside {
            width: 20%;
            background-color: #ecf0f1;
            padding: 20px;
        }

        footer {
            background-color: #2c3e50;
            padding: 20px;
            color: #ecf0f1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        footer .social-media {
            flex: 1;
            text-align: left;
        }

        footer .social-media ul {
            list-style: none;
            padding: 0;
        }

        footer .social-media ul li {
            margin-bottom: 5px;
        }

        footer .social-media ul li a {
            text-decoration: none;
            color: #ecf0f1;
            font-size: 14px;
        }

        footer .copyright {
            text-align: center;
            flex: 1;
        }

        footer .web-info {
            text-align: right;
            flex: 1;
        }

        footer h3 {
            margin: 0;
            font-size: 20px;
        }

        footer p {
            font-size: 14px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .card p,
        .card ul {
            font-size: 14px;
            color: #34495e;
        }

        .stat-box {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .stat-item {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            flex: 1;
        }

        .stat-item h3 {
            font-size: 16px;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .stat-item p {
            font-size: 24px;
            font-weight: bold;
            color: #2980b9;
        }

        .status {
            color: #f39c12;
            font-weight: bold;
        }

        ul {
            list-style: none;
        }
    </style>
</head>
<body>
<header>
    <div class="header-content">
        <!-- Bagian logo -->
        <div class="header-logo">
            <img src="logo-upj.jpg" alt="Logo">
        </div>

        <!-- Bagian Nama Web, Slogan, dan Alamat -->
        <div class="header-text">
            <h3><?php echo htmlspecialchars($footerSlogan['website_name']); ?></h3>
            <p class="slogan"><?php echo htmlspecialchars($footerSlogan['slogan']); ?></p>
            <p class="alamat"><?php echo htmlspecialchars($footerSlogan['alamat']); ?></p>
        </div>
    </div>

    <!-- Tombol Logout -->
    <a href="logout.php" class="btn-logout">Logout</a>
</header>




    <div class="container">
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="javascript:void(1);" id="lihat-proposal-btn">Lihat Proposal</a></li>
                <?php if ($user['role'] !== 'Kaprodi' && $user['role'] !== 'Koordinator HIMA' && $user['role'] !== 'Fakultas' && $user['role'] !== 'Biro Kemahasiswaan Alumni'): ?>
                    <li><a href="javascript:void(1);" id="tambah-proposal-btn">Tambah Proposal</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <section id="content-area">
            <!-- Ringkasan Pengguna -->
            <div class="card">
                <h2>Ringkasan Pengguna</h2>
                <p><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($user['nama_lengkap']); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            </div>

            <!-- Statistik Proposal -->
            <div class="card">
                <h2>Statistik Proposal</h2>
                <div class="stat-box">
                    <div class="stat-item">
                        <h3>Proposal Diterima</h3>
                        <p>5</p>
                    </div>
                    <div class="stat-item">
                        <h3>Proposal Ditolak</h3>
                        <p>2</p>
                    </div>
                    <div class="stat-item">
                        <h3>Proposal Sedang Diproses</h3>
                        <p>3</p>
                    </div>
                </div>
            </div>

            <!-- Tugas Terbaru -->
            <div class="card">
                <h2>Tugas Terbaru</h2>
                <ul>
                    <li>Review proposal #1023 - <span class="status">Pending</span></li>
                    <li>Approve proposal #1025 - <span class="status">Pending</span></li>
                    <li>Update proposal #1021 - <span class="status">Completed</span></li>
                </ul>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <h2>Recent Activity</h2>
                <ul>
                    <li>Proposal #1021 updated on 01 Jan 2025</li>
                    <li>Proposal #1023 submitted on 30 Dec 2024</li>
                    <li>Proposal #1020 approved on 29 Dec 2024</li>
                </ul>
            </div>

            <!-- Notifikasi -->
            <div class="card">
                <h2>Notifikasi</h2>
                <p>Anda memiliki <strong>2</strong> proposal baru yang perlu ditinjau.</p>
            </div>
        </section>

        <aside>
            <h1>Welcome, <?php echo htmlspecialchars($user['nama_lengkap']); ?> (<?php echo htmlspecialchars($user['role']); ?>)</h1>
        </aside>
    </div>

    <footer>
        <div class="social-media">
            <ul>
                <li>Twitter: <a href="https://twitter.com/akun"><?php echo htmlspecialchars($user['nama_lengkap']); ?>@Twitter</a></li>
                <li>Facebook: <a href="https://facebook.com/akun"><?php echo htmlspecialchars($user['nama_lengkap']); ?>@facebook</a></li>
                <li>Instagram: <a href="https://instagram.com/akun"><?php echo htmlspecialchars($user['nama_lengkap']); ?>@instagram</a></li>
            </ul>
        </div>
        <div class="copyright">
            <p>&copy; Copyright 2020. All Rights Reserved</p>
        </div>
        <div class="web-info">
            <h3><?php echo htmlspecialchars($footerSlogan['website_name']); ?></h3>
            <p><?php echo htmlspecialchars($footerSlogan['slogan']); ?></p>
        </div>
    </footer>

    <script>
        // Menangani klik pada tombol "Lihat Proposal"
        document.getElementById('lihat-proposal-btn').addEventListener('click', function() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'proposal.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('content-area').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });

        // Menangani klik pada tombol "Tambah Proposal"
        document.getElementById('tambah-proposal-btn').addEventListener('click', function() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'crud/create_proposal.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('content-area').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });
    </script>
</body>
</html>
