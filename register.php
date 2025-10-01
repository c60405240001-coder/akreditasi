<?php
session_start();
require_once 'config.php'; // Koneksi ke database
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Validasi password
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Password dan konfirmasi password tidak cocok!";
        header("Location: register.php");
        exit;
    }

    // Enkripsi password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Query untuk memasukkan data ke database
    $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $hashed_password, $role);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Akun berhasil dibuat. Silakan login!";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal membuat akun. Silakan coba lagi.";
        header("Location: register.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun</title>
    <link rel="stylesheet" href="style.css"> <!-- Gaya CSS Terpisah -->
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Registrasi Akun</h2>

            <!-- Menampilkan pesan error atau sukses -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Form Registrasi -->
            <form action="register.php" method="POST">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Masukkan Email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan Password" required>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                </div>

                <!-- Pilihan Role -->
                <div class="input-group">
                    <label for="role">Role</label>
                    <select name="role" id="role" required>
                        <option value="admin">Admin</option>
                        <option value="qa_officer">QA Officer</option>
                        <option value="faculty_staff">Faculty Staff</option>
                        <option value="program_coordinator">Program Coordinator</option>
                        <option value="accreditation_officer">Accreditation Officer</option>
                        <option value="audit_manager">Audit Manager</option>
                        <option value="student">Student</option>
                        <option value="faculty_admin">Faculty Admin</option>
                    </select>
                </div>

                <button type="submit" name="register" class="btn-signin">Daftar</button>
            </form>
            
            <p>Sudah punya akun? <a href="login.php" class="btn-register">Login di sini</a></p>
        </div>
    </div>
</body>
</html>
