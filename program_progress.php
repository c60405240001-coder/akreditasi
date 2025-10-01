<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role program_coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'program_coordinator') {
    header("Location: login.php");
    exit;
}

// Ambil progres program dari database
$query = "SELECT id, program_name, progress, status FROM program_progress";  // Pastikan tabel 'program_progress' ada
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
    <title>Lihat Progres Program</title>
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
                <li><a href="program_schedule.php">Kelola Jadwal Program</a></li>
                <li><a href="program_progress.php">Lihat Progres Program</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Lihat Progres Program</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Daftar Progres Program</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nama Program</th>
                    <th>Progres</th>
                    <th>Status</th>
                </tr>
                <?php while ($progress = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $progress['id']; ?></td>
                        <td><?php echo $progress['program_name']; ?></td>
                        <td><?php echo $progress['progress']; ?></td>
                        <td><?php echo $progress['status']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
