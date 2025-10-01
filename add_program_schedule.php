<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role program_coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'program_coordinator') {
    header("Location: login.php");
    exit;
}

// Proses penyimpanan data jadwal program
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $program_name = $_POST['program_name'];
    $program_date = $_POST['program_date'];
    $status = $_POST['status'];

    // Validasi input
    if (empty($program_name) || empty($program_date) || empty($status)) {
        $_SESSION['error'] = "Semua field harus diisi!";
        header("Location: add_program_schedule.php");
        exit;
    }

    // Masukkan data jadwal program ke database
    $stmt = $conn->prepare("INSERT INTO program_schedule (program_name, program_date, status) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $program_name, $program_date, $status);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Jadwal program berhasil ditambahkan!";
        header("Location: program_schedule.php"); // Redirect setelah berhasil
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan jadwal program.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jadwal Program</title>
    <link rel="stylesheet" href="style.css">  <!-- Gaya CSS -->
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
            <h2>Tambah Jadwal Program</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Form Penambahan Jadwal Program</h3>

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

            <!-- Form untuk tambah jadwal program -->
            <form action="add_program_schedule.php" method="POST">
                <label for="program_name">Nama Program:</label>
                <input type="text" name="program_name" id="program_name" required>

                <label for="program_date">Tanggal Program:</label>
                <input type="date" name="program_date" id="program_date" required>

                <label for="status">Status Program:</label>
                <select name="status" id="status">
                    <option value="planned">Planned</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>

                <button type="submit" class="btn-signin">Tambah Jadwal Program</button>
            </form>
        </div>
    </div>
</body>
</html>
