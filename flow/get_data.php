<?php
require_once '../koneksi/connection.php';

$token = $_COOKIE['user_auth_token'] ?? '';
if ($token === '') {
    http_response_code(401);
    exit(json_encode(["success" => false, "message" => "Unauthorized"]));
}

try {
    $tokenHash = hash('sha256', $token);
    $stmt_user = $database_connection->prepare("SELECT id FROM data_user WHERE token = ?");
    $stmt_user->execute([$tokenHash]);
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        exit(json_encode(["success" => false]));
    }

    $id_mahasiswa = $user['id'];

    $sql = "SELECT p.*, k.nama_kategori 
            FROM `peminjaman` p
            JOIN `kategori_fasilitas` k ON p.id_kategori = k.id_kategori
            WHERE p.id = ?
            ORDER BY p.tgl_pinjam DESC";
            
    $connect = $database_connection->prepare($sql);
    $connect->execute([$id_mahasiswa]);

    $data = $connect->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($data);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Tidak dapat memuat basis data: " . $e->getMessage()
    ]);
}
?>