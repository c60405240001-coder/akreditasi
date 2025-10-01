<?php
session_start();
require_once 'config.php'; // Pastikan koneksi ke database
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query untuk mencari pengguna berdasarkan email
    $stmt = $conn->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Cek apakah email ada dan password cocok
    if ($user && password_verify($password, $user['password'])) {
        // Menyimpan data user ke session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Redirect ke halaman dashboard.php setelah login berhasil
        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akreditasi</title>
    <link rel="stylesheet" href="style.css"> <!-- Gaya CSS Terpisah -->
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Akreditasi</h2>

            <!-- Menampilkan pesan error atau sukses -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <h3>Login</h3>
            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Masukkan Email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan Password" required>
                </div>
                <button type="submit" name="login" class="btn-signin">Sign In</button>
            </form>

            <p>Belum punya akun? <a href="register.php" class="btn-register">Daftar di sini</a></p>
        </div>
    </div>
</body>
</html>
