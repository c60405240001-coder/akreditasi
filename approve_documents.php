<?php
session_start();
require_once 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Pastikan session aktif dan role benar
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'qa_officer') {
    header("Location: login.php");
    exit;
}

// Jika ada aksi approve/reject
if (isset($_GET['id'], $_GET['action'])) {
    $report_id = (int)$_GET['id'];  // Ambil ID laporan
    $action = $_GET['action'];  // Aksi approve atau reject
    
    if (in_array($action, ['approved', 'rejected'], true)) {
        // Update status pada tabel 'reports'
        $stmt = $conn->prepare("UPDATE reports SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $report_id);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $_SESSION['message'] = "Laporan berhasil " . ($action === 'approved' ? 'disetujui' : 'ditolak') . "!";

            // Logging approve/reject
            log_action($conn, "report_{$action}", "reports", $report_id);
        } else {
            $_SESSION['error'] = "Tidak ada perubahan. Periksa ID/status.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Aksi tidak valid.";
    }
    header("Location: approve_documents.php");  // Redirect ke halaman yang sama setelah aksi
    exit;
}

// Ambil semua laporan (termasuk yang status 'draft', 'approved', dan 'rejected')
$query = "SELECT id, report_title, report_date, status FROM reports";
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
    <title>Approve Laporan</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f4f4f4;
        }
        .btn {
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            color: white;
        }
        .btn-approve { background: green; }
        .btn-reject { background: red; }
        .alert { margin: 8px 0; padding: 10px; border-radius: 4px; }
        .alert-success { background: #e8f5e9; }
        .alert-error { background: #ffebee; }
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
            <h2>Approve Laporan</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Daftar Laporan untuk Persetujuan</h3>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Judul Laporan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($report = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $report['id']; ?></td>
                            <td><?php echo $report['report_title']; ?></td>
                            <td><?php echo $report['report_date']; ?></td>
                            <td><?php echo ucfirst($report['status']); ?></td>
                            <td>
                                <?php if ($report['status'] === 'draft'): ?>
                                    <a href="approve_documents.php?id=<?php echo $report['id']; ?>&action=approved" class="btn btn-approve">Approve</a>
                                    <a href="approve_documents.php?id=<?php echo $report['id']; ?>&action=rejected" class="btn btn-reject">Reject</a>
                                <?php else: ?>
                                    <span>Status: <?php echo ucfirst($report['status']); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">Tidak ada dokumen yang tersedia.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>
