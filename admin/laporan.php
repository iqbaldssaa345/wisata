<?php
require_once __DIR__ . '/../koneksi.php';
require_role('admin');

$user = auth_user();

// Total Pendapatan
$total_income = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as s, SUM(jumlah_tiket) as t FROM pemesanan_tiket WHERE status_pemesanan IN ('lunas', 'selesai')"));
$pendapatan = $total_income['s'] ?? 0;
$tiket_terjual = $total_income['t'] ?? 0;

// Query Laporan per Destinasi
$laporan_destinasi = mysqli_query($conn, "SELECT d.nama_destinasi, pr.nama_provinsi, d.harga_tiket, 
                                          COALESCE(SUM(p.jumlah_tiket), 0) as total_tiket,
                                          COALESCE(SUM(CASE WHEN p.status_pemesanan IN ('lunas', 'selesai') THEN p.total_harga ELSE 0 END), 0) as total_omset
                                          FROM destinasi d 
                                          LEFT JOIN provinsi pr ON d.provinsi_id = pr.id 
                                          LEFT JOIN pemesanan_tiket p ON d.id = p.destinasi_id 
                                          GROUP BY d.id 
                                          ORDER BY total_omset DESC, d.id ASC");

// Semua transaksi
$laporan_transaksi = mysqli_query($conn, "SELECT p.*, u.nama_lengkap as nama_pemesan, d.nama_destinasi 
                                          FROM pemesanan_tiket p 
                                          JOIN users u ON p.user_id = u.id 
                                          JOIN destinasi d ON p.destinasi_id = d.id 
                                          ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Pendapatan & Kunjungan - Pesona Wisata Indonesia</title>
  
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
      <div class="sidebar-brand-icon">
        <i class="fas fa-mountain-sun"></i>
      </div>
      <div class="sidebar-brand-title">
        PESONA WISATA INDONESIA
        <small>Portal Admin</small>
      </div>
    </a>

    <ul class="sidebar-menu">
      <li class="sidebar-heading">Menu Utama</li>
      <li class="sidebar-item">
        <a href="index.php" class="sidebar-link">
          <i class="fas fa-gauge-high"></i> Dashboard
        </a>
      </li>
      <li class="sidebar-item">
        <a href="destinasi.php" class="sidebar-link">
          <i class="fas fa-map-location-dot"></i> Kelola 10 Destinasi
        </a>
      </li>
      <li class="sidebar-item">
        <a href="pesanan.php" class="sidebar-link">
          <i class="fas fa-ticket"></i> Semua Pesanan Tiket
        </a>
      </li>
      <li class="sidebar-item">
        <a href="users.php" class="sidebar-link">
          <i class="fas fa-users-gear"></i> Kelola Pengguna
        </a>
      </li>
      <li class="sidebar-item">
        <a href="laporan.php" class="sidebar-link active">
          <i class="fas fa-file-invoice-dollar"></i> Laporan Keuangan
        </a>
      </li>
    </ul>

    <div class="sidebar-footer">
      <div class="user-mini-card">
        <div class="user-mini-avatar bg-danger">
          <i class="fas fa-user-shield"></i>
        </div>
        <div class="user-mini-info">
          <div class="user-mini-name"><?= htmlspecialchars($user['nama']); ?></div>
          <div class="user-mini-role text-warning fw-bold">Super Admin</div>
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
        <h2 class="topbar-title">Laporan Penjualan Tiket & Rekapitulasi</h2>
      </div>

      <div class="topbar-right">
        <button onclick="window.print()" class="btn btn-luxury-primary btn-sm rounded-pill px-3 shadow">
          <i class="fas fa-print me-1"></i> Cetak Laporan (PDF)
        </button>
      </div>
    </header>

    <div class="dashboard-container">
      
      <!-- REPORT HEADER FOR PRINT -->
      <div class="text-center mb-4 pb-3 border-bottom d-none d-print-block">
        <h2 class="fw-bold mb-0">LAPORAN REKAPITULASI PENDAPATAN WISATA</h2>
        <h4 class="text-muted">PESONA WISATA INDONESIA</h4>
        <p class="mb-0 text-muted">Tanggal Cetak: <?= tanggal_indo(date('Y-m-d')); ?> | Dicetak Oleh: <?= htmlspecialchars($user['nama']); ?></p>
      </div>

      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <div class="metric-card">
            <div class="metric-info">
              <h6>Total Penerimaan Tiket</h6>
              <h2 class="text-success fw-bold"><?= rupiah($pendapatan); ?></h2>
              <small class="text-muted">Dari tiket berstatus Lunas & Selesai</small>
            </div>
            <div class="metric-icon-box bg-emerald-light">
              <i class="fas fa-vault"></i>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="metric-card">
            <div class="metric-info">
              <h6>Total Wisatawan Masuk</h6>
              <h2 class="text-primary fw-bold"><?= number_format($tiket_terjual); ?> Orang</h2>
              <small class="text-muted">Kunjungan di seluruh 10 destinasi Jawa</small>
            </div>
            <div class="metric-icon-box bg-blue-light">
              <i class="fas fa-users-viewfinder"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- REKAP PER DESTINASI -->
      <div class="card-luxury mb-4">
        <div class="card-luxury-header">
          <h5 class="card-luxury-title">
            <i class="fas fa-chart-pie text-primary"></i> Rekapitulasi Omset Berdasarkan Destinasi
          </h5>
        </div>
        <div class="table-responsive">
          <table class="table table-luxury">
            <thead>
              <tr>
                <th width="50">No</th>
                <th>Nama Destinasi Wisata</th>
                <th>Provinsi</th>
                <th>Tarif Tiket</th>
                <th class="text-center">Tiket Terjual</th>
                <th>Total Pendapatan</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $no = 1;
              while ($ld = mysqli_fetch_assoc($laporan_destinasi)): ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><strong><?= htmlspecialchars($ld['nama_destinasi']); ?></strong></td>
                  <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($ld['nama_provinsi']); ?></span></td>
                  <td><?= rupiah($ld['harga_tiket']); ?></td>
                  <td class="text-center fw-bold"><?= number_format($ld['total_tiket']); ?> tiket</td>
                  <td class="fw-bold text-success"><?= rupiah($ld['total_omset']); ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- DETAIL SEMUA TRANSAKSI -->
      <div class="card-luxury">
        <div class="card-luxury-header">
          <h5 class="card-luxury-title">
            <i class="fas fa-list-check text-primary"></i> Rincian Seluruh Transaksi Wisatawan
          </h5>
        </div>
        <div class="table-responsive">
          <table class="table table-luxury">
            <thead>
              <tr>
                <th>No</th>
                <th>Kode Booking</th>
                <th>Tgl Kunjungan</th>
                <th>Nama Pemesan</th>
                <th>Destinasi</th>
                <th>Jumlah Tiket</th>
                <th>Total Nominal</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $no = 1;
              while ($lt = mysqli_fetch_assoc($laporan_transaksi)): ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><code class="fw-bold"><?= htmlspecialchars($lt['kode_booking']); ?></code></td>
                  <td><?= tanggal_indo($lt['tanggal_kunjungan']); ?></td>
                  <td><?= htmlspecialchars($lt['nama_pemesan']); ?></td>
                  <td><?= htmlspecialchars($lt['nama_destinasi']); ?></td>
                  <td class="text-center"><?= $lt['jumlah_tiket']; ?> Orang</td>
                  <td class="fw-bold text-success"><?= rupiah($lt['total_harga']); ?></td>
                  <td>
                    <?php 
                      $status_label = $lt['status_pemesanan'];
                      $badge_class = 'pending';
                      if ($status_label === 'lunas') $badge_class = 'dikonfirmasi';
                      if ($status_label === 'selesai') $badge_class = 'selesai';
                      if ($status_label === 'dibatalkan') $badge_class = 'dibatalkan';
                    ?>
                    <span class="badge-status badge-status-<?= $badge_class; ?>">
                      <?= strtoupper($status_label); ?>
                    </span>
                  </td>
                </tr>
              <?php endwhile; ?>
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
