<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role audit_manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'audit_manager') {
    header("Location: login.php");
    exit;
}

// Proses menambah laporan audit baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department = $_POST['department'];
    $audit_date = $_POST['audit_date'];
    $start_date = $_POST['start_date'];  // Tambah input untuk start date
    $end_date = $_POST['end_date'];      // Tambah input untuk end date
    $findings = $_POST['findings'];
    $corrective_action = $_POST['corrective_action'];
    $status = $_POST['status'];

    // Insert data audit baru ke database
    $stmt = $conn->prepare("INSERT INTO audits (department, audit_date, start_date, end_date, findings, corrective_action, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $department, $audit_date, $start_date, $end_date, $findings, $corrective_action, $status);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Laporan audit berhasil ditambahkan!";
        header("Location: manage_audit.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan laporan audit.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Audit</title>
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
            <h2>Kelola Audit</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Tambah Laporan Audit</h3>
            
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

            <form action="manage_audit.php" method="POST">
                <label for="department">Departemen:</label>
                <input type="text" name="department" id="department" required>

                <label for="audit_date">Tanggal Audit:</label>
                <input type="date" name="audit_date" id="audit_date" required>

                <label for="start_date">Tanggal Mulai:</label>
                <input type="date" name="start_date" id="start_date" required>

                <label for="end_date">Tanggal Selesai:</label>
                <input type="date" name="end_date" id="end_date" required>

                <label for="findings">Temuan:</label>
                <textarea name="findings" id="findings" required></textarea>

                <label for="corrective_action">Tindakan Korektif:</label>
                <textarea name="corrective_action" id="corrective_action" required></textarea>

                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="planned">Rencanakan</option>
                    <option value="in_progress">Sedang Berjalan</option>
                    <option value="completed">Selesai</option>
                </select>

                <button type="submit" class="btn-signin">Tambah Laporan</button>
            </form>
        </div>
    </div>
</body>
</html>
