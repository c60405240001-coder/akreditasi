<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role 'student'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

// Ambil daftar mata kuliah dan nilai yang diambil oleh student
$query = "
    SELECT courses.course_name, student_courses.grade 
    FROM student_courses 
    JOIN courses ON student_courses.course_id = courses.id
    WHERE student_courses.student_id = ?
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
    <title>Lihat Nilai</title>
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
                <!-- Menu lain yang sesuai dengan student role -->
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Lihat Nilai</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Daftar Nilai</h3>
            <table>
                <tr>
                    <th>Nama Mata Kuliah</th>
                    <th>Nilai</th>
                </tr>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($grade = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($grade['course_name']); ?></td> <!-- Menggunakan htmlspecialchars untuk mencegah XSS -->
                            <td><?php echo htmlspecialchars($grade['grade']); ?></td> <!-- Menggunakan htmlspecialchars untuk mencegah XSS -->
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2">Tidak ada nilai yang ditemukan.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>
