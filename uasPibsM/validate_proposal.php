<?php
session_start();
include 'config/db.php';

// Jika parameter untuk membuat proposal baru ada
if (isset($_GET['action']) && $_GET['action'] === 'create') {
    // Ambil data dari form input
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];

    // Masukkan data proposal baru ke database
    $stmt = $conn->prepare("INSERT INTO proposal (judul, deskripsi, status, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param('sss', $judul, $deskripsi, $status);

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = 'Proposal berhasil dibuat dengan status Pending.';
    } else {
        $_SESSION['flash_message'] = 'Gagal membuat proposal.';
    }
    $stmt->close();
    header('Location: dashboard.php');
    exit();
}

// Logika untuk pembaruan status
if (isset($_GET['id'], $_GET['action'], $_GET['role'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    $role = $_GET['role'];

    // Tentukan kolom yang akan diperbarui berdasarkan role
    $column = '';
    switch ($role) {
        case 'PRD':
            $column = 'kaprodi';
            break;
        case 'KRD':
            $column = 'koordinator_hima';
            break;
        case 'FKT':
            $column = 'fakultas';
            break;
        case 'BKL':
            $column = 'bkal';
            break;
        default:
            $_SESSION['flash_message'] = 'Role tidak valid.';
            header('Location: dashboard.php');
            exit();
    }

    // Validasi aksi (approve/decline)
    $status = '';
    if ($action === 'approve') {
        $status = 'Setuju';
    } elseif ($action === 'decline') {
        $status = 'Tidak Setuju';
    } else {
        $_SESSION['flash_message'] = 'Aksi tidak valid.';
        header('Location: dashboard.php');
        exit();
    }

    // Update status role di database
    $stmt = $conn->prepare("UPDATE proposal SET $column = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
    if ($stmt->execute()) {
        // Periksa status semua role setelah pembaruan
        $query = "SELECT kaprodi, koordinator_hima, fakultas, bkal, status FROM proposal WHERE id = ?";
        $stmt_check = $conn->prepare($query);
        $stmt_check->bind_param('i', $id);
        $stmt_check->execute();
        $stmt_check->bind_result($kaprodi, $koordinator_hima, $fakultas, $bkal, $current_status);
        $stmt_check->fetch();
        $stmt_check->close();

        // Tentukan status keseluruhan berdasarkan status role
        if ($kaprodi === 'Tidak Setuju' || $koordinator_hima === 'Tidak Setuju' || $fakultas === 'Tidak Setuju' || $bkal === 'Tidak Setuju') {
            $overall_status = 'Declined'; // Salah satu Tidak Setuju
        }

        elseif ($kaprodi === 'Pending' || $koordinator_hima === 'Pending' || $fakultas === 'Pending' || $bkal === 'Pending') {
            $overall_status = 'Pending'; // Jika ada status Pending
        }

        elseif ($kaprodi === 'Setuju' && $koordinator_hima === 'Setuju' && $fakultas === 'Setuju' && $bkal === 'Setuju') {
            $overall_status = 'Verified'; // Semua Setuju
        }

        else {
            $overall_status = 'Ongoing'; // Belum semua status diperbarui
        }

        // Jika status saat ini adalah Declined dan proposal diedit, set status ke Updated
        if ($current_status === 'Declined') {
            $overall_status = 'Updated';
        }

        // Update status keseluruhan
        $update_status_stmt = $conn->prepare("UPDATE proposal SET status = ? WHERE id = ?");
        $update_status_stmt->bind_param('si', $overall_status, $id);
        $update_status_stmt->execute();
        $update_status_stmt->close();

        $_SESSION['flash_message'] = 'Status berhasil diperbarui.';
    } else {
        $_SESSION['flash_message'] = 'Gagal memperbarui status.';
    }
    $stmt->close();
    header('Location: dashboard.php');
    exit();
}
?>
