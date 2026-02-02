<?php
require_once "../koneksi/connection.php";

$token = $_COOKIE['user_auth_token'] ?? '';

if ($token === '') {
    header("Location: ../index.php"); 
    exit;
}

$tokenHash = hash('sha256', $token);
$stmt_user = $database_connection->prepare("SELECT id FROM data_user WHERE token = ?");
$stmt_user->execute([$tokenHash]);
$user_db = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user_db) {
    die("Sesi tidak valid.");
}

$id_mahasiswa_asli = $user_db['id']; 
$id_peminjaman = $_POST['id_peminjaman'] ?? '';

if (!empty($id_peminjaman)) {
    try {
        $sql = "DELETE FROM `peminjaman` WHERE `id_peminjaman` = ? AND `id` = ?";
        $connect = $database_connection->prepare($sql);
        $connect->execute([$id_peminjaman, $id_mahasiswa_asli]);

        header("Location: ../riwayat.php?status=deleted");
        exit;

    } catch (PDOException $e) {
        die("Error deleting data: " . $e->getMessage());
    }
} else {
    header("Location: ../riwayat.php");
    exit;
}
?>