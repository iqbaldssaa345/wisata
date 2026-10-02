<?php
require_once __DIR__ . '/../koneksi.php';
require_role('admin');

$user = auth_user();
$error = '';

$kategori_query = mysqli_query($conn, "SELECT * FROM kategori_wisata ORDER BY id ASC");
$provinsi_query = mysqli_query($conn, "SELECT * FROM provinsi ORDER BY id ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_destinasi = sanitize($conn, $_POST['nama_destinasi'] ?? '');
    $kategori_id = (int)($_POST['kategori_id'] ?? 1);
    $provinsi_id = (int)($_POST['provinsi_id'] ?? 1);
    $lokasi_spesifik = sanitize($conn, $_POST['lokasi_spesifik'] ?? '');
    $harga_tiket = (float)($_POST['harga_tiket'] ?? 0);
    $rating = (float)($_POST['rating'] ?? 4.8);
    $jam_buka = sanitize($conn, $_POST['jam_buka'] ?? '07:00');
    $jam_tutup = sanitize($conn, $_POST['jam_tutup'] ?? '17:00');
    $gambar_utama = sanitize($conn, $_POST['gambar_utama'] ?? '');
    $ringkasan = sanitize($conn, $_POST['ringkasan'] ?? '');
    $deskripsi_lengkap = sanitize($conn, $_POST['deskripsi_lengkap'] ?? '');

    if (empty($nama_destinasi) || empty($lokasi_spesifik) || empty($deskripsi_lengkap)) {
        $error = 'Harap lengkapi nama destinasi, lokasi, dan deskripsi.';
    } else {
        if (empty($gambar_utama)) {
            $gambar_utama = 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=1200&q=80';
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama_destinasi)));

        $sql = "INSERT INTO destinasi (provinsi_id, kategori_id, nama_destinasi, slug, ringkasan, deskripsi_lengkap, lokasi_spesifik, harga_tiket, jam_buka, jam_tutup, rating, gambar_utama, status, created_at) 
                VALUES ('{$provinsi_id}', '{$kategori_id}', '{$nama_destinasi}', '{$slug}', '{$ringkasan}', '{$deskripsi_lengkap}', '{$lokasi_spesifik}', '{$harga_tiket}', '{$jam_buka}', '{$jam_tutup}', '{$rating}', '{$gambar_utama}', 'aktif', NOW())";

        if (mysqli_query($conn, $sql)) {
            set_flash('success', 'Destinasi wisata ' . htmlspecialchars($nama_destinasi) . ' berhasil ditambahkan.');
            header('Location: destinasi.php');
            exit;
        } else {
            $error = 'Gagal menyimpan destinasi: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Destinasi Wisata - Pesona Wisata Indonesia</title>
  
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏛️</text></svg>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="dashboard-body">

<div class="dashboard-layout">
  <main class="dashboard-main ms-0">
    <header class="dashboard-topbar">
      <div class="topbar-left">
        <a href="destinasi.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3 me-2">
          <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <h2 class="topbar-title">Tambah Destinasi Wisata Baru</h2>
      </div>
    </header>

    <div class="dashboard-container">
      <div class="card-luxury max-w-800 mx-auto" style="max-width: 850px;">
        <div class="card-luxury-header">
          <h5 class="card-luxury-title">
            <i class="fas fa-pen-nib text-primary"></i> Formulir Destinasi Wisata Baru
          </h5>
        </div>

        <div class="card-luxury-body">
          <?php if ($error): ?>
            <div class="alert alert-danger rounded-3 mb-3"><?= htmlspecialchars($error); ?></div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label fw-bold fs-7">Nama Destinasi Wisata</label>
                <input type="text" name="nama_destinasi" class="form-control" placeholder="Contoh: Dataran Tinggi Dieng" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold fs-7">Kategori Wisata</label>
                <select name="kategori_id" class="form-select">
                  <?php while ($k = mysqli_fetch_assoc($kategori_query)): ?>
                    <option value="<?= $k['id']; ?>"><?= htmlspecialchars($k['nama_kategori']); ?></option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-bold fs-7">Wilayah Provinsi</label>
                <select name="provinsi_id" class="form-select">
                  <?php while ($p = mysqli_fetch_assoc($provinsi_query)): ?>
                    <option value="<?= $p['id']; ?>"><?= htmlspecialchars($p['nama_provinsi']); ?></option>
                  <?php endwhile; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-bold fs-7">Lokasi / Alamat Lengkap</label>
                <input type="text" name="lokasi_spesifik" class="form-control" placeholder="Contoh: Kejajar, Kabupaten Wonosobo" required>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold fs-7">Harga Tiket Masuk (Rp)</label>
                <input type="number" name="harga_tiket" class="form-control" placeholder="Contoh: 30000" min="0" step="1000" required>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold fs-7">Rating Awal (1.0 - 5.0)</label>
                <input type="number" name="rating" class="form-control" value="4.8" min="1.0" max="5.0" step="0.1" required>
              </div>

              <div class="col-md-2">
                <label class="form-label fw-bold fs-7">Jam Buka</label>
                <input type="text" name="jam_buka" class="form-control" value="07:00" required>
              </div>

              <div class="col-md-2">
                <label class="form-label fw-bold fs-7">Jam Tutup</label>
                <input type="text" name="jam_tutup" class="form-control" value="17:00" required>
              </div>

              <div class="col-12">
                <label class="form-label fw-bold fs-7">URL Foto / Gambar Destinasi (Unsplash/Web)</label>
                <input type="url" name="gambar_utama" class="form-control" placeholder="https://images.unsplash.com/photo-..." value="https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=1200&q=80">
              </div>

              <div class="col-12">
                <label class="form-label fw-bold fs-7">Ringkasan Singkat (1 Kalimat)</label>
                <input type="text" name="ringkasan" class="form-control" placeholder="Ringkasan singkat pesona destinasi...">
              </div>

              <div class="col-12">
                <label class="form-label fw-bold fs-7">Deskripsi Lengkap Wisata</label>
                <textarea name="deskripsi_lengkap" class="form-control" rows="4" placeholder="Jelaskan keindahan, sejarah, dan daya tarik utama destinasi..." required></textarea>
              </div>

              <div class="col-12 text-end mt-4">
                <a href="destinasi.php" class="btn btn-light border px-4 rounded-pill me-2">Batal</a>
                <button type="submit" class="btn btn-luxury-primary px-4 rounded-pill">
                  <i class="fas fa-save me-1"></i> Simpan Destinasi
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
