<?php
require_once __DIR__ . '/../koneksi.php';
require_role('pengunjung');

$user = auth_user();
$uid = $user['id'];
$error = '';

// Proses simpan ulasan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destinasi_id = (int)($_POST['destinasi_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 5);
    $komentar = sanitize($conn, $_POST['komentar'] ?? '');

    if ($rating < 1 || $rating > 5) $rating = 5;

    if ($destinasi_id <= 0 || empty($komentar)) {
        $error = 'Harap pilih destinasi wisata dan tuliskan pengalaman ulasan Anda.';
    } else {
        $sql = "INSERT INTO ulasan_wisata (destinasi_id, user_id, rating, komentar, status_tampil, created_at) 
                VALUES ('{$destinasi_id}', '{$uid}', '{$rating}', '{$komentar}', 1, NOW())";

        if (mysqli_query($conn, $sql)) {
            set_flash('success', 'Terima kasih! Ulasan wisata Anda telah berhasil dipublikasikan.');
            header('Location: ulasan.php');
            exit;
        } else {
            $error = 'Gagal menyimpan ulasan: ' . mysqli_error($conn);
        }
    }
}

// Hapus ulasan
if (isset($_GET['hapus'])) {
    $hid = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM ulasan_wisata WHERE id = {$hid} AND user_id = {$uid}");
    set_flash('success', 'Ulasan Anda berhasil dihapus.');
    header('Location: ulasan.php');
    exit;
}

$destinasi_options = mysqli_query($conn, "SELECT d.id, d.nama_destinasi, pr.nama_provinsi 
                                          FROM destinasi d 
                                          LEFT JOIN provinsi pr ON d.provinsi_id = pr.id 
                                          ORDER BY d.nama_destinasi ASC");

$ulasan_saya = mysqli_query($conn, "SELECT u.*, d.nama_destinasi, d.gambar_utama, pr.nama_provinsi 
                                    FROM ulasan_wisata u 
                                    JOIN destinasi d ON u.destinasi_id = d.id 
                                    LEFT JOIN provinsi pr ON d.provinsi_id = pr.id 
                                    WHERE u.user_id = {$uid} 
                                    ORDER BY u.id DESC");

$selected_dest_id = (int)($_GET['destinasi_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ulasan Wisata Saya - Pesona Wisata Indonesia</title>
  
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
        <a href="ulasan.php" class="sidebar-link active">
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
        <h2 class="topbar-title">Bagikan Pengalaman & Ulasan Wisata</h2>
      </div>

      <div class="topbar-right">
        <a href="../logout.php?role=pengunjung" class="btn btn-outline-danger btn-sm rounded-pill px-3">
          <i class="fas fa-arrow-right-from-bracket me-1"></i> Logout Pengunjung
        </a>
      </div>
    </header>

    <div class="dashboard-container">
      <?php display_flash(); ?>

      <div class="row g-4">
        <!-- FORM TULIS ULASAN -->
        <div class="col-lg-5">
          <div class="card-luxury">
            <div class="card-luxury-header">
              <h5 class="card-luxury-title">
                <i class="fas fa-pen-fancy text-primary"></i> Tulis Ulasan Baru
              </h5>
            </div>
            <div class="card-luxury-body">
              <?php if ($error): ?>
                <div class="alert alert-danger rounded-3 mb-3"><?= htmlspecialchars($error); ?></div>
              <?php endif; ?>

              <form method="POST" action="">
                <div class="mb-3">
                  <label class="form-label fw-bold fs-7">Pilih Destinasi Wisata</label>
                  <select name="destinasi_id" class="form-select py-2">
                    <?php while ($opt = mysqli_fetch_assoc($destinasi_options)): ?>
                      <option value="<?= $opt['id']; ?>" <?= ($opt['id'] == $selected_dest_id) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($opt['nama_destinasi']); ?> (<?= htmlspecialchars($opt['nama_provinsi']); ?>)
                      </option>
                    <?php endwhile; ?>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold fs-7">Beri Penilaian Bintang</label>
                  <select name="rating" class="form-select py-2 text-warning fw-bold">
                    <option value="5">⭐⭐⭐⭐⭐ (5 Bintang - Sangat Sempurna)</option>
                    <option value="4">⭐⭐⭐⭐ (4 Bintang - Sangat Bagus)</option>
                    <option value="3">⭐⭐⭐ (3 Bintang - Cukup Memuaskan)</option>
                    <option value="2">⭐⭐ (2 Bintang - Perlu Peningkatan)</option>
                    <option value="1">⭐ (1 Bintang - Kurang Memuaskan)</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold fs-7">Pengalaman & Cerita Anda</label>
                  <textarea name="komentar" class="form-control" rows="4" placeholder="Ceritakan bagaimana keindahan tempat, kebersihan, pemandangan, atau tips untuk wisatawan lain..." required></textarea>
                </div>

                <button type="submit" class="btn btn-luxury-primary w-100 py-2 rounded-pill shadow">
                  <i class="fas fa-paper-plane me-2"></i> Kirim Ulasan Wisata
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- DAFTAR ULASAN SAYA -->
        <div class="col-lg-7">
          <div class="card-luxury">
            <div class="card-luxury-header">
              <h5 class="card-luxury-title">
                <i class="fas fa-comments text-primary"></i> Riwayat Ulasan Anda
              </h5>
              <span class="badge bg-secondary-subtle text-dark px-3 py-2 rounded-pill font-monospace">
                Total: <?= mysqli_num_rows($ulasan_saya); ?> Ulasan
              </span>
            </div>
            <div class="card-luxury-body p-0">
              <?php if ($ulasan_saya && mysqli_num_rows($ulasan_saya) > 0): ?>
                <div class="list-group list-group-flush">
                  <?php while ($u = mysqli_fetch_assoc($ulasan_saya)): ?>
                    <div class="list-group-item p-4">
                      <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-3">
                          <img src="<?= htmlspecialchars($u['gambar_utama']); ?>" class="rounded-3" style="width: 55px; height: 55px; object-fit: cover;">
                          <div>
                            <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($u['nama_destinasi']); ?></h6>
                            <small class="text-muted"><i class="fas fa-map-pin text-danger me-1"></i> <?= htmlspecialchars($u['nama_provinsi']); ?></small>
                          </div>
                        </div>
                        <a href="ulasan.php?hapus=<?= $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus ulasan ini?');">
                          <i class="fas fa-trash"></i>
                        </a>
                      </div>

                      <div class="text-warning mb-2 fs-7">
                        <?php for ($i=1; $i<=5; $i++): ?>
                          <i class="fas fa-star <?= ($i <= $u['rating']) ? '' : 'text-muted opacity-25'; ?>"></i>
                        <?php endfor; ?>
                        <span class="text-dark fw-bold ms-1">(<?= $u['rating']; ?>/5)</span>
                      </div>

                      <p class="text-muted mb-1 fs-7 fst-italic">"<?= htmlspecialchars($u['komentar']); ?>"</p>
                      <small class="text-muted fs-8"><i class="far fa-clock me-1"></i> Ditulis pada <?= tanggal_indo($u['created_at']); ?></small>
                    </div>
                  <?php endwhile; ?>
                </div>
              <?php else: ?>
                <div class="p-5 text-center text-muted">
                  <i class="fas fa-star-half-stroke fs-1 mb-3 text-muted"></i>
                  <h5>Belum Ada Ulasan</h5>
                  <p class="fs-7 mb-0">Tuliskan ulasan pertama Anda melalui formulir di sebelah kiri.</p>
                </div>
              <?php endif; ?>
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
