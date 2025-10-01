<?php
session_start();
require_once 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Hanya faculty_admin dan faculty_staff yang boleh input/edit nilai
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['faculty_admin','faculty_staff'])) {
    header("Location: login.php");
    exit;
}

// Ambil daftar course untuk ditampilkan di form
$courses = $conn->query("SELECT id, course_name FROM courses ORDER BY course_name");

// Ambil daftar student (role = student di tabel users)
$students = $conn->query("SELECT id, email FROM users WHERE role = 'student' ORDER BY id");

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $course_id  = (int)$_POST['course_id'];
    $grade      = trim($_POST['grade']);

    if ($student_id > 0 && $course_id > 0 && $grade !== '') {
        // Insert / Update nilai
        $stmt = $conn->prepare("
            INSERT INTO student_courses (student_id, course_id, grade)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE grade = VALUES(grade)
        ");
        $stmt->bind_param("iis", $student_id, $course_id, $grade);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Nilai berhasil disimpan.";

            // Logging input/update nilai
            $action = "input_grade:{$grade}";
            log_action($conn, $action, "student_courses", $student_id);
        } else {
            $_SESSION['error'] = "Gagal menyimpan nilai: " . $stmt->error;
        }
        $stmt->close();

        header("Location: input_grades.php");
        exit;
    } else {
        $_SESSION['error'] = "Semua field wajib diisi.";
        header("Location: input_grades.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Input Nilai Mahasiswa</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div id="sidebar" class="sidebar">
    <div class="sidebar-header"><h2>Dashboard</h2></div>
    <div class="sidebar-menu">
        <h3>Menu</h3>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="view_courses.php">Lihat Mata Kuliah</a></li>
            <li><a href="input_grades.php">Input Nilai</a></li>
        </ul>
    </div>
</div>

<div id="main-content" class="main-content">
    <header class="header">
        <h2>Input Nilai Mahasiswa</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <div class="content">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="post" action="input_grades.php">
            <label for="student_id">Mahasiswa:</label>
            <select name="student_id" id="student_id" required>
                <option value="">-- Pilih Mahasiswa --</option>
                <?php while ($s = $students->fetch_assoc()): ?>
                    <option value="<?php echo $s['id']; ?>">
                        <?php echo htmlspecialchars($s['email']); ?> (ID: <?php echo $s['id']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="course_id">Mata Kuliah:</label>
            <select name="course_id" id="course_id" required>
                <option value="">-- Pilih Mata Kuliah --</option>
                <?php while ($c = $courses->fetch_assoc()): ?>
                    <option value="<?php echo $c['id']; ?>">
                        <?php echo htmlspecialchars($c['course_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="grade">Nilai:</label>
            <input type="text" name="grade" id="grade" placeholder="contoh: A, B+, C" required>

            <button type="submit">Simpan Nilai</button>
        </form>
    </div>
</div>
</body>
</html>
