<?php
require_once __DIR__ . '/../koneksi.php';
require_role('admin');

$user = auth_user();

// Hitung metrik
$total_destinasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM destinasi"))['t'] ?? 0;
$total_pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pemesanan_tiket"))['t'] ?? 0;
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as t FROM pemesanan_tiket WHERE status_pemesanan IN ('lunas', 'selesai')"))['t'] ?? 0;
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM users"))['t'] ?? 0;

// Pesanan terbaru
$pesanan_terbaru = mysqli_query($conn, "SELECT p.*, u.nama_lengkap as nama_pemesan, d.nama_destinasi 
                                       FROM pemesanan_tiket p 
                                       JOIN users u ON p.user_id = u.id 
                                       JOIN destinasi d ON p.destinasi_id = d.id 
                                       ORDER BY p.id DESC LIMIT 6");

// Destinasi dengan rating tertinggi
$top_destinasi = mysqli_query($conn, "SELECT d.*, k.nama_kategori, p.nama_provinsi 
                                      FROM destinasi d 
                                      LEFT JOIN kategori_wisata k ON d.kategori_id = k.id 
                                      LEFT JOIN provinsi p ON d.provinsi_id = p.id 
                                      ORDER BY d.rating DESC, d.id ASC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Administrator - Pesona Wisata Indonesia</title>
  
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
        <a href="index.php" class="sidebar-link active">
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
        <a href="laporan.php" class="sidebar-link">
          <i class="fas fa-file-invoice-dollar"></i> Laporan Keuangan
        </a>
      </li>

      <li class="sidebar-heading mt-3">Navigasi Multi-Tab</li>
      <li class="sidebar-item">
        <a href="../index.php" target="_blank" class="sidebar-link">
          <i class="fas fa-external-link-alt"></i> Buka Website (Tab Baru)
        </a>
      </li>
      <li class="sidebar-item">
        <a href="../petugas/index.php" class="sidebar-link">
          <i class="fas fa-clipboard-check"></i> Coba Tab Petugas
        </a>
      </li>
      <li class="sidebar-item">
        <a href="../pengunjung/index.php" class="sidebar-link">
          <i class="fas fa-compass"></i> Coba Tab Pengunjung
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

  <!-- MAIN AREA -->
  <main class="dashboard-main">
    <header class="dashboard-topbar">
      <div class="topbar-left">
        <button class="btn btn-light d-lg-none me-2" id="sidebarToggleBtn">
          <i class="fas fa-bars"></i>
        </button>
        <h2 class="topbar-title">Ringkasan Sistem & Statistik Wisata</h2>
      </div>

      <div class="topbar-right">
        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill font-monospace">
          <i class="fas fa-circle text-danger me-1"></i> Mode Admin Aktif
        </span>
        <a href="../logout.php?role=admin" class="btn btn-outline-danger btn-sm rounded-pill px-3">
          <i class="fas fa-arrow-right-from-bracket me-1"></i> Logout Admin
        </a>
      </div>
    </header>

    <div class="dashboard-container">
      <?php display_flash(); ?>

      <!-- METRIC CARDS -->
      <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
          <div class="metric-card">
            <div class="metric-info">
              <h6>Total Destinasi</h6>
              <h3><?= number_format($total_destinasi); ?></h3>
              <small class="text-success"><i class="fas fa-check-circle me-1"></i> Lengkap Se-Jawa</small>
            </div>
            <div class="metric-icon-box bg-emerald-light">
              <i class="fas fa-mountain"></i>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6">
          <div class="metric-card">
            <div class="metric-info">
              <h6>Total Transaksi</h6>
              <h3><?= number_format($total_pesanan); ?></h3>
              <small class="text-muted">Pemesanan Tiket</small>
            </div>
            <div class="metric-icon-box bg-blue-light">
              <i class="fas fa-ticket-alt"></i>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6">
          <div class="metric-card">
            <div class="metric-info">
              <h6>Pendapatan Bersih</h6>
              <h3 style="font-size: 1.45rem;"><?= rupiah($total_pendapatan); ?></h3>
              <small class="text-success"><i class="fas fa-arrow-trend-up me-1"></i> Terverifikasi Lunas</small>
            </div>
            <div class="metric-icon-box bg-amber-light">
              <i class="fas fa-coins"></i>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6">
          <div class="metric-card">
            <div class="metric-info">
              <h6>Total Pengguna</h6>
              <h3><?= number_format($total_users); ?></h3>
              <small class="text-muted">Admin, Petugas, Wisatawan</small>
            </div>
            <div class="metric-icon-box bg-purple-light">
              <i class="fas fa-users"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <!-- RECENT BOOKINGS -->
        <div class="col-lg-8">
          <div class="card-luxury">
            <div class="card-luxury-header">
              <h5 class="card-luxury-title">
                <i class="fas fa-clock-rotate-left text-primary"></i> Transaksi Tiket Terbaru
              </h5>
              <a href="pesanan.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
            </div>
            <div class="table-responsive">
              <table class="table table-luxury">
                <thead>
                  <tr>
                    <th>Kode Booking</th>
                    <th>Nama Pemesan</th>
                    <th>Destinasi</th>
                    <th>Total</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($pesanan_terbaru && mysqli_num_rows($pesanan_terbaru) > 0): ?>
                    <?php while ($p = mysqli_fetch_assoc($pesanan_terbaru)): ?>
                      <tr>
                        <td>
                          <span class="font-monospace fw-bold text-dark"><?= htmlspecialchars($p['kode_booking']); ?></span>
                          <small class="d-block text-muted"><?= tanggal_indo($p['tanggal_kunjungan']); ?></small>
                        </td>
                        <td><?= htmlspecialchars($p['nama_pemesan']); ?></td>
                        <td><?= htmlspecialchars($p['nama_destinasi']); ?></td>
                        <td class="fw-bold text-success"><?= rupiah($p['total_harga']); ?></td>
                        <td>
                          <?php 
                            $status_label = $p['status_pemesanan'];
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
                  <?php else: ?>
                    <tr>
                      <td colspan="5" class="text-center py-4 text-muted">Belum ada data pesanan tiket masuk.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- TOP RATED DESTINATIONS -->
        <div class="col-lg-4">
          <div class="card-luxury">
            <div class="card-luxury-header">
              <h5 class="card-luxury-title">
                <i class="fas fa-star text-warning"></i> Wisata Terfavorit
              </h5>
              <a href="destinasi.php" class="btn btn-sm btn-light border rounded-pill">Kelola</a>
            </div>
            <div class="card-luxury-body p-0">
              <ul class="list-group list-group-flush">
                <?php while ($d = mysqli_fetch_assoc($top_destinasi)): ?>
                  <li class="list-group-item d-flex align-items-center justify-content-between p-3">
                    <div class="d-flex align-items-center gap-3">
                      <img src="<?= htmlspecialchars($d['gambar_utama']); ?>" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;" alt="<?= htmlspecialchars($d['nama_destinasi']); ?>">
                      <div>
                        <h6 class="mb-0 fw-bold fs-7 text-dark"><?= htmlspecialchars($d['nama_destinasi']); ?></h6>
                        <small class="text-muted"><i class="fas fa-map-pin me-1 text-danger"></i> <?= htmlspecialchars($d['nama_provinsi']); ?></small>
                      </div>
                    </div>
                    <span class="badge bg-warning text-dark font-monospace fw-bold px-2 py-1">
                      <i class="fas fa-star"></i> <?= $d['rating']; ?>
                    </span>
                  </li>
                <?php endwhile; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
