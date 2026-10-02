<?php
require_once __DIR__ . '/../koneksi.php';
require_role('petugas');

$user = auth_user();

$filter = sanitize($conn, $_GET['filter'] ?? 'all');
$where = "WHERE 1=1";

if ($filter === 'hari_ini') {
    $where .= " AND p.tanggal_kunjungan = CURDATE()";
} elseif ($filter === 'pending') {
    $where .= " AND p.status_pemesanan = 'menunggu_pembayaran'";
} elseif ($filter === 'lunas') {
    $where .= " AND p.status_pemesanan = 'lunas'";
} elseif ($filter === 'selesai') {
    $where .= " AND p.status_pemesanan = 'selesai'";
}

$tiket_all = mysqli_query($conn, "SELECT p.*, u.nama_lengkap as nama_pemesan, u.no_telp, u.email, d.nama_destinasi, pr.nama_provinsi 
                                  FROM pemesanan_tiket p 
                                  JOIN users u ON p.user_id = u.id 
                                  JOIN destinasi d ON p.destinasi_id = d.id 
                                  LEFT JOIN provinsi pr ON d.provinsi_id = pr.id 
                                  {$where}
                                  ORDER BY p.tanggal_kunjungan ASC, p.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Reservasi Tiket - Petugas Pesona Wisata Indonesia</title>
  
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏛️</text></svg>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="dashboard-body">

<div class="dashboard-layout">
  <!-- SIDEBAR -->
  <aside class="dashboard-sidebar">
    <a href="../index.php" class="sidebar-brand">
      <div class="sidebar-brand-icon bg-warning text-dark">
        <i class="fas fa-clipboard-check"></i>
      </div>
      <div class="sidebar-brand-title">
        PESONA WISATA INDONESIA
        <small>Portal Petugas Loket</small>
      </div>
    </a>

    <ul class="sidebar-menu">
      <li class="sidebar-heading">Menu Petugas</li>
      <li class="sidebar-item">
        <a href="index.php" class="sidebar-link">
          <i class="fas fa-qrcode"></i> Validasi Tiket
        </a>
      </li>
      <li class="sidebar-item">
        <a href="daftar_tiket.php" class="sidebar-link active">
          <i class="fas fa-list-check"></i> Semua Reservasi
        </a>
      </li>
    </ul>

    <div class="sidebar-footer">
      <div class="user-mini-card">
        <div class="user-mini-avatar bg-warning text-dark">
          <i class="fas fa-user-check"></i>
        </div>
        <div class="user-mini-info">
          <div class="user-mini-name"><?= htmlspecialchars($user['nama']); ?></div>
          <div class="user-mini-role text-warning fw-bold">Petugas Verifikasi</div>
        </div>
      </div>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="dashboard-main">
    <header class="dashboard-topbar">
      <div class="topbar-left">
        <button class="btn btn-light d-lg-none me-2" id="sidebarToggleBtn">
          <i class="fas fa-bars"></i>
        </button>
        <h2 class="topbar-title">Daftar Reservasi & Jadwal Kunjungan</h2>
      </div>

      <div class="topbar-right d-flex gap-2 align-items-center">
        <a href="index.php" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold text-dark">
          <i class="fas fa-qrcode me-1"></i> Mode Input / Scan Kode
        </a>
        <a href="../logout.php?role=petugas" class="btn btn-outline-danger btn-sm rounded-pill px-3">
          <i class="fas fa-arrow-right-from-bracket me-1"></i> Logout Petugas
        </a>
      </div>
    </header>

    <div class="dashboard-container">
      <?php display_flash(); ?>

      <!-- Filter Tabs -->
      <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="daftar_tiket.php?filter=all" class="btn btn-sm <?= ($filter === 'all') ? 'btn-dark' : 'btn-outline-secondary'; ?> rounded-pill px-3">
          Semua Tiket
        </a>
        <a href="daftar_tiket.php?filter=hari_ini" class="btn btn-sm <?= ($filter === 'hari_ini') ? 'btn-primary' : 'btn-outline-primary'; ?> rounded-pill px-3">
          <i class="fas fa-calendar-day me-1"></i> Jadwal Hari Ini
        </a>
        <a href="daftar_tiket.php?filter=pending" class="btn btn-sm <?= ($filter === 'pending') ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'; ?> rounded-pill px-3">
          <i class="fas fa-clock me-1"></i> Menunggu Bayar
        </a>
        <a href="daftar_tiket.php?filter=lunas" class="btn btn-sm <?= ($filter === 'lunas') ? 'btn-info text-white' : 'btn-outline-info'; ?> rounded-pill px-3">
          <i class="fas fa-check-circle me-1"></i> Lunas
        </a>
        <a href="daftar_tiket.php?filter=selesai" class="btn btn-sm <?= ($filter === 'selesai') ? 'btn-success' : 'btn-outline-success'; ?> rounded-pill px-3">
          <i class="fas fa-check-double me-1"></i> Selesai
        </a>
      </div>

      <div class="card-luxury">
        <div class="card-luxury-header">
          <h5 class="card-luxury-title">
            <i class="fas fa-ticket text-primary"></i> Data Reservasi Masuk
          </h5>
          <span class="badge bg-secondary-subtle text-dark px-3 py-2 rounded-pill font-monospace">
            <?= mysqli_num_rows($tiket_all); ?> Data Ditampilkan
          </span>
        </div>

        <div class="table-responsive">
          <table class="table table-luxury">
            <thead>
              <tr>
                <th>Kode Booking</th>
                <th>Tanggal Kunjungan</th>
                <th>Wisatawan</th>
                <th>Destinasi</th>
                <th>Jumlah Tiket</th>
                <th>Total Tarif</th>
                <th>Status</th>
                <th class="text-center">Aksi Cepat</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($tiket_all && mysqli_num_rows($tiket_all) > 0): ?>
                <?php while ($t = mysqli_fetch_assoc($tiket_all)): ?>
                  <tr>
                    <td><code class="fw-bold fs-7"><?= htmlspecialchars($t['kode_booking']); ?></code></td>
                    <td>
                      <strong><?= tanggal_indo($t['tanggal_kunjungan']); ?></strong>
                      <?php if ($t['tanggal_kunjungan'] === date('Y-m-d')): ?>
                        <span class="badge bg-primary ms-1">HARI INI</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div><?= htmlspecialchars($t['nama_pemesan']); ?></div>
                      <small class="text-muted"><i class="fab fa-whatsapp text-success me-1"></i> <?= htmlspecialchars($t['no_telp'] ?: '-'); ?></small>
                    </td>
                    <td><?= htmlspecialchars($t['nama_destinasi']); ?></td>
                    <td><?= $t['jumlah_tiket']; ?> Orang</td>
                    <td class="fw-bold text-success"><?= rupiah($t['total_harga']); ?></td>
                    <td>
                      <?php 
                        $stat = $t['status_pemesanan'];
                        $b_class = 'pending';
                        if ($stat === 'lunas') $b_class = 'dikonfirmasi';
                        if ($stat === 'selesai') $b_class = 'selesai';
                        if ($stat === 'dibatalkan') $b_class = 'dibatalkan';
                      ?>
                      <span class="badge-status badge-status-<?= $b_class; ?>">
                        <?= strtoupper($stat); ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <?php if ($t['status_pemesanan'] === 'menunggu_pembayaran'): ?>
                        <a href="index.php?konfirmasi_id=<?= $t['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                          Konfirmasi Lunas
                        </a>
                      <?php elseif ($t['status_pemesanan'] === 'lunas'): ?>
                        <a href="index.php?checkin_id=<?= $t['id']; ?>" class="btn btn-sm btn-success rounded-pill">
                          Check-in
                        </a>
                      <?php else: ?>
                        <span class="text-muted fs-8">-</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">Tidak ada tiket yang ditemukan dengan filter ini.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
