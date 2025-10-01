<?php
session_start();
require_once 'config.php';

// Pastikan session aktif dan role benar
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'qa_officer') {
    header("Location: login.php");
    exit;
}

// Query untuk mengambil laporan dengan status 'draft'
$query = "SELECT id, report_title, report_date, status FROM reports WHERE status = 'draft'";
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
    <title>Tinjau Laporan</title>
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
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Tinjau Laporan (QA Officer)</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Daftar Laporan yang Perlu Ditinjau</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Judul Laporan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($report = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $report['id']; ?></td>
                        <td><?php echo $report['report_title']; ?></td>
                        <td><?php echo $report['report_date']; ?></td>
                        <td><?php echo ucfirst($report['status']); ?></td>
                        <td>
                            <a href="view_report_detail.php?id=<?php echo $report['id']; ?>">Lihat Detail</a> | 
                            <a href="approve_documents.php?id=<?php echo $report['id']; ?>&action=approved" class="btn btn-approve">Setujui</a> | 
                            <a href="approve_documents.php?id=<?php echo $report['id']; ?>&action=rejected" class="btn btn-reject">Tolak</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
