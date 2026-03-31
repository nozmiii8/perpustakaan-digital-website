<?php
// Memulai session agar bisa mengakses data yang ingin dihapus
session_start();

// Menghapus semua variabel session
$_SESSION = array();

// Jika ingin menghapus session cookie (opsional tapi lebih aman)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Menghancurkan session sepenuhnya
session_destroy();

// Mengarahkan pengguna kembali ke halaman login
// Pastikan file login.php ada di folder yang sama dengan logout.php
header("Location: login.php");
exit;
?>