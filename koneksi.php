<?php
// ==============================================================
// KONEKSI UTAMA DATABASE & MULTI-TAB SESSION HELPER
// ==============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['tabs']) || !is_array($_SESSION['tabs'])) {
    $_SESSION['tabs'] = [];
}
if (!isset($_SESSION['multi_roles']) || !is_array($_SESSION['multi_roles'])) {
    $_SESSION['multi_roles'] = [];
}

// Inisialisasi output buffer untuk menyisipkan tab isolation script secara otomatis ke seluruh halaman
if (!ob_get_level()) {
    ob_start(function($buffer) {
        if (stripos($buffer, '</body>') !== false) {
            $script = <<<'HTML'
<script>
(function() {
  try {
    var urlParams = new URLSearchParams(window.location.search);
    var urlTab = urlParams.get('tab');
    var tabId = sessionStorage.getItem('wisata_tab_id');
    var path = window.location.pathname.toLowerCase();
    var isAuthPage = path.indexOf('login.php') !== -1 || path.indexOf('daftar.php') !== -1;

    // Jika tab baru membuka halaman login/daftar, buat tabId baru agar tidak menimpa tab yang sudah login
    if (isAuthPage && !tabId) {
      tabId = 'tab_' + Date.now().toString(36) + '_' + Math.random().toString(36).substr(2, 6);
      sessionStorage.setItem('wisata_tab_id', tabId);
    } else if (urlTab && (!tabId || tabId !== urlTab)) {
      tabId = urlTab;
      sessionStorage.setItem('wisata_tab_id', tabId);
    } else if (!tabId) {
      tabId = 'tab_' + Date.now().toString(36) + '_' + Math.random().toString(36).substr(2, 6);
      sessionStorage.setItem('wisata_tab_id', tabId);
    }
    window.name = tabId;

    function syncActiveTab() {
      if (tabId) {
        document.cookie = 'wisata_active_tab=' + encodeURIComponent(tabId) + '; path=/; SameSite=Lax';
      }
    }
    syncActiveTab();

    window.addEventListener('focus', syncActiveTab);
    document.addEventListener('visibilitychange', function() {
      if (document.visibilityState === 'visible') syncActiveTab();
    });

    // Otomatis inject input tab pada form submission
    document.addEventListener('submit', function(e) {
      syncActiveTab();
      var form = e.target;
      if (form && !form.querySelector('input[name="tab"]')) {
        var inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'tab';
        inp.value = tabId;
        form.appendChild(inp);
      }
    }, true);

    // Otomatis lampirkan ?tab= pada semua internal links
    document.addEventListener('click', function(e) {
      syncActiveTab();
      var a = e.target.closest('a');
      if (!a || !a.href) return;
      if (!a.href.startsWith(window.location.origin)) return;
      var rawHref = a.getAttribute('href') || '';
      if (rawHref.startsWith('#') || rawHref.startsWith('javascript:')) return;
      try {
        var u = new URL(a.href);
        if (!u.searchParams.has('tab')) {
          u.searchParams.set('tab', tabId);
          a.href = u.toString();
        }
      } catch (err) {}
    }, true);

    // Perbarui URL browser dengan tabId saat ini tanpa reload halaman
    if (!urlTab && window.history && window.history.replaceState) {
      urlParams.set('tab', tabId);
      var qs = urlParams.toString();
      var newUrl = window.location.pathname + (qs ? '?' + qs : '') + window.location.hash;
      window.history.replaceState(null, '', newUrl);
    }
  } catch (err) {
    console.error('MultiTab Handler Error:', err);
  }
})();
</script>
HTML;
            return str_ireplace('</body>', $script . "\n</body>", $buffer);
        }
        return $buffer;
    });
}


$db_host = 'localhost';
$db_user = 'root';
$db_passwords = ['12345678', '', 'root', '123456'];
$db_name = 'wisata_jawa';

