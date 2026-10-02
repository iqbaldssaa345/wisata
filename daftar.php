<?php
require_once __DIR__ . '/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($conn, $_POST['nama'] ?? '');
    $username = sanitize($conn, strtolower(trim($_POST['username'] ?? '')));
    $email = sanitize($conn, strtolower(trim($_POST['email'] ?? '')));
    $no_hp = sanitize($conn, $_POST['no_hp'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($nama) || empty($username) || empty($email) || empty($password)) {
        $error = 'Semua field wajib diisi lengkap.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format alamat email tidak valid.';
    } elseif (strlen($password) < 5) {
        $error = 'Kata sandi minimal terdiri dari 5 karakter.';
    } elseif ($password !== $password_confirm) {
        $error = 'Konfirmasi kata sandi tidak cocok. Harap periksa kembali.';
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '{$username}' OR email = '{$email}' LIMIT 1");
        if ($check && mysqli_num_rows($check) > 0) {
            $error = 'Username atau alamat email sudah terdaftar. Silakan gunakan yang lain atau langsung masuk.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            $query_insert = "INSERT INTO users (nama_lengkap, username, email, password, role, no_telp, status_aktif, created_at) 
                             VALUES ('{$nama}', '{$username}', '{$email}', '{$hashed_password}', 'pengunjung', '{$no_hp}', 1, NOW())";

            if (mysqli_query($conn, $query_insert)) {
                $new_id = mysqli_insert_id($conn);
                $user_data = [
                    'id' => $new_id,
                    'nama' => $nama,
                    'username' => $username,
                    'email' => $email,
                    'role' => 'pengunjung'
                ];
                $tab_id = $_POST['tab'] ?? $_GET['tab'] ?? $_COOKIE['wisata_active_tab'] ?? null;
                if (!$tab_id || !preg_match('/^[a-zA-Z0-9_\-]+$/', $tab_id)) {
                    $tab_id = 'tab_' . substr(md5(uniqid(mt_rand(), true)), 0, 10);
                }

                if (!isset($_SESSION['tabs'])) {
                    $_SESSION['tabs'] = [];
                }
                $_SESSION['tabs'][$tab_id] = $user_data;
                $_SESSION['multi_roles']['pengunjung'] = $user_data;

                $_SESSION['user_id'] = $new_id;
                $_SESSION['user_nama'] = $nama;
                $_SESSION['user_username'] = $username;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'pengunjung';

                setcookie('wisata_active_tab', $tab_id, time() + 86400, '/');
                set_flash('success', 'Selamat datang, ' . htmlspecialchars($nama) . '! Akun Anda berhasil didaftarkan.');
                header('Location: pengunjung/index.php?tab=' . urlencode($tab_id));
                exit;
            } else {
                $error = 'Terjadi kesalahan saat mendaftar: ' . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Akun Pengunjung - Pesona Wisata Indonesia</title>
  
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏛️</text></svg>">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome 6 Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <!-- Google Fonts: Cinzel & Plus Jakarta Sans -->
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #064e3b;
      --primary-dark: #022c22;
      --primary-light: #059669;
      --secondary: #b45309;
      --secondary-gold: #f59e0b;
      --font-serif: 'Cinzel', serif;
      --font-sans: 'Plus Jakarta Sans', sans-serif;
    }
    
    body {
      font-family: var(--font-sans);
      background: #0f172a;
      min-height: 100vh;
      margin: 0;
      color: #1e293b;
    }
    
    .register-split-wrapper {
      min-height: 100vh;
      display: flex;
    }
    
    /* LEFT HERO SHOWCASE */
    .register-visual-pane {
      flex: 1.1;
      background: linear-gradient(135deg, rgba(2, 44, 34, 0.88), rgba(15, 23, 42, 0.92)),
                  url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1600&q=85') center/cover no-repeat;
      padding: 60px 50px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      color: #fff;
      position: relative;
      overflow: hidden;
    }

    .register-visual-pane::before {
      content: "";
      position: absolute;
      top: -20%;
      right: -20%;
      width: 450px;
      height: 450px;
      background: radial-gradient(circle, rgba(245, 158, 11, 0.25), transparent 70%);
      border-radius: 50%;
    }
    
    .brand-logo-lockup {
      display: inline-flex;
      align-items: center;
      gap: 14px;
      text-decoration: none;
      z-index: 2;
    }
    
    .brand-icon-box {
      width: 50px;
      height: 50px;
      border-radius: 14px;
      background: linear-gradient(135deg, #059669, #064e3b);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fef08a;
      font-size: 1.4rem;
      box-shadow: 0 10px 25px rgba(6, 78, 59, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.25);
    }

    .brand-text-content h2 {
      font-family: var(--font-serif);
      color: #fff;
      font-size: 1.4rem;
      font-weight: 800;
      margin: 0;
      letter-spacing: -0.01em;
    }
    
    .brand-text-content span {
      color: var(--secondary-gold);
      font-size: 0.75rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      font-weight: 700;
    }
    
    .hero-quote-block {
      max-width: 540px;
      z-index: 2;
    }
    
    .hero-quote-title {
      font-family: var(--font-serif);
      font-size: clamp(2rem, 3.2vw, 2.8rem);
      font-weight: 800;
      line-height: 1.2;
      color: #fff;
      margin-bottom: 20px;
    }
    
    .hero-quote-title span {
      background: linear-gradient(135deg, #fef08a, #f59e0b);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .feature-pill-grid {
      display: flex;
      flex-direction: column;
      gap: 14px;
      margin-top: 30px;
    }
    
    .feature-pill-item {
      display: flex;
      align-items: center;
      gap: 12px;
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(10px);
      padding: 12px 18px;
      border-radius: 12px;
      border: 1px solid rgba(255, 255, 255, 0.12);
      font-size: 0.9rem;
    }

    .feature-pill-item i {
      color: var(--secondary-gold);
      font-size: 1.1rem;
    }

    /* RIGHT FORM PANE */
    .register-form-pane {
      flex: 1.2;
      background: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 25px;
      position: relative;
    }

    .form-luxury-card {
      width: 100%;
      max-width: 540px;
      background: #ffffff;
      border-radius: 24px;
      box-shadow: 0 20px 50px -10px rgba(15, 23, 42, 0.12);
      border: 1px solid #e2e8f0;
      padding: 42px 38px;
    }
    
    .form-title-lead {
      font-family: var(--font-serif);
      font-size: 1.8rem;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 6px;
    }

    .input-luxury-group {
      position: relative;
      margin-bottom: 18px;
    }
    
    .input-luxury-group label {
      font-size: 0.82rem;
      font-weight: 700;
      color: #334155;
      margin-bottom: 8px;
      display: block;
    }
    
    .input-luxury-box {
      position: relative;
    }

    .input-luxury-box i.input-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 1rem;
      transition: color 0.2s ease;
    }

    .input-luxury-field {
      width: 100%;
      padding: 13px 16px 13px 46px;
      border: 1.8px solid #e2e8f0;
      border-radius: 14px;
      font-size: 0.92rem;
      font-family: var(--font-sans);
      background: #fafafa;
      color: #0f172a;
      transition: all 0.25s ease;
      outline: none;
    }
    
    .input-luxury-field:focus {
      border-color: var(--primary-light);
      background: #fff;
      box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.12);
    }

    .input-luxury-box:focus-within i.input-icon {
      color: var(--primary-light);
    }

    .toggle-pass-btn {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #94a3b8;
      cursor: pointer;
      font-size: 1rem;
      padding: 4px;
    }

    .toggle-pass-btn:hover {
      color: #0f172a;
    }

    /* Submit Button */
    .btn-submit-gold {
      width: 100%;
      background: linear-gradient(135deg, #b45309 0%, #d97706 50%, #f59e0b 100%);
      color: #fff;
      border: none;
      padding: 14px 20px;
      border-radius: 14px;
      font-weight: 700;
      font-size: 1rem;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      box-shadow: 0 10px 25px rgba(217, 119, 6, 0.35);
      cursor: pointer;
    }
    
    .btn-submit-gold:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 30px rgba(217, 119, 6, 0.45);
      color: #fff;
    }

    @media (max-width: 991.98px) {
      .register-visual-pane {
        display: none;
      }
      .register-form-pane {
        background: radial-gradient(circle at top, #ecfdf5, #f8fafc 60%, #e2e8f0);
      }
    }
  </style>
</head>
<body>

  <div class="register-split-wrapper">
    <!-- LEFT COLUMN: VISUAL HERITAGE SHOWCASE -->
    <div class="register-visual-pane">
      <div>
        <a href="index.php" class="brand-logo-lockup">
          <div class="brand-icon-box">
            <i class="fas fa-mountain-sun"></i>
          </div>
          <div class="brand-text-content">
            <h2>PESONA WISATA INDONESIA</h2>
            <span>Wonderful Heritage & Nature</span>
          </div>
        </a>
      </div>

      <div class="hero-quote-block">
        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold text-uppercase mb-3 font-monospace">
          <i class="fas fa-user-plus me-1"></i> Akun Wisatawan Resmi
        </span>
        <h1 class="hero-quote-title">
          Mulai Petualangan Magis <br><span>Keajaiban Nusantara</span>
        </h1>
        <p class="text-white-50 fs-6 lh-base mb-0">
          Daftarkan akun pengunjung Anda untuk mendapatkan akses reservasi e-tiket resmi, promo musiman, serta kemudahan check-in bebas antre di seluruh destinasi unggulan Indonesia.
        </p>

        <div class="feature-pill-grid">
          <div class="feature-pill-item">
            <i class="fas fa-ticket"></i>
            <span>Simpan Seluruh E-Tiket Digital di Satu Tempat</span>
          </div>
          <div class="feature-pill-item">
            <i class="fas fa-star"></i>
            <span>Beri Rating & Tulis Ulasan Wisatawan Nyata</span>
          </div>
          <div class="feature-pill-item">
            <i class="fas fa-shield-check"></i>
            <span>Garansi Tiket Resmi & Bebas Antrean Loket</span>
          </div>
        </div>
      </div>

      <div class="d-flex align-items-center justify-content-between pt-4 border-top border-white border-opacity-10 text-white-50 fs-8">
        <span>&copy; <?= date('Y'); ?> Pesona Wisata Indonesia</span>
        <span>Layanan Bantuan 24/7</span>
      </div>
    </div>

    <!-- RIGHT COLUMN: REGISTER FORM -->
    <div class="register-form-pane">
      <div class="form-luxury-card">
        
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div>
            <h2 class="form-title-lead">Pendaftaran Akun</h2>
            <p class="text-muted fs-7 mb-0">Lengkapi data diri Anda sebagai wisatawan</p>
          </div>
          <a href="index.php" class="btn btn-sm btn-light rounded-pill border px-3 text-muted" title="Kembali ke Beranda">
            <i class="fas fa-house"></i>
          </a>
        </div>

        <?php if ($error): ?>
          <div class="alert alert-danger alert-dismissible fade show rounded-3 p-3 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
              <i class="fas fa-circle-exclamation fs-5 text-danger"></i>
              <div class="fs-7"><?= htmlspecialchars($error); ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <!-- FORM REGISTRASI -->
        <form method="POST" action="daftar.php">
          <div class="input-luxury-group">
            <label for="namaInput">Nama Lengkap Sesuai KTP / Paspor</label>
            <div class="input-luxury-box">
              <input type="text" name="nama" id="namaInput" class="input-luxury-field" placeholder="Contoh: Raden Surya Atmaja" required value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>" autofocus>
              <i class="fas fa-id-card input-icon"></i>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="input-luxury-group">
                <label for="usernameInput">Username Unik</label>
                <div class="input-luxury-box">
                  <input type="text" name="username" id="usernameInput" class="input-luxury-field" placeholder="username_kamu" required value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>">
                  <i class="fas fa-user input-icon"></i>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-luxury-group">
                <label for="nohpInput">Nomor WhatsApp / HP</label>
                <div class="input-luxury-box">
                  <input type="text" name="no_hp" id="nohpInput" class="input-luxury-field" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($_POST['no_hp'] ?? ''); ?>">
                  <i class="fab fa-whatsapp input-icon"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="input-luxury-group">
            <label for="emailInput">Alamat Email Aktif</label>
            <div class="input-luxury-box">
              <input type="email" name="email" id="emailInput" class="input-luxury-field" placeholder="nama@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>">
              <i class="fas fa-envelope input-icon"></i>
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="input-luxury-group mb-0">
                <label for="pass1Input">Kata Sandi Akun</label>
                <div class="input-luxury-box">
                  <input type="password" name="password" id="pass1Input" class="input-luxury-field" placeholder="Min. 5 karakter" required>
                  <i class="fas fa-lock input-icon"></i>
                  <button type="button" class="toggle-pass-btn" onclick="togglePass('pass1Input', 'eyeIcon1')">
                    <i class="fas fa-eye" id="eyeIcon1"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-luxury-group mb-0">
                <label for="pass2Input">Konfirmasi Sandi</label>
                <div class="input-luxury-box">
                  <input type="password" name="password_confirm" id="pass2Input" class="input-luxury-field" placeholder="Ketik ulang sandi" required>
                  <i class="fas fa-shield-alt input-icon"></i>
                  <button type="button" class="toggle-pass-btn" onclick="togglePass('pass2Input', 'eyeIcon2')">
                    <i class="fas fa-eye" id="eyeIcon2"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <button type="submit" class="btn-submit-gold">
            <i class="fas fa-user-plus"></i> Buat Akun & Mulai Berwisata
          </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top">
          <p class="fs-7 text-muted mb-2">Sudah memiliki akun wisata?</p>
          <a href="login.php" class="btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold">
            <i class="fas fa-arrow-right-to-bracket me-1"></i> Masuk Sekarang
          </a>
        </div>

        <div class="text-center mt-3">
          <a href="index.php" class="text-decoration-none fs-8 text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Halaman Utama
          </a>
        </div>

      </div>
    </div>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePass(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    }
  </script>
</body>
</html>
