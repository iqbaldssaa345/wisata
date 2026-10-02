<?php
require_once __DIR__ . '/../koneksi.php';
require_role('admin');

$user = auth_user();

$dest_query = mysqli_query($conn, "SELECT d.*, p.nama_provinsi, k.nama_kategori 
                                   FROM destinasi d 
                                   LEFT JOIN provinsi p ON d.provinsi_id = p.id 
                                   LEFT JOIN kategori_wisata k ON d.kategori_id = k.id 
                                   ORDER BY d.id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola 10 Destinasi Wisata - Pesona Wisata Indonesia</title>
  
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
        <a href="destinasi.php" class="sidebar-link active">
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
        <h2 class="topbar-title">Katalog & Kelola Destinasi Wisata</h2>
      </div>

      <div class="topbar-right">
        <a href="destinasi_tambah.php" class="btn btn-luxury-primary btn-sm rounded-pill px-3 shadow">
          <i class="fas fa-plus-circle me-1"></i> Tambah Wisata Baru
        </a>
        <a href="../logout.php?role=admin" class="btn btn-outline-danger btn-sm rounded-pill px-3">
          <i class="fas fa-arrow-right-from-bracket me-1"></i> Logout Admin
        </a>
      </div>
    </header>

    <div class="dashboard-container">
      <?php display_flash(); ?>

      <div class="card-luxury">
        <div class="card-luxury-header">
          <h5 class="card-luxury-title">
            <i class="fas fa-list text-primary"></i> Daftar Destinasi Wisata Unggulan Indonesia
          </h5>
          <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill font-monospace">
            Total: <?= mysqli_num_rows($dest_query); ?> Destinasi Aktif
          </span>
        </div>

        <div class="table-responsive">
          <table class="table table-luxury">
            <thead>
              <tr>
                <th width="50">No</th>
                <th>Foto</th>
                <th>Nama Wisata</th>
                <th>Kategori</th>
                <th>Lokasi & Provinsi</th>
                <th>Harga Tiket</th>
                <th>Rating</th>
                <th class="text-center" width="160">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $no = 1;
              while ($d = mysqli_fetch_assoc($dest_query)): ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td>
                    <img src="<?= htmlspecialchars($d['gambar_utama']); ?>" class="rounded-3 shadow-sm" style="width: 65px; height: 50px; object-fit: cover;" alt="Foto">
                  </td>
                  <td>
                    <strong class="d-block text-dark"><?= htmlspecialchars($d['nama_destinasi']); ?></strong>
                    <small class="text-muted"><i class="far fa-clock"></i> <?= htmlspecialchars($d['jam_buka']); ?> - <?= htmlspecialchars($d['jam_tutup']); ?></small>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border px-2 py-1"><?= htmlspecialchars($d['nama_kategori']); ?></span>
                  </td>
                  <td>
                    <div class="fs-7"><i class="fas fa-location-dot text-danger me-1"></i> <?= htmlspecialchars($d['lokasi_spesifik']); ?></div>
                    <small class="badge bg-secondary-subtle text-dark"><?= htmlspecialchars($d['nama_provinsi']); ?></small>
                  </td>
                  <td class="fw-bold text-success">
                    <?= rupiah($d['harga_tiket']); ?>
                  </td>
                  <td>
                    <span class="badge bg-warning text-dark font-monospace">
                      <i class="fas fa-star"></i> <?= $d['rating']; ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <div class="btn-group btn-group-sm">
                      <a href="destinasi_edit.php?id=<?= $d['id']; ?>" class="btn btn-outline-primary" title="Edit Wisata">
                        <i class="fas fa-pen-to-square"></i>
                      </a>
                      <a href="destinasi_hapus.php?id=<?= $d['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus destinasi <?= addslashes($d['nama_destinasi']); ?>?');" title="Hapus Wisata">
                        <i class="fas fa-trash"></i>
                      </a>
                    </div>
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
