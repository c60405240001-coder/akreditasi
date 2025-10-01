<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['faculty_admin','faculty_staff'])) {
    header("Location: login.php");
    exit;
}

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($courseId <= 0) {
    $_SESSION['error'] = "ID mata kuliah tidak valid.";
    header("Location: view_courses.php");
    exit;
}

// Ambil data awal
$stmt = $conn->prepare("SELECT id, course_name, course_code, credits, program_studi FROM courses WHERE id = ?");
$stmt->bind_param("i", $courseId);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$course) {
    $_SESSION['error'] = "Mata kuliah tidak ditemukan.";
    header("Location: view_courses.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name   = trim($_POST['course_name']);
    $course_code   = trim($_POST['course_code']);
    $credits       = (int)$_POST['credits'];
    $program_studi = trim($_POST['program_studi']);

    if ($course_name === '' || $course_code === '' || $credits <= 0 || $program_studi === '') {
        $_SESSION['error'] = "Semua field wajib diisi dengan benar.";
        header("Location: edit_course.php?id=".$courseId);
        exit;
    }

    $upd = $conn->prepare("UPDATE courses SET course_name=?, course_code=?, credits=?, program_studi=? WHERE id=?");
    $upd->bind_param("ssisi", $course_name, $course_code, $credits, $program_studi, $courseId);
    if ($upd->execute()) {
        $_SESSION['message'] = "Mata kuliah berhasil diperbarui.";
        header("Location: view_courses.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal memperbarui: ".$upd->error;
        header("Location: edit_course.php?id=".$courseId);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Mata Kuliah</title>
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
        <h2>Edit Mata Kuliah</h2>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <div class="content">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <form method="post" action="edit_course.php?id=<?php echo (int)$course['id']; ?>">
            <label for="course_name">Nama Mata Kuliah:</label>
            <input type="text" id="course_name" name="course_name" required value="<?php echo htmlspecialchars($course['course_name']); ?>">

            <label for="course_code">Kode Mata Kuliah:</label>
            <input type="text" id="course_code" name="course_code" required value="<?php echo htmlspecialchars($course['course_code']); ?>">

            <label for="credits">Jumlah SKS:</label>
            <input type="number" id="credits" name="credits" required value="<?php echo (int)$course['credits']; ?>">

            <label for="program_studi">Program Studi:</label>
            <select id="program_studi" name="program_studi" required>
                <?php
                $opsi = ['Ilmu Komputer','Teknik Informatika','Ekonomi','Manajemen','Teknik Mesin','Teknik Sipil','Kedokteran','General'];
                foreach ($opsi as $opt) {
                    $sel = ($course['program_studi'] === $opt) ? 'selected' : '';
                    echo '<option value="'.htmlspecialchars($opt).'" '.$sel.'>'.htmlspecialchars($opt).'</option>';
                }
                ?>
            </select>

            <button type="submit">Simpan Perubahan</button>
        </form>
    </div>
</div>
</body>
</html>
