<?php
require_once __DIR__ . '/../koneksi.php';
require_role('admin');

$user = auth_user();
$error = '';

// Tambah akun baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_user'])) {
    $nama = sanitize($conn, $_POST['nama'] ?? '');
    $username = sanitize($conn, strtolower(trim($_POST['username'] ?? '')));
    $email = sanitize($conn, strtolower(trim($_POST['email'] ?? '')));
    $role = sanitize($conn, $_POST['role'] ?? 'petugas');
    $no_hp = sanitize($conn, $_POST['no_hp'] ?? '');
    $password = $_POST['password'] ?? '123456';

    $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '{$username}' OR email = '{$email}' LIMIT 1");
    if ($check && mysqli_num_rows($check) > 0) {
        $error = 'Username atau email sudah digunakan oleh akun lain.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $insert = mysqli_query($conn, "INSERT INTO users (nama_lengkap, username, email, password, role, no_telp, status_aktif, created_at) 
                                      VALUES ('{$nama}', '{$username}', '{$email}', '{$hash}', '{$role}', '{$no_hp}', 1, NOW())");
        if ($insert) {
            set_flash('success', "Akun {$role} '{$nama}' berhasil dibuat!");
            header('Location: users.php');
            exit;
        } else {
            $error = 'Gagal membuat akun: ' . mysqli_error($conn);
        }
    }
}

// Hapus akun
if (isset($_GET['hapus'])) {
    $del_id = (int)$_GET['hapus'];
    if ($del_id === (int)$user['id']) {
        set_flash('danger', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id = {$del_id}");
        set_flash('success', 'Akun berhasil dihapus dari sistem.');
    }
    header('Location: users.php');
    exit;
}

$users_query = mysqli_query($conn, "SELECT * FROM users ORDER BY FIELD(role, 'admin', 'petugas', 'pengunjung'), id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Pengguna Sistem - Pesona Wisata Indonesia</title>
  
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
        <a href="users.php" class="sidebar-link active">
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
        <h2 class="topbar-title">Hak Akses & Manajemen Pengguna</h2>
      </div>

      <div class="topbar-right">
        <button type="button" class="btn btn-luxury-primary btn-sm rounded-pill px-3 shadow" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
          <i class="fas fa-user-plus me-1"></i> Tambah Akun Baru
        </button>
        <a href="../logout.php?role=admin" class="btn btn-outline-danger btn-sm rounded-pill px-3">
          <i class="fas fa-arrow-right-from-bracket me-1"></i> Logout Admin
        </a>
      </div>
    </header>

    <div class="dashboard-container">
      <?php display_flash(); ?>

      <?php if ($error): ?>
        <div class="alert alert-danger rounded-3"><?= htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="card-luxury">
        <div class="card-luxury-header">
          <h5 class="card-luxury-title">
            <i class="fas fa-users text-primary"></i> Daftar Akun Terdaftar di Sistem
          </h5>
          <span class="badge bg-secondary-subtle text-dark px-3 py-2 rounded-pill font-monospace">
            Total: <?= mysqli_num_rows($users_query); ?> Akun
          </span>
        </div>

        <div class="table-responsive">
          <table class="table table-luxury">
            <thead>
              <tr>
                <th width="50">No</th>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Email</th>
                <th>No WhatsApp</th>
                <th>Peran (Role)</th>
                <th>Terdaftar</th>
                <th class="text-center" width="100">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $no = 1;
              while ($u = mysqli_fetch_assoc($users_query)): ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td>
                    <strong class="d-block text-dark"><?= htmlspecialchars($u['nama_lengkap'] ?: $u['username']); ?></strong>
                    <?php if ($u['id'] == $user['id']): ?>
                      <span class="badge bg-danger-subtle text-danger font-monospace fs-8">Akun Anda</span>
                    <?php endif; ?>
                  </td>
                  <td><code class="text-primary font-monospace"><?= htmlspecialchars($u['username']); ?></code></td>
                  <td><?= htmlspecialchars($u['email']); ?></td>
                  <td><?= htmlspecialchars($u['no_telp'] ?: '-'); ?></td>
                  <td>
                    <span class="role-badge-pill role-<?= $u['role']; ?>">
                      <?= strtoupper($u['role']); ?>
                    </span>
                  </td>
                  <td><small class="text-muted"><?= tanggal_indo($u['created_at']); ?></small></td>
                  <td class="text-center">
                    <?php if ($u['id'] != $user['id']): ?>
                      <a href="users.php?hapus=<?= $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pengguna <?= addslashes($u['nama_lengkap'] ?: $u['username']); ?>?');">
                        <i class="fas fa-trash"></i>
                      </a>
                    <?php else: ?>
                      <span class="text-muted fs-8">-</span>
                    <?php endif; ?>
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

<!-- MODAL TAMBAH USER -->
<div class="modal fade" id="modalTambahUser" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header bg-dark text-white rounded-top-4">
        <h5 class="modal-title font-serif"><i class="fas fa-user-plus text-warning me-2"></i> Tambah Pengguna Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="">
        <input type="hidden" name="tambah_user" value="1">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold fs-7">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" required placeholder="Nama lengkap staf / pengguna">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold fs-7">Username</label>
            <input type="text" name="username" class="form-control" required placeholder="username_staf">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold fs-7">Email</label>
            <input type="email" name="email" class="form-control" required placeholder="staf@pesonawisataindonesia.id">
          </div>
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-bold fs-7">Peran (Role)</label>
              <select name="role" class="form-select">
                <option value="petugas">Petugas Lapangan / Loket</option>
                <option value="admin">Administrator</option>
                <option value="pengunjung">Pengunjung Biasa</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label fw-bold fs-7">Nomor WhatsApp</label>
              <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold fs-7">Kata Sandi</label>
            <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter" value="petugas123">
            <small class="text-muted fs-8">Default awal: petugas123</small>
          </div>
        </div>
        <div class="modal-footer bg-light p-3">
          <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-luxury-primary rounded-pill px-4">Simpan Akun</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
