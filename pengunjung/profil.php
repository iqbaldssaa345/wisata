<?php
require_once __DIR__ . '/../koneksi.php';
require_role('pengunjung');

$user = auth_user();
$uid = $user['id'];
$error = '';

$user_db = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = {$uid} LIMIT 1"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($conn, $_POST['nama'] ?? '');
    $no_hp = sanitize($conn, $_POST['no_hp'] ?? '');
    $alamat = sanitize($conn, $_POST['alamat'] ?? '');
    $password_baru = $_POST['password_baru'] ?? '';

    if (empty($nama)) {
        $error = 'Nama lengkap tidak boleh kosong.';
    } else {
        if (!empty($password_baru)) {
            if (strlen($password_baru) < 5) {
                $error = 'Kata sandi baru minimal 5 karakter.';
            } else {
                $hash = password_hash($password_baru, PASSWORD_BCRYPT);
                mysqli_query($conn, "UPDATE users SET nama_lengkap = '{$nama}', no_telp = '{$no_hp}', alamat = '{$alamat}', password = '{$hash}' WHERE id = {$uid}");
                update_auth_user(['nama' => $nama]);
                set_flash('success', 'Profil dan kata sandi Anda berhasil diperbarui!');
                header('Location: profil.php');
                exit;
            }
        } else {
            mysqli_query($conn, "UPDATE users SET nama_lengkap = '{$nama}', no_telp = '{$no_hp}', alamat = '{$alamat}' WHERE id = {$uid}");
            update_auth_user(['nama' => $nama]);
            set_flash('success', 'Profil Anda berhasil diperbarui!');
            header('Location: profil.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Saya - Pesona Wisata Indonesia</title>
  
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
        <a href="profil.php" class="sidebar-link active">
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
        <h2 class="topbar-title">Pengaturan Profil Pengguna</h2>
      </div>

      <div class="topbar-right">
        <a href="../logout.php?role=pengunjung" class="btn btn-outline-danger btn-sm rounded-pill px-3">
          <i class="fas fa-arrow-right-from-bracket me-1"></i> Logout Pengunjung
        </a>
      </div>
    </header>

    <div class="dashboard-container">
      <?php display_flash(); ?>

      <div class="card-luxury max-w-800 mx-auto" style="max-width: 700px;">
        <div class="card-luxury-header">
          <h5 class="card-luxury-title">
            <i class="fas fa-user-pen text-primary"></i> Data Diri & Keamanan Akun
          </h5>
        </div>
        <div class="card-luxury-body">
          <?php if ($error): ?>
            <div class="alert alert-danger rounded-3 mb-3"><?= htmlspecialchars($error); ?></div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="mb-3">
              <label class="form-label fw-bold fs-7">Nama Lengkap</label>
              <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user_db['nama_lengkap'] ?: $user_db['username']); ?>" required>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-bold fs-7">Username</label>
                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user_db['username']); ?>" readonly>
                <small class="text-muted fs-8">Username unik Anda tidak dapat diubah</small>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold fs-7">Email</label>
                <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user_db['email']); ?>" readonly>
                <small class="text-muted fs-8">Email terdaftar</small>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold fs-7">Nomor WhatsApp / HP</label>
              <input type="text" name="no_hp" class="form-control" value="<?= htmlspecialchars($user_db['no_telp'] ?? ''); ?>" placeholder="08xxxxxxxxxx">
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold fs-7">Alamat Tempat Tinggal</label>
              <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat asal kota / domisili..."><?= htmlspecialchars($user_db['alamat'] ?? ''); ?></textarea>
            </div>

            <hr class="my-4">

            <div class="mb-4">
              <label class="form-label fw-bold fs-7">Ganti Kata Sandi (Kosongkan jika tidak ingin diubah)</label>
              <input type="password" name="password_baru" class="form-control" placeholder="Ketik kata sandi baru (minimal 5 karakter)">
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-luxury-primary px-4 rounded-pill shadow">
                <i class="fas fa-save me-1"></i> Simpan Perubahan Profil
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
