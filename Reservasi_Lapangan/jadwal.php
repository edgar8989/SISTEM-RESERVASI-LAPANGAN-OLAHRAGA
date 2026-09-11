<?php 
include 'koneksi.php';
$id_lap = $_GET['id_lapangan'];

// Info Lapangan
$stmt = $conn->prepare("SELECT * FROM lapangan WHERE id_lapangan = ?");
$stmt->execute([$id_lap]);
$lapangan = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Lapangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h3>Jadwal: <?= $lapangan['nama_lapangan'] ?></h3>
    <p class="text-muted">Silakan pilih slot waktu yang <strong>"Tersedia"</strong>.</p>
    
    <table class="table table-bordered table-hover mt-3">
        <thead class="table-dark">
            <tr>
                <th>Tanggal</th>
                <th>Jam Mulai</th>
                <th>Jam Selesai</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // ambil jadwal dari tanggal hari ini ke depan
            $sql = "SELECT * FROM jadwal 
                    WHERE id_lapangan = ? 
                    ORDER BY tanggal, jam_mulai ASC";

            $q = $conn->prepare($sql);
            $q->execute([$id_lap]);
            
            while($j = $q->fetch(PDO::FETCH_ASSOC)){

                $dbStatus = $j['status'];
                $tampilanStatus = '';
                $warnaClass = '';
                $tombol = '';

                // ============================
                // STATUS BARU (TIDAK ADA KONFIRMASI ADMIN)
                // ============================

                if ($dbStatus == 'tersedia') {
                    $tampilanStatus = 'Tersedia';
                    $warnaClass = 'text-success fw-bold';
                    
                    // 👇 Arahkan ke fasilitas.php
                    $tombol = '
                        <a href="fasilitas.php?id_jadwal='.$j['id_jadwal'].'" 
                           class="btn btn-sm btn-primary">
                           Booking Sekarang
                        </a>';
                } 
                else { 
                    // Semua status selain tersedia dianggap penuh
                    $tampilanStatus = 'Sudah Dibooking';
                    $warnaClass = 'text-danger fw-bold';

                    $tombol = '<button class="btn btn-sm btn-secondary" disabled>
                                Full Booked
                              </button>';
                }
            ?>
            <tr>
                <td><?= date('d-m-Y', strtotime($j['tanggal'])) ?></td>
                <td><?= substr($j['jam_mulai'],0,5) ?></td>
                <td><?= substr($j['jam_selesai'],0,5) ?></td>
                
                <td class="<?= $warnaClass ?>">
                    <?= $tampilanStatus ?>
                </td>
                
                <td>
                    <?= $tombol ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
