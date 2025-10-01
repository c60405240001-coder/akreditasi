<?php
$servername = "localhost";
$username = "root";  // Username default untuk XAMPP
$password = "";  // Password default untuk XAMPP
$dbname = "iqa";  // Nama database yang digunakan

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

function log_action($conn, $action, $entity, $entity_id = null) {
    // Data dari session & server
    $actorEmail = $_SESSION['email'] ?? 'unknown';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    // Query insert log
    $sql = "INSERT INTO audit_logs (actor, action, entity, entity_id, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $actorEmail, $action, $entity, $entity_id, $ip, $ua);
    $stmt->execute();
    $stmt->close();
}
?>
