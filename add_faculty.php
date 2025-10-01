<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role faculty_admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty_admin') {
    header("Location: login.php");
    exit;
}

// Proses penyimpanan data fakultas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_name = $_POST['faculty_name'];
    $description = $_POST['description'];
    $student_count = $_POST['student_count'];
    $faculty_count = $_POST['faculty_count'];
    $user_id = $_SESSION['user_id']; // Menggunakan user_id untuk mengaitkan dengan pengguna yang login

    // Validasi input
    if (empty($faculty_name) || empty($description) || empty($student_count) || empty($faculty_count)) {
        $_SESSION['error'] = "Semua field harus diisi!";
        header("Location: add_faculty.php");
        exit;
    }

    // Insert data ke tabel fakultas
    $stmt = $conn->prepare("INSERT INTO faculty (user_id, faculty_name, description, student_count, faculty_count) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issii", $user_id, $faculty_name, $description, $student_count, $faculty_count);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Data fakultas berhasil ditambahkan!";
        header("Location: view_faculty_profile.php"); // Redirect setelah berhasil
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan data fakultas: " . $stmt->error;
        header("Location: add_faculty.php"); // Redirect kembali ke form
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Fakultas</title>
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
                <li><a href="view_faculty_profile.php">Lihat Profil Fakultas</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Tambah Data Fakultas</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Formulir Input Fakultas</h3>

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

            <!-- Form Input Data Fakultas -->
            <form action="add_faculty.php" method="POST">
                <label for="faculty_name">Nama Fakultas:</label>
                <input type="text" name="faculty_name" id="faculty_name" required>

                <label for="description">Prodi:</label>
                <textarea name="description" id="description" rows="5" required></textarea>

                <label for="student_count">Jumlah Mahasiswa:</label>
                <input type="number" name="student_count" id="student_count" required>

                <label for="faculty_count">Jumlah Dosen:</label>
                <input type="number" name="faculty_count" id="faculty_count" required>

                <button type="submit" class="btn-signin">Submit Data Fakultas</button>
            </form>
        </div>
    </div>
</body>
</html>
