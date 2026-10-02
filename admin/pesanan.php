<?php
require_once __DIR__ . '/../koneksi.php';
require_role('admin');

$user = auth_user();

// Handle aksi update status
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action_id = (int)$_GET['id'];
    $status_new = sanitize($conn, $_GET['action']);
    $allowed_status = ['menunggu_pembayaran', 'lunas', 'selesai', 'dibatalkan'];

    if (in_array($status_new, $allowed_status)) {
        mysqli_query($conn, "UPDATE pemesanan_tiket SET status_pemesanan = '{$status_new}' WHERE id = {$action_id}");
        set_flash('success', 'Status pemesanan berhasil diubah menjadi ' . strtoupper($status_new));
        header('Location: pesanan.php');
        exit;
    }
}

// Filter status jika ada
$filter_status = sanitize($conn, $_GET['status'] ?? 'all');
$where_clause = "";
if ($filter_status !== 'all' && in_array($filter_status, ['menunggu_pembayaran', 'lunas', 'selesai', 'dibatalkan'])) {
    $where_clause = "WHERE p.status_pemesanan = '{$filter_status}'";
}

$pesanan_query = mysqli_query($conn, "SELECT p.*, u.nama_lengkap as nama_pemesan, u.email, u.no_telp, d.nama_destinasi, pr.nama_provinsi 
                                      FROM pemesanan_tiket p 
                                      JOIN users u ON p.user_id = u.id 
                                      JOIN destinasi d ON p.destinasi_id = d.id 
                                      LEFT JOIN provinsi pr ON d.provinsi_id = pr.id 
                                      {$where_clause}
                                      ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Semua Pesanan Tiket - Pesona Wisata Indonesia</title>
  
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
        <a href="pesanan.php" class="sidebar-link active">
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
        <h2 class="topbar-title">Manajemen Transaksi & Reservasi Tiket</h2>
      </div>

      <div class="topbar-right">
        <a href="laporan.php" class="btn btn-light border btn-sm rounded-pill px-3">
          <i class="fas fa-print me-1"></i> Cetak Laporan
        </a>
        <a href="../logout.php?role=admin" class="btn btn-outline-danger btn-sm rounded-pill px-3">
          <i class="fas fa-arrow-right-from-bracket me-1"></i> Logout Admin
        </a>
      </div>
    </header>

    <div class="dashboard-container">
      <?php display_flash(); ?>

      <!-- Filter Buttons -->
      <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="pesanan.php?status=all" class="btn btn-sm <?= ($filter_status === 'all') ? 'btn-dark' : 'btn-outline-secondary'; ?> rounded-pill px-3">
          Semua Pesanan
        </a>
        <a href="pesanan.php?status=menunggu_pembayaran" class="btn btn-sm <?= ($filter_status === 'menunggu_pembayaran') ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'; ?> rounded-pill px-3">
          <i class="fas fa-clock me-1"></i> Menunggu Bayar
        </a>
        <a href="pesanan.php?status=lunas" class="btn btn-sm <?= ($filter_status === 'lunas') ? 'btn-info text-white' : 'btn-outline-info'; ?> rounded-pill px-3">
          <i class="fas fa-check-circle me-1"></i> Lunas (Siap Masuk)
        </a>
        <a href="pesanan.php?status=selesai" class="btn btn-sm <?= ($filter_status === 'selesai') ? 'btn-success' : 'btn-outline-success'; ?> rounded-pill px-3">
          <i class="fas fa-check-double me-1"></i> Selesai Berkunjung
        </a>
        <a href="pesanan.php?status=dibatalkan" class="btn btn-sm <?= ($filter_status === 'dibatalkan') ? 'btn-danger' : 'btn-outline-danger'; ?> rounded-pill px-3">
          <i class="fas fa-ban me-1"></i> Dibatalkan
        </a>
      </div>

      <div class="card-luxury">
        <div class="card-luxury-header">
          <h5 class="card-luxury-title">
            <i class="fas fa-receipt text-primary"></i> Data Transaksi Tiket Masuk Wisata
          </h5>
          <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill font-monospace">
            <?= mysqli_num_rows($pesanan_query); ?> Transaksi Ditampilkan
          </span>
        </div>

        <div class="table-responsive">
          <table class="table table-luxury">
            <thead>
              <tr>
                <th>Kode Booking</th>
                <th>Pemesan & Kontak</th>
                <th>Destinasi Wisata</th>
                <th>Tgl Kunjungan</th>
                <th>Jumlah Tiket</th>
                <th>Total Bayar</th>
                <th>Status</th>
                <th class="text-center" width="170">Ubah Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($pesanan_query && mysqli_num_rows($pesanan_query) > 0): ?>
                <?php while ($p = mysqli_fetch_assoc($pesanan_query)): ?>
                  <tr>
                    <td>
                      <span class="font-monospace fw-bold text-primary"><?= htmlspecialchars($p['kode_booking']); ?></span>
                      <small class="d-block text-muted"><?= date('d/m/Y H:i', strtotime($p['created_at'])); ?></small>
                    </td>
                    <td>
                      <strong class="d-block text-dark"><?= htmlspecialchars($p['nama_pemesan']); ?></strong>
                      <small class="text-muted"><i class="fab fa-whatsapp text-success me-1"></i> <?= htmlspecialchars($p['no_telp'] ?: '-'); ?></small>
                    </td>
                    <td>
                      <strong class="text-dark"><?= htmlspecialchars($p['nama_destinasi']); ?></strong>
                      <small class="d-block text-muted"><i class="fas fa-map-pin text-danger me-1"></i> <?= htmlspecialchars($p['nama_provinsi']); ?></small>
                    </td>
                    <td>
                      <span class="fw-semibold"><?= tanggal_indo($p['tanggal_kunjungan']); ?></span>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-secondary rounded-pill px-2 py-1"><?= $p['jumlah_tiket']; ?> Orang</span>
                    </td>
                    <td class="fw-bold text-success fs-7">
                      <?= rupiah($p['total_harga']); ?>
                    </td>
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
                    <td class="text-center">
                      <div class="dropdown">
                        <button class="btn btn-sm btn-outline-dark dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown">
                          Tindakan
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                          <li>
                            <a class="dropdown-item py-2 text-primary" href="pesanan.php?action=lunas&id=<?= $p['id']; ?>">
                              <i class="fas fa-check-circle me-1 text-primary"></i> Set Lunas (Konfirmasi)
                            </a>
                          </li>
                          <li>
                            <a class="dropdown-item py-2 text-success" href="pesanan.php?action=selesai&id=<?= $p['id']; ?>">
                              <i class="fas fa-check-double me-1 text-success"></i> Tandai Selesai (Check-in)
                            </a>
                          </li>
                          <li>
                            <a class="dropdown-item py-2 text-warning" href="pesanan.php?action=menunggu_pembayaran&id=<?= $p['id']; ?>">
                              <i class="fas fa-clock me-1 text-warning"></i> Set Menunggu Bayar
                            </a>
                          </li>
                          <li><hr class="dropdown-divider"></li>
                          <li>
                            <a class="dropdown-item py-2 text-danger" href="pesanan.php?action=dibatalkan&id=<?= $p['id']; ?>" onclick="return confirm('Batalkan pesanan ini?');">
                              <i class="fas fa-ban me-1 text-danger"></i> Batalkan Pesanan
                            </a>
                          </li>
                        </ul>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">Tidak ada data pesanan tiket yang sesuai.</td>
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
