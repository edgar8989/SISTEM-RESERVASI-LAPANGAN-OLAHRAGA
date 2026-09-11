<?php
require '../koneksi.php';
session_start();

// Logika Statistik (Tetap sama)
$total_gor = $conn->query("SELECT COUNT(*) as total FROM gor")->fetch()['total'];
$total_lapangan = $conn->query("SELECT COUNT(*) as total FROM lapangan")->fetch()['total'];
$total_booking = $conn->query("SELECT COUNT(*) as total FROM reservasi WHERE status_reservasi='dibooking'")->fetch()['total'];
$total_selesai = $conn->query("SELECT COUNT(*) as total FROM reservasi WHERE status_reservasi='selesai'")->fetch()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="reservasi.php">Reservasi</a></li>
                <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h3 class="mb-4">Ringkasan Data</h3>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white p-3 text-center">
                <h5>Total GOR</h5>
                <h2 class="m-0"><?= $total_gor ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white p-3 text-center">
                <h5>Total Lapangan</h5>
                <h2 class="m-0"><?= $total_lapangan ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white p-3 text-center">
                <h5>Booking Aktif</h5>
                <h2 class="m-0"><?= $total_booking ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white p-3 text-center">
                <h5>Selesai</h5>
                <h2 class="m-0"><?= $total_selesai ?></h2>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">Reservasi Terbaru</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Lapangan</th>
                            <th>Waktu</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT r.*, p.nama, l.nama_lapangan, j.tanggal, j.jam_mulai, j.jam_selesai
                                FROM reservasi r
                                JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan
                                JOIN jadwal j ON r.id_jadwal = j.id_jadwal
                                JOIN lapangan l ON j.id_lapangan = l.id_lapangan
                                ORDER BY r.tanggal_reservasi DESC LIMIT 10";
                        $result = $conn->query($sql);

                        if ($result->rowCount() > 0) {
                            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $statusColor = $row['status_reservasi'] == 'dibooking' ? 'warning' : 'success';
                                echo "<tr>";
                                echo "<td>#{$row['kode_booking']}</td>";
                                echo "<td>{$row['nama']}</td>";
                                echo "<td>{$row['nama_lapangan']}</td>";
                                echo "<td>" . date('d/m', strtotime($row['tanggal'])) . " (" . substr($row['jam_mulai'],0,5) . ")</td>";
                                echo "<td><span class='badge bg-$statusColor'>{$row['status_reservasi']}</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-3'>Belum ada data</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>