<?php
include 'koneksi.php';
session_start();

// ==============================
// AMBIL ID JADWAL
// ==============================
if (!isset($_GET['id_jadwal'])) {
    die("ID Jadwal tidak ditemukan.");
}

$id_jadwal = $_GET['id_jadwal'];

// ==============================
// AMBIL DATA JADWAL + LAPANGAN
// ==============================
$stmt = $conn->prepare("
    SELECT j.*, l.nama_lapangan, l.id_lapangan
    FROM jadwal j
    JOIN lapangan l ON j.id_lapangan = l.id_lapangan
    WHERE j.id_jadwal = ?
");
$stmt->execute([$id_jadwal]);
$jadwal = $stmt->fetch();

if (!$jadwal) {
    die("Jadwal tidak ditemukan.");
}

$id_lapangan = $jadwal['id_lapangan'];

// ==========================================
// AMBIL FASILITAS YANG DIMILIKI LAPANGAN TERSEBUT
// ==========================================
$fasilitasQuery = $conn->prepare("
    SELECT f.id_fasilitas, f.nama_fasilitas
    FROM fasilitas f
    JOIN lapangan_fasilitas lf ON lf.id_fasilitas = f.id_fasilitas
    WHERE lf.id_lapangan = ?
");
$fasilitasQuery->execute([$id_lapangan]);
$fasilitas = $fasilitasQuery->fetchAll(PDO::FETCH_ASSOC);

// Jika tidak ada fasilitas, array akan kosong — dan nanti ditangani di tampilan
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pilih Fasilitas Tambahan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container py-5">
    <h3 class="mb-3">Pilih Fasilitas Tambahan</h3>

    <p class="text-muted">
        Lapangan: <strong><?= $jadwal['nama_lapangan'] ?></strong><br>
        Tanggal: <strong><?= date('d-m-Y', strtotime($jadwal['tanggal'])) ?></strong><br>
        Jam: <strong><?= substr($jadwal['jam_mulai'],0,5) ?> - <?= substr($jadwal['jam_selesai'],0,5) ?></strong>
    </p>

    <form action="form_reservasi.php" method="GET">
        <input type="hidden" name="id_jadwal" value="<?= $id_jadwal ?>">

        <div class="card p-3">
            <h5 class="mb-3">Fasilitas Tersedia:</h5>

            <?php if (empty($fasilitas)): ?>
                <p class="text-danger">Tidak ada fasilitas tambahan untuk lapangan ini.</p>
            <?php else: ?>
                <?php foreach ($fasilitas as $f): ?>
                    <div class="form-check fs-5">
                        <input class="form-check-input" type="checkbox" 
                               name="fasilitas[]" 
                               value="<?= $f['id_fasilitas'] ?>" 
                               id="f<?= $f['id_fasilitas'] ?>">
                        <label class="form-check-label" for="f<?= $f['id_fasilitas'] ?>">
                            <?= $f['nama_fasilitas'] ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary mt-4 w-100">Lanjut ke Form Reservasi</button>
    </form>
</div>
</body>
</html>
