<?php
// audit_logs.php
session_start();
require_once 'config.php';

// Wajib login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['admin', 'audit_manager'], true)) {
    http_response_code(403);
    echo "Akses ditolak.";
    exit;
}

// Filter
$limit = 25;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$actor = trim($_GET['actor'] ?? '');
$action = trim($_GET['action'] ?? '');
$since = trim($_GET['since'] ?? '');

$where = [];
$params = [];
$paramTypes = '';

if ($actor !== '') {
    $where[] = "actor LIKE ?";
    $kw1 = "%{$actor}%";
    $params[] = &$kw1;
    $paramTypes .= 's';
}
if ($action !== '') {
    $where[] = "action LIKE ?";
    $kw2 = "%{$action}%";
    $params[] = &$kw2;
    $paramTypes .= 's';
}
if ($since !== '') {
    $where[] = "created_at >= ?";
    $kw3 = $since . " 00:00:00";
    $params[] = &$kw3;
    $paramTypes .= 's';
}

$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

// Hitung total
$sqlCount = "SELECT COUNT(*) FROM audit_logs {$whereSql}";
$stmt = $conn->prepare($sqlCount);
if ($paramTypes !== '') {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$stmt->bind_result($totalRows);
$stmt->fetch();
$stmt->close();

$totalPages = max(1, (int)ceil($totalRows / $limit));

// Ambil data log
$sql = "SELECT id, actor, action, entity, entity_id, ip_address, user_agent, created_at 
        FROM audit_logs {$whereSql} 
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

if ($paramTypes !== '') {
    $paramTypes2 = $paramTypes . 'ii';
    $params2 = array_merge($params, [ &$limit, &$offset ]);
    $stmt->bind_param($paramTypes2, ...$params2);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$logs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Log Aktivitas</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .container{max-width:1100px;margin:20px auto;padding:16px;background:#fff;border:1px solid #e5e7eb;border-radius:10px}
        .topbar{display:flex;gap:10px;align-items:center;justify-content:space-between;flex-wrap:wrap}
        .filters{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}
        .filters input{padding:8px;border:1px solid #e5e7eb;border-radius:8px}
        .btn{display:inline-flex;align-items:center;gap:6px;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;text-decoration:none;background:#fff;cursor:pointer}
        .btn-primary{background:#10b981;color:#fff;border-color:#059669}
        table{width:100%;border-collapse:collapse;margin-top:12px}
        th,td{border:1px solid #e5e7eb;padding:10px;text-align:left;vertical-align:top}
        th{background:#f9fafb}
        .mono{font-family: ui-monospace, monospace; font-size:12px}
        .pagination{display:flex;gap:6px;align-items:center;margin-top:12px}
        .badge{background:#eef2ff;color:#3730a3;border-radius:999px;padding:3px 8px;font-size:12px;margin-left:6px}
        .msg{margin:10px 0;padding:10px;border-radius:8px;background:#fff7ed;border:1px solid #fdba74;color:#9a3412}
    </style>
</head>
<body>
<div class="container">
    <div class="topbar">
        <h2><i class="fa-solid fa-clipboard-list"></i> Log Aktivitas 
            <span class="badge"><?= (int)$totalRows ?> entri</span>
        </h2>
        <div>
            <a class="btn" href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
        </div>
    </div>

    <form class="filters" method="get">
        <input type="text" name="actor" value="<?= h($actor) ?>" placeholder="Pelaku">
        <input type="text" name="action" value="<?= h($action) ?>" placeholder="Aksi">
        <input type="date" name="since" value="<?= h($since) ?>">
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-filter"></i> Filter</button>
        <a class="btn" href="audit_logs.php"><i class="fa-solid fa-rotate"></i> Reset</a>
    </form>

    <?php if (!$logs): ?>
        <div class="msg"><i class="fa-solid fa-triangle-exclamation"></i> Tidak ada data log.</div>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Waktu</th>
                <th>Pelaku</th>
                <th>Aksi</th>
                <th>Entitas</th>
                <th>IP</th>
                <th>User Agent</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= (int)$log['id'] ?></td>
                    <td><?= h($log['created_at']) ?></td>
                    <td class="mono"><?= h($log['actor']) ?></td>
                    <td class="mono"><?= h($log['action']) ?></td>
                    <td>
                        <div><strong><?= h($log['entity']) ?></strong></div>
                        <div class="mono">ID: <?= h($log['entity_id']) ?></div>
                    </td>
                    <td class="mono"><?= h($log['ip_address']) ?></td>
                    <td class="mono"><?= h($log['user_agent']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a class="btn" href="?<?= http_build_query(['actor'=>$actor,'action'=>$action,'since'=>$since,'page'=>$page-1]) ?>"><i class="fa-solid fa-chevron-left"></i> Sebelumnya</a>
            <?php endif; ?>
            <span>Halaman <?= $page ?> dari <?= $totalPages ?></span>
            <?php if ($page < $totalPages): ?>
                <a class="btn" href="?<?= http_build_query(['actor'=>$actor,'action'=>$action,'since'=>$since,'page'=>$page+1]) ?>">Berikutnya <i class="fa-solid fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
