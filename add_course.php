<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role yang sesuai
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['faculty_admin', 'faculty_staff'])) {
    header("Location: login.php");
    exit;
}

// Proses penyimpanan data mata kuliah
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $course_code = $_POST['course_code'];
    $credits = $_POST['credits'];
    $program_studi = $_POST['program_studi'];  // Menggunakan program_studi

    // Validasi input
    if (empty($course_name) || empty($course_code) || empty($credits) || empty($program_studi)) {
        $_SESSION['error'] = "Semua field harus diisi!";
        header("Location: add_course.php");
        exit;
    }

    // Insert data ke tabel courses
    $stmt = $conn->prepare("INSERT INTO courses (course_name, course_code, credits, program_studi) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $course_name, $course_code, $credits, $program_studi);  // Menggunakan program_studi

    if ($stmt->execute()) {
        $_SESSION['message'] = "Mata kuliah berhasil ditambahkan!";
        header("Location: dashboard.php"); // Redirect setelah berhasil
        exit;
    } else {
        $_SESSION['error'] = "Gagal menambahkan mata kuliah: " . $stmt->error;
        header("Location: add_course.php"); // Redirect kembali ke form
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mata Kuliah</title>
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
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Tambah Mata Kuliah</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Formulir Input Mata Kuliah</h3>

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

            <!-- Form Input Mata Kuliah -->
            <form action="add_course.php" method="POST">
                <label for="course_name">Nama Mata Kuliah:</label>
                <input type="text" name="course_name" id="course_name" required>

                <label for="course_code">Kode Mata Kuliah:</label>
                <input type="text" name="course_code" id="course_code" required>

                <label for="credits">Jumlah SKS:</label>
                <input type="number" name="credits" id="credits" required>

                <!-- Ganti 'department' menjadi 'program_studi' -->
                <label for="program_studi">Program Studi:</label>
                <select name="program_studi" id="program_studi" required>
                    <option value="Ilmu Komputer">Ilmu Komputer</option>
                    <option value="Teknik Informatika">Teknik Informatika</option>
                    <option value="Ekonomi">Ekonomi</option>
                    <option value="Manajemen">Manajemen</option>
                    <option value="Teknik Mesin">Teknik Mesin</option>
                    <option value="Teknik Sipil">Teknik Sipil</option>
                    <option value="Kedokteran">Kedokteran</option>
                    <option value="General">Umum</option>
                </select>

                <button type="submit" class="btn-signin">Tambah Mata Kuliah</button>
            </form>
        </div>
    </div>
</body>
</html>
