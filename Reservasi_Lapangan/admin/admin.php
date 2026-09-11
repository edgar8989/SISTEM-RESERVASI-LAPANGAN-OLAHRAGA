<?php
require '../koneksi.php';
session_start();

$message = "";

// Jika form submit
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_telepon = $_POST['no_telepon'];

    try {
        // Insert admin baru
        $stmt = $conn->prepare("INSERT INTO admin (nama, email, no_telepon) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $email, $no_telepon]);

        $message = "<div class='alert alert-success'>Admin berhasil ditambahkan!</div>";
    } catch (PDOException $e) {
        // Jika email duplikat
        if ($e->errorInfo[1] == 1062) {
            $message = "<div class='alert alert-danger'>Email sudah terdaftar!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Terjadi kesalahan: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h4 class="m-0">Tambah Admin Baru</h4>
        </div>

        <div class="card-body">
            <?= $message ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Admin</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Admin</label>
                    <input type="email" name="email" class="form-control" required>
                    <small class="text-muted">Email tidak boleh sama dengan admin lain.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="no_telepon" class="form-control">
                </div>

                <button type="submit" name="submit" class="btn btn-primary w-100">
                    Simpan Admin
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
