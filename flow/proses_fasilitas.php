<?php
require_once '../koneksi/connection.php';

// Proteksi Admin
$token = $_COOKIE['admin_auth_token'] ?? ''; 
if (!$token) { exit(); }

// Logika Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $sql = "DELETE FROM kategori_fasilitas WHERE id_kategori = ?";
    $database_connection->prepare($sql)->execute([$id]);
    header("Location: ../kelola_fasilitas.php?status=success");
}

// Logika Tambah & Edit
if (isset($_POST['simpan'])) {
    $id = $_POST['id_kategori'];
    $nama = $_POST['nama_kategori'];

    if (empty($id)) {
        // Mode Tambah
        $sql = "INSERT INTO kategori_fasilitas (nama_kategori) VALUES (?)";
        $database_connection->prepare($sql)->execute([$nama]);
    } else {
        // Mode Edit
        $sql = "UPDATE kategori_fasilitas SET nama_kategori = ? WHERE id_kategori = ?";
        $database_connection->prepare($sql)->execute([$nama, $id]);
    }
    header("Location: ../kelola_fasilitas.php?status=success");
}