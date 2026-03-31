<?php
session_start();
require_once '../config/database.php';

// 1. Proteksi Halaman
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['siswa', 'admin'])) {
    header("Location: ../login.php");
    exit;
}

$id_pinjam = mysqli_real_escape_string($conn, $_GET['id'] ?? '');
$role_user = $_SESSION['role'];
$tgl_kembali_real = date('Y-m-d');

// Tentukan arah pulang (Admin ke kelola peminjaman, Siswa ke riwayat)
$redirect_history = ($role_user === 'admin') ? 'peminjaman.php' : 'riwayat_pinjam.php';

if (empty($id_pinjam)) {
    header("Location: $redirect_history");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoring Stock... - Zanith Libs</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; overflow: hidden; }
        .swal2-popup { border-radius: 3rem !important; padding: 3rem 2rem !important; }
        .swal2-title { font-weight: 900 !important; text-transform: uppercase; font-style: italic; letter-spacing: -0.05em; }
        .swal2-confirm { border-radius: 1.5rem !important; font-weight: 800 !important; text-transform: uppercase; font-style: italic; font-size: 10px !important; letter-spacing: 0.2em !important; padding: 1.2rem 2.5rem !important; }
        
        .loader-bar {
            width: 0%; height: 4px; background: #10b981; /* Hijau untuk kembalikan */
            position: fixed; top: 0; left: 0; transition: width 1.5s ease-in-out;
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen">

    <div class="loader-bar" id="topLoader"></div>

    <div class="text-center">
        <div class="relative w-20 h-20 mx-auto mb-8">
            <div class="absolute inset-0 border-4 border-emerald-100 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
        </div>
        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] italic mb-2">Inventory Sync</p>
        <h2 class="text-xl font-black text-slate-800 uppercase italic tracking-tighter">Validating Return...</h2>
    </div>

<?php
// --- LOGIKA DATABASE ---

$query_cari = mysqli_query($conn, "SELECT p.id_buku, b.judul 
                                   FROM peminjaman p 
                                   JOIN buku b ON p.id_buku = b.id_buku 
                                   WHERE p.id_peminjaman = '$id_pinjam' AND p.status = 'dipinjam'");
$data_pinjam = mysqli_fetch_assoc($query_cari);

if ($data_pinjam) {
    $id_buku = $data_pinjam['id_buku'];
    $judul_buku = $data_pinjam['judul'];

    mysqli_begin_transaction($conn);
    try {
        // A. Update Status
        mysqli_query($conn, "UPDATE peminjaman SET status = 'kembali', tanggal_kembali = '$tgl_kembali_real' WHERE id_peminjaman = '$id_pinjam'");
        
        // B. Restore Stok
        mysqli_query($conn, "UPDATE buku SET stok = stok + 1 WHERE id_buku = '$id_buku'");

        mysqli_commit($conn);

        echo "<script>
            document.getElementById('topLoader').style.width = '100%';
            let timerInterval;
            Swal.fire({
                icon: 'success',
                title: 'RETURN SUCCESS',
                html: `
                    <p class='text-sm text-slate-500 font-medium mb-4'>Buku <b>" . htmlspecialchars($judul_buku) . "</b> telah kembali ke rak.</p>
                    <div class='bg-emerald-50 p-4 rounded-2xl border border-emerald-100 mb-2'>
                        <p class='text-[9px] font-black text-emerald-400 uppercase italic'>Database Synced In</p>
                        <b id='countdown' class='text-2xl font-black text-emerald-600 italic'>2</b>
                    </div>
                `,
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                didOpen: () => {
                    const b = Swal.getHtmlContainer().querySelector('#countdown');
                    timerInterval = setInterval(() => {
                        b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                    }, 100);
                },
                willClose: () => { clearInterval(timerInterval); }
            }).then(() => { window.location.href = '$redirect_history'; });
        </script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        showError("DATABASE ERROR", "Gagal melakukan sinkronisasi stok.", $redirect_history);
    }
} else {
    showError("INVALID ACTION", "Data peminjaman tidak ditemukan atau sudah dikembalikan.", $redirect_history);
}

function showError($title, $text, $url) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: '$title',
            text: '$text',
            confirmButtonText: 'BACK TO DASHBOARD',
            confirmButtonColor: '#ef4444'
        }).then(() => { window.location.href = '$url'; });
    </script>";
    exit;
}
?>
</body>
</html>