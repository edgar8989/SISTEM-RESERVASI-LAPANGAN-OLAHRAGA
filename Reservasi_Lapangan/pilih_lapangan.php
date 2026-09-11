<?php 
include 'koneksi.php'; 
$id_gor = $_GET['id_gor'];

// Filter Jenis Lapangan jika ada
$where_jenis = "";
if(isset($_GET['jenis']) && $_GET['jenis'] != 'semua'){
    $jenis = $_GET['jenis'];
    $where_jenis = "AND jenis_lapangan = '$jenis'";
}

// Ambil Data GOR
$q_gor = $conn->prepare("SELECT * FROM gor WHERE id_gor = ?");
$q_gor->execute([$id_gor]);
$gor = $q_gor->fetch();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pilih Lapangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <a href="index.php" class="btn btn-secondary mb-3">&laquo; Kembali</a>
    <h2>Daftar Lapangan di <?= $gor['nama_gor'] ?></h2>
    
    <div class="btn-group my-4">
        <a href="?id_gor=<?=$id_gor?>&jenis=semua" class="btn btn-outline-primary">Semua</a>
        <a href="?id_gor=<?=$id_gor?>&jenis=futsal" class="btn btn-outline-primary">Futsal</a>
        <a href="?id_gor=<?=$id_gor?>&jenis=basket" class="btn btn-outline-primary">Basket</a>
        <a href="?id_gor=<?=$id_gor?>&jenis=voli" class="btn btn-outline-primary">Voli</a>
        <a href="?id_gor=<?=$id_gor?>&jenis=badminton" class="btn btn-outline-primary">Badminton</a>
    </div>

    <div class="row">
        <?php
        $sql = "SELECT * FROM lapangan WHERE id_gor = ? $where_jenis AND status='aktif'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_gor]);
        
        if($stmt->rowCount() == 0){
            echo "<div class='alert alert-warning'>Tidak ada lapangan tersedia untuk kategori ini.</div>";
        }

        while($lap = $stmt->fetch(PDO::FETCH_ASSOC)){
        ?>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-info text-white text-capitalize">
                    <?= $lap['jenis_lapangan'] ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= $lap['nama_lapangan'] ?></h5>
                    <p class="card-text fw-bold">Rp <?= number_format($lap['harga_per_jam'],0,',','.') ?> / Jam</p>
                    <a href="jadwal.php?id_lapangan=<?= $lap['id_lapangan'] ?>" class="btn btn-success">Lihat Jadwal</a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
</body>
</html>