$conn = null;
foreach ($db_passwords as $pwd) {
    $c = @mysqli_connect($db_host, $db_user, $pwd);
    if ($c) {
        $conn = $c;
        break;
    }
}

if (!$conn) {
    die("<div style='font-family:sans-serif;padding:30px;background:#fef2f2;color:#991b1b;border:1px solid #f87171;border-radius:12px;max-width:600px;margin:50px auto;'>
        <h3 style='margin-top:0;'>⚠️ Gagal Terhubung ke MySQL</h3>
        <p>Pastikan Apache & MySQL di AppServ sudah berjalan.</p>
        <p><small>Pesan Error: " . htmlspecialchars(mysqli_connect_error()) . "</small></p>
    </div>");
}

mysqli_set_charset($conn, 'utf8mb4');

@mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
mysqli_select_db($conn, $db_name);

// -------------------------------------------------------------
// HELPER FUNCTIONS & MULTI-TAB ROLE HANDLING
// -------------------------------------------------------------

function rupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

function tanggal_indo($tanggal) {
    if (!$tanggal || $tanggal == '0000-00-00' || $tanggal == '0000-00-00 00:00:00') return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $timestamp = strtotime($tanggal);
    $d = date('d', $timestamp);
    $m = (int)date('m', $timestamp);
    $y = date('Y', $timestamp);
    return $d . ' ' . ($bulan[$m] ?? '') . ' ' . $y;
}

function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

function set_flash($type, $message) {
    $_SESSION['flash_msg'] = [
        'type' => $type,
        'message' => $message
    ];
}

function display_flash() {
    if (isset($_SESSION['flash_msg'])) {
        $type = $_SESSION['flash_msg']['type'];
        $message = $_SESSION['flash_msg']['message'];
        unset($_SESSION['flash_msg']);
        
        $colorClass = 'alert-' . $type;
        $icon = 'info-circle';
        if ($type === 'success') $icon = 'check-circle';
        if ($type === 'danger') $icon = 'exclamation-triangle';
        if ($type === 'warning') $icon = 'exclamation-circle';
        
        echo "<div class='alert {$colorClass} alert-dismissible fade show rounded-3' role='alert'>
                <i class='fas fa-{$icon} me-2'></i> " . htmlspecialchars($message) . "
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    }
}

// Deteksi Tab ID aktif dari GET, POST, atau Cookie
function get_current_tab_id() {
    $tab = $_GET['tab'] ?? $_POST['tab'] ?? $_COOKIE['wisata_active_tab'] ?? null;
    if ($tab && preg_match('/^[a-zA-Z0-9_\-]+$/', $tab)) {
        return $tab;
    }
    return null;
}

// Deteksi konteks portal aktif dari path file (/admin/, /petugas/, /pengunjung/)
function get_current_portal_role() {
    $script = str_replace('\\', '/', ($_SERVER['PHP_SELF'] ?? '') . ' ' . ($_SERVER['SCRIPT_NAME'] ?? ''));
    if (strpos($script, '/admin/') !== false) {
        return 'admin';
    } elseif (strpos($script, '/petugas/') !== false) {
        return 'petugas';
    } elseif (strpos($script, '/pengunjung/') !== false) {
        return 'pengunjung';
    }
    return null;
}

// Dapatkan data user sesuai konteks tab masing-masing
function auth_user($context_role = null) {
    if (!$context_role) {
        $context_role = get_current_portal_role();
    }
    $tab_id = get_current_tab_id();

    // 1. Prioritas Utama: Cek sesi khusus tab saat ini
    if ($tab_id && isset($_SESSION['tabs'][$tab_id]) && is_array($_SESSION['tabs'][$tab_id])) {
        $tab_user = $_SESSION['tabs'][$tab_id];
        // Jika ada konteks portal (/pengunjung/, /admin/, /petugas/), pastikan role akun tab ini sesuai
        if (!$context_role || $tab_user['role'] === $context_role) {
            return $tab_user;
        }
    }

    // 2. Cek sesi multi-role per-portal (jika tab_id belum terikat atau fallback)
    if ($context_role && isset($_SESSION['multi_roles'][$context_role]) && is_array($_SESSION['multi_roles'][$context_role])) {
        $role_user = $_SESSION['multi_roles'][$context_role];
        if ($tab_id) {
            $_SESSION['tabs'][$tab_id] = $role_user;
        }
        return $role_user;
    }

    // 3. Cek sesi umum jika role cocok dengan konteks portal aktif
    if ($context_role && isset($_SESSION['user_id'])) {
        $cur_role = $_SESSION['user_role'] ?? '';
        if ($cur_role === $context_role) {
            $gen_user = [
                'id' => $_SESSION['user_id'],
                'nama' => $_SESSION['user_nama'] ?? 'Pengguna',
                'username' => $_SESSION['user_username'] ?? '',
                'email' => $_SESSION['user_email'] ?? '',
                'role' => $cur_role ?: 'pengunjung'
            ];
            if ($tab_id) {
                $_SESSION['tabs'][$tab_id] = $gen_user;
            }
            return $gen_user;
        }
    }

    return null;
}

// Validasi izin akses halaman per-portal dan per-tab tanpa bentrok
function require_role($role_required) {
    $portal_role = get_current_portal_role();
    $target_role = $portal_role ?: (is_array($role_required) ? ($role_required[0] ?? 'pengunjung') : $role_required);
    $tab_id = get_current_tab_id();
    $user = null;

    // 1. Cek sesi tab aktif
    if ($tab_id && isset($_SESSION['tabs'][$tab_id]) && is_array($_SESSION['tabs'][$tab_id])) {
        if ($_SESSION['tabs'][$tab_id]['role'] === $target_role) {
            $user = $_SESSION['tabs'][$tab_id];
        }
    }

    // 2. Cek sesi multi_roles
    if (!$user && isset($_SESSION['multi_roles'][$target_role]) && is_array($_SESSION['multi_roles'][$target_role])) {
        $user = $_SESSION['multi_roles'][$target_role];
        if ($tab_id) {
            $_SESSION['tabs'][$tab_id] = $user;
        }
    }

    // 3. Cek sesi umum jika role cocok
    if (!$user && isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === $target_role) {
        $user = [
            'id' => $_SESSION['user_id'],
            'nama' => $_SESSION['user_nama'] ?? 'Pengguna',
            'username' => $_SESSION['user_username'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $target_role
        ];
        if ($tab_id) {
            $_SESSION['tabs'][$tab_id] = $user;
        }
    }

    if ($user) {
        // Sinkronkan data sesi aktif untuk backward compatibility dengan kode lama
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        return;
    }

    // Belum login dengan role ini, arahkan ke login tab tersebut
    set_flash('danger', 'Halaman ini memerlukan login sebagai akun ' . strtoupper($target_role) . '. Silakan masuk.');
    $redir_param = '?role=' . urlencode($target_role);
    if ($tab_id) {
        $redir_param .= '&tab=' . urlencode($tab_id);
    }
    header('Location: ../login.php' . $redir_param);
    exit;
}

// Helper untuk memperbarui data user pada tab aktif (misal saat edit profil)
function update_auth_user($fields = []) {
    $tab_id = get_current_tab_id();
    $role = get_current_portal_role() ?: ($_SESSION['user_role'] ?? 'pengunjung');

    if ($tab_id && isset($_SESSION['tabs'][$tab_id])) {
        foreach ($fields as $k => $v) {
            $_SESSION['tabs'][$tab_id][$k] = $v;
        }
    }
    if (isset($_SESSION['multi_roles'][$role])) {
        foreach ($fields as $k => $v) {
            $_SESSION['multi_roles'][$role][$k] = $v;
        }
    }
    if (isset($fields['nama'])) {
        $_SESSION['user_nama'] = $fields['nama'];
    }
}

