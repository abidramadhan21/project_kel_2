<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['kode_role'] != 'MHS') {
    header('Location: ../login.php');
    exit();
}

include '../config/db.php';

// Validasi parameter 'id'
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID proposal tidak valid.");
}

$proposal_id = intval($_GET['id']);

// Ambil data proposal untuk ditampilkan di form edit
$query = "SELECT * FROM proposal WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $proposal_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Proposal tidak ditemukan untuk ID $proposal_id.");
}

$proposal = $result->fetch_assoc();

// Proses update jika data diterima melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proposal_id = intval($_POST['id']); // Ambil ID proposal dari POST
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    // Validasi input
    if (empty($title) || empty($description)) {
        die("Title dan Description tidak boleh kosong.");
    }

    // Query untuk update proposal
    $query = "UPDATE proposal SET title = ?, description = ?, updated_at = NOW(), kaprodi = 'Pending', koordinator_hima = 'Pending', fakultas = 'Pending', bkal = 'Pending' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $title, $description, $proposal_id);

    if ($stmt->execute()) {
        echo "Proposal berhasil diperbarui";
    } else {
        echo "Error: " . $stmt->error;
    }
    exit(); // Menghentikan eksekusi setelah update
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Proposal</title>
    <link rel="stylesheet" href="../style/styleedit.css"> <!-- Sesuaikan path file CSS -->
</head>
<body>
    <h1>Edit Proposal</h1>

    <!-- Div untuk pesan sukses -->
    <div id="alertMessage" class="alert" style="display: none;"></div>

    <form method="POST" id="editForm">
        <!-- Input tersembunyi untuk ID proposal -->
        <input type="hidden" name="id" value="<?= htmlspecialchars($proposal['id']); ?>">

        <label>Title:</label><br>
        <input type="text" name="title" value="<?= htmlspecialchars($proposal['title']); ?>" required><br>

        <label>Description:</label><br>
        <textarea name="description" required><?= htmlspecialchars($proposal['description']); ?></textarea><br><br>

        <button type="submit">Update</button>
    </form>

    <script>
        document.getElementById('editForm').addEventListener('submit', function (e) {
            e.preventDefault(); // Mencegah pengiriman form secara default
            var formData = new FormData(this); // Ambil data form

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true); // Kirim ke halaman yang sama untuk diproses
            xhr.onload = function () {
                console.log(xhr.status, xhr.responseText); // Debugging: Log status dan response server
                if (xhr.status === 200 && xhr.responseText.trim() === "Proposal berhasil diperbarui") {
                    // Tampilkan pesan sukses
                    document.getElementById('alertMessage').innerText = 'Proposal berhasil diperbarui!';
                    document.getElementById('alertMessage').style.display = 'block';
                    document.getElementById('alertMessage').style.backgroundColor = '#dff0d8'; // Hijau
                    setTimeout(function () {
                        window.location.href = '../dashboard.php'; // Redirect setelah beberapa detik
                    }, 2000); // Tunggu 2 detik sebelum redirect
                } else {
                    console.error('Error:', xhr.status, xhr.statusText); // Debugging: Tampilkan error jika ada
                    document.getElementById('alertMessage').innerText = 'Gagal memperbarui proposal.';
                    document.getElementById('alertMessage').style.display = 'block';
                    document.getElementById('alertMessage').style.backgroundColor = '#f2dede'; // Merah
                }
            };
            xhr.onerror = function() {
                console.error('Request failed'); // Debugging: Error jika request gagal
                document.getElementById('alertMessage').innerText = 'Terjadi kesalahan pada server.';
                document.getElementById('alertMessage').style.display = 'block';
                document.getElementById('alertMessage').style.backgroundColor = '#f2dede'; // Merah
            };
            xhr.send(formData); // Kirim data ke server
        });
    </script>
</body>
</html>
