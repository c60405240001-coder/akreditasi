<?php
session_start();
require_once 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Pastikan session aktif dan role benar
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'faculty_admin' && $_SESSION['role'] !== 'faculty_staff')) {
    header("Location: login.php");
    exit;
}

// Proses penyimpanan data laporan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_title = $_POST['report_title'];
    $report_date = $_POST['report_date'];
    $report_content = $_POST['report_content'];
    $status = 'draft'; // Default status untuk laporan yang di-submit
    $author = $_SESSION['user_id']; // ID pengguna yang menginput laporan

    // Validasi input
    if (empty($report_title) || empty($report_date) || empty($report_content)) {
        $_SESSION['error'] = "Semua field harus diisi!";
        header("Location: add_report.php");
        exit;
    }

    // Insert data ke tabel laporan
    $stmt = $conn->prepare("INSERT INTO reports (report_title, report_date, report_content, status, author) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $report_title, $report_date, $report_content, $status, $author);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Laporan berhasil ditambahkan!";
        header("Location: dashboard.php"); // Redirect setelah berhasil
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan laporan: " . $stmt->error;
        header("Location: add_report.php"); // Redirect kembali ke form
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Laporan</title>
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
            <h2>Tambah Laporan</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
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

            <!-- Form Input Laporan -->
            <form action="add_report.php" method="POST">
                <label for="report_title">Judul Laporan:</label>
                <input type="text" name="report_title" id="report_title" required>

                <label for="report_date">Tanggal Laporan:</label>
                <input type="date" name="report_date" id="report_date" required>

                <label for="report_content">Konten Laporan:</label>
                <textarea name="report_content" id="report_content" rows="5" required></textarea>

                <button type="submit" class="btn-signin">Submit Laporan</button>
            </form>
        </div>
    </div>
</body>
</html>
