<?php
require_once __DIR__ . '/../koneksi.php';
require_role('pengunjung');

$user = auth_user();
$uid = $user['id'];

// Metrik Pengunjung
$total_tiket_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pemesanan_tiket WHERE user_id = {$uid} AND status_pemesanan IN ('menunggu_pembayaran', 'lunas')"))['t'] ?? 0;
$total_riwayat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pemesanan_tiket WHERE user_id = {$uid} AND status_pemesanan = 'selesai'"))['t'] ?? 0;
$total_ulasan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM ulasan_wisata WHERE user_id = {$uid}"))['t'] ?? 0;

// Tiket aktif terbaru
$tiket_aktif_list = mysqli_query($conn, "SELECT p.*, d.nama_destinasi, d.lokasi_spesifik, pr.nama_provinsi, d.gambar_utama 
                                         FROM pemesanan_tiket p 
                                         JOIN destinasi d ON p.destinasi_id = d.id 
                                         LEFT JOIN provinsi pr ON d.provinsi_id = pr.id 
                                         WHERE p.user_id = {$uid} 
                                         ORDER BY p.id DESC LIMIT 4");

// Rekomendasi destinasi
$rekomendasi = mysqli_query($conn, "SELECT d.*, k.nama_kategori, pr.nama_provinsi 
                                    FROM destinasi d 
                                    LEFT JOIN kategori_wisata k ON d.kategori_id = k.id 
                                    LEFT JOIN provinsi pr ON d.provinsi_id = pr.id 
                                    ORDER BY d.rating DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Area Pengunjung - Pesona Wisata Indonesia</title>
  
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏛️</text></svg>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="dashboard-body">

<div class="dashboard-layout">
  <!-- SIDEBAR PENGUNJUNG -->
  <aside class="dashboard-sidebar">
    <a href="../index.php" class="sidebar-brand">
      <div class="sidebar-brand-icon">
        <i class="fas fa-mountain-sun"></i>
      </div>
      <div class="sidebar-brand-title">
        PESONA WISATA INDONESIA
        <small>Portal Wisatawan</small>
      </div>
    </a>

    <ul class="sidebar-menu">
      <li class="sidebar-heading">Menu Utama</li>
      <li class="sidebar-item">
        <a href="index.php" class="sidebar-link active">
          <i class="fas fa-compass"></i> Beranda Wisatawan
        </a>
      </li>
      <li class="sidebar-item">
        <a href="pesan_tiket.php" class="sidebar-link">
          <i class="fas fa-cart-plus"></i> Pesan Tiket Baru
        </a>
      </li>
      <li class="sidebar-item">
        <a href="tiket_saya.php" class="sidebar-link">
          <i class="fas fa-ticket"></i> E-Tiket Saya
        </a>
      </li>
      <li class="sidebar-item">
        <a href="ulasan.php" class="sidebar-link">
          <i class="fas fa-star-half-stroke"></i> Ulasan Wisata
        </a>
      </li>
      <li class="sidebar-item">
        <a href="profil.php" class="sidebar-link">
          <i class="fas fa-id-card"></i> Profil Saya
        </a>
      </li>

      <li class="sidebar-heading mt-3">Navigasi Multi-Tab</li>
      <li class="sidebar-item">
        <a href="../index.php" target="_blank" class="sidebar-link">
          <i class="fas fa-external-link-alt"></i> Buka Beranda (Tab Baru)
        </a>
      </li>
      <?php if ($user['role'] === 'admin'): ?>
        <li class="sidebar-item">
          <a href="../admin/index.php" class="sidebar-link">
            <i class="fas fa-gauge-high"></i> Beralih ke Admin
          </a>
        </li>
      <?php endif; ?>
    </ul>

    <div class="sidebar-footer">
      <div class="user-mini-card">
        <div class="user-mini-avatar bg-emerald-light text-primary">
          <?= strtoupper(substr($user['nama'], 0, 1)); ?>
        </div>
        <div class="user-mini-info">
          <div class="user-mini-name"><?= htmlspecialchars($user['nama']); ?></div>
          <div class="user-mini-role text-success fw-bold">Wisatawan Terdaftar</div>
        </div>
      </div>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="dashboard-main">
    <header class="dashboard-topbar">
      <div class="topbar-left">
        <button class="btn btn-light d-lg-none me-2" id="sidebarToggleBtn">
          <i class="fas fa-bars"></i>
        </button>
        <h2 class="topbar-title">Halo, <?= htmlspecialchars($user['nama']); ?>! 👋</h2>
      </div>

      <div class="topbar-right">
        <a href="pesan_tiket.php" class="btn btn-luxury-gold btn-sm rounded-pill px-3 shadow">
          <i class="fas fa-ticket me-1"></i> Pesan Tiket Wisata
        </a>
        <a href="../logout.php?role=pengunjung" class="btn btn-outline-danger btn-sm rounded-pill px-3">
          <i class="fas fa-arrow-right-from-bracket me-1"></i> Logout Pengunjung
        </a>
      </div>
    </header>

    <div class="dashboard-container">
      <?php display_flash(); ?>

      <!-- BANNER HERO WISATAWAN -->
      <div class="card-luxury border-0 mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);">
        <div class="card-luxury-body p-4 p-md-5 position-relative">
          <div class="row align-items-center">
            <div class="col-lg-8">
              <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold text-uppercase mb-3">Pesona Wisata Indonesia</span>
              <h2 class="font-serif fw-bold mb-2">Jelajahi 10 Mahakarya Wisata Terbaik</h2>
              <p class="text-white-50 mb-4" style="max-width: 580px;">
                Dapatkan tiket masuk resmi ke Borobudur, Bromo, Kawah Ijen, Dieng hingga Karimunjawa dengan konfirmasi instan dan e-tiket langsung di smartphone Anda.
              </p>
              <div class="d-flex flex-wrap gap-2">
                <a href="pesan_tiket.php" class="btn btn-light text-success fw-bold rounded-pill px-4">
                  <i class="fas fa-cart-shopping me-1"></i> Pesan Tiket Baru
                </a>
                <a href="tiket_saya.php" class="btn btn-outline-light rounded-pill px-4">
                  <i class="fas fa-qrcode me-1"></i> Lihat E-Tiket Saya
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- METRIC CARDS -->
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="metric-card">
            <div class="metric-info">
              <h6>Tiket Aktif / Siap Pakai</h6>
              <h3 class="text-primary"><?= $total_tiket_aktif; ?> Tiket</h3>
              <small class="text-muted">Siap untuk digunakan berwisata</small>
            </div>
            <div class="metric-icon-box bg-blue-light">
              <i class="fas fa-ticket-alt"></i>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="metric-card">
            <div class="metric-info">
              <h6>Wisata Selesai Dikunjungi</h6>
              <h3 class="text-success"><?= $total_riwayat; ?> Kali</h3>
              <small class="text-muted">Koleksi petualangan berharga</small>
            </div>
            <div class="metric-icon-box bg-emerald-light">
              <i class="fas fa-check-double"></i>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="metric-card">
            <div class="metric-info">
              <h6>Ulasan yang Diberikan</h6>
              <h3 class="text-warning"><?= $total_ulasan; ?> Review</h3>
              <small class="text-muted">Membantu sesama pelancong</small>
            </div>
            <div class="metric-icon-box bg-amber-light">
              <i class="fas fa-star"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- TIKET AKTIF SAYA -->
      <div class="card-luxury mb-4">
        <div class="card-luxury-header">
          <h5 class="card-luxury-title">
            <i class="fas fa-receipt text-primary"></i> Tiket Terkini Anda
          </h5>
          <a href="tiket_saya.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua E-Tiket</a>
        </div>
        <div class="table-responsive">
          <table class="table table-luxury">
            <thead>
              <tr>
                <th>Kode Booking</th>
                <th>Destinasi Wisata</th>
                <th>Tanggal Kunjungan</th>
                <th>Jumlah Tiket</th>
                <th>Total Bayar</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($tiket_aktif_list && mysqli_num_rows($tiket_aktif_list) > 0): ?>
                <?php while ($tk = mysqli_fetch_assoc($tiket_aktif_list)): ?>
                  <tr>
                    <td><code class="fw-bold fs-7"><?= htmlspecialchars($tk['kode_booking']); ?></code></td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <img src="<?= htmlspecialchars($tk['gambar_utama']); ?>" class="rounded-2" style="width: 45px; height: 35px; object-fit: cover;">
                        <div>
                          <strong class="d-block text-dark"><?= htmlspecialchars($tk['nama_destinasi']); ?></strong>
                          <small class="text-muted"><?= htmlspecialchars($tk['nama_provinsi']); ?></small>
                        </div>
                      </div>
                    </td>
                    <td><?= tanggal_indo($tk['tanggal_kunjungan']); ?></td>
                    <td><?= $tk['jumlah_tiket']; ?> Orang</td>
                    <td class="fw-bold text-success"><?= rupiah($tk['total_harga']); ?></td>
                    <td>
                      <?php 
                        $stat = $tk['status_pemesanan'];
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
                      <a href="tiket_saya.php#tiket<?= $tk['id']; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                        <i class="fas fa-eye me-1"></i> Detail
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">
                    Anda belum memiliki pesanan tiket aktif. <a href="pesan_tiket.php" class="text-primary fw-bold">Pesan tiket sekarang!</a>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- REKOMENDASI WISATA TERPOPULER -->
      <h5 class="fw-bold text-dark mb-3"><i class="fas fa-fire text-danger me-2"></i> Rekomendasi Wisata Favorit</h5>
      <div class="row g-3">
        <?php while ($rek = mysqli_fetch_assoc($rekomendasi)): ?>
          <div class="col-md-4 col-sm-6">
            <div class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden bg-white">
              <div class="position-relative" style="height: 160px;">
                <img src="<?= htmlspecialchars($rek['gambar_utama']); ?>" class="w-100 h-100 object-fit-cover" alt="<?= htmlspecialchars($rek['nama_destinasi']); ?>">
                <span class="badge bg-dark bg-opacity-75 text-white position-absolute top-0 start-0 m-2 rounded-pill px-2 py-1 fs-8">
                  <?= htmlspecialchars($rek['nama_provinsi']); ?>
                </span>
                <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 rounded-pill px-2 py-1 fs-8 fw-bold">
                  <i class="fas fa-star"></i> <?= $rek['rating']; ?>
                </span>
              </div>
              <div class="p-3 d-flex flex-column flex-grow-1">
                <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($rek['nama_destinasi']); ?></h6>
                <small class="text-muted mb-2"><i class="fas fa-location-dot text-danger me-1"></i> <?= htmlspecialchars($rek['lokasi_spesifik']); ?></small>
                <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top">
                  <span class="fw-bold text-success fs-7"><?= rupiah($rek['harga_tiket']); ?></span>
                  <a href="pesan_tiket.php?destinasi_id=<?= $rek['id']; ?>" class="btn btn-sm btn-luxury-primary rounded-pill px-3">
                    Pesan
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
