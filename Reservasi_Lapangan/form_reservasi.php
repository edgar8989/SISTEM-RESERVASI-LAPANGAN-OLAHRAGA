<?php
include 'koneksi.php';
session_start();

if (!isset($_GET['id_jadwal'])) {
    die("ID Jadwal tidak ditemukan!");
}

$id_jadwal = $_GET['id_jadwal'];

// Ambil fasilitas yang dipilih (jika ada)
$fasilitas_dipilih = isset($_GET['fasilitas']) ? $_GET['fasilitas'] : [];

// Ambil data jadwal
$stmt = $conn->prepare("SELECT j.*, l.nama_lapangan 
                        FROM jadwal j 
                        JOIN lapangan l ON l.id_lapangan = j.id_lapangan
                        WHERE j.id_jadwal = ?");
$stmt->execute([$id_jadwal]);
$jadwal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jadwal) {
    die("Jadwal tidak ditemukan.");
}

// Jika user submit form
if (isset($_POST['submit'])) {

    // SIMPAN KE SESSION
    $_SESSION['booking_nama'] = $_POST['nama'];
    $_SESSION['booking_email'] = $_POST['email'];
    $_SESSION['booking_telepon'] = $_POST['telepon'];
    $_SESSION['booking_id_jadwal'] = $id_jadwal;
    
    // SIMPAN FASILITAS YANG DIPILIH KE SESSION
    $_SESSION['booking_fasilitas'] = $fasilitas_dipilih;

    // Redirect ke pembayaran
    header("Location: pembayaran.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Reservasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
    <h3>Form Reservasi Lapangan</h3>

    <div class="card my-4">
        <div class="card-body">
            <h5>Detail Jadwal</h5>
            <p><b>Lapangan:</b> <?= $jadwal['nama_lapangan'] ?></p>
            <p><b>Tanggal:</b> <?= date('d-m-Y', strtotime($jadwal['tanggal'])) ?></p>
            <p><b>Jam:</b> <?= substr($jadwal['jam_mulai'],0,5) ?> - <?= substr($jadwal['jam_selesai'],0,5) ?></p>
            
            <?php if (!empty($fasilitas_dipilih)): ?>
                <hr>
                <h5>Fasilitas Tambahan:</h5>
                <ul>
                    <?php 
                    // Ambil nama fasilitas yang dipilih
                    $placeholders = implode(',', array_fill(0, count($fasilitas_dipilih), '?'));
                    $stmtFas = $conn->prepare("SELECT nama_fasilitas FROM fasilitas WHERE id_fasilitas IN ($placeholders)");
                    $stmtFas->execute($fasilitas_dipilih);
                    $fasilitas_list = $stmtFas->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($fasilitas_list as $f): ?>
                        <li><?= $f['nama_fasilitas'] ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-muted"><i>Tidak ada fasilitas tambahan dipilih</i></p>
            <?php endif; ?>
        </div>
    </div>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" required class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" required class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Nomor Telepon</label>
            <input type="text" name="telepon" required class="form-control">
        </div>

        <button type="submit" name="submit" class="btn btn-primary w-100">
            Lanjut Pembayaran
        </button>
    </form>

</div>

</body>
</html>