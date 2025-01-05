<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['kode_role'] != 'MHS') {
    header('Location: ../login.php');
    exit();
}

include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $created_by = $_SESSION['user']['id_user'];

    // Memeriksa apakah folder uploads ada, jika tidak maka buat folder tersebut
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true); // Membuat folder dengan izin penuh
    }

    // Proses upload file
    if (isset($_FILES['file'])) {
        $file_name = time() . '_' . basename($_FILES['file']['name']); // Menambahkan waktu agar nama file unik
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_error = $_FILES['file']['error'];

        // Cek jika ada error pada upload
        if ($file_error === 0) {
            // Memeriksa ukuran file
            $max_size = 5 * 1024 * 1024; // 5MB
            if ($file_size > $max_size) {
                echo "Error: File size exceeds the limit.";
                exit();
            }

            // Memeriksa jenis file yang diizinkan
            $allowed_extensions = ['pdf', 'docx', 'jpg', 'jpeg'];
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

            if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                echo "Error: Invalid file type.";
                exit();
            }

            // Menentukan folder tujuan untuk menyimpan file
            $file_dest = 'uploads/' . $file_name;

            // Memindahkan file ke folder tujuan
            if (move_uploaded_file($file_tmp, $file_dest)) {
                // Simpan data proposal ke database termasuk file path
                $query = "INSERT INTO proposal (title, description, created_by, file_path) 
                          VALUES ('$title', '$description', '$created_by', '$file_dest')";
                if (mysqli_query($conn, $query)) {
                    header('Location: ../dashboard.php');
                    exit();
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                echo "Error: File upload failed.";
            }
        } else {
            echo "Error: There was an error uploading the file.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buat Proposal</title>
</head>

<style>
    h1 {
        text-align: center;
        color: #2c3e50;
    }

    form {
        background-color: #34495e;
        color: #ecf0f1;
        width: 50%;
        margin: 20px auto;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    label {
        display: block;
        margin: 10px 0 5px;
        font-weight: bold;
    }

    input[type="text"],
    textarea,
    input[type="file"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #bdc3c7;
        border-radius: 5px;
        background-color: #ecf0f1;
        color: #2c3e50;
    }

    button {
        background-color: #2980b9;
        color: #ecf0f1;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        width: 100%; 
        box-sizing: border-box; 
        transition: background-color 0.3s ease, padding-left 0.3s ease;
    }

    button:hover {
        background-color:rgb(34, 98, 141);
    }


    input[type="text"]:focus,
    textarea:focus,
    input[type="file"]:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
    }    
</style>

<body>
    <h1>Buat Proposal Baru</h1>
    <form method="POST" enctype="multipart/form-data" action="crud/create_proposal.php">
        <label>Judul Proposal:</label><br>
        <input type="text" name="title" required><br>
        <label>Deskripsi:</label><br>
        <textarea name="description" required></textarea><br><br>
        <label>Upload File:</label><br>
        <input type="file" name="file" required><br><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
