<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['role'])) { header("Location: ../login.php"); exit; }
require_once '../config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: peminjaman.php");
    exit;
}

$id_buku = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM buku WHERE id_buku = '$id_buku'");
$buku = mysqli_fetch_assoc($query);

if (!$buku) { header("Location: peminjaman.php"); exit; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($buku['judul']); ?> - Zanith Libs</title>
    <link rel="icon" type="image/png" href="../assets/logo/logo.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #fcfdfe; 
            color: #1e293b;
        }
        .text-gradient {
            background: linear-gradient(to right, #1e293b, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        /* Custom SweetAlert Style agar selaras dengan Zanith */
        .swal2-popup {
            border-radius: 2.5rem !important;
            padding: 2rem !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .swal2-styled.swal2-confirm {
            background-color: #0f172a !important;
            border-radius: 1rem !important;
            padding: 1rem 2rem !important;
            text-transform: uppercase !important;
            font-weight: 800 !important;
            font-style: italic !important;
            font-size: 0.75rem !important;
            letter-spacing: 0.1em !important;
        }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen">

    <?php include 'include/header.php'; ?>

    <main class="flex-1 p-6 md:p-12 lg:p-16 max-w-7xl mx-auto w-full">
        
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-20">
            
            <div class="w-full lg:w-2/5 shrink-0">
                <div class="sticky top-8">
                    <div class="p-2">
                        <div class="aspect-[3/4.2] rounded-[2.5rem] overflow-hidden shadow-[0_35px_80px_-15px_rgba(0,0,0,0.18)] border-4 border-white">
                            <?php 
                            $gambar_path = "../assets/buku/" . $buku['gambar'];
                            if(!empty($buku['gambar']) && file_exists($gambar_path)): ?>
                                <img src="<?= $gambar_path; ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-50 to-slate-200 text-slate-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <span class="font-black italic tracking-tighter text-3xl uppercase">Zanith Libs</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-between px-8 py-5 bg-white rounded-[2rem] border border-slate-100 shadow-sm">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Availability</p>
                            <span class="text-xs font-black <?= $buku['stok'] > 0 ? 'text-emerald-500' : 'text-rose-500' ?> uppercase italic">
                                <?= $buku['stok'] > 0 ? '● Tersedia' : '✕ Kosong' ?>
                            </span>
                        </div>
                        <div class="text-right border-l pl-8 border-slate-100">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 italic">Total</p>
                            <p class="text-2xl font-black text-slate-800 italic"><?= $buku['stok']; ?> <span class="text-[10px] text-slate-300">UNIT</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-1">
                <div class="mb-8">
                    <span class="inline-block px-6 py-2 bg-blue-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-lg shadow-lg shadow-blue-500/20 italic">
                        <?= htmlspecialchars($buku['kategori']); ?>
                    </span>
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-black text-slate-800 italic uppercase leading-[0.95] tracking-tighter mb-6 text-gradient">
                    <?= htmlspecialchars($buku['judul']); ?>
                </h1>
                
                <p class="text-xl font-bold text-slate-400 uppercase italic tracking-[0.2em] mb-12 flex items-center gap-4">
                    <span class="w-10 h-[2px] bg-blue-600"></span>
                    Author: <span class="text-slate-800"><?= htmlspecialchars($buku['penulis']); ?></span>
                </p>

                <div class="grid grid-cols-2 gap-4 mb-16">
                    <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
                        <span class="text-[8px] font-black text-slate-300 uppercase italic mb-1">Publisher</span>
                        <p class="text-sm font-black text-slate-700 italic uppercase tracking-wider"><?= $buku['penerbit'] ?: 'Zanith Press'; ?></p>
                    </div>
                    <div class="p-6 bg-white rounded-3xl border border-slate-100 shadow-sm">
                        <span class="text-[8px] font-black text-slate-300 uppercase italic mb-1">Year</span>
                        <p class="text-sm font-black text-slate-700 italic uppercase tracking-wider"><?= $buku['tahun_terbit'] ?: '2024'; ?></p>
                    </div>
                </div>

                <div class="mb-20">
                    <h3 class="text-xs font-black text-slate-900 uppercase italic tracking-[0.5em] flex items-center gap-6 mb-8">
                        Sinopsis <span class="h-[1px] flex-1 bg-slate-100"></span>
                    </h3>
                    <p class="text-slate-500 font-medium italic leading-[1.8] text-lg text-justify">
                        <?= nl2br(htmlspecialchars($buku['deskripsi'] ?: 'Informasi deskripsi belum tersedia untuk koleksi ini.')); ?>
                    </p>
                </div>

                <div class="pt-10 border-t border-slate-50 flex flex-col sm:flex-row items-center gap-10">
                    <?php if ($buku['stok'] > 0): ?>
                        <button onclick="konfirmasiPinjam(<?= $buku['id_buku']; ?>, '<?= htmlspecialchars($buku['judul']); ?>')"
                           class="w-full sm:w-auto bg-slate-900 text-white px-14 py-7 rounded-[2rem] text-xs font-black uppercase italic tracking-[0.3em] shadow-[0_25px_50px_-12px_rgba(15,23,42,0.4)] hover:bg-blue-600 hover:-translate-y-2 transition-all active:scale-95 flex items-center justify-center gap-5 group">
                            Pinjam Sekarang
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:translate-x-2 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    <?php else: ?>
                        <div class="w-full sm:w-auto bg-slate-100 text-slate-400 px-14 py-7 rounded-[2rem] text-xs font-black uppercase italic tracking-[0.3em] flex items-center justify-center border border-slate-200">
                            Stok Habis
                        </div>
                    <?php endif; ?>

                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <p class="text-[9px] font-bold uppercase italic text-slate-400 leading-tight">Zanith Security<br><span class="text-slate-300">Verified System</span></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
    function konfirmasiPinjam(id, judul) {
        Swal.fire({
            title: 'KONFIRMASI PINJAM',
            html: `Apakah Anda yakin ingin meminjam buku <br><b style="color:#3b82f6 italic">"${judul}"</b>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'YA, PINJAM SEKARANG',
            cancelButtonText: 'BATAL',
            reverseButtons: true,
            padding: '3em',
            color: '#1e293b',
            background: '#fff',
            backdrop: `rgba(15, 23, 42, 0.4) blur(4px)`
        }).then((result) => {
            if (result.isConfirmed) {
                // Beri efek loading sebelum redirect (opsional tapi keren)
                Swal.fire({
                    title: 'Memproses...',
                    didOpen: () => { Swal.showLoading() },
                    timer: 1000,
                    showConfirmButton: false
                });
                
                setTimeout(() => {
                    window.location.href = `proses_pinjam.php?id=${id}`;
                }, 800);
            }
        })
    }
    </script>

</body>
</html>