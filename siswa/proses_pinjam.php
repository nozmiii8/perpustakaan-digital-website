<?php
session_start();
require_once '../config/database.php';

// 1. Proteksi Halaman (Siswa DAN Admin diperbolehkan akses)
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'siswa' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../login.php");
    exit;
}

$id_buku = $_GET['id'] ?? '';
$id_user = $_SESSION['id_user'] ?? '';
$role_user = $_SESSION['role'] ?? '';
$tgl_pinjam = date('Y-m-d');
$tgl_kembali = date('Y-m-d', strtotime('+7 days'));

$redirect_back = ($role_user === 'admin') ? 'buku.php' : 'peminjaman.php';
$redirect_history = ($role_user === 'admin') ? 'peminjaman.php' : 'riwayat_pinjam.php';

if (empty($id_buku) || empty($id_user)) {
    header("Location: $redirect_back");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Securing Transaction... - Zanith Libs</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f8fafc; 
            overflow: hidden;
        }
        /* Custom SweetAlert agar lebih modern */
        .swal2-popup { 
            border-radius: 3rem !important; 
            padding: 3rem 2rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1) !important;
        }
        .swal2-title { 
            font-weight: 900 !important; 
            text-transform: uppercase; 
            font-style: italic; 
            letter-spacing: -0.05em;
            color: #0f172a !important;
        }
        .swal2-confirm { 
            border-radius: 1.5rem !important; 
            font-weight: 800 !important; 
            text-transform: uppercase; 
            font-style: italic; 
            font-size: 10px !important; 
            letter-spacing: 0.2em !important; 
            padding: 1.2rem 2.5rem !important; 
        }
        /* Loader Animation */
        .loader-bar {
            width: 0%;
            height: 4px;
            background: #2563eb;
            position: fixed;
            top: 0;
            left: 0;
            transition: width 2s linear;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen">

    <div class="loader-bar" id="topLoader"></div>

    <div class="text-center animate-pulse">
        <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-6"></div>
        <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.4em] italic mb-2">System Processing</p>
        <h2 class="text-xl font-black text-slate-800 uppercase italic tracking-tighter">Securing your book...</h2>
    </div>

<?php
// --- VALIDASI & TRANSAKSI ---

$stmt_buku = $conn->prepare("SELECT judul, stok FROM buku WHERE id_buku = ?");
$stmt_buku->bind_param("s", $id_buku);
$stmt_buku->execute();
$data_buku = $stmt_buku->get_result()->fetch_assoc();

// Error: Stok Habis
if (!$data_buku || $data_buku['stok'] <= 0) {
    showError("OUT OF STOCK", "Maaf, unit buku sudah tidak tersedia di database.", $redirect_back);
}

// Cek Limit
$limit_maks = ($role_user === 'admin') ? 10 : 3;
$stmt_limit = $conn->prepare("SELECT COUNT(*) as total FROM peminjaman WHERE id_user = ? AND status = 'dipinjam'");
$stmt_limit->bind_param("s", $id_user);
$stmt_limit->execute();
$count_limit = $stmt_limit->get_result()->fetch_assoc()['total'];

if ($count_limit >= $limit_maks) {
    showError("LIMIT OVERFLOW", "Batas pinjam maksimal ($limit_maks buku) telah tercapai.", $redirect_back);
}

// Cek Double
$stmt_double = $conn->prepare("SELECT id_peminjaman FROM peminjaman WHERE id_user = ? AND id_buku = ? AND status = 'dipinjam'");
$stmt_double->bind_param("ss", $id_user, $id_buku);
$stmt_double->execute();
if ($stmt_double->get_result()->num_rows > 0) {
    showError("DUPLICATE ENTRY", "Kamu sedang meminjam buku ini di sesi aktif.", $redirect_back);
}

// Eksekusi
$conn->begin_transaction();
try {
    $stmt_insert = $conn->prepare("INSERT INTO peminjaman (id_user, id_buku, tanggal_pinjam, tanggal_kembali, status) VALUES (?, ?, ?, ?, 'dipinjam')");
    $stmt_insert->bind_param("ssss", $id_user, $id_buku, $tgl_pinjam, $tgl_kembali);
    $stmt_insert->execute();

    $stmt_update = $conn->prepare("UPDATE buku SET stok = stok - 1 WHERE id_buku = ?");
    $stmt_update->bind_param("s", $id_buku);
    $stmt_update->execute();

    $conn->commit();

    // Tampilkan Berhasil dengan Countdown
    echo "<script>
        document.getElementById('topLoader').style.width = '100%';
        
        let timerInterval;
        Swal.fire({
            icon: 'success',
            title: 'ACCESS GRANTED',
            html: `
                <p class='text-sm text-slate-500 font-medium mb-4'>Buku <b>" . htmlspecialchars($data_buku['judul']) . "</b> siap dibaca!</p>
                <div class='bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-4'>
                    <p class='text-[9px] font-black text-slate-400 uppercase italic'>Auto Redirect In</p>
                    <b id='countdown' class='text-2xl font-black text-blue-600 italic'>3</b>
                </div>
                <small class='text-[10px] font-bold text-slate-300 uppercase tracking-widest italic'>Return Date: $tgl_kembali</small>
            `,
            showConfirmButton: true,
            confirmButtonText: 'OPEN HISTORY NOW',
            confirmButtonColor: '#0f172a',
            timer: 3000,
            timerProgressBar: true,
            didOpen: () => {
                const b = Swal.getHtmlContainer().querySelector('#countdown');
                timerInterval = setInterval(() => {
                    b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                }, 100);
            },
            willClose: () => {
                clearInterval(timerInterval);
            }
        }).then((result) => {
            window.location.href = '$redirect_history';
        });
    </script>";

} catch (Exception $e) {
    $conn->rollback();
    showError("SYSTEM BREACH", "Gagal melakukan penulisan data ke server.", $redirect_back);
}

function showError($title, $text, $url) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: '$title',
            text: '$text',
            confirmButtonText: 'RETRY ACCESS',
            confirmButtonColor: '#ef4444'
        }).then(() => { window.location.href = '$url'; });
    </script>";
    exit;
}
?>
</body>
</html>