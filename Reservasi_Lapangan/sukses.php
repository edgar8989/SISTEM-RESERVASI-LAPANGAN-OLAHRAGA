<?php
include 'koneksi.php';

if (!isset($_GET['id_reservasi'])) {
    die("ID Reservasi tidak ditemukan.");
}

$id_reservasi = $_GET['id_reservasi'];

// Ambil detail reservasi + pelanggan + jadwal + lapangan + GOR
$stmt = $conn->prepare("
    SELECT 
        r.kode_booking,
        r.tanggal_reservasi,
        p.nama AS nama_pelanggan,
        p.email,
        p.no_telepon,
        l.nama_lapangan,
        g.nama_gor,
        j.tanggal,
        j.jam_mulai,
        j.jam_selesai
    FROM reservasi r
    JOIN pelanggan p ON p.id_pelanggan = r.id_pelanggan
    JOIN jadwal j ON j.id_jadwal = r.id_jadwal
    JOIN lapangan l ON l.id_lapangan = j.id_lapangan
    JOIN gor g ON g.id_gor = l.id_gor
    WHERE r.id_reservasi = ?
");
$stmt->execute([$id_reservasi]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data reservasi tidak ditemukan.");
}

// Ambil fasilitas lapangan yang dipinjam
$stmtF = $conn->prepare("
    SELECT f.nama_fasilitas
    FROM reservasi_fasilitas rf
    JOIN fasilitas f ON rf.id_fasilitas = f.id_fasilitas
    WHERE rf.id_reservasi = ?
");
$stmtF->execute([$id_reservasi]);
$fasilitas = $stmtF->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reservasi Berhasil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="card shadow">
        <div class="card-body">

            <h3 class="text-success">Reservasi Berhasil!</h3>
            <p>Berikut detail lengkap reservasi Anda:</p>

            <hr>

            <h5>🔑 Kode Booking</h5>
            <p class="fw-bold fs-5"><?= $data['kode_booking'] ?></p>

            <h5>👤 Data Pemesan</h5>
            <p><b>Nama:</b> <?= $data['nama_pelanggan'] ?></p>
            <p><b>Email:</b> <?= $data['email'] ?></p>
            <p><b>No Telepon:</b> <?= $data['no_telepon'] ?></p>

            <hr>

            <h5>🏟️ Detail Lapangan</h5>
            <p><b>GOR:</b> <?= $data['nama_gor'] ?></p>
            <p><b>Lapangan:</b> <?= $data['nama_lapangan'] ?></p>

            <h5>🗓️ Jadwal</h5>
            <p><b>Tanggal:</b> <?= date('d-m-Y', strtotime($data['tanggal'])) ?></p>
            <p><b>Jam:</b> 
                <?= substr($data['jam_mulai'], 0, 5) ?> - 
                <?= substr($data['jam_selesai'], 0, 5) ?>
            </p>

            <hr>

            <h5>🧩 Fasilitas yang Dipinjam</h5>
            <?php if (count($fasilitas) > 0): ?>
                <ul>
                    <?php foreach ($fasilitas as $f): ?>
                        <li><?= $f['nama_fasilitas'] ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><i>Tidak ada fasilitas tambahan.</i></p>
            <?php endif; ?>

            <hr>

            <a href="index.php" class="btn btn-primary">Kembali ke Beranda</a>
        </div>
    </div>
</div>
</body>
</html>
