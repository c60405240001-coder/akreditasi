<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role audit_manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'audit_manager') {
    header("Location: login.php");
    exit;
}

// Ambil daftar laporan audit dari database
$query = "SELECT id, department, audit_date, status FROM audits";  // Pastikan tabel 'audits' ada
$result = $conn->query($query);

if (!$result) {
    die("Query Error: " . $conn->error);  // Menampilkan pesan error jika query gagal
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Laporan Audit</title>
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
                <li><a href="view_audit_reports.php">Lihat Laporan Audit</a></li>
                <li><a href="manage_audit.php">Kelola Audit</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Lihat Laporan Audit</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Daftar Laporan Audit</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Departemen</th>
                    <th>Tanggal Audit</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($audit = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $audit['id']; ?></td>
                        <td><?php echo $audit['department']; ?></td>
                        <td><?php echo $audit['audit_date']; ?></td>
                        <td><?php echo ucfirst($audit['status']); ?></td>
                        <td><a href="view_audit_detail.php?id=<?php echo $audit['id']; ?>">Lihat Detail</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
