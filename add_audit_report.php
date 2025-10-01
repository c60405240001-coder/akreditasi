<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role audit_manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'audit_manager') {
    header("Location: login.php");
    exit;
}

// Proses tambah laporan audit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department = $_POST['department'];
    $audit_date = $_POST['audit_date'];
    $findings = $_POST['findings'];
    $corrective_action = $_POST['corrective_action'];
    $status = 'planned';  // Status awal adalah 'planned'

    // Insert data laporan audit
    $stmt = $conn->prepare("INSERT INTO audits (department, audit_date, findings, corrective_action, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $department, $audit_date, $findings, $corrective_action, $status);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Laporan audit berhasil ditambahkan!";
        header("Location: view_audit_reports.php");
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
    <title>Tambah Laporan Audit</title>
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
            <h2>Tambah Laporan Audit</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Form Tambah Laporan Audit</h3>
            <form action="add_audit_report.php" method="POST">
                <label for="department">Departemen:</label>
                <input type="text" name="department" id="department" required>

                <label for="audit_date">Tanggal Audit:</label>
                <input type="date" name="audit_date" id="audit_date" required>

                <label for="findings">Temuan:</label>
                <textarea name="findings" id="findings" required></textarea>

                <label for="corrective_action">Langkah Perbaikan:</label>
                <textarea name="corrective_action" id="corrective_action" required></textarea>

                <button type="submit" class="btn-signin">Tambah Laporan</button>
            </form>
        </div>
    </div>
</body>
</html>
