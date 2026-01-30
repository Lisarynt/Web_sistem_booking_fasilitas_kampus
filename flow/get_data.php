<?php
// flow/get_data.php
require_once '../koneksi/connection.php';

// Pengecekan Token sesuai standar dosen kamu
$token = $_COOKIE['auth_token'] ?? '';

if ($token === '') {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "No auth cookie"]);
    exit;
}

// Ambil ID mahasiswa dari cookie
$id_mahasiswa = $_COOKIE['id'] ?? 1;

try {
    // Query untuk mengambil riwayat peminjaman user tersebut
    // Kita gunakan JOIN agar nama kategori fasilitasnya ikut terbawa
    $sql = "SELECT p.*, k.nama_kategori 
            FROM `peminjaman` p
            JOIN `kategori_fasilitas` k ON p.id_kategori = k.id_kategori
            WHERE p.id = ?
            ORDER BY p.tgl_pinjam DESC";
            
    $connect = $database_connection->prepare($sql);
    $connect->execute([$id_mahasiswa]);

    $data = array();
    while ($row = $connect->fetch(PDO::FETCH_ASSOC)) {
        // Kamu bisa menambahkan logika manipulasi data di sini jika perlu
        // Contoh: Format tanggal atau status agar lebih rapi sebelum dikirim ke JSON
        array_push($data, $row);
    }

    // Mengirimkan hasil dalam format JSON agar bisa dibaca oleh Chart.js atau tabel dinamis
    header('Content-Type: application/json');
    echo json_encode($data);

} catch (PDOException $e) {
    // Menggunakan variabel $database_name dari connection.php
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Tidak dapat memuat basis data: " . $e->getMessage()
    ]);
}
?>