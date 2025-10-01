<?php
session_start();
require_once 'config.php';

// Aktifkan error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cek apakah pengguna sudah login dan apakah role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Cek apakah ID pengguna yang akan diedit ada di URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Ambil data pengguna berdasarkan ID
    $stmt = $conn->prepare("SELECT id, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Pengguna tidak ditemukan.";
        exit;
    }

    $user = $result->fetch_assoc();
} else {
    echo "ID pengguna tidak diberikan.";
    exit;
}

// Proses penyimpanan data yang diubah
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $role = $_POST['role'];

    // Validasi input
    if (empty($email) || empty($role)) {
        $_SESSION['error'] = "Email dan Role harus diisi!";
        header("Location: edit_user.php?id=" . $user_id);
        exit;
    }

    // Update data pengguna
    $stmt = $conn->prepare("UPDATE users SET email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssi", $email, $role, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Pengguna berhasil diperbarui!";
        header("Location: manage_users.php"); // Redirect setelah berhasil
        exit;
    } else {
        $_SESSION['error'] = "Gagal memperbarui data pengguna.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
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
                <li><a href="manage_users.php">Kelola Pengguna</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Edit Pengguna</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Edit Data Pengguna</h3>

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

            <form action="edit_user.php?id=<?php echo $user['id']; ?>" method="POST">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo $user['email']; ?>" required>

                <label for="role">Role:</label>
                <select name="role" id="role">
                    <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="qa_officer" <?php echo ($user['role'] === 'qa_officer') ? 'selected' : ''; ?>>QA Officer</option>
                    <option value="faculty_staff" <?php echo ($user['role'] === 'faculty_staff') ? 'selected' : ''; ?>>Faculty Staff</option>
                    <option value="program_coordinator" <?php echo ($user['role'] === 'program_coordinator') ? 'selected' : ''; ?>>Program Coordinator</option>
                    <option value="accreditation_officer" <?php echo ($user['role'] === 'accreditation_officer') ? 'selected' : ''; ?>>Accreditation Officer</option>
                    <option value="audit_manager" <?php echo ($user['role'] === 'audit_manager') ? 'selected' : ''; ?>>Audit Manager</option>
                    <option value="student" <?php echo ($user['role'] === 'student') ? 'selected' : ''; ?>>Student</option>
                    <option value="faculty_admin" <?php echo ($user['role'] === 'faculty_admin') ? 'selected' : ''; ?>>Faculty Admin</option>
                </select>

                <button type="submit" class="btn-signin">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</body>
</html>
