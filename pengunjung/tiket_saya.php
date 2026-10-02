<?php
require_once __DIR__ . '/../koneksi.php';
require_role('pengunjung');

$user = auth_user();
$uid = $user['id'];

// Ambil semua tiket milik user
$tiket_query = mysqli_query($conn, "SELECT p.*, d.nama_destinasi, d.lokasi_spesifik, d.gambar_utama, pr.nama_provinsi, k.nama_kategori 
                                    FROM pemesanan_tiket p 
                                    JOIN destinasi d ON p.destinasi_id = d.id 
                                    LEFT JOIN provinsi pr ON d.provinsi_id = pr.id 
                                    LEFT JOIN kategori_wisata k ON d.kategori_id = k.id 
                                    WHERE p.user_id = {$uid} 
                                    ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-Tiket Saya - Pesona Wisata Indonesia</title>
  
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
        <a href="index.php" class="sidebar-link">
          <i class="fas fa-compass"></i> Beranda Wisatawan
        </a>
      </li>
      <li class="sidebar-item">
        <a href="pesan_tiket.php" class="sidebar-link">
          <i class="fas fa-cart-plus"></i> Pesan Tiket Baru
        </a>
      </li>
      <li class="sidebar-item">
        <a href="tiket_saya.php" class="sidebar-link active">
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

  <!-- MAIN -->
  <main class="dashboard-main">
    <header class="dashboard-topbar">
      <div class="topbar-left">
        <button class="btn btn-light d-lg-none me-2" id="sidebarToggleBtn">
          <i class="fas fa-bars"></i>
        </button>
        <h2 class="topbar-title">E-Tiket Resmi & Bukti Reservasi</h2>
      </div>

      <div class="topbar-right d-flex gap-2 align-items-center">
        <a href="pesan_tiket.php" class="btn btn-luxury-primary btn-sm rounded-pill px-3">
          <i class="fas fa-plus me-1"></i> Pesan Tiket Lain
        </a>
        <a href="../logout.php?role=pengunjung" class="btn btn-outline-danger btn-sm rounded-pill px-3">
          <i class="fas fa-arrow-right-from-bracket me-1"></i> Logout Pengunjung
        </a>
      </div>
    </header>

    <div class="dashboard-container">
      <?php display_flash(); ?>

      <?php if ($tiket_query && mysqli_num_rows($tiket_query) > 0): ?>
        <div class="row g-4">
          <?php while ($t = mysqli_fetch_assoc($tiket_query)): ?>
            <div class="col-lg-6" id="tiket<?= $t['id']; ?>">
              <!-- E-TICKET LUXURY CARD -->
              <div class="eticket-container">
                <!-- TICKET HEADER -->
                <div class="eticket-header">
                  <div>
                    <span class="badge bg-warning text-dark font-monospace px-2 py-1 fs-8 fw-bold mb-1">
                      OFFICIAL BOARDING PASS
                    </span>
                    <h4 class="mb-0 font-serif text-white fw-bold">PESONA WISATA INDONESIA</h4>
                    <small class="text-white-50">Eksplorasi Keagungan Nusantara Indonesia</small>
                  </div>
                  <div class="text-end">
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
                  </div>
                </div>

                <!-- TICKET BODY -->
                <div class="eticket-body">
                  <div class="row align-items-center mb-3">
                    <div class="col-8">
                      <span class="text-muted fs-8 text-uppercase">Destinasi Wisata</span>
                      <h4 class="font-serif fw-bold text-dark mb-0"><?= htmlspecialchars($t['nama_destinasi']); ?></h4>
                      <small class="text-muted"><i class="fas fa-map-pin text-danger me-1"></i> <?= htmlspecialchars($t['lokasi_spesifik']); ?>, <?= htmlspecialchars($t['nama_provinsi']); ?></small>
                    </div>
                    <div class="col-4 text-end">
                      <img src="<?= htmlspecialchars($t['gambar_utama']); ?>" class="rounded-3 shadow-sm" style="width: 80px; height: 60px; object-fit: cover;">
                    </div>
                  </div>

                  <div class="row g-3 py-3 border-top border-bottom my-2 bg-light rounded-3 px-2">
                    <div class="col-6">
                      <small class="text-muted d-block fs-8">Nama Pemesan</small>
                      <strong class="text-dark fs-7"><?= htmlspecialchars($user['nama']); ?></strong>
                    </div>
                    <div class="col-6">
                      <small class="text-muted d-block fs-8">Tanggal Kunjungan</small>
                      <strong class="text-primary fs-7"><i class="far fa-calendar-check me-1"></i> <?= tanggal_indo($t['tanggal_kunjungan']); ?></strong>
                    </div>
                    <div class="col-6">
                      <small class="text-muted d-block fs-8">Jumlah Tiket Masuk</small>
                      <strong class="text-dark fs-7"><?= $t['jumlah_tiket']; ?> Orang</strong>
                    </div>
                    <div class="col-6">
                      <small class="text-muted d-block fs-8">Total Pembayaran</small>
                      <strong class="text-success fs-7"><?= rupiah($t['total_harga']); ?></strong>
                    </div>
                  </div>

                  <!-- PERFORATION DIVIDER -->
                  <div class="eticket-divider my-4"></div>

                  <!-- BARCODE & QR SIMULATION -->
                  <div class="row align-items-center">
                    <div class="col-sm-8 mb-2 mb-sm-0">
                      <small class="text-muted fs-8 d-block mb-1">KODE BOOKING VERIFIKASI</small>
                      <div class="eticket-barcode">
                        *<?= htmlspecialchars($t['kode_booking']); ?>*
                      </div>
                      <small class="text-muted fs-8 d-block mt-1 text-center">Tunjukkan kode ini kepada petugas gerbang</small>
                    </div>

                    <div class="col-sm-4 text-center">
                      <div class="p-2 border rounded-3 bg-white d-inline-block shadow-sm">
                        <svg width="65" height="65" viewBox="0 0 100 100" fill="#0f172a">
                          <rect x="0" y="0" width="30" height="30" />
                          <rect x="5" y="5" width="20" height="20" fill="#fff" />
                          <rect x="10" y="10" width="10" height="10" />
                          <rect x="70" y="0" width="30" height="30" />
                          <rect x="75" y="5" width="20" height="20" fill="#fff" />
                          <rect x="80" y="10" width="10" height="10" />
                          <rect x="0" y="70" width="30" height="30" />
                          <rect x="5" y="75" width="20" height="20" fill="#fff" />
                          <rect x="10" y="80" width="10" height="10" />
                          <rect x="40" y="40" width="20" height="20" />
                          <rect x="65" y="65" width="15" height="15" />
                          <rect x="40" y="70" width="10" height="20" />
                        </svg>
                      </div>
                    </div>
                  </div>

                  <!-- ACTIONS -->
                  <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between btn-print-hide">
                    <button onclick="window.print()" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                      <i class="fas fa-print me-1"></i> Cetak Tiket
                    </button>
                    <a href="ulasan.php?destinasi_id=<?= $t['destinasi_id']; ?>" class="btn btn-sm btn-luxury-gold rounded-pill px-3">
                      <i class="fas fa-star me-1"></i> Beri Ulasan
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <div class="card-luxury p-5 text-center">
          <i class="fas fa-ticket text-muted fs-1 mb-3"></i>
          <h4>Belum Ada E-Tiket</h4>
          <p class="text-muted mx-auto" style="max-width: 500px;">
            Anda belum melakukan pemesanan tiket wisata. Segera rencanakan liburan Anda ke 10 destinasi terbaik di Indonesia.
          </p>
          <div>
            <a href="pesan_tiket.php" class="btn btn-luxury-primary rounded-pill px-4">
              <i class="fas fa-cart-shopping me-1"></i> Pesan Tiket Sekarang
            </a>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
