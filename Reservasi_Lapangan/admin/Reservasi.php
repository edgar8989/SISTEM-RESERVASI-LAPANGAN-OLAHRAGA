<?php
require '../koneksi.php';
session_start();

/**
 * reservasi.php (Fixed: Logic based on ID Jadwal)
 */

/* Helper: redirect with query */
function redirect($url) {
    header("Location: $url");
    exit;
}

// Handle Update Reservasi (Status + Pindah Jadwal + Fasilitas)
if (isset($_POST['update_reservasi'])) {
    $id_reservasi = $_POST['id_reservasi'];
    $status = $_POST['status'];
    $id_jadwal_current = $_POST['id_jadwal']; // Jadwal asal (sebelum diedit)
    $id_jadwal_baru = $_POST['id_jadwal_baru']; // Jadwal target (dari dropdown)
    $fasilitas_baru = isset($_POST['fasilitas']) ? $_POST['fasilitas'] : [];

    try {
        $conn->beginTransaction();

        // 1. Cek apakah admin memindahkan slot jadwal?
        if ($id_jadwal_baru != $id_jadwal_current) {
            
            // Cek status slot baru apakah tersedia?
            $stmtCheck = $conn->prepare("SELECT status FROM jadwal WHERE id_jadwal = ?");
            $stmtCheck->execute([$id_jadwal_baru]);
            $status_baru = $stmtCheck->fetchColumn();

            if ($status_baru != 'tersedia') {
                $conn->rollBack();
                redirect("reservasi.php?error=bentrok");
            }

            // a. Jadwal lama dikembalikan jadi 'tersedia'
            $stmt = $conn->prepare("UPDATE jadwal SET status = 'tersedia' WHERE id_jadwal = ?");
            $stmt->execute([$id_jadwal_current]);

            // b. Jadwal baru diubah jadi 'dibooking'
            $stmt = $conn->prepare("UPDATE jadwal SET status = 'dibooking' WHERE id_jadwal = ?");
            $stmt->execute([$id_jadwal_baru]);

            // c. Update pointer reservasi ke jadwal baru
            $stmt = $conn->prepare("UPDATE reservasi SET id_jadwal = ? WHERE id_reservasi = ?");
            $stmt->execute([$id_jadwal_baru, $id_reservasi]);

            // Update variabel current agar logika di bawah tetap sinkron
            $id_jadwal_current = $id_jadwal_baru;
        }

        // 2. Update status reservasi (dibooking/selesai)
        $stmt = $conn->prepare("UPDATE reservasi SET status_reservasi = ? WHERE id_reservasi = ?");
        $stmt->execute([$status, $id_reservasi]);

        // 3. Update fasilitas (Reset lalu Insert ulang)
        $stmt = $conn->prepare("DELETE FROM reservasi_fasilitas WHERE id_reservasi = ?");
        $stmt->execute([$id_reservasi]);

        if (!empty($fasilitas_baru)) {
            $stmtFas = $conn->prepare("INSERT INTO reservasi_fasilitas (id_reservasi, id_fasilitas) VALUES (?, ?)");
            foreach ($fasilitas_baru as $id_fas) {
                $stmtFas->execute([$id_reservasi, $id_fas]);
            }
        }

        // 4. Sinkronisasi akhir status jadwal berdasarkan status reservasi
        // Jika status diubah jadi 'selesai', maka jadwal harus 'tersedia' (kosong kembali)
        // Jika masih 'dibooking', pastikan jadwal statusnya 'dibooking'
        if ($status == 'selesai') {
            $stmt = $conn->prepare("UPDATE jadwal SET status = 'tersedia' WHERE id_jadwal = ?");
            $stmt->execute([$id_jadwal_current]);
        } else {
            $stmt = $conn->prepare("UPDATE jadwal SET status = 'dibooking' WHERE id_jadwal = ?");
            $stmt->execute([$id_jadwal_current]);
        }

        $conn->commit();
        redirect("reservasi.php?success=update");

    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        // error_log($e->getMessage()); // Debugging
        redirect("reservasi.php?error=server_error");
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $conn->beginTransaction();
        $stmt = $conn->prepare("SELECT id_jadwal, id_pelanggan FROM reservasi WHERE id_reservasi = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($res) {
            $id_jadwal = $res['id_jadwal'];
            $id_pelanggan = $res['id_pelanggan'];

            $conn->prepare("UPDATE jadwal SET status = 'tersedia' WHERE id_jadwal = ?")->execute([$id_jadwal]);
            $conn->prepare("DELETE FROM reservasi_fasilitas WHERE id_reservasi = ?")->execute([$id]);
            $conn->prepare("DELETE FROM reservasi WHERE id_reservasi = ?")->execute([$id]);

            // Cek pelanggan yatim
            $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM reservasi WHERE id_pelanggan = ?");
            $stmtCheck->execute([$id_pelanggan]);
            if ((int)$stmtCheck->fetchColumn() === 0) {
                $conn->prepare("DELETE FROM pelanggan WHERE id_pelanggan = ?")->execute([$id_pelanggan]);
            }
        }
        $conn->commit();
        redirect("reservasi.php?success=delete");
    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        redirect("reservasi.php?error=server_error");
    }
}

