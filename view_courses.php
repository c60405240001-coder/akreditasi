<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role 'student'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// Ambil daftar mata kuliah yang diambil oleh student
$query = "
    SELECT c.course_name, c.course_code, c.credits
    FROM student_courses sc
    JOIN courses c ON sc.course_id = c.id
    WHERE sc.student_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']); // Binding user_id dari session
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query Error: " . $conn->error);  // Menampilkan pesan error jika query gagal
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Mata Kuliah</title>
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
            <h2>Lihat Mata Kuliah</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Daftar Mata Kuliah yang Kamu Ambil</h3>
            <table>
                <tr>
                    <th>Nama Mata Kuliah</th>
                    <th>Kode Mata Kuliah</th>
                    <th>SKS</th>
                </tr>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($course = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                            <td><?php echo htmlspecialchars($course['credits']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3">Belum ada mata kuliah yang ditemukan.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>
