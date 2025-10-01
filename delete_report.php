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

// Query untuk menghapus laporan
$query = "DELETE FROM reports WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $report_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Laporan berhasil dihapus!";
    header("Location: view_reports.php");  // Redirect setelah berhasil
    exit;
} else {
    $_SESSION['error'] = "Gagal menghapus laporan. Silakan coba lagi.";
    header("Location: view_reports.php");  // Redirect kembali dengan pesan error
    exit;
}
?>
