<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role faculty_admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty_admin') {
    header("Location: login.php");
    exit;
}

// Cek apakah ID laporan audit ada di URL
if (isset($_GET['id'])) {
    $audit_id = $_GET['id'];

    // Ambil data laporan audit dari database
    $stmt = $conn->prepare("SELECT * FROM audits WHERE id = ?");
    $stmt->bind_param("i", $audit_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Laporan audit tidak ditemukan.";
        exit;
    }

    $audit = $result->fetch_assoc();
} else {
    echo "ID laporan audit tidak diberikan.";
    exit;
}

// Proses pembaruan laporan audit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department = $_POST['department'];
    $audit_date = $_POST['audit_date'];
    $findings = $_POST['findings'];
    $corrective_action = $_POST['corrective_action'];
    $status = $_POST['status'];

    // Update data laporan audit di database
    $update_query = "UPDATE audits SET department = ?, audit_date = ?, findings = ?, corrective_action = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssssi", $department, $audit_date, $findings, $corrective_action, $status, $audit_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Laporan audit berhasil diperbarui!";
        header("Location: view_audit_detail.php?id=" . $audit_id);  // Redirect ke halaman detail setelah update
        exit;
    } else {
        $_SESSION['error'] = "Gagal memperbarui laporan audit.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan Audit</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .alert {
            background-color: #f44336;
            color: white;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background-color: #4CAF50;
        }
    </style>
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
                <li><a href="view_audit_detail.php?id=<?php echo $audit['id']; ?>">Kembali ke Detail</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Edit Laporan Audit</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Edit Laporan Audit</h3>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form action="edit_audit.php?id=<?php echo $audit['id']; ?>" method="post">
                <label for="department">Departemen:</label>
                <input type="text" name="department" id="department" value="<?php echo htmlspecialchars($audit['department']); ?>" required><br>

                <label for="audit_date">Tanggal Audit:</label>
                <input type="date" name="audit_date" id="audit_date" value="<?php echo $audit['audit_date']; ?>" required><br>

                <label for="findings">Temuan:</label>
                <textarea name="findings" id="findings" rows="4" required><?php echo htmlspecialchars($audit['findings']); ?></textarea><br>

                <label for="corrective_action">Langkah Perbaikan:</label>
                <textarea name="corrective_action" id="corrective_action" rows="4" required><?php echo htmlspecialchars($audit['corrective_action']); ?></textarea><br>

                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="planned" <?php echo ($audit['status'] == 'planned') ? 'selected' : ''; ?>>Planned</option>
                    <option value="in_progress" <?php echo ($audit['status'] == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo ($audit['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                </select><br><br>

                <button type="submit">Update Laporan</button>
            </form>
        </div>
    </div>
</body>
</html>
