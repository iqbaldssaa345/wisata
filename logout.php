<?php
require_once __DIR__ . '/koneksi.php';

$role = sanitize($conn, $_GET['role'] ?? '');
$tab = sanitize($conn, $_GET['tab'] ?? $_COOKIE['wisata_active_tab'] ?? '');
$all = isset($_GET['all']) && $_GET['all'] == '1';

if ($all) {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    setcookie('wisata_active_tab', '', time() - 42000, '/');
    session_destroy();
    session_start();
    set_flash('info', 'Anda telah berhasil keluar dari seluruh sesi. Terima kasih.');
    header('Location: login.php');
    exit;
}

// 1. Logout spesifik per-tab
if (!empty($tab) && isset($_SESSION['tabs'][$tab])) {
    $logged_out_role = $_SESSION['tabs'][$tab]['role'] ?? '';
    unset($_SESSION['tabs'][$tab]);

    // Cek apakah masih ada tab lain dengan role ini
    $still_has_role = false;
    if (!empty($_SESSION['tabs'])) {
        foreach ($_SESSION['tabs'] as $t) {
            if (($t['role'] ?? '') === $logged_out_role) {
                $still_has_role = true;
                break;
            }
        }
    }
    if (!$still_has_role && isset($_SESSION['multi_roles'][$logged_out_role])) {
        unset($_SESSION['multi_roles'][$logged_out_role]);
    }

    set_flash('info', 'Anda telah keluar dari akun pada tab ini. Sesi di tab lainnya tetap aman.');
    header('Location: login.php');
    exit;
}

// 2. Logout spesifik per-role tab
if (!empty($role) && isset($_SESSION['multi_roles'][$role])) {
    unset($_SESSION['multi_roles'][$role]);
    if (!empty($_SESSION['tabs'])) {
        foreach ($_SESSION['tabs'] as $k => $t) {
            if (($t['role'] ?? '') === $role) {
                unset($_SESSION['tabs'][$k]);
            }
        }
    }
    set_flash('info', 'Anda telah keluar dari peran ' . strtoupper($role) . '. Sesi di tab peran lain tetap aktif.');
    header('Location: login.php?role=' . urlencode($role));
    exit;
}

// 3. Logout total default
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
setcookie('wisata_active_tab', '', time() - 42000, '/');
session_destroy();
session_start();
set_flash('info', 'Anda telah berhasil keluar dari seluruh sesi. Terima kasih.');
header('Location: login.php');
exit;

