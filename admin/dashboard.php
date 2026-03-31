<?php
session_start();

// 1. Proteksi Halaman Admin & Keamanan Session
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

// Ambil Nama Admin dengan fallback
$nama_admin = htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Administrator');

/** * 2. Statistik Data (Optimized Queries)
 * Menggunakan alias untuk kemudahan pembacaan data
 */
$stats_queries = [
    'buku' => "SELECT COUNT(*) as total FROM buku",
    'siswa' => "SELECT COUNT(*) as total FROM users WHERE role = 'siswa'",
    'aktif' => "SELECT COUNT(*) as total FROM peminjaman WHERE status = 'dipinjam'",
    'total_pjm' => "SELECT COUNT(*) as total FROM peminjaman"
];

$total_buku      = mysqli_fetch_assoc(mysqli_query($conn, $stats_queries['buku']))['total'];
$total_siswa     = mysqli_fetch_assoc(mysqli_query($conn, $stats_queries['siswa']))['total'];
$pinjam_aktif    = mysqli_fetch_assoc(mysqli_query($conn, $stats_queries['aktif']))['total'];
$total_transaksi = mysqli_fetch_assoc(mysqli_query($conn, $stats_queries['total_pjm']))['total'];

// Kalkulasi Persentase untuk Progres Bar
$persen_aktif = $total_transaksi > 0 ? round(($pinjam_aktif / $total_transaksi) * 100) : 0;
$persen_selesai = 100 - $persen_aktif;

/**
 * 3. Ambil 5 Aktivitas Terbaru (Join Table)
 */
$query_terbaru = "SELECT p.*, b.judul, u.nama_lengkap 
                  FROM peminjaman p 
                  JOIN buku b ON p.id_buku = b.id_buku 
                  JOIN users u ON p.id_user = u.id_user 
                  ORDER BY p.id_peminjaman DESC LIMIT 5";
