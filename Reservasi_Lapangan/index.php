<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Reservasi Lapangan Olahraga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-5">Sistem Reservasi Lapangan Olahraga</h1>
        <h4 class="text-center mb-4">Pilih Lokasi GOR</h4>
        
        <div class="row justify-content-center">
            <?php
            $stmt = $conn->query("SELECT * FROM gor");
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm hover-shadow">
                    <div class="card-body text-center">
                        <h3 class="card-title"><?= $row['nama_gor'] ?></h3>
                        <p class="card-text text-muted"><?= $row['kota'] ?></p>
                        <p><?= $row['deskripsi'] ?></p>
                        <a href="pilih_lapangan.php?id_gor=<?= $row['id_gor'] ?>" class="btn btn-primary w-100">Pilih GOR Ini</a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>