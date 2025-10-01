<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role accreditation_officer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'accreditation_officer') {
    header("Location: login.php");
    exit;
}

// Ambil data proses akreditasi dari database
$query = "SELECT id, program_name, accreditation_status FROM accreditation_process";  // Pastikan tabel 'accreditation_process' ada
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
    <title>Proses Akreditasi</title>
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
                <li><a href="accreditation_process.php">Proses Akreditasi</a></li>
                <li><a href="verify_documents.php">Verifikasi Dokumen</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Proses Akreditasi</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Daftar Proses Akreditasi</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nama Program</th>
                    <th>Status Akreditasi</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($process = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $process['id']; ?></td>
                        <td><?php echo $process['program_name']; ?></td>
                        <td><?php echo $process['accreditation_status']; ?></td>
                        <td><a href="view_accreditation_details.php?id=<?php echo $process['id']; ?>">Lihat Detail</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
