<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Cek apakah ID pengguna ada di URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Hapus data pengguna dari database
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Pengguna berhasil dihapus!";
        header("Location: manage_users.php"); // Redirect ke halaman manage_users setelah berhasil
        exit;
    } else {
        $_SESSION['error'] = "Gagal menghapus pengguna. Silakan coba lagi.";
        header("Location: manage_users.php"); // Redirect kembali dengan pesan error
        exit;
    }
} else {
    $_SESSION['error'] = "ID pengguna tidak diberikan.";
    header("Location: manage_users.php"); // Redirect kembali jika ID tidak ditemukan
    exit;
}
?>
