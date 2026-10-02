<?php
require_once __DIR__ . '/../koneksi.php';
require_role('pengunjung');

$user = auth_user();
$uid = $user['id'];
$error = '';

// Ambil daftar seluruh 10 destinasi
$dest_query = mysqli_query($conn, "SELECT d.*, k.nama_kategori, pr.nama_provinsi 
                                   FROM destinasi d 
                                   LEFT JOIN kategori_wisata k ON d.kategori_id = k.id 
                                   LEFT JOIN provinsi pr ON d.provinsi_id = pr.id 
                                   ORDER BY d.id ASC");
$destinasi_data = [];
while ($row = mysqli_fetch_assoc($dest_query)) {
    $destinasi_data[$row['id']] = $row;
}

// Destinasi awal jika dipilih via query param
$selected_id = (int)($_GET['destinasi_id'] ?? array_key_first($destinasi_data));
if (!isset($destinasi_data[$selected_id])) {
    $selected_id = array_key_first($destinasi_data);
}

// Proses pemesanan tiket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destinasi_id = (int)($_POST['destinasi_id'] ?? 0);
    $tanggal_kunjungan = sanitize($conn, $_POST['tanggal_kunjungan'] ?? '');
    $jumlah_tiket = (int)($_POST['jumlah_tiket'] ?? 1);
    $catatan = sanitize($conn, $_POST['catatan'] ?? '');

    if ($jumlah_tiket < 1) $jumlah_tiket = 1;

    if (!isset($destinasi_data[$destinasi_id])) {
        $error = 'Destinasi wisata yang dipilih tidak valid.';
    } elseif (empty($tanggal_kunjungan)) {
        $error = 'Harap tentukan tanggal rencana kunjungan wisata Anda.';
    } else {
        $harga_satuan = $destinasi_data[$destinasi_id]['harga_tiket'];
        $total_harga = $harga_satuan * $jumlah_tiket;
        
        $kode_booking = 'JW-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $sql_pesan = "INSERT INTO pemesanan_tiket (kode_booking, user_id, destinasi_id, tanggal_kunjungan, jumlah_tiket, harga_satuan, total_harga, status_pemesanan, status_checkin, catatan, created_at) 
                      VALUES ('{$kode_booking}', '{$uid}', '{$destinasi_id}', '{$tanggal_kunjungan}', '{$jumlah_tiket}', '{$harga_satuan}', '{$total_harga}', 'lunas', 'belum', '{$catatan}', NOW())";

        if (mysqli_query($conn, $sql_pesan)) {
            set_flash('success', "Tiket Anda berhasil dipesan dengan Kode: {$kode_booking}. E-Tiket resmi siap digunakan!");
            header('Location: tiket_saya.php');
            exit;
        } else {
            $error = 'Terjadi kendala saat memproses tiket: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pesan Tiket Wisata - Pesona Wisata Indonesia</title>
  
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
        <a href="pesan_tiket.php" class="sidebar-link active">
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
        <h2 class="topbar-title">Formulir Pemesanan Tiket Elektronik</h2>
      </div>

      <div class="topbar-right">
        <a href="tiket_saya.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
          <i class="fas fa-ticket me-1"></i> E-Tiket Saya
        </a>
        <a href="../logout.php?role=pengunjung" class="btn btn-outline-danger btn-sm rounded-pill px-3">
          <i class="fas fa-arrow-right-from-bracket me-1"></i> Logout Pengunjung
        </a>
      </div>
    </header>

    <div class="dashboard-container">
      <?php if ($error): ?>
        <div class="alert alert-danger rounded-3 mb-4"><?= htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <div class="row g-4">
        <!-- FORM PEMESANAN -->
        <div class="col-lg-7">
          <div class="card-luxury">
            <div class="card-luxury-header">
              <h5 class="card-luxury-title">
                <i class="fas fa-ticket-simple text-primary"></i> Rincian Reservasi Tiket
              </h5>
            </div>

            <div class="card-luxury-body">
              <form method="POST" action="" id="bookingForm">
                <div class="mb-4">
                  <label class="form-label fw-bold fs-7">Pilih Destinasi Wisata Indonesia</label>
                  <select name="destinasi_id" id="destinasiSelect" class="form-select py-2 fs-6">
                    <?php foreach ($destinasi_data as $id => $d): ?>
                      <option value="<?= $id; ?>" 
                              data-price="<?= $d['harga_tiket']; ?>"
                              data-foto="<?= htmlspecialchars($d['gambar_utama']); ?>"
                              data-provinsi="<?= htmlspecialchars($d['nama_provinsi']); ?>"
                              data-lokasi="<?= htmlspecialchars($d['lokasi_spesifik']); ?>"
                              data-jam="<?= htmlspecialchars($d['jam_buka']) . ' - ' . htmlspecialchars($d['jam_tutup']); ?>"
                              data-kategori="<?= htmlspecialchars($d['nama_kategori']); ?>"
                              <?= ($id == $selected_id) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($d['nama_destinasi']); ?> - <?= rupiah($d['harga_tiket']); ?> (<?= htmlspecialchars($d['nama_provinsi']); ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <label class="form-label fw-bold fs-7">Tanggal Rencana Kunjungan</label>
                    <input type="date" name="tanggal_kunjungan" class="form-control py-2" min="<?= date('Y-m-d'); ?>" value="<?= date('Y-m-d', strtotime('+1 day')); ?>" required>
                    <small class="text-muted fs-8">Pilih tanggal rencana Anda berwisata</small>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-bold fs-7">Jumlah Tiket / Pengunjung</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light text-muted"><i class="fas fa-users"></i></span>
                      <input type="number" name="jumlah_tiket" id="jumlahTiket" class="form-control py-2" value="1" min="1" max="50" required>
                    </div>
                    <small class="text-muted fs-8">Maksimal 50 tiket per transaksi</small>
                  </div>
                </div>

                <div class="mb-4">
                  <label class="form-label fw-bold fs-7">Catatan Tambahan (Opsional)</label>
                  <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Rombongan keluarga, sewa jeep di lokasi, atau kebutuhan kursi roda..."></textarea>
                </div>

                <!-- TOTAL PRICE SUMMARY -->
                <div class="p-3 bg-light rounded-4 border mb-4">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted fs-7">Harga Satuan Tiket:</span>
                    <span id="unitPriceDisplay" class="fw-semibold">Rp 0</span>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted fs-7">Jumlah Pengunjung:</span>
                    <span id="qtyDisplay" class="fw-semibold">1 Orang</span>
                  </div>
                  <hr class="my-2">
                  <div class="d-flex justify-content-between align-items-center">
                    <strong class="fs-6 text-dark">Total Pembayaran:</strong>
                    <h3 class="text-success fw-bold mb-0" id="totalPricePreview">Rp 0</h3>
                  </div>
                </div>

                <button type="submit" class="btn btn-luxury-primary w-100 py-3 shadow-lg fs-6">
                  <i class="fas fa-check-circle me-2"></i> Konfirmasi & Terbitkan E-Tiket
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- PREVIEW DESTINASI KARTU -->
        <div class="col-lg-5">
          <div class="card-luxury">
            <div class="card-luxury-header">
              <h5 class="card-luxury-title">
                <i class="fas fa-circle-info text-primary"></i> Preview Informasi Destinasi
              </h5>
            </div>
            <div class="p-0">
              <img id="previewFoto" src="" class="w-100" style="height: 220px; object-fit: cover;" alt="Preview Wisata">
            </div>
            <div class="card-luxury-body">
              <span id="previewKategori" class="badge bg-success-subtle text-success rounded-pill px-3 py-1 mb-2 font-monospace">Kategori</span>
              <h4 id="previewNama" class="font-serif fw-bold text-dark mb-1">Nama Wisata</h4>
              <p id="previewLokasi" class="text-muted fs-7 mb-3"><i class="fas fa-location-dot text-danger me-1"></i> Lokasi</p>
              
              <div class="p-3 bg-light rounded-3 border mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="text-muted fs-7"><i class="far fa-clock text-warning me-1"></i> Jam Operasional:</span>
                  <strong id="previewJam" class="fs-7">-</strong>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <span class="text-muted fs-7"><i class="fas fa-map-pin text-primary me-1"></i> Wilayah Provinsi:</span>
                  <strong id="previewProvinsi" class="fs-7">-</strong>
                </div>
              </div>

              <div class="alert alert-info border-0 rounded-3 mb-0 fs-8">
                <i class="fas fa-shield-check text-info me-1"></i> E-Tiket langsung terverifikasi dengan barcode digital dan siap ditunjukkan kepada petugas di lokasi.
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const destSelect = document.getElementById('destinasiSelect');
  const ticketInput = document.getElementById('jumlahTiket');
  const unitPriceDisplay = document.getElementById('unitPriceDisplay');
  const qtyDisplay = document.getElementById('qtyDisplay');
  const totalPricePreview = document.getElementById('totalPricePreview');

  const previewFoto = document.getElementById('previewFoto');
  const previewKategori = document.getElementById('previewKategori');
  const previewNama = document.getElementById('previewNama');
  const previewLokasi = document.getElementById('previewLokasi');
  const previewJam = document.getElementById('previewJam');
  const previewProvinsi = document.getElementById('previewProvinsi');

  function updateBookingPreview() {
    const selectedOption = destSelect.options[destSelect.selectedIndex];
    if (!selectedOption) return;

    const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
    const foto = selectedOption.getAttribute('data-foto');
    const provinsi = selectedOption.getAttribute('data-provinsi');
    const lokasi = selectedOption.getAttribute('data-lokasi');
    const jam = selectedOption.getAttribute('data-jam');
    const kategori = selectedOption.getAttribute('data-kategori');
    const nama = selectedOption.text.split(' - ')[0];

    previewFoto.src = foto;
    previewKategori.textContent = kategori;
    previewNama.textContent = nama;
    previewLokasi.innerHTML = `<i class="fas fa-location-dot text-danger me-1"></i> ${lokasi}`;
    previewJam.textContent = jam;
    previewProvinsi.textContent = provinsi;

    let qty = parseInt(ticketInput.value) || 1;
    if (qty < 1) qty = 1;

    const total = price * qty;

    unitPriceDisplay.textContent = 'Rp ' + price.toLocaleString('id-ID');
    qtyDisplay.textContent = qty + ' Orang';
    totalPricePreview.textContent = 'Rp ' + total.toLocaleString('id-ID');
  }

  destSelect.addEventListener('change', updateBookingPreview);
  ticketInput.addEventListener('input', updateBookingPreview);
  ticketInput.addEventListener('change', updateBookingPreview);
  window.addEventListener('DOMContentLoaded', updateBookingPreview);
</script>
</body>
</html>
