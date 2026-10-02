<?php
require_once __DIR__ . '/../koneksi.php';
require_role('petugas');

$user = auth_user();
$petugas_id = $user['id'];

// Aksi konfirmasi pembayaran lunas
if (isset($_GET['konfirmasi_id'])) {
    $kid = (int)$_GET['konfirmasi_id'];
    mysqli_query($conn, "UPDATE pemesanan_tiket SET status_pemesanan = 'lunas' WHERE id = {$kid}");
    set_flash('success', 'Pembayaran tiket berhasil dikonfirmasi! E-Tiket berstatus LUNAS.');
    header('Location: index.php');
    exit;
}

// Aksi check-in pengunjung
if (isset($_GET['checkin_id'])) {
    $cid = (int)$_GET['checkin_id'];
    mysqli_query($conn, "UPDATE pemesanan_tiket SET status_pemesanan = 'selesai', status_checkin = 'sudah', waktu_checkin = NOW() WHERE id = {$cid}");
    mysqli_query($conn, "INSERT INTO checkin_petugas (pemesanan_id, petugas_id, waktu_checkin, catatan) 
                         VALUES ({$cid}, {$petugas_id}, NOW(), 'Check-in pintu gerbang')");
    set_flash('success', 'Check-in berhasil! Tiket telah divalidasi dan dicatat.');
    header('Location: index.php');
    exit;
}

// Pencarian cepat kode booking
$search_code = sanitize($conn, $_GET['search_code'] ?? '');
$search_result = null;
if (!empty($search_code)) {
    $q_search = mysqli_query($conn, "SELECT p.*, u.nama_lengkap as nama_pemesan, u.no_telp, d.nama_destinasi, d.lokasi_spesifik, pr.nama_provinsi 
                                     FROM pemesanan_tiket p 
                                     JOIN users u ON p.user_id = u.id 
                                     JOIN destinasi d ON p.destinasi_id = d.id 
                                     LEFT JOIN provinsi pr ON d.provinsi_id = pr.id 
                                     WHERE p.kode_booking = '{$search_code}' LIMIT 1");
    if ($q_search && mysqli_num_rows($q_search) > 0) {
        $search_result = mysqli_fetch_assoc($q_search);
    } else {
        $search_not_found = true;
    }
}

// Metrik Petugas
$tiket_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pemesanan_tiket WHERE status_pemesanan = 'menunggu_pembayaran'"))['t'] ?? 0;
$tiket_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pemesanan_tiket WHERE tanggal_kunjungan = CURDATE() AND status_pemesanan = 'lunas'"))['t'] ?? 0;
$tiket_selesai_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pemesanan_tiket WHERE status_checkin = 'sudah' AND DATE(waktu_checkin) = CURDATE()"))['t'] ?? 0;

// Daftar tiket terbaru
$antrian_tiket = mysqli_query($conn, "SELECT p.*, u.nama_lengkap as nama_pemesan, u.no_telp, d.nama_destinasi 
                                     FROM pemesanan_tiket p 
                                     JOIN users u ON p.user_id = u.id 
                                     JOIN destinasi d ON p.destinasi_id = d.id 
                                     ORDER BY FIELD(p.status_pemesanan, 'menunggu_pembayaran', 'lunas', 'selesai', 'dibatalkan'), p.id DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Petugas Loket & Validasi - Pesona Wisata Indonesia</title>
  
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
        <a href="index.php" class="sidebar-link active">
          <i class="fas fa-qrcode"></i> Validasi Tiket
        </a>
      </li>
      <li class="sidebar-item">
        <a href="daftar_tiket.php" class="sidebar-link">
          <i class="fas fa-list-check"></i> Semua Reservasi
        </a>
      </li>

      <li class="sidebar-heading mt-3">Navigasi Multi-Tab</li>
      <li class="sidebar-item">
        <a href="../index.php" target="_blank" class="sidebar-link">
          <i class="fas fa-external-link-alt"></i> Lihat Website Utama
        </a>
      </li>
      <?php if ($user['role'] === 'admin'): ?>
        <li class="sidebar-item">
          <a href="../admin/index.php" class="sidebar-link">
            <i class="fas fa-gauge-high"></i> Beralih ke Admin
          </a>
        </li>
      <?php endif; ?>
      <li class="sidebar-item">
        <a href="../pengunjung/index.php" class="sidebar-link">
          <i class="fas fa-compass"></i> Tab Pengunjung
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
        <h2 class="topbar-title">Verifikasi & Pemindaian Tiket Pengunjung</h2>
      </div>

      <div class="topbar-right">
        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-pill font-monospace">
          <i class="fas fa-id-badge me-1"></i> Petugas Lapangan
        </span>
        <a href="../logout.php?role=petugas" class="btn btn-outline-danger btn-sm rounded-pill px-3">
          <i class="fas fa-arrow-right-from-bracket me-1"></i> Logout Petugas
        </a>
      </div>
    </header>

    <div class="dashboard-container">
      <?php display_flash(); ?>

      <!-- SCAN / QUICK SEARCH BOX -->
      <div class="card-luxury mb-4 border-warning shadow-sm">
        <div class="card-luxury-header bg-warning-subtle">
          <h5 class="card-luxury-title text-dark">
            <i class="fas fa-barcode text-warning me-2"></i> Pindai / Masukkan Kode Booking E-Tiket
          </h5>
        </div>
        <div class="card-luxury-body">
          <form method="GET" action="index.php" class="row g-2 align-items-center">
            <div class="col-md-9">
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-magnifying-glass text-warning"></i></span>
                <input type="text" name="search_code" class="form-control border-start-0 py-2 fs-6 font-monospace" placeholder="Ketik kode tiket (Contoh: JW-20261001-001)" value="<?= htmlspecialchars($search_code); ?>" required>
              </div>
            </div>
            <div class="col-md-3">
              <button type="submit" class="btn btn-warning w-100 py-2 fw-bold text-dark rounded-pill">
                <i class="fas fa-search me-1"></i> Periksa Tiket
              </button>
            </div>
          </form>

          <?php if (isset($search_not_found)): ?>
            <div class="alert alert-danger mt-3 mb-0 rounded-3">
              <i class="fas fa-circle-xmark me-2"></i> Kode booking <strong><?= htmlspecialchars($search_code); ?></strong> tidak ditemukan. Silakan periksa kembali.
            </div>
          <?php endif; ?>

          <?php if ($search_result): ?>
            <!-- HASIL PENCARIAN TIKET -->
            <div class="mt-4 p-4 border rounded-4 bg-white shadow-sm">
              <div class="row align-items-center">
                <div class="col-md-8">
                  <?php 
                    $stat = $search_result['status_pemesanan'];
                    $b_class = 'pending';
                    if ($stat === 'lunas') $b_class = 'dikonfirmasi';
                    if ($stat === 'selesai') $b_class = 'selesai';
                    if ($stat === 'dibatalkan') $b_class = 'dibatalkan';
                  ?>
                  <span class="badge-status badge-status-<?= $b_class; ?> mb-2">
                    STATUS TIKET: <?= strtoupper($stat); ?>
                  </span>
                  <h4 class="font-serif fw-bold text-dark mb-1"><?= htmlspecialchars($search_result['nama_destinasi']); ?></h4>
                  <p class="text-muted mb-2"><i class="fas fa-location-dot text-danger me-1"></i> <?= htmlspecialchars($search_result['lokasi_spesifik']); ?>, <?= htmlspecialchars($search_result['nama_provinsi']); ?></p>
                  
                  <div class="row g-2 mt-2">
                    <div class="col-6 col-sm-4">
                      <small class="text-muted d-block">Nama Wisatawan</small>
                      <strong><?= htmlspecialchars($search_result['nama_pemesan']); ?></strong>
                    </div>
                    <div class="col-6 col-sm-4">
                      <small class="text-muted d-block">Tanggal Kunjungan</small>
                      <strong class="text-primary"><?= tanggal_indo($search_result['tanggal_kunjungan']); ?></strong>
                    </div>
                    <div class="col-6 col-sm-4">
                      <small class="text-muted d-block">Jumlah Orang</small>
                      <strong class="badge bg-dark rounded-pill px-3 py-1"><?= $search_result['jumlah_tiket']; ?> Orang</strong>
                    </div>
                  </div>
                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                  <div class="mb-2">
                    <span class="text-muted fs-8 d-block">Total Tarif</span>
                    <h3 class="text-success fw-bold mb-0"><?= rupiah($search_result['total_harga']); ?></h3>
                  </div>

                  <div class="d-flex flex-column gap-2">
                    <?php if ($search_result['status_pemesanan'] === 'menunggu_pembayaran'): ?>
                      <a href="index.php?konfirmasi_id=<?= $search_result['id']; ?>" class="btn btn-primary rounded-pill btn-sm">
                        <i class="fas fa-check-circle me-1"></i> Konfirmasi Lunas
                      </a>
                    <?php elseif ($search_result['status_pemesanan'] === 'lunas'): ?>
                      <a href="index.php?checkin_id=<?= $search_result['id']; ?>" class="btn btn-success rounded-pill btn-sm">
                        <i class="fas fa-qrcode me-1"></i> Check-in Pengunjung Sekarang
                      </a>
                    <?php elseif ($search_result['status_pemesanan'] === 'selesai'): ?>
                      <span class="badge bg-success py-2 rounded-pill"><i class="fas fa-check-double me-1"></i> Tiket Sudah Check-in</span>
                    <?php else: ?>
                      <span class="badge bg-danger py-2 rounded-pill"><i class="fas fa-ban me-1"></i> Tiket Dibatalkan</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- METRIC CARDS PETUGAS -->
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="metric-card">
            <div class="metric-info">
              <h6>Menunggu Pembayaran</h6>
              <h3 class="text-warning"><?= $tiket_pending; ?> Tiket</h3>
              <small class="text-muted">Perlu verifikasi kasir</small>
            </div>
            <div class="metric-icon-box bg-amber-light">
              <i class="fas fa-clock"></i>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="metric-card">
            <div class="metric-info">
              <h6>Siap Kunjungan Hari Ini</h6>
              <h3 class="text-primary"><?= $tiket_hari_ini; ?> Tiket</h3>
              <small class="text-muted">Status lunas belum check-in</small>
            </div>
            <div class="metric-icon-box bg-blue-light">
              <i class="fas fa-calendar-day"></i>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="metric-card">
            <div class="metric-info">
              <h6>Sudah Masuk Hari Ini</h6>
              <h3 class="text-success"><?= $tiket_selesai_hari_ini; ?> Pengunjung</h3>
              <small class="text-muted">Telah check-in di gerbang</small>
            </div>
            <div class="metric-icon-box bg-emerald-light">
              <i class="fas fa-check-double"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- ANTRIAN TIKET TERBARU -->
      <div class="card-luxury">
        <div class="card-luxury-header">
          <h5 class="card-luxury-title">
            <i class="fas fa-clipboard-list text-primary"></i> Antrian Tiket & Riwayat Pemeriksaan
          </h5>
          <a href="daftar_tiket.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Lihat Semua</a>
        </div>
        <div class="table-responsive">
          <table class="table table-luxury">
            <thead>
              <tr>
                <th>Kode Booking</th>
                <th>Pemesan</th>
                <th>Destinasi Wisata</th>
                <th>Tgl Kunjungan</th>
                <th>Jml</th>
                <th>Status</th>
                <th class="text-center" width="160">Aksi Cepat</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($antrian_tiket && mysqli_num_rows($antrian_tiket) > 0): ?>
                <?php while ($a = mysqli_fetch_assoc($antrian_tiket)): ?>
                  <tr>
                    <td>
                      <code class="fw-bold fs-7"><?= htmlspecialchars($a['kode_booking']); ?></code>
                    </td>
                    <td>
                      <strong><?= htmlspecialchars($a['nama_pemesan']); ?></strong>
                      <small class="d-block text-muted"><?= htmlspecialchars($a['no_telp'] ?: '-'); ?></small>
                    </td>
                    <td><?= htmlspecialchars($a['nama_destinasi']); ?></td>
                    <td><?= tanggal_indo($a['tanggal_kunjungan']); ?></td>
                    <td><?= $a['jumlah_tiket']; ?> org</td>
                    <td>
                      <?php 
                        $stat = $a['status_pemesanan'];
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
                      <?php if ($a['status_pemesanan'] === 'menunggu_pembayaran'): ?>
                        <a href="index.php?konfirmasi_id=<?= $a['id']; ?>" class="btn btn-sm btn-primary rounded-pill px-2" title="Konfirmasi Lunas">
                          <i class="fas fa-check me-1"></i> Konfirmasi
                        </a>
                      <?php elseif ($a['status_pemesanan'] === 'lunas'): ?>
                        <a href="index.php?checkin_id=<?= $a['id']; ?>" class="btn btn-sm btn-success rounded-pill px-2" title="Check-in Masuk">
                          <i class="fas fa-qrcode me-1"></i> Check-in
                        </a>
                      <?php else: ?>
                        <span class="text-muted fs-8"><i class="fas fa-circle-check text-success"></i> Selesai</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">Belum ada pesanan tiket.</td>
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
