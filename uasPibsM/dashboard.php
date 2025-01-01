<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];

// Debugging: Menampilkan role untuk memastikan nilainya


// Koneksi ke database
include 'config/db.php'; // Gunakan koneksi dari file config/db.php untuk menghindari kode yang duplikat

// Ambil data footer dan slogan dari database
$footerQuery = "SELECT * FROM users LIMIT 1";
$sloganQuery = "SELECT * FROM footer_info LIMIT 1";

$footerResult = $conn->query($footerQuery);
$footerSloganResult = $conn->query($sloganQuery);

$footerData = $footerResult->num_rows > 0 ? $footerResult->fetch_assoc() : ['nama_lengkap' => 'N/A'];
$footerSlogan = $footerSloganResult->num_rows > 0 ? $footerSloganResult->fetch_assoc() : ['website_name' => 'N/A', 'slogan' => 'N/A'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Dashboard</title>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($user['nama_lengkap']); ?> (<?php echo htmlspecialchars($user['role']); ?>)</h1>
        <a href="logout.php" class="btn-logout">Logout</a>
    </header>

    <div class="container">
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="javascript:void(1);" id="lihat-proposal-btn">Lihat Proposal</a></li>
                <?php if ($user['role'] !== 'Kaprodi' && $user['role'] !== 'Koordinator HIMA' && $user['role'] !== 'Fakultas' && $user['role'] !== 'Biro Kemahasiswaan Alumni'): // Sembunyikan menu jika role 'Kaprodi' atau 'Koordinator HIMA' ?>
    <li><a href="javascript:void(1);" id="tambah-proposal-btn">Tambah Proposal</a></li>
<?php endif; ?>

                <li><a href="laporan.php">Lihat Laporan</a></li>
            </ul>
        </nav>

        <section id="content-area">
            <!-- Konten proposal.php akan dimuat di sini -->
        </section>

        <aside>
            <h3>Hobi</h3><br>
            <ul>
                <li>Bermain Gitar</li>
                <li>Bola</li>
                <li>Desain UI</li>
                <li>Membaca</li>
                <li>Mendengar musik</li>
            </ul>
        </aside>
    </div>

    <footer>
        <div class="social-media">
            <ul>
                <li>Twitter: <?php echo htmlspecialchars($user['nama_lengkap']); ?>@twitter</li>
                <li>Facebook: <?php echo htmlspecialchars($user['nama_lengkap']); ?>@facebook</li>
                <li>Instagram: <?php echo htmlspecialchars($user['nama_lengkap']); ?>@instagram</li>
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
    // Menangani klik pada tombol "Lihat Proposal" di dashboard.php
    document.getElementById('lihat-proposal-btn').addEventListener('click', function() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'proposal.php', true); // Mengambil proposal.php
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Ganti konten section dengan response dari proposal.php
                document.getElementById('content-area').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    });

    // Fungsi untuk memuat form edit proposal
function loadEditForm(proposalId) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'crud/edit_proposal.php?id=' + proposalId, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            // Masukkan konten form edit ke dalam section #content-area
            document.getElementById('content-area').innerHTML = xhr.responseText;
        } else {
            alert('Gagal memuat form edit. Silakan coba lagi.');
        }
    };
    xhr.onerror = function () {
        alert('Terjadi kesalahan saat memuat form edit.');
    };
    xhr.send();
}

function loadProposals() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'proposal.php', true);  // Pastikan ini mengarah ke halaman yang berisi daftar proposal
    xhr.onload = function () {
        if (xhr.status === 200) {
            document.getElementById('content-area').innerHTML = xhr.responseText;
        } else {
            alert('Gagal memuat daftar proposal.');
        }
    };
    xhr.send();
}



    </script>

    <script>
    // Menangani klik pada tombol "Tambah Proposal" di dashboard.php
    document.getElementById('tambah-proposal-btn').addEventListener('click', function() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'crud/create_proposal.php', true); // Mengambil proposal.php
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Ganti konten section dengan response dari proposal.php
                document.getElementById('content-area').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    });
    </script>
</body>
</html>
