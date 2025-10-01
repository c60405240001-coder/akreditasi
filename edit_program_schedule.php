<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role program_coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'program_coordinator') {
    header("Location: login.php");
    exit;
}

// Cek apakah ID jadwal program ada di URL
if (isset($_GET['id'])) {
    $schedule_id = $_GET['id'];

    // Ambil data jadwal program berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM program_schedule WHERE id = ?");
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Jadwal program tidak ditemukan.";
        exit;
    }

    $schedule = $result->fetch_assoc();
} else {
    echo "ID jadwal program tidak diberikan.";
    exit;
}

// Proses penyimpanan data yang diubah
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $program_name = $_POST['program_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $description = $_POST['description'];

    // Validasi input
    if (empty($program_name) || empty($start_date) || empty($end_date)) {
        $_SESSION['error'] = "Semua field harus diisi!";
        header("Location: edit_program_schedule.php?id=" . $schedule_id);
        exit;
    }

    // Update data jadwal program
    $stmt = $conn->prepare("UPDATE program_schedule SET program_name = ?, start_date = ?, end_date = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $program_name, $start_date, $end_date, $description, $schedule_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Jadwal program berhasil diperbarui!";
        header("Location: program_schedule.php"); // Redirect setelah berhasil
        exit;
    } else {
        $_SESSION['error'] = "Gagal memperbarui jadwal program.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jadwal Program</title>
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
            <h2>Edit Jadwal Program</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Edit Jadwal Program</h3>

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

            <!-- Form Input Jadwal Program -->
            <form action="edit_program_schedule.php?id=<?php echo $schedule['id']; ?>" method="POST">
                <label for="program_name">Nama Program:</label>
                <input type="text" name="program_name" id="program_name" value="<?php echo $schedule['program_name']; ?>" required>

                <label for="start_date">Tanggal Mulai:</label>
                <input type="date" name="start_date" id="start_date" value="<?php echo $schedule['start_date']; ?>" required>

                <label for="end_date">Tanggal Selesai:</label>
                <input type="date" name="end_date" id="end_date" value="<?php echo $schedule['end_date']; ?>" required>

                <label for="description">Deskripsi:</label>
                <textarea name="description" id="description" rows="5"><?php echo $schedule['description']; ?></textarea>

                <button type="submit" class="btn-signin">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</body>
</html>
