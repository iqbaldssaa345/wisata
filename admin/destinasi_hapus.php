<?php
require_once __DIR__ . '/../koneksi.php';
require_role('admin');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // Cek destinasi
    $check = mysqli_query($conn, "SELECT nama_wisata FROM destinasi WHERE id = {$id} LIMIT 1");
    if ($check && mysqli_num_rows($check) > 0) {
        $nama = mysqli_fetch_assoc($check)['nama_wisata'];
        mysqli_query($conn, "DELETE FROM destinasi WHERE id = {$id}");
        set_flash('success', 'Destinasi "' . htmlspecialchars($nama) . '" berhasil dihapus.');
    } else {
        set_flash('danger', 'Destinasi tidak ditemukan.');
    }
}

header('Location: destinasi.php');
exit;
