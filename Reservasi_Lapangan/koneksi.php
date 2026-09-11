<?php
// koneksi.php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'reservasi_lapangan_new';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>