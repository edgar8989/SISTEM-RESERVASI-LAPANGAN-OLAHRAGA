<?php
session_start();
include 'koneksi.php';

// Pastikan data booking ada
if (
    !isset($_SESSION['booking_nama']) ||
    !isset($_SESSION['booking_email']) ||
    !isset($_SESSION['booking_telepon']) ||
    !isset($_SESSION['booking_id_jadwal'])
) {
    die("Data reservasi tidak ditemukan. Silakan ulangi pemesanan.");
}

$nama = $_SESSION['booking_nama'];
$email = $_SESSION['booking_email'];
$telepon = $_SESSION['booking_telepon'];
$id_jadwal = $_SESSION['booking_id_jadwal'];
$fasilitas_dipilih = isset($_SESSION['booking_fasilitas']) ? $_SESSION['booking_fasilitas'] : [];

// Ambil detail jadwal
$stmtJ = $conn->prepare("SELECT j.*, l.nama_lapangan 
                         FROM jadwal j 
                         JOIN lapangan l ON j.id_lapangan = l.id_lapangan
                         WHERE id_jadwal = ?");
$stmtJ->execute([$id_jadwal]);
$jadwal = $stmtJ->fetch(PDO::FETCH_ASSOC);

// Jika user klik tombol bayar
if (isset($_POST['konfirmasi_bayar'])) {

    // 1. Cek pelanggan
    $stmtCheck = $conn->prepare("SELECT id_pelanggan FROM pelanggan WHERE email = ?");
    $stmtCheck->execute([$email]);
    $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $id_pelanggan = $existing['id_pelanggan'];
        $conn->prepare("UPDATE pelanggan SET nama=?, no_telepon=? WHERE id_pelanggan=?")
             ->execute([$nama, $telepon, $id_pelanggan]);
    } else {
        $conn->prepare("INSERT INTO pelanggan (nama, email, no_telepon) VALUES (?, ?, ?)")
             ->execute([$nama, $email, $telepon]);
        $id_pelanggan = $conn->lastInsertId();
    }

    // 2. Generate kode booking
    $kode_booking = "BK-" . date("Ymd") . "-" . strtoupper(substr(md5(time()), 0, 4));

    // ======== ADMIN DEFAULT ID = 1 (TANPA LOGIN) ========
    $id_admin = 1;

    // 3. Insert Reservasi + admin
    $conn->prepare("INSERT INTO reservasi (id_pelanggan, id_jadwal, id_admin, tanggal_reservasi, status_reservasi, kode_booking) 
                    VALUES (?, ?, ?, NOW(), 'dibooking', ?)")
         ->execute([$id_pelanggan, $id_jadwal, $id_admin, $kode_booking]);

    $id_reservasi = $conn->lastInsertId();

    // 4. Simpan fasilitas
    if (!empty($fasilitas_dipilih)) {
        $stmtFasilitas = $conn->prepare("INSERT INTO reservasi_fasilitas (id_reservasi, id_fasilitas) VALUES (?, ?)");
        
        foreach ($fasilitas_dipilih as $id_fasilitas) {
            $stmtFasilitas->execute([$id_reservasi, $id_fasilitas]);
        }
    }

    // 5. Update jadwal
    $conn->prepare("UPDATE jadwal SET status='dibooking' WHERE id_jadwal=?")
         ->execute([$id_jadwal]);

    // 6. Hitung total bayar
    $stmtHarga = $conn->prepare("
        SELECT l.harga_per_jam 
        FROM jadwal j 
        JOIN lapangan l ON j.id_lapangan = l.id_lapangan
        WHERE j.id_jadwal = ?
    ");
    $stmtHarga->execute([$id_jadwal]);
    $harga = $stmtHarga->fetchColumn();

    // 7. Insert pembayaran
    $stmtBayar = $conn->prepare("
        INSERT INTO pembayaran (id_reservasi, metode_pembayaran, total_bayar, status_pembayaran, tanggal_bayar)
        VALUES (?, ?, ?, 'berhasil', NOW())
    ");
    $stmtBayar->execute([$id_reservasi, "QRIS", $harga]);

    // Hapus session booking
    session_unset();

    // Redirect sukses
    header("Location: sukses.php?id_reservasi=$id_reservasi");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
    <h3>Konfirmasi Pembayaran</h3>

    <div class="card my-4">
        <div class="card-body">
            <h5>Detail Pemesan</h5>
            <p><b>Nama:</b> <?= $nama ?></p>
            <p><b>Email:</b> <?= $email ?></p>
            <p><b>Telepon:</b> <?= $telepon ?></p>

            <hr>

            <h5>Detail Jadwal</h5>
            <p><b>Lapangan:</b> <?= $jadwal['nama_lapangan'] ?></p>
            <p><b>Tanggal:</b> <?= date('d-m-Y', strtotime($jadwal['tanggal'])) ?></p>
            <p><b>Jam:</b> <?= substr($jadwal['jam_mulai'], 0, 5) ?> - <?= substr($jadwal['jam_selesai'], 0, 5) ?></p>

            <?php if (!empty($fasilitas_dipilih)): ?>
                <hr>
                <h5>Fasilitas Tambahan</h5>
                <ul>
                    <?php 
                    $placeholders = implode(',', array_fill(0, count($fasilitas_dipilih), '?'));
                    $stmtFas = $conn->prepare("SELECT nama_fasilitas FROM fasilitas WHERE id_fasilitas IN ($placeholders)");
                    $stmtFas->execute($fasilitas_dipilih);
                    $fasilitas_list = $stmtFas->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($fasilitas_list as $f): ?>
                        <li><?= $f['nama_fasilitas'] ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="text-center mb-4">
        <h5>Silakan Scan untuk Pembayaran</h5>
        <img src="qris.png" width="300">
    </div>

    <form method="POST">
        <button name="konfirmasi_bayar" class="btn btn-success w-100">
            Saya Sudah Bayar
        </button>
    </form>
</div>

</body>
</html>
