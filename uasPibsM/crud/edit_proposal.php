<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['kode_role'] != 'MHS') {
    header('Location: ../login.php');
    exit();
}

include '../config/db.php';

// Cek jika parameter 'id' ada dan valid
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID proposal tidak valid.");
}

$proposal_id = intval($_GET['id']);

// Ambil data proposal untuk ditampilkan di form edit
$query = "SELECT * FROM proposal WHERE id = $proposal_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error query: " . mysqli_error($conn)); // Debugging: Menampilkan error query
}

$proposal = mysqli_fetch_assoc($result);

// Debugging: Periksa apakah data proposal ditemukan
if (!$proposal) {
    die("Proposal tidak ditemukan untuk ID $proposal_id.");
}

// Proses update jika data diterima melalui POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $proposal_id = intval($_POST['id']); // Ambil ID proposal dari POST
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Update proposal
    $query = "
    UPDATE proposal 
    SET 
        title = '$title', 
        description = '$description'
    WHERE id = $proposal_id
    ";

    if (mysqli_query($conn, $query)) {
        echo "Proposal berhasil diperbarui"; // Berikan respons sukses
    } else {
        echo "Error: " . mysqli_error($conn); // Berikan respons error
    }
    exit(); // Menghentikan eksekusi skrip setelah update
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Proposal</title>
</head>
<body>
    <h1>Edit Proposal</h1>
    <form method="POST" id="editForm">
        <!-- Input tersembunyi untuk ID proposal -->
        <input type="hidden" name="id" value="<?= $proposal_id; ?>">

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

            // Debugging: Tampilkan data form yang akan dikirim
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]); // Log data form yang akan dikirim
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'edit_proposal.php', true); // Kirim ke halaman yang sama untuk diproses
            xhr.onload = function () {
                console.log(xhr.status, xhr.responseText); // Debugging: Log status dan response server
                if (xhr.status === 200) {
                    console.log(xhr.responseText); // Debugging: Periksa respons dari server
                    alert('Proposal berhasil diperbarui!');
                    window.location.href = 'dashboard.php'; // Redirect setelah update berhasil
                } else {
                    console.error('Error:', xhr.status, xhr.statusText); // Debugging: Tampilkan error jika ada
                    alert('Gagal memperbarui proposal.');
                }
            };
            xhr.onerror = function() {
                console.error('Request failed'); // Debugging: Error jika request gagal
            };
            xhr.send(formData); // Kirim data ke server
        });
    </script>
</body>
</html>