// Filter View
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where = "";
if ($filter == 'dibooking') $where = "WHERE r.status_reservasi = 'dibooking'";
elseif ($filter == 'selesai') $where = "WHERE r.status_reservasi = 'selesai'";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kelola Reservasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><i class="bi bi-shield-lock"></i> Admin Panel</a>
        <div class="ms-auto">
            <a href="index.php" class="btn btn-light btn-sm"><i class="bi bi-house"></i> Dashboard</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= ($_GET['success'] == 'update') ? "Reservasi berhasil diupdate!" : "Reservasi berhasil dihapus!"; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php
            if ($_GET['error'] == 'bentrok') echo "⚠️ Slot jam tersebut sudah dibooking orang lain!";
            elseif ($_GET['error'] == 'jadwal_tidak_ada') echo "⚠️ Slot jadwal tidak valid.";
            else echo "Terjadi kesalahan server.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-bookmark-check"></i> Kelola Reservasi</h2>
        <div class="btn-group">
            <a href="reservasi.php?filter=all" class="btn btn-sm <?= $filter == 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
            <a href="reservasi.php?filter=dibooking" class="btn btn-sm <?= $filter == 'dibooking' ? 'btn-warning' : 'btn-outline-warning' ?>">Dibooking</a>
            <a href="reservasi.php?filter=selesai" class="btn btn-sm <?= $filter == 'selesai' ? 'btn-success' : 'btn-outline-success' ?>">Selesai</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Kode Booking</th>
                            <th>Pelanggan</th>
                            <th>Lapangan</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT r.*, p.nama as nama_pelanggan, p.email, p.no_telepon,
                                l.nama_lapangan, l.id_lapangan, g.nama_gor,
                                j.tanggal, j.jam_mulai, j.jam_selesai, j.id_jadwal
                                FROM reservasi r
                                JOIN pelanggan p ON r.id_pelanggan = p.id_pelanggan
                                JOIN jadwal j ON r.id_jadwal = j.id_jadwal
                                JOIN lapangan l ON j.id_lapangan = l.id_lapangan
                                JOIN gor g ON l.id_gor = g.id_gor
                                $where
                                ORDER BY r.tanggal_reservasi DESC";
                        $result = $conn->query($sql);

                        if ($result->rowCount() > 0) {
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $badge_class = $row['status_reservasi'] == 'dibooking' ? 'bg-warning' : 'bg-success';
                                
                                // Get Fasilitas Terpilih
                                $stmtFasTerpilih = $conn->prepare("SELECT id_fasilitas FROM reservasi_fasilitas WHERE id_reservasi = ?");
                                $stmtFasTerpilih->execute([$row['id_reservasi']]);
                                $fasilitas_terpilih = $stmtFasTerpilih->fetchAll(PDO::FETCH_COLUMN);
                                
                                // Get Semua Fasilitas
                                $stmtAllFas = $conn->prepare("SELECT f.id_fasilitas, f.nama_fasilitas 
                                                              FROM fasilitas f 
                                                              JOIN lapangan_fasilitas lf ON f.id_fasilitas = lf.id_fasilitas 
                                                              WHERE lf.id_lapangan = ?");
                                $stmtAllFas->execute([$row['id_lapangan']]);
                                $all_fasilitas = $stmtAllFas->fetchAll(PDO::FETCH_ASSOC);

                                echo "<tr>";
                                echo "<td>{$row['id_reservasi']}</td>";
                                echo "<td><strong>{$row['kode_booking']}</strong></td>";
                                echo "<td>{$row['nama_pelanggan']}<br><small class='text-muted'>{$row['no_telepon']}</small></td>";
                                echo "<td><strong>{$row['nama_lapangan']}</strong><br><small class='text-muted'>{$row['nama_gor']}</small></td>";
                                echo "<td>".date('d-m-Y', strtotime($row['tanggal']))."</td>";
                                echo "<td>".substr($row['jam_mulai'],0,5)." - ".substr($row['jam_selesai'],0,5)."</td>";
                                echo "<td><span class='badge {$badge_class}'>{$row['status_reservasi']}</span></td>";
                                echo "<td>
                                        <button class='btn btn-sm btn-info' data-bs-toggle='modal' data-bs-target='#detailModal{$row['id_reservasi']}'><i class='bi bi-eye'></i></button>
                                        <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#editModal{$row['id_reservasi']}'><i class='bi bi-pencil'></i> Edit</button>
                                        <a href='reservasi.php?delete={$row['id_reservasi']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Yakin hapus reservasi ini?')\"><i class='bi bi-trash'></i></a>
                                      </td>";
                                echo "</tr>";
                                
                                // Modal Detail (Code omitted for brevity as requested, logic same as before)
                                echo "<div class='modal fade' id='detailModal{$row['id_reservasi']}' tabindex='-1'>
                                        <div class='modal-dialog'>
                                            <div class='modal-content'>
                                                <div class='modal-header'><h5 class='modal-title'>Detail</h5><button type='button' class='btn-close' data-bs-dismiss='modal'></button></div>
                                                <div class='modal-body'>
                                                    <p><strong>Kode:</strong> {$row['kode_booking']}</p>
                                                    <p><strong>Pelanggan:</strong> {$row['nama_pelanggan']}</p>
                                                    <p><strong>Jam:</strong> ".substr($row['jam_mulai'],0,5)." - ".substr($row['jam_selesai'],0,5)."</p>
                                                </div>
                                            </div>
                                        </div>
                                      </div>";

                                // --- START MODAL EDIT ---
                                // 1. Ambil SLOTS jadwal (ID JADWAL sebagai value)
                                $stmtSlot = $conn->prepare("
                                    SELECT id_jadwal, jam_mulai, jam_selesai, status
                                    FROM jadwal 
                                    WHERE id_lapangan = ? AND tanggal = ?
                                    ORDER BY jam_mulai ASC
                                ");
                                $stmtSlot->execute([$row['id_lapangan'], $row['tanggal']]);
                                $slots = $stmtSlot->fetchAll(PDO::FETCH_ASSOC);

                                $options_html = "";
                                foreach ($slots as $s) {
                                    // Logic: Value sekarang adalah ID_JADWAL
                                    $is_selected = ($s['id_jadwal'] == $row['id_jadwal']) ? 'selected' : '';
                                    
                                    // Label tampilan tetap jam
                                    $label = substr($s['jam_mulai'], 0, 5) . " - " . substr($s['jam_selesai'], 0, 5);
                                    
                                    $status_info = "";
                                    // Disable jika dibooking orang lain (opsional, tapi UX bagus)
                                    $disabled = "";
                                    if ($s['status'] != 'tersedia' && $is_selected == '') {
                                        $status_info = " (Tidak Tersedia)";
                                        $disabled = "disabled"; 
                                    }

                                    // Render Option dengan value ID
                                    $options_html .= "<option value='{$s['id_jadwal']}' {$is_selected} {$disabled}>{$label}{$status_info}</option>";
                                }

                                echo "
                                <div class='modal fade' id='editModal{$row['id_reservasi']}' tabindex='-1'>
                                    <div class='modal-dialog modal-lg'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title'>Edit Reservasi</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                            </div>
                                            <form method='POST'>
                                                <div class='modal-body'>
                                                    <input type='hidden' name='id_reservasi' value='{$row['id_reservasi']}'>
                                                    <input type='hidden' name='id_jadwal' value='{$row['id_jadwal']}'>
                                                    
                                                    <div class='alert alert-info'>
                                                        <strong>Kode:</strong> {$row['kode_booking']} | 
                                                        <strong>Lapangan:</strong> {$row['nama_lapangan']}
                                                    </div>
                                                    
                                                    <div class='row'>
                                                        <div class='col-md-4'>
                                                            <div class='mb-3'>
                                                                <label class='form-label'>Status *</label>
                                                                <select name='status' class='form-select' required>
                                                                    <option value='dibooking' ".($row['status_reservasi'] == 'dibooking' ? 'selected' : '').">Dibooking</option>
                                                                    <option value='selesai' ".($row['status_reservasi'] == 'selesai' ? 'selected' : '').">Selesai</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class='col-md-8'>
                                                            <div class='mb-3'>
                                                                <label class='form-label'>Pilih Slot Jam *</label>
                                                                <select name='id_jadwal_baru' class='form-select' required>
                                                                    {$options_html}
                                                                </select>
                                                                <small class='text-muted'>Hanya pilih slot yang tersedia.</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <hr>
                                                    <h6>Fasilitas Tambahan</h6>
                                                    <div class='border p-3 rounded'>";
                                                        if (!empty($all_fasilitas)) {
                                                            foreach ($all_fasilitas as $fas) {
                                                                $checked = in_array($fas['id_fasilitas'], $fasilitas_terpilih) ? 'checked' : '';
                                                                echo "<div class='form-check'>
                                                                        <input class='form-check-input' type='checkbox' name='fasilitas[]' value='{$fas['id_fasilitas']}' id='fas{$row['id_reservasi']}_{$fas['id_fasilitas']}' {$checked}>
                                                                        <label class='form-check-label' for='fas{$row['id_reservasi']}_{$fas['id_fasilitas']}'>{$fas['nama_fasilitas']}</label>
                                                                      </div>";
                                                            }
                                                        } else {
                                                            echo "<p class='text-muted mb-0'><i>Tidak ada fasilitas</i></p>";
                                                        }
                                echo "              </div>
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                                                    <button type='submit' name='update_reservasi' class='btn btn-primary'><i class='bi bi-save'></i> Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>Belum ada reservasi</td></tr>";
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