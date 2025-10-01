<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login dan apakah role faculty_admin atau faculty_staff
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'faculty_admin' && $_SESSION['role'] !== 'faculty_staff')) {
    header("Location: login.php");
    exit;
}

// Ambil data fakultas secara umum dari tabel 'faculty'
$query = "SELECT * FROM faculty";  // Mengambil data semua fakultas
$result = $conn->query($query);

if (!$result) {
    die("Query Error: " . $conn->error);  // Menampilkan pesan error jika query gagal
}

// Jika tidak ada data fakultas
if ($result->num_rows == 0) {
    die("Tidak ada data fakultas.");
}

$faculty_profiles = [];
while ($row = $result->fetch_assoc()) {
    $faculty_profiles[] = $row;  // Menyimpan semua data fakultas ke dalam array
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Fakultas</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f4f4f4;
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
                <li><a href="accreditation_process.php">Proses Akreditasi</a></li>
                <li><a href="view_faculty_profile.php">Profil Fakultas</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header">
            <h2>Profil Fakultas</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <div class="content">
            <h3>Informasi Fakultas</h3>

            <?php if (count($faculty_profiles) > 0): ?>
                <table>
                    <tr>
                        <th>Nama Fakultas</th>
                        <th>Program Studi</th>
                        <th>Jumlah Mahasiswa</th>
                        <th>Jumlah Dosen</th>
                    </tr>
                    <?php foreach ($faculty_profiles as $faculty_profile): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($faculty_profile['faculty_name']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($faculty_profile['description'])); ?></td>
                            <td><?php echo htmlspecialchars($faculty_profile['student_count']); ?></td>
                            <td><?php echo htmlspecialchars($faculty_profile['faculty_count']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>Belum ada data fakultas yang tersedia.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