$transaksi_terbaru = mysqli_query($conn, $query_terbaru);
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Zanith Libs</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f8fafc; 
            color: #1e293b; 
        }
        /* Custom scrollbar untuk elemen tabel mobile */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .card-shadow { box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.03), 0 8px 10px -6px rgb(0 0 0 / 0.03); }
        
        /* Animasi halus untuk hover */
        .glass-morph {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="flex flex-col md:flex-row min-h-screen relative overflow-x-hidden">

    <?php include 'include/header.php'; ?>

    <main class="flex-1 p-4 md:p-10 lg:p-12 w-full flex flex-col items-center">
        
        <header class="mb-10 mt-6 md:mt-0 flex flex-col items-center text-center w-full max-w-4xl">
            <p class="text-[9px] md:text-[10px] font-black text-blue-600 uppercase tracking-[0.4em] italic mb-3">
                Central Command Center
            </p>
            <h2 class="text-3xl md:text-5xl lg:text-6xl font-black text-slate-900 tracking-tighter italic uppercase leading-tight">
                Control Panel
            </h2>
            
            <div class="flex flex-col items-center gap-4 mt-6">
                <p class="text-slate-400 font-bold italic text-[10px] md:text-xs uppercase tracking-[0.2em]">
                    Operator: <span class="text-slate-800 underline decoration-blue-500/30 underline-offset-8 decoration-2"><?= $nama_admin; ?></span>
                </p>
                
                <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-full border border-slate-100 shadow-sm glass-morph">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[9px] font-black text-emerald-600 uppercase italic tracking-widest">System Online</span>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 mb-12 w-full max-w-6xl">
            
            <div class="bg-white p-8 md:p-10 rounded-[2.5rem] md:rounded-[3rem] border border-slate-100 card-shadow relative overflow-hidden group transition-all hover:-translate-y-2">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic mb-1">Library Stock</p>
                    <p class="text-5xl md:text-6xl font-black text-slate-900 tracking-tighter"><?= number_format($total_buku); ?></p>
                    <a href="buku.php" class="inline-block mt-4 text-[9px] font-black text-blue-600 uppercase tracking-widest hover:text-slate-900 transition-all underline underline-offset-4 decoration-blue-200">Manage Collection →</a>
                </div>
                <div class="absolute -right-6 -bottom-6 opacity-[0.03] group-hover:opacity-10 transition-all text-slate-900 rotate-12 hidden sm:block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" />
                    </svg>
                </div>
            </div>

            <div class="bg-white p-8 md:p-10 rounded-[2.5rem] md:rounded-[3rem] border border-slate-100 card-shadow relative overflow-hidden group transition-all hover:-translate-y-2">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] italic mb-1">Total Members</p>
                    <p class="text-5xl md:text-6xl font-black text-slate-900 tracking-tighter"><?= number_format($total_siswa); ?></p>
                    <p class="mt-4 text-[9px] font-black text-slate-300 uppercase tracking-widest italic">Registered Students</p>
                </div>
                <div class="absolute -right-6 -bottom-6 opacity-[0.03] group-hover:opacity-10 transition-all text-slate-900 rotate-12 hidden sm:block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>

            <div class="bg-blue-600 p-8 md:p-10 rounded-[2.5rem] md:rounded-[3rem] shadow-2xl shadow-blue-200 relative overflow-hidden group transition-all hover:bg-slate-900 hover:-translate-y-2 sm:col-span-2 lg:col-span-1">
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-blue-100 uppercase tracking-[0.2em] italic mb-1">Active Loans</p>
                    <p class="text-5xl md:text-6xl font-black text-white tracking-tighter"><?= number_format($pinjam_aktif); ?></p>
                    <a href="peminjaman.php" class="inline-block mt-4 text-[9px] font-black text-blue-200 uppercase tracking-widest hover:text-white transition-colors">View Transactions →</a>
                </div>
                <div class="absolute -right-6 -bottom-6 opacity-20 transition-all hidden sm:block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] md:rounded-[3.5rem] border border-slate-100 overflow-hidden card-shadow w-full max-w-6xl">
            
            <div class="p-8 md:p-12 border-b border-slate-50 flex flex-col lg:flex-row justify-between items-center gap-10 bg-slate-50/30">
                <div class="text-center lg:text-left">
                    <h3 class="font-black text-slate-900 uppercase italic tracking-tighter text-2xl leading-none">Sirkulasi Data</h3>
                    <p class="text-[9px] font-bold text-slate-400 uppercase italic mt-2 tracking-widest">Live Transaction Analytics</p>
                </div>
                
                <div class="w-full lg:w-96">
                    <div class="flex justify-between items-end mb-3 px-1">
                        <span class="text-[10px] font-black text-slate-800 uppercase italic tracking-widest">Aktivitas Pinjam</span>
                        <span class="text-xl font-black text-blue-600 italic tracking-tighter"><?= $persen_aktif; ?>%</span>
                    </div>
                    <div class="w-full bg-emerald-400 rounded-full h-3 overflow-hidden flex shadow-inner border border-white">
                        <div class="bg-blue-600 h-full transition-all duration-1000 border-r-2 border-white/20" 
                             style="width: <?= $persen_aktif; ?>%"></div>
                    </div>
                    <div class="flex justify-center lg:justify-start gap-6 mt-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            <span class="text-[9px] font-black text-slate-500 uppercase italic">Dipinjam (<?= $pinjam_aktif; ?>)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                            <span class="text-[9px] font-black text-slate-500 uppercase italic">Selesai (<?= $total_transaksi - $pinjam_aktif; ?>)</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left min-w-[700px]">
                    <thead>
                        <tr class="bg-white uppercase italic text-[9px] text-slate-400 tracking-[0.2em] border-b border-slate-50">
                            <th class="px-10 py-7">Peminjam</th>
                            <th class="px-10 py-7">Informasi Buku</th>
                            <th class="px-10 py-7">Waktu Pinjam</th>
                            <th class="px-10 py-7 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php if(mysqli_num_rows($transaksi_terbaru) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($transaksi_terbaru)): ?>
                            <tr class="hover:bg-slate-50/50 transition-all group">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors border-4 border-white shadow-sm text-[11px] font-black italic uppercase">
                                            <?= substr($row['nama_lengkap'], 0, 1); ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-800 italic uppercase tracking-tighter leading-none"><?= $row['nama_lengkap']; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <p class="text-[10px] font-bold text-slate-500 italic truncate max-w-[200px] uppercase"><?= $row['judul']; ?></p>
                                </td>
                                <td class="px-10 py-6">
                                    <span class="text-[10px] font-black text-slate-800 uppercase italic"><?= date('d M Y', strtotime($row['tanggal_pinjam'])); ?></span>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <?php if ($row['status'] == 'dipinjam'): ?>
                                        <span class="bg-amber-50 text-amber-500 text-[8px] font-black px-5 py-2 rounded-full uppercase tracking-widest border border-amber-100 italic">Aktif</span>
                                    <?php else: ?>
                                        <span class="bg-emerald-50 text-emerald-500 text-[8px] font-black px-5 py-2 rounded-full uppercase tracking-widest border border-emerald-100 italic">Returned</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <p class="text-slate-300 italic font-black uppercase text-[10px] tracking-[0.3em]">No Activity Logged Today</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="p-8 bg-slate-50/50 text-center border-t border-slate-50">
                <a href="peminjaman.php" class="text-[10px] font-black text-slate-400 hover:text-blue-600 uppercase tracking-[0.3em] italic transition-all">
                    Access Full Database →
                </a>
            </div>
        </div>

    </main>

    <script>
        // Tambahkan interaksi JS di sini jika perlu (misal: toggle sidebar)
    </script>
</body>
</html>