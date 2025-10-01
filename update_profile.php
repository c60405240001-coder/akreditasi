<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = $_POST['email'];

    // Validasi email baru
    if (empty($new_email)) {
        $_SESSION['error'] = "Email tidak boleh kosong!";
        header("Location: profile.php");
        exit;
    }

    // Update email pengguna di database
    $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
    $stmt->bind_param("si", $new_email, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Profil berhasil diperbarui!";
        header("Location: profile.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal memperbarui profil.";
    }
}
?>
