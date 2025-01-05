<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit();
}

$user = $_SESSION['user'];
include '../config/db.php';

$proposal_id = $_GET['id'];
$role = isset($_GET['role']) ? $_GET['role'] : ''; // Memastikan role ada dalam GET

// Ambil data proposal berdasarkan id
$query = "SELECT * FROM proposal WHERE id = '$proposal_id'";
$result = mysqli_query($conn, $query);
$proposal = mysqli_fetch_assoc($result);

// Pastikan role valid sebelum mengakses status
if (!empty($role) && isset($proposal[$role])) {
    $current_status = $proposal[$role];
} else {
    $current_status = 'Pending'; // Default status jika role tidak valid atau tidak ada
}

// Proses validasi status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];

    // Update status berdasarkan role
    if ($role == 'PRD') {
        $query = "UPDATE proposal SET kaprodi = '$status' WHERE id = '$proposal_id'";
    } elseif ($role == 'KRD') {
        $query = "UPDATE proposal SET koordinator_hima = '$status' WHERE id = '$proposal_id'";
    } elseif ($role == 'FKT') {
        $query = "UPDATE proposal SET fakultas = '$status' WHERE id = '$proposal_id'";
    } elseif ($role == 'BKL') {
        $query = "UPDATE proposal SET bkal = '$status' WHERE id = '$proposal_id'";
    }

    if (mysqli_query($conn, $query)) {
        // Setelah update, arahkan kembali ke proposal.php
        header('Location: ../proposal.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>View Proposal</title>
</head>
<body>
    <h1>Detail Proposal</h1>
    <p>Title: <?= htmlspecialchars($proposal['title']); ?></p>
    <p>Description: <?= htmlspecialchars($proposal['description']); ?></p>
    <p>File: 
        <?php if (!empty($proposal['file_path']) && file_exists($proposal['file_path'])): ?>
            <a href="<?= $proposal['file_path']; ?>" target="_blank">Download File</a>
        <?php else: ?>
            No file uploaded
        <?php endif; ?>
    </p>

    <h2>Validasi Status</h2>
<form method="POST" onsubmit="return confirmSubmit()">
    <label for="status">Status: </label>
    <select name="status" id="status">
        <option value="Setuju" <?= $current_status == 'Setuju' ? 'selected' : ''; ?>>Setuju</option>
        <option value="Tidak Setuju" <?= $current_status == 'Tidak Setuju' ? 'selected' : ''; ?>>Tidak Setuju</option>
        <option value="Pending" <?= $current_status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
    </select>
    <br><br>
    <button type="submit">Submit</button>
</form>

<a href="proposal.php">Back to Home</a>

<script>
function confirmSubmit() {
    var status = document.getElementById("status").value;

    // Menampilkan alert berdasarkan pilihan status
    if (status === "Setuju") {
        return confirm("Apakah Anda yakin untuk menyetujui proposal ini?");
    } else if (status === "Tidak Setuju") {
        return confirm("Apakah Anda yakin untuk menolak proposal ini?");
    } else if (status === "Pending") {
        return confirm("Apakah Anda ingin mengembalikan status ke Pending?");
    }
    return true; // Jika status tidak dipilih atau ada kesalahan
}
</script>
