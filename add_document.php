<?php
session_start();
require_once 'config.php'; // Pastikan koneksi ke database

// Cek apakah pengguna sudah login dan apakah role accreditation_officer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'accreditation_officer') {
    header("Location: login.php");
    exit;
}

// Proses upload dokumen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $status = $_POST['status'];
    $file_path = "";  // Menyimpan lokasi file upload (akan diisi setelah upload)

    // Validasi input
    if (empty($title) || empty($type) || empty($status)) {
        $_SESSION['error'] = "Judul, Tipe, dan Status harus diisi!";
        header("Location: add_document.php");
        exit;
    }

    // Upload file
    if (isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
        $file_tmp = $_FILES['document']['tmp_name'];
        $file_name = $_FILES['document']['name'];
        $file_path = 'uploads/' . basename($file_name); // Menyimpan file di folder 'uploads'

        if (move_uploaded_file($file_tmp, $file_path)) {
            // Insert data dokumen ke database
            $stmt = $conn->prepare("INSERT INTO documents (title, file_path, type, status, uploader) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $title, $file_path, $type, $status, $_SESSION['user_id']);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Dokumen berhasil di-upload!";
                header("Location: view_documents.php");
                exit;
            } else {
                $_SESSION['error'] = "Gagal mengupload dokumen.";
            }
        } else {
            $_SESSION['error'] = "Terjadi kesalahan saat meng-upload file.";
        }
    } else {
        $_SESSION['error'] = "File tidak di-upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Dokumen</title>
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
                <li><a href="view_documents.php">Lihat Dokumen</a></li>
                <li><a href="add_document.php">Tambah Dokumen</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Tambah Dokumen</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Form Tambah Dokumen</h3>

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

            <form action="add_document.php" method="POST" enctype="multipart/form-data">
                <label for="title">Judul Dokumen:</label>
                <input type="text" name="title" id="title" required>

                <label for="type">Tipe Dokumen:</label>
                <input type="text" name="type" id="type" required>

                <label for="status">Status Dokumen:</label>
                <select name="status" id="status">
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="approved">Approved</option>
                </select>

                <label for="document">Pilih Dokumen:</label>
                <input type="file" name="document" id="document" required>

                <button type="submit" class="btn-signin">Simpan Dokumen</button>
            </form>
        </div>
    </div>
</body>
</html>
