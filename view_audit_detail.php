<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role yang sesuai
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['audit_manager', 'student', 'faculty_admin', 'faculty_staff'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah ID laporan audit ada di URL
if (isset($_GET['id'])) {
    $audit_id = $_GET['id'];

    // Ambil data laporan audit dari database
    $stmt = $conn->prepare("SELECT * FROM audits WHERE id = ?");
    $stmt->bind_param("i", $audit_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Laporan audit tidak ditemukan.";
        exit;
    }

    $audit = $result->fetch_assoc();
} else {
    echo "ID laporan audit tidak diberikan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan Audit</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .alert {
            background-color: #f44336;
            color: white;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
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
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Detail Laporan Audit</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Detail Laporan Audit</h3>
            <p><strong>Departemen:</strong> <?php echo $audit['department']; ?></p>
            <p><strong>Tanggal Audit:</strong> <?php echo $audit['audit_date']; ?></p>
            <p><strong>Temuan:</strong> <?php echo nl2br($audit['findings']); ?></p>
            <p><strong>Langkah Perbaikan:</strong> <?php echo nl2br($audit['corrective_action']); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst($audit['status']); ?></p>

            <?php if ($_SESSION['role'] === 'faculty_admin'): ?>
                <!-- Tombol Edit hanya untuk faculty_admin -->
                <a href="edit_audit.php?id=<?php echo $audit['id']; ?>" class="btn btn-edit">Edit Laporan</a>
            <?php elseif ($_SESSION['role'] === 'faculty_staff'): ?>
                <!-- Pesan jika faculty_staff mencoba mengakses tombol edit -->
                <div class="alert">
                    Akses tidak bisa dilakukan. Hanya <strong>faculty_admin</strong> yang dapat mengedit laporan audit ini.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
