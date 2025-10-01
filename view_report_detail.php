<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID laporan dari URL
$report_id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($report_id == 0) {
    die("Laporan tidak ditemukan.");
}

// Ambil detail laporan dari database
$query = "SELECT * FROM reports WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Laporan tidak ditemukan.");
}

$report = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan</title>
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
                <li><a href="view_reports.php">Lihat Laporan</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Detail Laporan: <?php echo $report['report_title']; ?></h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Judul: <?php echo $report['report_title']; ?></h3>
            <p><strong>Tanggal: </strong><?php echo $report['report_date']; ?></p>
            <p><strong>Isi Laporan:</strong><br><?php echo nl2br($report['report_content']); ?></p>

            <a href="edit_report.php?id=<?php echo $report['id']; ?>" class="btn-register">Edit Laporan</a>
            <a href="delete_report.php?id=<?php echo $report['id']; ?>" class="btn-register" style="color:red;">Hapus Laporan</a>
        </div>
    </div>
</body>
</html>
