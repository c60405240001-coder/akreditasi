<?php
session_start();
require_once 'config.php'; // Pastikan koneksi ke database

// Cek apakah pengguna sudah login dan apakah role accreditation_officer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'accreditation_officer') {
    header("Location: login.php");
    exit;
}

// Cek apakah ID proses akreditasi ada di URL
if (isset($_GET['id'])) {
    $accreditation_id = $_GET['id'];

    // Ambil data proses akreditasi dari database
    $stmt = $conn->prepare("SELECT * FROM accreditation_process WHERE id = ?");
    $stmt->bind_param("i", $accreditation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Proses akreditasi tidak ditemukan.";
        exit;
    }

    $accreditation = $result->fetch_assoc();
} else {
    echo "ID proses akreditasi tidak diberikan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Proses Akreditasi</title>
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
                <li><a href="verify_documents.php">Verifikasi Dokumen</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Detail Proses Akreditasi</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Detail Proses Akreditasi: <?php echo $accreditation['program_name']; ?></h3>
            <p><strong>Nama Program:</strong> <?php echo $accreditation['program_name']; ?></p>
            <p><strong>Status Akreditasi:</strong> <?php echo ucfirst($accreditation['accreditation_status']); ?></p>
            <p><strong>Tanggal Dibuat:</strong> <?php echo $accreditation['created_at']; ?></p>
        </div>
    </div>
</body>
</html>
