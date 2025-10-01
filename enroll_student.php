<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['faculty_admin','faculty_staff'])) {
    header("Location: login.php");
    exit;
}

$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $course_id  = (int)$_POST['course_id'];

    if ($student_id > 0 && $course_id > 0) {
        // Cegah duplikasi enroll
        $check = $conn->prepare("SELECT 1 FROM student_courses WHERE student_id=? AND course_id=?");
        $check->bind_param("ii", $student_id, $course_id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            $_SESSION['error'] = "Student sudah terdaftar di mata kuliah ini.";
        } else {
            $stmt = $conn->prepare("INSERT INTO student_courses (student_id, course_id, grade) VALUES (?, ?, NULL)");
            $stmt->bind_param("ii", $student_id, $course_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Enroll berhasil.";
            } else {
                $_SESSION['error'] = "Gagal enroll: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $_SESSION['error'] = "Input tidak valid.";
    }
    header("Location: view_courses.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Enroll Mahasiswa</title>
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
        </ul>
    </div>
</div>

<div id="main-content" class="main-content">
    <header class="header">
        <h2>Enroll Mahasiswa ke Mata Kuliah</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <div class="content">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="post" action="enroll_student.php">
            <label for="student_id">Student ID:</label>
            <input type="number" name="student_id" id="student_id" required placeholder="misal: 6">

            <label for="course_id">Course ID:</label>
            <input type="number" name="course_id" id="course_id" required value="<?php echo $courseId ?: ''; ?>" placeholder="ID course">

            <button type="submit">Enroll</button>
        </form>
    </div>
</div>
</body>
</html>
