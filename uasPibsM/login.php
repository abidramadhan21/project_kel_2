<?php
session_start();
include 'config/db.php';

// Periksa jika ada pesan error di sesi dan hapus setelah ditampilkan
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']); // Hapus pesan error setelah ditampilkan
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];  

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password); // 'ss' untuk string (username dan password)
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['user'] = $user;
        header('Location: dashboard.php');
        exit();
    } else {
        $_SESSION['error'] = "Username atau password salah!";
        header('Location: login.php');
        exit();
    }
}
?>

<style>

body {
    background: linear-gradient(45deg, #0a3d61, #276a8e, #6cb4e3);
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    margin: 0;
    padding: 0;
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: background-color 1s ease-out;
    animation: fadeIn 1.5s ease-out;
}

h1 {
    text-align: center;
    color: #F5EFE7;
    margin-bottom: 20px;
    font-size: 2.5rem;
}

p {
    color: #FF6B6B;
    font-size: 1rem;
    margin: 10px 0;
    text-align: center;
}

.register a {
    text-decoration: none;
    color: black;
}

.register a:hover {
    text-decoration: none;
    color: #1a2b45;
    transform: scale(1.01);
}

.container {
    background-color: #3E5879;
    padding: 50px;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); 
    text-align: center;
    width: 100%;
    max-width: 450px; 
    margin-bottom: 50px; 
    box-sizing: border-box;
}

input[type="text"], 
input[type="password"], 
button {
    width: 100%;
    padding: 14px 18px;
    margin: 15px 0;
    border: none;
    border-radius: 25px;
    outline: none;
    background-color: #F5EFE7;
    color: #213555;
    font-size: 1rem;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
    box-sizing: border-box;
}

input[type="text"]::placeholder, 
input[type="password"]::placeholder {
    color: #999;
}

input[type="text"]:focus, 
input[type="password"]:focus {
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
    transform: scale(1.02);
}

button {
    background-color: #213555;
    color: #F5EFE7;
    border-radius: 30px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    padding: 14px 18px;
    margin-top: 20px;
}

button:hover {
    background-color: #18273e;
    transform: scale(1.05);
}

button:active {
    background-color: #18273e;
    transform: scale(0.98);
}


/** ANIMATION */
@keyframes fadeIn {
    0% {
        transform: translateY(-30px);
        opacity: 0;
    }
    100% {
        transform: translateX(0);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .container {
        padding: 25px;
        margin: 0 10px; /* Mengurangi jarak margin */
    }
    h1 {
        font-size: 2.2rem;
    }
}

@media (max-width: 480px) {
    body {
        font-size: 14px;
    }

    h1 {
        font-size: 2rem;
    }

    .container {
        padding: 20px;
    }

    input[type="text"], 
    input[type="password"], 
    button {
        font-size: 0.9rem;
        padding: 12px 15px;
    }
}

</style>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <div class="container">
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
            <div class="register">
            <p style="color: #F5EFE7;">Belum memiliki akun?  <a href="register.php">Daftar di sini</a></p>
            </div>
        </form>
        <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    </div>
</body>
</html>
