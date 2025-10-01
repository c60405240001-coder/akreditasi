<?php
session_start();
require_once 'config.php'; // Pastikan koneksi ke database

// Cek apakah pengguna sudah login dan apakah role accreditation_officer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'accreditation_officer') {
    header("Location: login.php");
    exit;
}

// Proses menambah program akreditasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $program_name = $_POST['program_name'];
    $accreditation_status = $_POST['accreditation_status'];

    // Validasi input
    if (empty($program_name) || empty($accreditation_status)) {
        $_SESSION['error'] = "Nama Program dan Status Akreditasi harus diisi!";
        header("Location: add_accreditation_process.php");
        exit;
    }

    // Insert data ke tabel accreditation_process
    $stmt = $conn->prepare("INSERT INTO accreditation_process (program_name, accreditation_status) VALUES (?, ?)");
    $stmt->bind_param("ss", $program_name, $accreditation_status);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Proses Akreditasi berhasil ditambahkan!";
        header("Location: accreditation_process.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambah proses akreditasi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Proses Akreditasi</title>
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
                <li><a href="accreditation_process.php">Proses Akreditasi</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Tambah Proses Akreditasi</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Form Tambah Proses Akreditasi</h3>

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

            <form action="add_accreditation_process.php" method="POST">
                <label for="program_name">Nama Program:</label>
                <input type="text" name="program_name" id="program_name" required>

                <label for="accreditation_status">Status Akreditasi:</label>
                <select name="accreditation_status" id="accreditation_status">
                    <option value="planned">Planned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>

                <button type="submit" class="btn-signin">Simpan Proses Akreditasi</button>
            </form>
        </div>
    </div>
</body>
</html>
