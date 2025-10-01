<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Proses penyimpanan data pengguna baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validasi password
    if (empty($email) || empty($password) || empty($role)) {
        $_SESSION['error'] = "Semua kolom harus diisi!";
        header("Location: add_user.php");
        exit;
    }

    // Enkripsi password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Query untuk memasukkan data pengguna baru ke database
    $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $hashed_password, $role);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Pengguna baru berhasil ditambahkan!";
        header("Location: manage_users.php"); // Redirect setelah berhasil
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan pengguna. Silakan coba lagi.";
        header("Location: add_user.php"); // Redirect kembali dengan pesan error
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h2>Dashboard</h2>
        </div>
        <div class="sidebar-menu">
            <h3>Menu</h3>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php">Kelola Pengguna</a></li>
                <li><a href="view_reports.php">Lihat Laporan</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Tambah Pengguna</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Form Tambah Pengguna Baru</h3>

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

            <!-- Form Tambah Pengguna -->
            <form action="add_user.php" method="POST">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Masukkan Email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan Password" required>
                </div>
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
                <button type="submit" class="btn-signin">Tambah Pengguna</button>
            </form>
        </div>
    </div>
</body>
</html>
