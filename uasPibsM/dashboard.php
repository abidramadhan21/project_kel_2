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
$proposalQuery = "SELECT * FROM proposal";

$footerResult = $conn->query($footerQuery);
$footerSloganResult = $conn->query($sloganQuery);
$proposalResult = $conn->query($proposalQuery);

$footerData = $footerResult->num_rows > 0 ? $footerResult->fetch_assoc() : ['nama_lengkap' => 'N/A'];
$footerSlogan = $footerSloganResult->num_rows > 0 ? $footerSloganResult->fetch_assoc() : ['website_name' => 'N/A', 'slogan' => 'N/A', 'alamat' => 'N/A'];
$proposal = $proposalResult->num_rows > 0 ? $proposalResult->fetch_assoc() : ['title' => '', 'status' => '', 'created_at' => ''];
$proposal1 = $proposalResult->num_rows > 1 ? $proposalResult->fetch_assoc() : ['title' => '', 'status' => '', 'created_at' => ''];
$proposal2 = $proposalResult->num_rows > 2 ? $proposalResult->fetch_assoc() : ['title' => '', 'status' => '', 'created_at' => ''];

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
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
        background-color:rgb(255, 255, 255);
    }

    h1 {
        text-align: center;
        color: #2c3e50;
    }

    /* Header Styles */
    header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #2c3e50;
        color: #ecf0f1;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        padding: 15px 20px;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .header-logo img {
        width: 60px;
        height: 60px;
        object-fit: cover;
    }

    .header-text h3 {
        font-size: 18px;
        color: #ecf0f1;
    }

    .header-text .slogan {
        font-size: 14px;
        color: #bdc3c7;
    }

    .header-text .alamat {
        font-size: 12px;
        color: #95a5a6;
    }

    .btn-logout {
        background-color: #ecf0f1;
        color: #34495e;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }

    .btn-logout:hover {
        background-color: #bdc3c7;
        color: #fff;
    }

    /* Main Layout Styles */
    .container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px;
    }

    nav {
        width: 100%;
        background-color: #34495e;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    nav:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
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
        background-color: #2980b9;
        padding-left: 15px;
    }

    section {
        flex: 1;
        background-color:rgb(236, 236, 236);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    section:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);   
    }

    aside {
        width: 100%;
        background-color: #34495e;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    aside:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    /* Footer Styles */
    footer {
        background-color: #2c3e50;
        padding: 10px;
        color: #ecf0f1;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        gap: 10px;
    }

    footer .social-media,
    footer .copyright,
    footer .web-info {
  
        text-align: center;
    }

    footer .copyright {
        margin-top: 33px;
        margin-right: 90px;
    }

    footer .web-info {
        margin-top: 19px;
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

    /* Card and Stat Box Styles */
    .card {
        background-color:rgb(255, 255, 255);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: scale(1.01);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .card h2 {
        font-size: 22px;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    .card p {
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

    /* Responsive Design */
    @media (min-width: 768px) {
        .container {
            flex-wrap: nowrap;
        }

        nav {
            width: 20%;
        }

        section {
            width: 60%;
        }

        aside {
            width: 20%;
        }
    }

    @media (max-width: 767px) {
        .header-content {
            flex-direction: column;
            text-align: center;
        }

        nav ul li {
            text-align: center;
        }

        .stat-box {
            flex-direction: column;
        }
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
        <!--Side Nav-->
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

            <!-- Tugas Terbaru -->
            <div class="card">
                <h2>Status Proposal</h2>
                <ul>
                    <li><p><strong>Judul Proposal:</strong> <?php echo htmlspecialchars($proposal['title']); ?> - <strong><?php echo htmlspecialchars($proposal['status']); ?></strong></p></li>
                    <li><p><strong>Judul Proposal:</strong> <?php echo htmlspecialchars($proposal1['title']); ?> - <strong><?php echo htmlspecialchars($proposal1['status']); ?></strong></p></li>
                    <li><p><strong>Judul Proposal:</strong> <?php echo htmlspecialchars($proposal2['title']); ?> - <strong><?php echo htmlspecialchars($proposal2['status']); ?></strong></p></li>
                </ul>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <h2>Proposal Dibuat</h2>
                <ul>
                    <li><p><strong><?php echo htmlspecialchars($proposal['title']); ?></strong> - Created At - <strong><?php echo htmlspecialchars($proposal['created_at']); ?></strong></p></li>
                    <li><p><strong><?php echo htmlspecialchars($proposal1['title']); ?></strong> - Created At - <strong><?php echo htmlspecialchars($proposal1['created_at']); ?></strong></p></li>
                    <li><p><strong><?php echo htmlspecialchars($proposal2['title']); ?></strong> - Created At - <strong><?php echo htmlspecialchars($proposal2['created_at']); ?></strong></p></li>
                </ul>
            </div>

        </section>

        <aside>
            <div class="card">
                <h1>Welcome, <?php echo htmlspecialchars($user['nama_lengkap']); ?> (<?php echo htmlspecialchars($user['role']); ?>)</h1>
            </div>
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
