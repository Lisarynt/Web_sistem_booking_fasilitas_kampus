<?php
// Mengambil koneksi dengan naik satu level folder
require_once "../koneksi/connection.php";

// Pengecekan Token sesuai referensi dosen
$token = $_COOKIE['user_auth_token'] ?? '';

if ($token === '') {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "No auth cookie"]);
    exit;
}

// Ambil ID user asli dari database berdasarkan Token yang sedang login
$tokenHash = hash('sha256', $token);
$stmt_user = $database_connection->prepare("SELECT id FROM data_user WHERE token = ?");
$stmt_user->execute([$tokenHash]);
$user_db = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user_db) {
    die("Sesi tidak valid, silakan login ulang.");
}

$id_mahasiswa = $user_db['id']; 
$id_kategori = $_POST['id_kategori'];
$kapasitas = $_POST['kapasitas_jumlah'];
$tgl_pinjam = $_POST['tgl_pinjam'];
$tgl_kembali = $_POST['tgl_kembali'];
$deskripsi = $_POST['deskripsi_kegiatan'];

// Logika Insert & Update sesuai referensi dosen
if (!empty($_POST["id_peminjaman"])) {
    // UPDATE DATA: Jika mahasiswa ingin mengubah jadwal atau deskripsi
    $sql = "UPDATE `peminjaman` 
            SET `id_kategori` = ?, 
                `kapasitas_jumlah` = ?, 
                `deskripsi_kegiatan` = ?, 
                `tgl_pinjam` = ?, 
                `tgl_kembali` = ? 
            WHERE `id_peminjaman` = ? AND `id` = ?;";

    $connect = $database_connection->prepare($sql);
    $connect->execute([
        $id_kategori,
        $kapasitas,
        $deskripsi,
        $tgl_pinjam,
        $tgl_kembali,
        $_POST["id_peminjaman"],
        $id_mahasiswa
    ]);
    
    header("Location: ../riwayat.php?status=updated");
    // Kamu bisa ganti echo dengan header("Location: ../riwayat.php");

} else {
    // INSERT DATA: Jika ini pengajuan baru
    $sql = "INSERT INTO `peminjaman` 
            (`id`, `id_kategori`, `kapasitas_jumlah`, `deskripsi_kegiatan`, `tgl_pinjam`, `tgl_kembali`, `status_pengajuan`) 
            VALUES (?, ?, ?, ?, ?, ?, ?);";

    $connect = $database_connection->prepare($sql);
    $connect->execute([
        $id_mahasiswa,
        $id_kategori,
        $kapasitas,
        $deskripsi,
        $tgl_pinjam,
        $tgl_kembali,
        'Pending' // Status awal selalu Pending
    ]);
    
    header("Location: ../riwayat.php?status=success");
    // Kamu bisa ganti echo dengan header("Location: ../riwayat.php");
}
?>