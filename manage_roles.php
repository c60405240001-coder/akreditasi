<?php
// manage_roles.php
session_start();
require_once 'config.php';

// Wajib login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Wajib admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Akses ditolak.";
    exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Role yang diizinkan (ikut enum)
$allowedRoles = [
    'admin',
    'qa_officer',
    'faculty_admin',
    'faculty_staff',
    'program_coordinator',
    'accreditation_officer',
    'audit_manager',
    'student',
    'super_admin'
];

// Proses update role
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'], $_POST['csrf_token'])) {
    if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $userId = (int) $_POST['user_id'];
        $newRole = $_POST['role'];

        if (in_array($newRole, $allowedRoles, true)) {
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->bind_param("si", $newRole, $userId);
            $stmt->execute();
            $stmt->close();
            $msg = 'Role berhasil diperbarui.';

            // Logging perubahan role
            log_action($conn, "update_role:{$newRole}", "users", $userId);
        } else {
            $msg = 'Role tidak valid.';
        }
    } else {
        $msg = 'Token tidak valid.';
    }
}

// Pagination & pencarian (berdasarkan email)
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$whereSql = '';
$params = [];
$paramTypes = '';

if ($keyword !== '') {
    $whereSql = "WHERE email LIKE ?";
    $kw = "%{$keyword}%";
    $params[] = &$kw;
    $paramTypes .= 's';
}

// Hitung total
$sqlCount = "SELECT COUNT(*) FROM users {$whereSql}";
$stmt = $conn->prepare($sqlCount);
if ($whereSql !== '') {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$stmt->bind_result($totalRows);
$stmt->fetch();
$stmt->close();

$totalPages = max(1, (int)ceil($totalRows / $limit));

// Ambil data user
$sql = "SELECT id, email, role, status, created_at FROM users {$whereSql} ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
if ($whereSql !== '') {
    $paramTypes2 = $paramTypes . 'ii';
    $params2 = array_merge($params, [ &$limit, &$offset ]);
    $stmt->bind_param($paramTypes2, ...$params2);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Role & Permission</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .container{max-width:1000px;margin:20px auto;padding:16px;background:#fff;border:1px solid #e5e7eb;border-radius:10px}
        .topbar{display:flex;gap:10px;align-items:center;justify-content:space-between;flex-wrap:wrap}
        .msg{margin:10px 0;padding:10px;border-radius:8px;background:#ecfeff;border:1px solid #67e8f9;color:#155e75}
        table{width:100%;border-collapse:collapse;margin-top:12px}
        th,td{border:1px solid #e5e7eb;padding:10px;text-align:left}
        th{background:#f9fafb}
        .pagination{display:flex;gap:6px;align-items:center;margin-top:12px}
        .badge{background:#eef2ff;color:#3730a3;border-radius:999px;padding:3px 8px;font-size:12px;margin-left:6px}
        .search input{padding:8px;border:1px solid #e5e7eb;border-radius:8px}
        .btn{display:inline-flex;align-items:center;gap:6px;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;text-decoration:none;background:#fff;cursor:pointer}
        .btn-primary{background:#3b82f6;color:#fff;border-color:#2563eb}
        .status-pill{padding:3px 8px;border-radius:999px;font-size:12px}
        .status-active{background:#dcfce7;color:#166534}
        .status-inactive{background:#fee2e2;color:#991b1b}
    </style>
</head>
<body>
<div class="container">
    <div class="topbar">
        <h2><i class="fa-solid fa-user-shield"></i> Kelola Role & Permission <span class="badge"><?= h($totalRows) ?> pengguna</span></h2>
        <form method="get" class="search">
            <input type="text" name="q" value="<?= h($keyword) ?>" placeholder="Cari email">
            <button class="btn"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            <a class="btn" href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
        </form>
    </div>

    <?php if ($msg): ?>
        <div class="msg"><i class="fa-solid fa-circle-info"></i> <?= h($msg) ?></div>
    <?php endif; ?>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Role Saat Ini</th>
            <th>Status</th>
            <th>Dibuat</th>
            <th>Ubah Role</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$users): ?>
            <tr><td colspan="6">Tidak ada data.</td></tr>
        <?php else: ?>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= (int)$u['id'] ?></td>
                    <td><?= h($u['email']) ?></td>
                    <td><?= h($u['role']) ?></td>
                    <td>
                        <?php if ($u['status'] === 'active'): ?>
                            <span class="status-pill status-active">Active</span>
                        <?php else: ?>
                            <span class="status-pill status-inactive">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td><?= h($u['created_at']) ?></td>
                    <td>
                        <form method="post" class="inline">
                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                            <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                            <select name="role" required>
                                <?php foreach ($allowedRoles as $r): ?>
                                    <option value="<?= $r ?>" <?= $r === $u['role'] ? 'selected' : '' ?>><?= $r ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a class="btn" href="?q=<?= urlencode($keyword) ?>&page=<?= $page-1 ?>"><i class="fa-solid fa-chevron-left"></i> Sebelumnya</a>
        <?php endif; ?>
        <span>Halaman <?= $page ?> dari <?= $totalPages ?></span>
        <?php if ($page < $totalPages): ?>
            <a class="btn" href="?q=<?= urlencode($keyword) ?>&page=<?= $page+1 ?>">Berikutnya <i class="fa-solid fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
