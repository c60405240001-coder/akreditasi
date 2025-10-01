<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role accreditation_officer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'accreditation_officer') {
    header("Location: login.php");
    exit;
}

// Ambil dokumen yang statusnya 'submitted' untuk verifikasi
$query = "SELECT id, title, file_path, type, timestamp, status FROM documents WHERE status = 'submitted'";
$result = $conn->query($query);

if (!$result) {
    die("Query Error: " . $conn->error);  // Menampilkan pesan error jika query gagal
}

// Proses verifikasi dokumen
if (isset($_GET['id'])) {
    $document_id = $_GET['id'];

    // Update status dokumen menjadi 'approved' setelah diverifikasi
    $update_query = "UPDATE documents SET status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $document_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Dokumen berhasil diverifikasi!";
        header("Location: verify_documents.php"); // Redirect untuk menghindari pengulangan submit
        exit;
    } else {
        $_SESSION['error'] = "Gagal memverifikasi dokumen.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen</title>
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
                <li><a href="accreditation_process.php">Proses Akreditasi</a></li>
                <li><a href="verify_documents.php">Verifikasi Dokumen</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Verifikasi Dokumen</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Daftar Dokumen untuk Verifikasi</h3>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Judul Dokumen</th>
                    <th>File</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($document = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $document['id']; ?></td>
                        <td><?php echo $document['title']; ?></td>
                        <td><a href="<?php echo $document['file_path']; ?>" target="_blank">Lihat Dokumen</a></td>
                        <td><?php echo ucfirst($document['status']); ?></td>
                        <td>
                            <?php if ($document['status'] === 'submitted'): ?>
                                <a href="verify_documents.php?id=<?php echo $document['id']; ?>" class="btn btn-approve">Verifikasi</a>
                            <?php else: ?>
                                <span>Status: <?php echo ucfirst($document['status']); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>
