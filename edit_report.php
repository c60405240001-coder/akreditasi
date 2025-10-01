<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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

// Proses pengeditan laporan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_title = $_POST['report_title'];
    $report_date = $_POST['report_date'];
    $report_content = $_POST['report_content'];

    // Validasi input
    if (empty($report_title) || empty($report_date) || empty($report_content)) {
        $_SESSION['error'] = "Semua kolom harus diisi!";
        header("Location: edit_report.php?id=" . $report_id);
        exit;
    }

    // Query untuk memperbarui laporan
    $update_query = "UPDATE reports SET report_title = ?, report_date = ?, report_content = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssi", $report_title, $report_date, $report_content, $report_id);

    if ($update_stmt->execute()) {
        $_SESSION['message'] = "Laporan berhasil diperbarui!";
        header("Location: view_reports.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal memperbarui laporan. Silakan coba lagi.";
        header("Location: edit_report.php?id=" . $report_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan</title>
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
            <h2>Edit Laporan: <?php echo $report['report_title']; ?></h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Form Edit Laporan</h3>

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

            <!-- Form Edit Laporan -->
            <form action="edit_report.php?id=<?php echo $report['id']; ?>" method="POST">
                <div class="input-group">
                    <label for="report_title">Judul Laporan</label>
                    <input type="text" id="report_title" name="report_title" value="<?php echo $report['report_title']; ?>" required>
                </div>
                <div class="input-group">
                    <label for="report_date">Tanggal</label>
                    <input type="date" id="report_date" name="report_date" value="<?php echo $report['report_date']; ?>" required>
                </div>
                <div class="input-group">
                    <label for="report_content">Isi Laporan</label>
                    <textarea id="report_content" name="report_content" rows="10" required><?php echo $report['report_content']; ?></textarea>
                </div>
                <button type="submit" class="btn-signin">Update Laporan</button>
            </form>
        </div>
    </div>
</body>
</html>
