<?php
require_once 'koneksi/connection.php';
$id = $_GET['id'] ?? '';
if (!$id) { die("ID Tidak ditemukan"); }

$sql = "SELECT p.*, u.nama, u.nim, k.nama_kategori 
        FROM peminjaman p 
        JOIN data_user u ON p.id = u.id 
        JOIN kategori_fasilitas k ON p.id_kategori = k.id_kategori 
        WHERE p.id_peminjaman = ?";
$stmt = $database_connection->prepare($sql);
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) { die("Data tidak ditemukan"); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export PDF - UniReserve</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
</head>
<body onload="generatePDF()">
    <p style="text-align: center; margin-top: 50px; font-family: sans-serif;">
        Sedang memproses PDF, harap tunggu...
    </p>

    <script>
    function generatePDF() {
        var docDefinition = {
            content: [
                { text: 'UNIVERSITAS UNIRESERVE', style: 'header', alignment: 'center' },
                { text: 'Jl. Raya Pendidikan No. 123, Kampus Terpadu', alignment: 'center', fontSize: 10 },
                { text: '__________________________________________________________', margin: [0, 0, 0, 20] },
                
                { text: 'E-TICKET / BUKTI PEMINJAMAN', style: 'subheader', alignment: 'center', decoration: 'underline' },
                { text: '\n\n' },
                
                {
                    table: {
                        widths: [150, 10, '*'],
                        body: [
                            ['ID Transaksi', ':', { text: '#<?= $data['id_peminjaman'] ?>', bold: true }],
                            ['Nama Mahasiswa', ':', '<?= htmlspecialchars($data['nama']) ?>'],
                            ['NIM', ':', '<?= htmlspecialchars($data['nim']) ?>'],
                            ['Fasilitas', ':', '<?= htmlspecialchars($data['nama_kategori']) ?>'],
                            ['Kegiatan', ':', '<?= htmlspecialchars($data['deskripsi_kegiatan']) ?>'],
                            ['Waktu Pinjam', ':', '<?= date('d M Y, H:i', strtotime($data['tgl_pinjam'])) ?> WIB'],
                        ]
                    },
                    layout: 'noBorders'
                },
                
                { text: '\n\nMohon tunjukkan dokumen ini kepada petugas di lokasi.', fontSize: 11, italics: true },
                
                {
                    columns: [
                        { text: '' },
                        {
                            text: '\nTangerang, <?= date('d F Y') ?>\n\n\n\n( Digital Approved )',
                            alignment: 'center',
                            margin: [0, 50, 0, 0]
                        }
                    ]
                }
            ],
            styles: {
                header: { fontSize: 20, bold: true },
                subheader: { fontSize: 15, bold: true, margin: [0, 10, 0, 5] }
            }
        };

        pdfMake.createPdf(docDefinition).download('Bukti_Pinjam_<?= $data['id_peminjaman'] ?>.pdf');

        setTimeout(function() {
            window.location.href = 'riwayat.php';
        }, 2000);
    }
    </script>
</body>
</html>