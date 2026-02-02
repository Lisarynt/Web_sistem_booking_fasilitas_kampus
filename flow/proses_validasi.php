<?php
require_once '../koneksi/connection.php';

$token = $_COOKIE['admin_auth_token'] ?? ''; 
if (!$token) {
    header("Location: ../login_admin.php");
    exit();
}   

$tokenHash = hash('sha256', $token);
$stmt = $database_connection->prepare("SELECT role FROM data_user WHERE token = ? AND role = 'admin'");
$stmt->execute([$tokenHash]);
if (!$stmt->fetch()) { 
    die("Akses Ditolak!"); 
}

$id_peminjaman = $_REQUEST['id_peminjaman'] ?? $_GET['id'] ?? '';
$aksi = $_REQUEST['aksi'] ?? ''; 
$alasan = $_POST['alasan'] ?? null; 

if (!$id_peminjaman || !$aksi) {
    die("Error: Parameter tidak lengkap.");
}

try {
    if ($aksi === 'Disetujui') {
        $sql = "UPDATE peminjaman SET status_pengajuan = 'Disetujui', alasan_penolakan = NULL WHERE id_peminjaman = ?";
        $stmt = $database_connection->prepare($sql);
        $stmt->execute([$id_peminjaman]);
    } 
    elseif ($aksi === 'Ditolak') {
        $sql = "UPDATE peminjaman SET status_pengajuan = 'Ditolak', alasan_penolakan = ? WHERE id_peminjaman = ?";
        $stmt = $database_connection->prepare($sql);
        $stmt->execute([$alasan, $id_peminjaman]);
    }

    header("Location: ../dashboard_admin.php?status=success");
    exit();

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>