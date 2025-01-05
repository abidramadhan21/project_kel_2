<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];
include 'config/db.php';

// Ambil data proposal dari database dengan kondisi status yang lebih kompleks
$query = "
    SELECT *, 
        CASE 
            -- Jika semua status sudah disetujui
            WHEN kaprodi = 'Setuju' AND koordinator_hima = 'Setuju' AND fakultas = 'Setuju' AND bkal = 'Setuju' THEN 'Verified'
            -- Jika ada salah satu status yang ditolak
            WHEN kaprodi = 'Tidak Setuju' OR koordinator_hima = 'Tidak Setuju' OR fakultas = 'Tidak Setuju' OR bkal = 'Tidak Setuju' THEN 'Declined'
            -- Jika ada status yang Pending, periksa apakah sudah diupdate
            WHEN kaprodi = 'Pending' OR koordinator_hima = 'Pending' OR fakultas = 'Pending' OR bkal = 'Pending' THEN 
                CASE 
                    WHEN updated_at IS NOT NULL AND TIMESTAMPDIFF(MINUTE, updated_at, NOW()) < 5 THEN 'Updated'  -- Status berubah jadi 'Updated' setelah diedit dalam waktu 5 menit
                    WHEN updated_at IS NOT NULL THEN 'Pending'  -- Jika sudah diedit, ubah semua status menjadi Pending
                    ELSE 'Ongoing'  -- Jika tidak ada update terbaru
                END
            -- Jika proposal baru, status langsung Ongoing
            WHEN created_at IS NOT NULL AND updated_at IS NULL THEN 'Ongoing'
            ELSE 'Unknown'
        END AS status 
    FROM proposal
";


$result = mysqli_query($conn, $query);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/proposal.css">
    <title>Proposal</title>
    <script>
        // Fungsi untuk menampilkan pop-up jika ada flash message
        window.onload = function() {
            <?php if (isset($_SESSION['flash_message'])): ?>
                alert("<?= $_SESSION['flash_message']; ?>");
                <?php unset($_SESSION['flash_message']); ?> // Menghapus pesan setelah ditampilkan
            <?php endif; ?>
        }
    </script>
</head>
<body>
    <h2 style="font-size: 50px; margin-bottom: 20px;">Daftar Proposal</h2>
    <h3 style="font-size: 20px; margin-bottom: 20px;">Proposal yang ada</h3>
   
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>File</th>
            <th>Status Kaprodi</th>
            <th>Status Koordinator HIMA</th>
            <th>Status Fakultas</th>
            <th>Status BKAL</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($proposal = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= htmlspecialchars($proposal['id']); ?></td>
            <td><?= htmlspecialchars($proposal['title']); ?></td>
            <td><?= htmlspecialchars($proposal['description']); ?></td>
            <td>
            <?php if ($proposal['file_path']): ?>
    <!-- Button untuk melihat file -->
             <a href="crud/<?= htmlspecialchars($proposal['file_path']); ?>" target="_blank">
            <button style="background-color: #4CAF50; color: white; padding: 5px 5px; border: none; border-radius: 5px; cursor: pointer;">
            View File
             </button></a>
                <?php else: ?>
                    No file uploaded
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($proposal['kaprodi']); ?></td>
            <td><?= htmlspecialchars($proposal['koordinator_hima']) ?? 'Pending'; ?></td>
            <td><?= htmlspecialchars($proposal['fakultas']) ?? 'Pending'; ?></td>
            <td><?= htmlspecialchars($proposal['bkal']) ?? 'Pending'; ?></td>
            <td>
                <!-- Status keseluruhan proposal -->
                <?= htmlspecialchars($proposal['status']); ?>
            </td>
            <td>
                <!-- Tombol validasi untuk Kaprodi -->
                <?php if ($user['kode_role'] == 'PRD' && $proposal['kaprodi'] == 'Pending'): ?>
                     <a href="validate_proposal.php?id=<?= htmlspecialchars($proposal['id']); ?>&action=approve&role=PRD" style="text-decoration: none; color: white;">
                        <button class="btn-setuju">Setuju</button>
                     </a>
                    <a href="validate_proposal.php?id=<?= htmlspecialchars($proposal['id']); ?>&action=decline&role=PRD" style="text-decoration: none; color: white;">
                        <button class="btn-tolak">Tidak Setuju</button>
                    </a>
                <!-- Tombol validasi untuk Koordinator HIMA (KRD) -->    
                <?php elseif ($user['kode_role'] == 'KRD' && $proposal['kaprodi'] == 'Setuju' && $proposal['koordinator_hima'] == 'Pending'): ?>
                    <a href="validate_proposal.php?id=<?= htmlspecialchars($proposal['id']); ?>&action=approve&role=KRD" style="text-decoration: none; color: white;">
                        <button class="btn-setuju">Setuju</button>
                    </a>
                    <a href="validate_proposal.php?id=<?= htmlspecialchars($proposal['id']); ?>&action=decline&role=KRD" style="text-decoration: none; color: white;">
                        <button class="btn-tolak">Tidak Setuju</button>
                    </a>
                <!-- Tombol validasi untuk Fakultas -->
                <?php elseif ($user['kode_role'] == 'FKT' && $proposal['kaprodi'] == 'Setuju' && $proposal['koordinator_hima'] == 'Setuju' && $proposal['fakultas'] == 'Pending'): ?>
                     <a href="validate_proposal.php?id=<?= htmlspecialchars($proposal['id']); ?>&action=approve&role=FKT" style="text-decoration: none; color: white;">
                        <button class="btn-setuju">Setuju</button>
                     </a>
                    <a href="validate_proposal.php?id=<?= htmlspecialchars($proposal['id']); ?>&action=decline&role=FKT" style="text-decoration: none; color: white;">
                        <button class="btn-tolak">Tidak Setuju</button>
                    </a>
                <!-- Tombol validasi untuk BKAL -->    
                <?php elseif ($user['kode_role'] == 'BKL' && $proposal['kaprodi'] == 'Setuju' && $proposal['koordinator_hima'] == 'Setuju' && $proposal['fakultas'] == 'Setuju' && $proposal['bkal'] == 'Pending'): ?>
                    <a href="validate_proposal.php?id=<?= htmlspecialchars($proposal['id']); ?>&action=approve&role=BKL" style="text-decoration: none; color: white;">
                        <button class="btn-setuju">Setuju</button>
                    </a>
                    <a href="validate_proposal.php?id=<?= htmlspecialchars($proposal['id']); ?>&action=decline&role=BKL" style="text-decoration: none; color: white;">
                        <button class="btn-tolak">Tidak Setuju</button>
                    </a>
                <?php elseif ($user['kode_role'] != 'PRD' && $user['kode_role'] != 'KRD' && $user['kode_role'] != 'FKT' && $user['kode_role'] != 'BKL'): ?>
                    <!-- Tombol Edit dan Delete -->
                    <div style="display: flex; justify-content: space-between;">
                        <a href="crud/edit_proposal.php?id=<?= htmlspecialchars($proposal['id']); ?>" class="btn-edit">Edit</a>
                        <a href="crud/delete_proposal.php?id=<?= htmlspecialchars($proposal['id']); ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus proposal ini?')">Delete</a>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
