<?php
session_start();
require_once 'config.php'; // untuk akses log_action()

// Simpan info user sebelum session dihancurkan
$userId = $_SESSION['user_id'] ?? null;
$userEmail = $_SESSION['email'] ?? 'unknown';

// Log aksi logout
if ($userId) {
    log_action($conn, "logout", "users", $userId);
} else {
    log_action($conn, "logout_unknown", "users", null);
}

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke halaman login dengan pesan sukses
session_start(); // mulai lagi supaya bisa set pesan
$_SESSION['message'] = "Anda telah berhasil logout.";
header("Location: login.php");
exit;
?>
