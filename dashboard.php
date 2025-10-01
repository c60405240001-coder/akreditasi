<?php
session_start();
require_once 'config.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];  // Ambil role dari session
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">  <!-- Gaya CSS -->
    <!-- Menambahkan Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Tambahan styling ringan, tidak mengganggu style.css */
        .quick-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin:16px 0}
        .card{border:1px solid #e5e7eb;border-radius:10px;padding:14px;background:#fff}
        .card h5{margin:0 0 6px 0;font-size:15px}
        .card p{margin:0;font-size:13px;color:#4b5563}
        .actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
        .actions a{display:inline-flex;align-items:center;gap:6px;border:1px solid #e5e7eb;border-radius:8px;padding:8px 10px;text-decoration:none;font-size:13px}
        .sidebar .sidebar-menu h3{display:flex;align-items:center;gap:8px}
        .badge{background:#eef2ff;color:#3730a3;border-radius:999px;padding:2px 8px;font-size:11px;margin-left:6px}
        .progress{height:10px;border-radius:8px;background:#f3f4f6;overflow:hidden}
        .progress-bar{height:100%;background:#3b82f6}
        .header-tools{display:flex;align-items:center;gap:10px}
        .icon-btn{border:1px solid #e5e7eb;border-radius:8px;padding:6px 10px;text-decoration:none}
        .muted{color:#6b7280}
        .menu-divider{margin:8px 0;border-top:1px dashed #e5e7eb}
    </style>
</head>
<body>
    <div id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fa-solid fa-gauge-high"></i> Dashboard</h2>
        </div>
        <div class="sidebar-menu">
            <h3><i class="fa-solid fa-list"></i> Menu</h3>
            <ul>
                <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>

                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="manage_users.php"><i class="fa-solid fa-users-gear"></i> Kelola Pengguna</a></li>
                    <li><a href="view_reports.php"><i class="fa-solid fa-chart-line"></i> Lihat Laporan</a></li>
                    <!-- Tambahan Admin -->
                    <li><a href="manage_roles.php"><i class="fa-solid fa-user-shield"></i> Kelola Role & Permission</a></li>
                    <li><a href="audit_logs.php"><i class="fa-solid fa-clipboard-list"></i> Log Aktivitas</a></li>
                    <li><a href="backup_restore.php"><i class="fa-solid fa-database"></i> Backup & Restore</a></li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'qa_officer'): ?>
                    <li><a href="approve_documents.php"><i class="fa-solid fa-check-double"></i> Approve Dokumen</a></li>
                    <!-- Tambahan QA -->
                    <li><a href="qa_monitoring.php"><i class="fa-solid fa-eye"></i> Monitoring QA</a></li>
                    <li><a href="qa_reports.php"><i class="fa-solid fa-file-shield"></i> Laporan Kualitas</a></li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'faculty_admin'): ?>
                    <li><a href="add_report.php"><i class="fa-solid fa-file-circle-plus"></i> Tambah Laporan</a></li>
                    <li><a href="add_faculty.php"><i class="fa-solid fa-user-plus"></i> Tambahkan Profil Fakultas</a></li>
                    <li><a href="view_faculty_profile.php"><i class="fa-solid fa-id-card-clip"></i> Lihat Profil Fakultas</a></li>
                    <li><a href="add_course.php"><i class="fa-solid fa-square-plus"></i> Input Mata Kuliah</a></li>
                    <li><a href="view_courses.php"><i class="fa-solid fa-table-list"></i> Lihat Mata Kuliah</a></li>
                    <li><a href="input_grades.php"><i class="fa-solid fa-pen-to-square"></i> Input Nilai</a></li>
                    <!-- Tambahan Faculty Admin -->
                    <li><a href="manage_lecturers.php"><i class="fa-solid fa-chalkboard-user"></i> Kelola Dosen</a></li>
                    <li><a href="manage_students.php"><i class="fa-solid fa-user-graduate"></i> Kelola Mahasiswa</a></li>
                    <li><a href="teaching_schedule.php"><i class="fa-solid fa-calendar-days"></i> Jadwal Kuliah</a></li>
                    <li><a href="attendance_recap.php"><i class="fa-solid fa-user-check"></i> Rekap Kehadiran</a></li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'faculty_staff'): ?>
                    <li><a href="add_report.php"><i class="fa-solid fa-file-circle-plus"></i> Tambah Laporan</a></li>
                    <li><a href="view_faculty_profile.php"><i class="fa-solid fa-id-card-clip"></i> Lihat Profil Fakultas</a></li>
                    <li><a href="add_course.php"><i class="fa-solid fa-square-plus"></i> Input Mata Kuliah</a></li>
                    <li><a href="view_courses.php"><i class="fa-solid fa-table-list"></i> Lihat Mata Kuliah</a></li>
                    <li><a href="input_grades.php"><i class="fa-solid fa-pen-to-square"></i> Input Nilai</a></li>
                    <!-- Tambahan Faculty Staff -->
                    <li><a href="teaching_schedule.php"><i class="fa-solid fa-calendar-days"></i> Jadwal Kuliah</a></li>
                    <li><a href="attendance_recap.php"><i class="fa-solid fa-user-check"></i> Rekap Kehadiran</a></li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'program_coordinator'): ?>
                    <li><a href="program_schedule.php"><i class="fa-solid fa-calendar-check"></i> Kelola Jadwal Program</a></li>
                    <li><a href="program_progress.php"><i class="fa-solid fa-chart-simple"></i> Lihat Progres Program</a></li>
                    <!-- Tambahan Program Coordinator -->
                    <li><a href="manage_curriculum.php"><i class="fa-solid fa-diagram-project"></i> Kelola Kurikulum</a></li>
                    <li><a href="program_students_report.php"><i class="fa-solid fa-clipboard-check"></i> Laporan Mahasiswa</a></li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'accreditation_officer'): ?>
                    <li><a href="accreditation_process.php"><i class="fa-solid fa-stamp"></i> Proses Akreditasi</a></li>
                    <li><a href="verify_documents.php"><i class="fa-solid fa-file-circle-check"></i> Verifikasi Dokumen</a></li>
                    <!-- Tambahan Akreditasi -->
                    <li><a href="accreditation_evidence.php"><i class="fa-solid fa-folder-open"></i> Upload Evidence</a></li>
                    <li><a href="accreditation_checklist.php"><i class="fa-solid fa-list-check"></i> Checklist Borang</a></li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'audit_manager'): ?>
                    <li><a href="audit_reports.php"><i class="fa-solid fa-file-invoice"></i> Lihat Laporan Audit</a></li>
                    <li><a href="manage_audit.php"><i class="fa-solid fa-magnifying-glass-chart"></i> Kelola Audit</a></li>
                    <!-- Tambahan Audit -->
                    <li><a href="audit_schedule.php"><i class="fa-solid fa-calendar-week"></i> Jadwal Audit</a></li>
                    <li><a href="audit_followups.php"><i class="fa-solid fa-clipboard-question"></i> Tindak Lanjut</a></li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'student'): ?>
                    <li><a href="view_courses.php"><i class="fa-solid fa-table-list"></i> Lihat Mata Kuliah</a></li>
                    <li><a href="view_grades.php"><i class="fa-solid fa-clipboard-user"></i> Lihat Nilai</a></li>
                    <!-- Tambahan Student -->
                    <li><a href="student_schedule.php"><i class="fa-solid fa-calendar-days"></i> Jadwal Kuliah</a></li>
                    <li><a href="student_attendance.php"><i class="fa-solid fa-user-check"></i> Absensi</a></li>
                    <li><a href="materials.php"><i class="fa-solid fa-book-open"></i> Bahan Ajar</a></li>
                    <li><a href="assignments.php"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Tugas</a></li>
                <?php endif; ?>

                <div class="menu-divider"></div>
                <!-- Menu universal -->
                <li><a href="notifications.php"><i class="fa-solid fa-bell"></i> Notifikasi <span class="badge" id="notif-count">0</span></a></li>
                <li><a href="profile.php"><i class="fa-solid fa-user"></i> Lihat Profil</a></li>
                <li><a href="change_password.php"><i class="fa-solid fa-key"></i> Ubah Password</a></li>
                <li><a href="help_center.php"><i class="fa-solid fa-circle-question"></i> Bantuan</a></li>
            </ul>
        </div>
    </div>

    <div id="main-content" class="main-content">
        <header class="header" style="display:flex;align-items:center;justify-content:space-between;gap:10px">
            <h2>Selamat datang, <?php echo ucfirst($role); ?>!</h2>
            <div class="header-tools">
                <a href="notifications.php" class="icon-btn"><i class="fa-solid fa-bell"></i> <span id="notif-count-top">0</span></a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </header>

        <div class="content">
            <h3>Menu Dashboard</h3>
            <p>Berikut adalah menu yang dapat Anda akses:</p>

            <!-- Widget Ringkas lintas role -->
            <div class="quick-cards">
                <div class="card">
                    <h5><i class="fa-solid fa-bell"></i> Notifikasi</h5>
                    <p id="card-notif" class="muted">Tidak ada notifikasi baru.</p>
                    <div class="actions">
                        <a href="notifications.php"><i class="fa-solid fa-inbox"></i> Lihat Semua</a>
                        <a href="settings_notifications.php"><i class="fa-solid fa-sliders"></i> Pengaturan</a>
                    </div>
                </div>

                <div class="card">
                    <h5><i class="fa-solid fa-calendar-days"></i> Agenda</h5>
                    <p class="muted">Cek jadwal dan tenggat Anda.</p>
                    <div class="actions">
                        <?php if ($role === 'program_coordinator' || $role === 'faculty_admin' || $role === 'faculty_staff'): ?>
                            <a href="teaching_schedule.php"><i class="fa-solid fa-chalkboard"></i> Jadwal Kuliah</a>
                        <?php elseif ($role === 'student'): ?>
                            <a href="student_schedule.php"><i class="fa-solid fa-book"></i> Jadwal Saya</a>
                        <?php elseif ($role === 'audit_manager'): ?>
                            <a href="audit_schedule.php"><i class="fa-solid fa-calendar-week"></i> Jadwal Audit</a>
                        <?php else: ?>
                            <a href="calendar.php"><i class="fa-solid fa-calendar"></i> Kalender</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <h5><i class="fa-solid fa-chart-line"></i> Progres</h5>
                    <p class="muted">Pantau status pekerjaan aktif.</p>
                    <div class="progress" title="60%">
                        <div class="progress-bar" style="width: 60%"></div>
                    </div>
                    <div class="actions">
                        <?php if ($role === 'accreditation_officer' || $role === 'qa_officer' || $role === 'admin'): ?>
                            <a href="accreditation_process.php"><i class="fa-solid fa-stamp"></i> Akreditasi</a>
                            <a href="verify_documents.php"><i class="fa-solid fa-file-circle-check"></i> Verifikasi</a>
                        <?php elseif ($role === 'program_coordinator'): ?>
                            <a href="program_progress.php"><i class="fa-solid fa-chart-simple"></i> Progres Program</a>
                        <?php elseif ($role === 'audit_manager'): ?>
                            <a href="audit_reports.php"><i class="fa-solid fa-file-invoice"></i> Laporan Audit</a>
                        <?php else: ?>
                            <a href="view_reports.php"><i class="fa-solid fa-chart-pie"></i> Laporan</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <h5><i class="fa-solid fa-user-gear"></i> Aksi Cepat</h5>
                    <p class="muted">Shortcut sesuai role Anda.</p>
                    <div class="actions">
                        <?php if ($role === 'admin'): ?>
                            <a href="manage_users.php"><i class="fa-solid fa-users-gear"></i> Kelola Pengguna</a>
                            <a href="audit_logs.php"><i class="fa-solid fa-clipboard-list"></i> Log</a>
                        <?php elseif ($role === 'qa_officer'): ?>
                            <a href="approve_documents.php"><i class="fa-solid fa-check-double"></i> Approve</a>
                            <a href="qa_monitoring.php"><i class="fa-solid fa-eye"></i> Monitoring</a>
                        <?php elseif ($role === 'faculty_admin' || $role === 'faculty_staff'): ?>
                            <a href="add_course.php"><i class="fa-solid fa-square-plus"></i> Input MK</a>
                            <a href="input_grades.php"><i class="fa-solid fa-pen-to-square"></i> Input Nilai</a>
                        <?php elseif ($role === 'program_coordinator'): ?>
                            <a href="manage_curriculum.php"><i class="fa-solid fa-diagram-project"></i> Kurikulum</a>
                            <a href="program_schedule.php"><i class="fa-solid fa-calendar-check"></i> Jadwal</a>
                        <?php elseif ($role === 'accreditation_officer'): ?>
                            <a href="accreditation_evidence.php"><i class="fa-solid fa-folder-open"></i> Evidence</a>
                            <a href="accreditation_checklist.php"><i class="fa-solid fa-list-check"></i> Checklist</a>
                        <?php elseif ($role === 'audit_manager'): ?>
                            <a href="manage_audit.php"><i class="fa-solid fa-magnifying-glass-chart"></i> Kelola Audit</a>
                            <a href="audit_followups.php"><i class="fa-solid fa-clipboard-question"></i> Tindak Lanjut</a>
                        <?php elseif ($role === 'student'): ?>
                            <a href="materials.php"><i class="fa-solid fa-book-open"></i> Bahan Ajar</a>
                            <a href="assignments.php"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Tugas</a>
                        <?php else: ?>
                            <a href="profile.php"><i class="fa-solid fa-user"></i> Profil</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simulasi jumlah notifikasi. Integrasikan dengan backend nanti.
        const notifCount = 3; // contoh
        document.getElementById('notif-count').textContent = notifCount;
        document.getElementById('notif-count-top').textContent = notifCount;
        if (notifCount === 0) {
            document.getElementById('card-notif').textContent = 'Tidak ada notifikasi baru.';
        } else {
            document.getElementById('card-notif').textContent = notifCount + ' notifikasi baru menunggu Anda.';
        }
    </script>
</body>
</html>
