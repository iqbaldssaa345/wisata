<?php
require_once __DIR__ . '/koneksi.php';

// Ambil data provinsi
$prov_query = mysqli_query($conn, "SELECT * FROM provinsi ORDER BY id ASC");
$provinsi_list = [];
if ($prov_query) {
    while ($p = mysqli_fetch_assoc($prov_query)) {
        $provinsi_list[] = $p;
    }
}

// Ambil data kategori wisata
$kat_query = mysqli_query($conn, "SELECT * FROM kategori_wisata ORDER BY id ASC");
$kategori_list = [];
if ($kat_query) {
    while ($k = mysqli_fetch_assoc($kat_query)) {
        $kategori_list[] = $k;
    }
}

// Ambil data 10 destinasi lengkap dengan provinsi dan kategori
$dest_query = mysqli_query($conn, "SELECT d.*, p.nama_provinsi, k.nama_kategori, k.icon as kat_icon 
                                   FROM destinasi d 
                                   LEFT JOIN provinsi p ON d.provinsi_id = p.id 
                                   LEFT JOIN kategori_wisata k ON d.kategori_id = k.id 
                                   ORDER BY d.id ASC");
$destinasi_list = [];
if ($dest_query) {
    while ($d = mysqli_fetch_assoc($dest_query)) {
        $destinasi_list[] = $d;
    }
}

// Ambil data fasilitas untuk masing-masing destinasi
$fasilitas_query = mysqli_query($conn, "SELECT * FROM destinasi_fasilitas ORDER BY id ASC");
$fasilitas_map = [];
if ($fasilitas_query) {
    while ($f = mysqli_fetch_assoc($fasilitas_query)) {
        $fasilitas_map[$f['destinasi_id']][] = $f;
    }
}

// Ambil ulasan terkini
$ulasan_query = mysqli_query($conn, "SELECT u.*, us.nama_lengkap as nama_user, d.nama_destinasi 
                                     FROM ulasan_wisata u 
                                     JOIN users us ON u.user_id = us.id 
                                     JOIN destinasi d ON u.destinasi_id = d.id 
                                     ORDER BY u.id DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title>Pesona Wisata Indonesia - Eksplorasi Keagungan & Keindahan Nusantara</title>
  
  <meta name="description" content="Portal resmi eksplorasi dan reservasi e-tiket 10 destinasi wisata mahakarya Indonesia. Nikmati kemudahan tiket digital terverifikasi dan tarif retribusi resmi.">
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏛️</text></svg>">
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome 6 Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Custom Luxury Stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- NAVBAR LUXURY -->
  <nav class="navbar navbar-expand-lg navbar-luxury">
    <div class="container">
      <a class="brand-badge" href="index.php">
        <div class="brand-icon">
          <i class="fas fa-mountain-sun"></i>
        </div>
        <div class="brand-text">
          <h1>PESONA WISATA INDONESIA</h1>
          <span>Wonderful Heritage & Nature</span>
        </div>
      </a>
      
      <button class="navbar-toggler border-0 shadow-none p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-label="Menu Navigasi">
        <i class="fas fa-bars-staggered fs-4" style="color: var(--primary);"></i>
      </button>

      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-1">
          <li class="nav-item">
            <a class="nav-link nav-link-custom active" href="index.php"><i class="fas fa-house me-1"></i> Beranda</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="#destinasiSection"><i class="fas fa-map-location-dot me-1"></i> 10 Wisata Ikonik</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="#kalkulatorSection"><i class="fas fa-calculator me-1"></i> Hitung Tiket</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="#keunggulanSection"><i class="fas fa-award me-1"></i> Keunggulan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="#ulasanSection"><i class="fas fa-comments me-1"></i> Ulasan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="#faqSection"><i class="fas fa-circle-question me-1"></i> FAQ</a>
          </li>
        </ul>

        <div class="d-flex align-items-center gap-2">
          <a href="login.php" class="btn btn-luxury-outline rounded-pill"><i class="fas fa-lock me-1"></i> Masuk</a>
          <a href="daftar.php" class="btn btn-luxury-gold rounded-pill"><i class="fas fa-user-plus me-1"></i> Daftar Akun</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- HERO SECTION LUXURY -->
  <header class="hero-wrapper">
    <div class="container position-relative" style="z-index: 5;">
      <div class="row align-items-center">
        <div class="col-lg-8 fade-in-up">
          <div class="hero-tag">
            <i class="fas fa-crown text-warning"></i> Wonderful Indonesia • Maestro Alam & Budaya Nusantara
          </div>
          <h1 class="hero-title">
            Pesona Wisata Indonesia <br><span class="shimmer-gold">Keajaiban Abadi Nusantara</span>
          </h1>
          <p class="hero-desc">
            Dari kemegahan mahakarya dunia Candi Borobudur abad ke-8, lautan pasir berbisik Gunung Bromo, api biru abadi Kawah Ijen, hingga pesona cagar budaya termahsyur nusantara. Rencanakan perjalanan berkelas Anda dengan e-tiket resmi, instan, dan terverifikasi di pintu masuk.
          </p>
          <div class="d-flex flex-wrap gap-3 mb-5">
            <a href="#destinasiSection" class="btn btn-luxury-gold btn-lg rounded-pill shadow-lg">
              <i class="fas fa-compass me-2"></i> Jelajahi 10 Wisata Ikonik
            </a>
            <a href="daftar.php" class="btn btn-outline-light btn-lg rounded-pill">
              <i class="fas fa-ticket me-2"></i> Pesan E-Tiket Online
            </a>
          </div>

          <!-- 4 METRIC STATS IN HERO -->
          <div class="row g-3">
            <div class="col-6 col-md-3">
              <div class="hero-stat-card">
                <div class="hero-stat-icon">
                  <i class="fas fa-map-marked-alt"></i>
                </div>
                <div>
                  <h4 class="mb-0 fw-bold"><?= count($provinsi_list); ?> Wilayah</h4>
                  <small class="text-white-50 fs-8">Provinsi Pulau Jawa</small>
                </div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="hero-stat-card">
                <div class="hero-stat-icon">
                  <i class="fas fa-landmark"></i>
                </div>
                <div>
                  <h4 class="mb-0 fw-bold"><?= count($destinasi_list); ?> Wisata</h4>
                  <small class="text-white-50 fs-8">Ikonik Terbaik</small>
                </div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="hero-stat-card">
                <div class="hero-stat-icon">
                  <i class="fas fa-star"></i>
                </div>
                <div>
                  <h4 class="mb-0 fw-bold">4.95 / 5.0</h4>
                  <small class="text-white-50 fs-8">Rating Wisatawan</small>
                </div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="hero-stat-card">
                <div class="hero-stat-icon">
                  <i class="fas fa-qrcode"></i>
                </div>
                <div>
                  <h4 class="mb-0 fw-bold">100%</h4>
                  <small class="text-white-50 fs-8">E-Tiket Resmi</small>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </header>

  <!-- PROVINCE SHOWCASE HORIZONTAL STRIP -->
  <div class="container province-showcase-wrapper">
    <div class="d-flex align-items-center justify-content-between mb-2 px-1">
      <small class="fw-bold text-uppercase text-muted fs-8 letter-spacing-1">
        <i class="fas fa-earth-asia text-warning me-1"></i> Jelajahi Wilayah Provinsi (Ketuk untuk filter):
      </small>
    </div>
    <div class="province-strip">
      <?php foreach ($provinsi_list as $prov): ?>
        <a href="#" class="province-card-luxury" data-province="<?= htmlspecialchars($prov['nama_provinsi']); ?>" title="Lihat wisata di <?= htmlspecialchars($prov['nama_provinsi']); ?>">
          <img src="<?= htmlspecialchars($prov['foto_banner'] ?: 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=500'); ?>" alt="<?= htmlspecialchars($prov['nama_provinsi']); ?>" loading="lazy">
          <div class="province-card-overlay">
            <span class="province-card-sub"><i class="fas fa-map-pin me-1"></i> <?= htmlspecialchars($prov['ibu_kota']); ?></span>
            <h5 class="province-card-title"><?= htmlspecialchars($prov['nama_provinsi']); ?></h5>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SEARCH & FILTER FLOATING ISLAND -->
  <div class="container">
    <div class="search-box-luxury">
      <div class="row g-3 align-items-center">
        <!-- Search Input -->
        <div class="col-lg-5 col-md-12">
          <div class="form-luxury-group">
            <i class="fas fa-magnifying-glass"></i>
            <input type="text" id="searchWisata" class="form-luxury-input" placeholder="Cari destinasi wisata (contoh: Borobudur, Bromo, Prambanan)...">
            <button type="button" id="searchClearBtn" class="search-clear-btn" title="Hapus pencarian">
              <i class="fas fa-xmark"></i>
            </button>
          </div>
        </div>
        
        <!-- Filter Provinsi -->
        <div class="col-lg-4 col-md-6">
          <div class="form-luxury-group">
            <i class="fas fa-location-dot"></i>
            <select id="filterProvinsi" class="form-luxury-input">
              <option value="all">Semua Wilayah Provinsi</option>
              <?php foreach ($provinsi_list as $prov): ?>
                <option value="<?= htmlspecialchars($prov['nama_provinsi']); ?>"><?= htmlspecialchars($prov['nama_provinsi']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Sort Selector -->
        <div class="col-lg-3 col-md-6">
          <div class="form-luxury-group">
            <i class="fas fa-arrow-down-wide-short"></i>
            <select id="sortWisata" class="form-luxury-input">
              <option value="populer">Urutkan: Terpopuler</option>
              <option value="rating">Rating Tertinggi</option>
              <option value="harga_asc">Harga: Rendah ke Tinggi</option>
              <option value="harga_desc">Harga: Tinggi ke Rendah</option>
              <option value="nama_asc">Nama: A ke Z</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Kategori Filter Pills -->
      <div class="filter-pills">
        <a href="#" class="pill-btn active" data-category="all">
          <i class="fas fa-layer-group"></i> Semua Kategori
        </a>
        <?php foreach ($kategori_list as $kat): ?>
          <a href="#" class="pill-btn" data-category="<?= htmlspecialchars($kat['nama_kategori']); ?>">
            <i class="fas fa-<?= htmlspecialchars($kat['icon']); ?>"></i> <?= htmlspecialchars($kat['nama_kategori']); ?>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- Search Results Counter & Reset Button -->
      <div class="search-results-bar">
        <div>
          Menampilkan <span class="badge-count" id="visibleCount"><?= count($destinasi_list); ?></span> dari <strong id="totalCount"><?= count($destinasi_list); ?></strong> Mahakarya Wisata Nusantara
        </div>
        <button type="button" id="resetFilterBtn" class="btn-reset-filter">
          <i class="fas fa-rotate-left me-1"></i> Reset Semua Filter
        </button>
      </div>
    </div>
  </div>

  <!-- DESTINASI GRID SECTION -->
  <section id="destinasiSection" class="py-5 mt-2">
    <div class="container">
      <div class="text-center mb-5">
        <span class="section-tag">
          <i class="fas fa-sparkles me-1 text-warning"></i> Koleksi Destinasi Terpopuler Nusantara
        </span>
        <h2 class="section-title">10 Pesona Wisata Mahakarya Indonesia</h2>
        <p class="text-muted mx-auto" style="max-width: 680px;">
          Jelajahi keagungan candi peninggalan leluhur, kaldera gunung api spektakuler, hingga cagar budaya nusantara dengan jaminan tarif retribusi resmi dan fasilitas terlengkap.
        </p>
      </div>

      <!-- Alert if no search results -->
      <div id="noResultsAlert" class="alert alert-warning text-center d-none rounded-4 p-5 shadow-sm">
        <i class="fas fa-circle-question fs-1 mb-3 text-warning"></i>
        <h4>Destinasi Wisata Tidak Ditemukan</h4>
        <p class="mb-3 text-muted">Coba gunakan kata kunci pencarian lain atau klik tombol reset filter di atas.</p>
        <button type="button" class="btn btn-luxury-primary rounded-pill px-4" onclick="document.getElementById('resetFilterBtn').click()">
          <i class="fas fa-rotate-left me-1"></i> Tampilkan Semua Wisata
        </button>
      </div>

      <div class="row g-4" id="destinasiGrid">
        <?php foreach ($destinasi_list as $dest): ?>
          <div class="col-lg-4 col-md-6 destinasi-item-col" 
               data-title="<?= strtolower(htmlspecialchars($dest['nama_destinasi'])); ?>" 
               data-province="<?= htmlspecialchars($dest['nama_provinsi']); ?>" 
               data-category="<?= htmlspecialchars($dest['nama_kategori']); ?>"
               data-location="<?= strtolower(htmlspecialchars($dest['lokasi_spesifik'])); ?>"
               data-price="<?= $dest['harga_tiket']; ?>"
               data-rating="<?= $dest['rating']; ?>">
            <div class="destinasi-card">
              <div class="card-img-wrapper">
                <img src="<?= htmlspecialchars($dest['gambar_utama']); ?>" alt="<?= htmlspecialchars($dest['nama_destinasi']); ?>" loading="lazy">
                
                <!-- Badge Provinsi -->
                <div class="badge-province">
                  <i class="fas fa-map-pin me-1"></i> <?= htmlspecialchars($dest['nama_provinsi']); ?>
                </div>

                <!-- Wishlist Heart Button -->
                <button type="button" class="card-fav-btn" data-id="<?= $dest['id']; ?>" data-title="<?= htmlspecialchars($dest['nama_destinasi']); ?>" title="Simpan ke Favorit">
                  <i class="far fa-heart"></i>
                </button>

                <!-- Rating Badge -->
                <div class="badge-rating">
                  <i class="fas fa-star text-warning"></i> <?= htmlspecialchars($dest['rating']); ?>
                </div>

                <!-- Operating Status Badge -->
                <div class="badge-status-open">
                  <i class="fas fa-circle-check text-success"></i> Buka <?= htmlspecialchars($dest['jam_buka']); ?> - <?= htmlspecialchars($dest['jam_tutup']); ?>
                </div>
              </div>

              <div class="card-content">
                <div>
                  <span class="badge-kategori-chip">
                    <i class="fas fa-<?= htmlspecialchars($dest['kat_icon'] ?: 'landmark'); ?>"></i> <?= htmlspecialchars($dest['nama_kategori']); ?>
                  </span>
                </div>
                <h3 class="destinasi-title"><?= htmlspecialchars($dest['nama_destinasi']); ?></h3>
                <div class="destinasi-location">
                  <i class="fas fa-location-dot text-danger"></i> <?= htmlspecialchars($dest['lokasi_spesifik']); ?>
                </div>
                <p class="destinasi-desc">
                  <?= htmlspecialchars($dest['ringkasan'] ?: $dest['deskripsi_lengkap']); ?>
                </p>
                
                <div class="card-footer-luxury">
                  <div class="price-tag">
                    <span>Tarif Retribusi Resmi</span>
                    <h4><?= rupiah($dest['harga_tiket']); ?></h4>
                  </div>
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalDestinasi<?= $dest['id']; ?>" title="Lihat detail lengkap">
                      <i class="fas fa-circle-info"></i> Detail
                    </button>
                    <a href="login.php" class="btn-luxury-primary btn-sm rounded-pill px-3" title="Login untuk memesan tiket">
                      <i class="fas fa-ticket"></i> Pesan
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- MODAL DETAIL DESTINASI LUXURY -->
          <div class="modal fade" id="modalDestinasi<?= $dest['id']; ?>" tabindex="-1" aria-labelledby="modalLabel<?= $dest['id']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content border-0 rounded-4 shadow-2xl overflow-hidden">
                <div class="position-relative">
                  <img src="<?= htmlspecialchars($dest['gambar_utama']); ?>" class="w-100" style="height: 360px; object-fit: cover;" alt="<?= htmlspecialchars($dest['nama_destinasi']); ?>">
                  <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 p-2 bg-dark rounded-circle opacity-75" data-bs-dismiss="modal" aria-label="Tutup"></button>
                  <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(transparent, rgba(2, 44, 34, 0.96)); color:#fff;">
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1 text-uppercase fw-bold mb-2 font-monospace">
                      <i class="fas fa-<?= htmlspecialchars($dest['kat_icon'] ?: 'landmark'); ?> me-1"></i> <?= htmlspecialchars($dest['nama_kategori']); ?>
                    </span>
                    <h3 class="modal-title font-serif text-white fw-bold mb-1" id="modalLabel<?= $dest['id']; ?>"><?= htmlspecialchars($dest['nama_destinasi']); ?></h3>
                    <p class="mb-0 text-white-75"><i class="fas fa-location-dot text-danger me-1"></i> <?= htmlspecialchars($dest['lokasi_spesifik']); ?> (<?= htmlspecialchars($dest['nama_provinsi']); ?>)</p>
                  </div>
                </div>
                
                <div class="modal-body p-4">
                  <!-- 4 Metrics in Modal -->
                  <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                      <div class="p-3 bg-light rounded-4 text-center border h-100">
                        <small class="text-muted text-uppercase d-block fw-bold fs-8">Tarif Resmi</small>
                        <h4 class="text-success fw-bold mt-1 mb-0"><?= rupiah($dest['harga_tiket']); ?></h4>
                      </div>
                    </div>
                    <div class="col-6 col-md-3">
                      <div class="p-3 bg-light rounded-4 text-center border h-100">
                        <small class="text-muted text-uppercase d-block fw-bold fs-8">Operasional</small>
                        <h6 class="fw-bold mt-2 mb-0 text-dark"><i class="far fa-clock text-warning me-1"></i> <?= htmlspecialchars($dest['jam_buka']); ?> - <?= htmlspecialchars($dest['jam_tutup']); ?></h6>
                      </div>
                    </div>
                    <div class="col-6 col-md-3">
                      <div class="p-3 bg-light rounded-4 text-center border h-100">
                        <small class="text-muted text-uppercase d-block fw-bold fs-8">Rating Wisatawan</small>
                        <h4 class="text-warning fw-bold mt-1 mb-0"><i class="fas fa-star me-1"></i> <?= htmlspecialchars($dest['rating']); ?> / 5.0</h4>
                      </div>
                    </div>
                    <div class="col-6 col-md-3">
                      <div class="p-3 bg-light rounded-4 text-center border h-100">
                        <small class="text-muted text-uppercase d-block fw-bold fs-8">Kuota Harian</small>
                        <h5 class="text-primary fw-bold mt-2 mb-0"><i class="fas fa-users me-1"></i> <?= number_format($dest['kuota_harian'] ?? 500, 0, ',', '.'); ?></h5>
                      </div>
                    </div>
                  </div>

                  <!-- Description -->
                  <h6 class="fw-bold text-dark mb-2"><i class="fas fa-align-left text-success me-2"></i> Deskripsi & Keistimewaan Wisata</h6>
                  <p class="text-muted lh-base mb-4"><?= nl2br(htmlspecialchars($dest['deskripsi_lengkap'])); ?></p>

                  <!-- Fasilitas Pendukung -->
                  <h6 class="fw-bold text-dark mb-2"><i class="fas fa-sparkles text-warning me-2"></i> Fasilitas & Sarana Prasarana</h6>
                  <div class="d-flex flex-wrap gap-2 mb-2">
                    <?php if (!empty($fasilitas_map[$dest['id']])): ?>
                      <?php foreach ($fasilitas_map[$dest['id']] as $fas): ?>
                        <span class="facility-pill">
                          <i class="fas <?= htmlspecialchars($fas['icon'] ?: 'fa-check-circle'); ?>"></i> <?= htmlspecialchars($fas['nama_fasilitas']); ?>
                        </span>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <span class="facility-pill"><i class="fas fa-info-circle"></i> Pusat Informasi & Loket Resmi</span>
                      <span class="facility-pill"><i class="fas fa-parking"></i> Area Parkir Luas</span>
                      <span class="facility-pill"><i class="fas fa-restroom"></i> Toilet Bersih & Mushola</span>
                      <span class="facility-pill"><i class="fas fa-shield-alt"></i> Petugas Keamanan Lapangan</span>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="modal-footer bg-light p-3 d-flex justify-content-between">
                  <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                  <a href="login.php" class="btn btn-luxury-primary rounded-pill px-4">
                    <i class="fas fa-arrow-right-to-bracket me-1"></i> Masuk Untuk Memesan
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- INTERACTIVE KALKULATOR TIKET BANNER -->
  <section id="kalkulatorSection" class="py-4">
    <div class="container">
      <div class="quick-calc-banner">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold text-uppercase mb-2 font-monospace">
              <i class="fas fa-calculator me-1"></i> Simulasi Cepat Biaya
            </span>
            <h2 class="font-serif fw-bold text-white mb-2">Hitung Biaya Tiket Wisata & Pesan Instan</h2>
            <p class="text-white-50 mb-3" style="max-width: 500px;">
              Pilih destinasi tujuan, tentukan jumlah rombongan, dan dapatkan rincian tarif resminya tanpa antrean dan tanpa biaya tersembunyi.
            </p>
            <div class="d-flex flex-wrap gap-3 text-white-75 fs-7">
              <span><i class="fas fa-check-circle text-warning me-1"></i> E-Tiket Resmi Barcode</span>
              <span><i class="fas fa-check-circle text-warning me-1"></i> Bebas Antre Loket</span>
              <span><i class="fas fa-check-circle text-warning me-1"></i> Garansi Akses Masuk</span>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="p-4 bg-white text-dark rounded-4 shadow-lg border">
              <div class="row g-3">
                <!-- Dropdown Destinasi -->
                <div class="col-12">
                  <label class="form-label fw-bold fs-7 mb-1"><i class="fas fa-landmark text-success me-1"></i> Pilih Destinasi Wisata</label>
                  <select id="calcDestinasi" class="form-select py-2 fs-7">
                    <?php foreach ($destinasi_list as $cd): ?>
                      <option value="<?= $cd['id']; ?>" data-price="<?= $cd['harga_tiket']; ?>">
                        <?= htmlspecialchars($cd['nama_destinasi']); ?> — <?= rupiah($cd['harga_tiket']); ?> / orang
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <!-- Stepper Jumlah Tiket -->
                <div class="col-12">
                  <label class="form-label fw-bold fs-7 mb-1 d-flex justify-content-between">
                    <span><i class="fas fa-users text-primary me-1"></i> Jumlah Orang / Tiket</span>
                    <span class="text-muted fs-8">Tarif Satuan: <strong class="text-dark" id="calcUnitPricePreview">Rp 50.000</strong></span>
                  </label>
                  <div class="calc-stepper mb-2">
                    <button type="button" class="calc-stepper-btn" id="calcMinusBtn" title="Kurangi">
                      <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" id="calcJumlah" class="calc-stepper-input" value="2" min="1" max="100">
                    <button type="button" class="calc-stepper-btn" id="calcPlusBtn" title="Tambah">
                      <i class="fas fa-plus"></i>
                    </button>
                  </div>
                  
                  <!-- Quick Preset Chips -->
                  <div class="d-flex flex-wrap gap-2">
                    <span class="calc-preset-chip" data-qty="1">1 Orang</span>
                    <span class="calc-preset-chip active" data-qty="2">2 Orang</span>
                    <span class="calc-preset-chip" data-qty="4">4 Orang (Keluarga)</span>
                    <span class="calc-preset-chip" data-qty="10">10 Orang (Rombongan)</span>
                  </div>
                </div>

                <!-- Calculation Summary & Action Button -->
                <div class="col-12 mt-3">
                  <div class="p-3 bg-light rounded-3 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 border">
                    <div>
                      <small class="text-muted d-block fs-8">Perkiraan Total Biaya:</small>
                      <h4 class="text-success fw-bold mb-0" id="calcTotalPreview">Rp 100.000</h4>
                    </div>
                    <a href="login.php" id="calcBookingBtn" class="btn btn-luxury-primary rounded-pill px-4">
                      Masuk & Pesan <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!-- KEUNGGULAN SECTION -->
  <section id="keunggulanSection" class="py-5 bg-white border-top border-bottom">
    <div class="container py-4">
      <div class="text-center mb-5">
        <span class="section-tag">
          <i class="fas fa-shield-halved me-1 text-warning"></i> Standar Layanan Unggulan
        </span>
        <h2 class="section-title">Kenapa Memilih Pesona Wisata Indonesia?</h2>
        <p class="text-muted mx-auto" style="max-width: 620px;">
          Platform reservasi tiket wisata resmi yang memadukan keaslian warisan nusantara dengan kemudahan e-tiket digital berstandar modern.
        </p>
      </div>

      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="feature-badge-box">
            <div class="feature-icon-circle">
              <i class="fas fa-bolt"></i>
            </div>
            <h5 class="fw-bold mb-2">Instan & Tanpa Antre</h5>
            <p class="text-muted fs-7 mb-0">Pesan tiket online kapan pun dari mana saja tanpa perlu mengantre panjang di loket masuk wisata.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="feature-badge-box">
            <div class="feature-icon-circle">
              <i class="fas fa-qrcode"></i>
            </div>
            <h5 class="fw-bold mb-2">E-Tiket & Kode QR</h5>
            <p class="text-muted fs-7 mb-0">Tiket elektronik resmi dengan Barcode/QR unik yang siap diverifikasi secara langsung oleh petugas di lokasi.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="feature-badge-box">
            <div class="feature-icon-circle">
              <i class="fas fa-shield-halved"></i>
            </div>
            <h5 class="fw-bold mb-2">Tarif Asli & Resmi</h5>
            <p class="text-muted fs-7 mb-0">Transparansi harga sesuai retribusi resmi pengelola cagar budaya dan balai taman nasional tanpa pungli.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="feature-badge-box">
            <div class="feature-icon-circle">
              <i class="fas fa-headset"></i>
            </div>
            <h5 class="fw-bold mb-2">Dukungan Pengelola 24/7</h5>
            <p class="text-muted fs-7 mb-0">Tim pengelola dan petugas lapangan siap sedia membantu proses konfirmasi dan panduan wisata Anda.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ULASAN WISATAWAN SECTION -->
  <section id="ulasanSection" class="py-5" style="background-color: var(--bg-soft);">
    <div class="container py-4">
      <div class="text-center mb-5">
        <span class="section-tag">
          <i class="fas fa-comments me-1 text-warning"></i> Cerita Wisatawan Nusantara
        </span>
        <h2 class="section-title">Apa Kata Pengunjung Pesona Wisata Indonesia?</h2>
        <p class="text-muted">Pengalaman autentik dan kisah tak terlupakan ribuan pelancong di destinasi terbaik nusantara</p>
      </div>

      <div class="row g-4">
        <?php if ($ulasan_query && mysqli_num_rows($ulasan_query) > 0): ?>
          <?php while ($ul = mysqli_fetch_assoc($ulasan_query)): ?>
            <div class="col-md-4">
              <div class="card h-100 border-0 rounded-4 p-4 shadow-sm bg-white">
                <div class="d-flex align-items-center mb-3">
                  <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-weight: bold; font-size: 1.1rem; background-color: var(--primary-soft); color: var(--primary);">
                    <?= strtoupper(substr($ul['nama_user'], 0, 1)); ?>
                  </div>
                  <div>
                    <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($ul['nama_user']); ?></h6>
                    <small class="text-muted"><i class="fas fa-location-arrow text-success"></i> <?= htmlspecialchars($ul['nama_destinasi']); ?></small>
                  </div>
                </div>
                <div class="mb-3 text-warning">
                  <?php for($i=1; $i<=5; $i++): ?>
                    <i class="fas fa-star <?= ($i <= $ul['rating']) ? '' : 'text-muted opacity-25'; ?>"></i>
                  <?php endfor; ?>
                </div>
                <p class="text-muted fst-italic fs-7 flex-grow-1">"<?= htmlspecialchars($ul['komentar']); ?>"</p>
                <div class="text-end border-top pt-2">
                  <small class="text-muted fs-8"><i class="far fa-calendar-check me-1"></i> <?= tanggal_indo($ul['created_at']); ?></small>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- FAQ SECTION ACCORDION -->
  <section id="faqSection" class="py-5 bg-white border-top">
    <div class="container py-4">
      <div class="text-center mb-5">
        <span class="section-tag">
          <i class="fas fa-circle-question me-1 text-warning"></i> Pusat Bantuan & Panduan
        </span>
        <h2 class="section-title">Pertanyaan yang Sering Diajukan (FAQ)</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">
          Semua informasi praktis dan panduan resmi yang Anda perlukan sebelum berkunjung ke destinasi pilihan di Indonesia.
        </p>
      </div>

      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="accordion accordion-flush" id="faqAccordion">
            <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
              <h2 class="accordion-header">
                <button class="accordion-button fw-bold text-dark collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                  <i class="fas fa-ticket text-success me-2"></i> Bagaimana cara mendapatkan E-Tiket setelah pemesanan?
                </button>
              </h2>
              <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted fs-7">
                  Setelah Anda memilih destinasi dan melakukan konfirmasi pemesanan, tiket elektronik (E-Tiket) resmi yang dilengkapi kode unik dan barcode akan otomatis terbit di menu <strong>E-Tiket Saya</strong>. Anda bisa mencetaknya atau cukup menunjukkan barcode dari smartphone ke petugas loket di lokasi.
                </div>
              </div>
            </div>

            <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
              <h2 class="accordion-header">
                <button class="accordion-button fw-bold text-dark collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                  <i class="fas fa-qrcode text-warning me-2"></i> Bagaimana proses verifikasi tiket di gerbang masuk?
                </button>
              </h2>
              <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted fs-7">
                  Petugas loket kami yang bertugas di lokasi destinasi akan memindai atau mencocokkan kode booking e-tiket Anda secara real-time pada sistem portal petugas. Setelah diverifikasi, Anda dan rombongan langsung dipersilakan masuk tanpa harus antre lagi.
                </div>
              </div>
            </div>

            <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
              <h2 class="accordion-header">
                <button class="accordion-button fw-bold text-dark collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                  <i class="fas fa-users text-primary me-2"></i> Apakah tiket bisa dipesan untuk rombongan keluarga besar?
                </button>
              </h2>
              <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted fs-7">
                  Tentu saja! Anda dapat menentukan jumlah tiket hingga puluhan orang dalam satu kali transaksi pemesanan. Total biaya tiket akan terkalkulasi secara otomatis dan seluruh rombongan tercakup dalam satu kode tiket terpadu.
                </div>
              </div>
            </div>

            <div class="accordion-item border rounded-3 mb-3 overflow-hidden shadow-sm">
              <h2 class="accordion-header">
                <button class="accordion-button fw-bold text-dark collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                  <i class="fas fa-shield-halved text-success me-2"></i> Apakah harga tiket sudah termasuk retribusi resmi pengelola?
                </button>
              </h2>
              <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body text-muted fs-7">
                  Ya, seluruh harga yang tertera adalah tarif retribusi resmi yang ditetapkan oleh Balai Konservasi Cagar Budaya, Balai Taman Nasional, atau Dinas Pariwisata Daerah tanpa biaya tersembunyi.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER LUXURY -->
  <footer class="luxury-footer">
    <div class="container">
      <div class="row g-4 mb-5">
        <div class="col-lg-4 col-md-6">
          <div class="footer-brand-wrap">
            <div class="footer-brand-icon">
              <i class="fas fa-mountain-sun"></i>
            </div>
            <div>
              <h3 class="footer-brand-title">PESONA WISATA INDONESIA</h3>
              <span class="footer-brand-sub shimmer-gold">Mahakarya Abadi Nusantara</span>
            </div>
          </div>
          <p class="footer-desc-luxury">
            Portal resmi eksplorasi dan reservasi digital mahakarya wisata nusantara. Memadukan keaslian cagar budaya leluhur, pesona bentang alam tropis, serta sistem e-tiket berstandar modern yang aman dan terverifikasi.
          </p>
          <div class="footer-trust-chips">
            <span class="footer-chip"><i class="fas fa-crown text-warning"></i> Wonderful Indonesia</span>
            <span class="footer-chip"><i class="fas fa-shield-halved text-success"></i> E-Tiket Resmi</span>
            <span class="footer-chip"><i class="fas fa-star text-warning"></i> Rating 4.95 / 5.0</span>
          </div>
          <div class="d-flex gap-2 mb-3">
            <a href="#" class="social-icon-btn" aria-label="Instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-icon-btn" aria-label="Facebook" title="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-icon-btn" aria-label="YouTube" title="YouTube"><i class="fab fa-youtube"></i></a>
            <a href="#" class="social-icon-btn" aria-label="TikTok" title="TikTok"><i class="fab fa-tiktok"></i></a>
            <a href="https://wa.me/6281234567890" target="_blank" class="social-icon-btn" aria-label="WhatsApp" title="WhatsApp Bantuan 24 Jam"><i class="fab fa-whatsapp text-success"></i></a>
          </div>
          <div class="footer-seal-badge">
            <i class="fas fa-landmark-dome text-warning fs-6"></i>
            <span>Dibina oleh Dinas Pariwisata & Kemenparekraf RI</span>
          </div>
        </div>

        <div class="col-lg-2 col-md-6">
          <h6 class="text-white fw-bold mb-3">Wisata Unggulan</h6>
          <ul class="footer-links fs-7">
            <li><a href="#destinasiSection">Candi Borobudur</a></li>
            <li><a href="#destinasiSection">Gunung Bromo</a></li>
            <li><a href="#destinasiSection">Candi Prambanan</a></li>
            <li><a href="#destinasiSection">Kawah Ijen</a></li>
            <li><a href="#destinasiSection">Kawah Putih</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6">
          <h6 class="text-white fw-bold mb-3">Portal Pengguna</h6>
          <ul class="footer-links fs-7">
            <li><a href="login.php">Masuk Pengguna / Petugas</a></li>
            <li><a href="daftar.php">Pendaftaran Wisatawan Baru</a></li>
            <li><a href="admin/index.php">Akses Administrator</a></li>
            <li><a href="petugas/index.php">Verifikasi Tiket Petugas</a></li>
            <li><a href="pengunjung/index.php">Area Pengunjung</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6">
          <h6 class="text-white fw-bold mb-3">Hubungi Pengelola</h6>
          <ul class="footer-links fs-7">
            <li><i class="fas fa-map-marker-alt text-warning me-2"></i> Jl. Malioboro No. 1, Yogyakarta</li>
            <li><i class="fas fa-phone text-warning me-2"></i> (0274) 555-7890</li>
            <li><i class="fas fa-envelope text-warning me-2"></i> info@pesonawisataindonesia.id</li>
            <li><i class="fas fa-clock text-warning me-2"></i> Layanan 24 Jam Online</li>
          </ul>
        </div>
      </div>

      <hr class="border-secondary opacity-25">
      <div class="row align-items-center fs-7 text-muted">
        <div class="col-md-6 text-center text-md-start">
          &copy; <?= date('Y'); ?> Pesona Wisata Indonesia. Hak Cipta Dilindungi Undang-Undang.
        </div>
        <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
          Wisata Indonesia Berkelas Dunia • Wonderful Indonesia
        </div>
      </div>
    </div>
  </footer>

  <!-- Floating Back to Top Button -->
  <button type="button" class="btn-back-to-top" id="backToTopBtn" title="Kembali ke atas">
    <i class="fas fa-arrow-up"></i>
  </button>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JS -->
  <script src="assets/js/main.js"></script>
</body>
</html>
