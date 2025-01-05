<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['kode_role'] != 'MHS') {
    header('Location: ../login.php');
    exit();
}

include '../config/db.php';

$proposal_id = $_GET['id'];
$query = "DELETE FROM proposal WHERE id = $proposal_id";
if (mysqli_query($conn, $query)) {
    header('Location: ../dashboard.php');
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
