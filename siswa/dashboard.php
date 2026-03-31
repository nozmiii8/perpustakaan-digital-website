<?php
session_start();

// Proteksi: Hanya boleh diakses oleh 'siswa' atau 'admin'
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['siswa', 'admin'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

// Menentukan identitas user
$nama_user = $_SESSION['nama_lengkap'] ?? $_SESSION['nama'] ?? 'User';
$id_user   = $_SESSION['id_user'];
$user_role = $_SESSION['role'];

// Statistik Data
$query_pinjam = "SELECT COUNT(*) as total FROM peminjaman WHERE id_user = '$id_user' AND status = 'dipinjam'";
$res_pinjam = mysqli_query($conn, $query_pinjam);
$count_pinjam = mysqli_fetch_assoc($res_pinjam)['total'] ?? 0;

$count_buku_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM buku");
$count_buku = mysqli_fetch_assoc($count_buku_res)['total'] ?? 0;

// Ambil 4 buku terbaru
$buku_terbaru = mysqli_query($conn, "SELECT * FROM buku ORDER BY id_buku DESC LIMIT 4");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Zanith Libs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="../assets/logo/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); }
        .book-card:hover .book-image { transform: scale(1.05) translateY(-5px); }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .transition-custom { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen antialiased">

    <?php include 'include/header.php'; ?>

    <main class="flex-1 p-6 md:p-10 lg:p-14">
        
        <section class="relative bg-slate-900 rounded-[2.5rem] md:rounded-[4rem] p-8 md:p-20 mb-12 text-white shadow-2xl overflow-hidden flex flex-col items-center md:items-start text-center md:text-left">
            <div class="relative z-10 max-w-2xl">
                <span class="bg-blue-500/20 text-blue-300 px-5 py-2 rounded-full text-[11px] font-bold tracking-[0.15em] uppercase mb-8 inline-block border border-blue-500/30">
                    Selamat Datang, <?= htmlspecialchars(explode(' ', $nama_user)[0]) ?>!
                </span>
                <h2 class="text-5xl md:text-7xl font-extrabold leading-[1.1] tracking-tighter mb-8">
                    Zanith <span class="text-blue-500">Digital</span> <br>Library.
                </h2>
                <p class="text-slate-400 font-medium text-lg leading-relaxed mb-10 max-w-md mx-auto md:mx-0">
                    Eksplorasi dunia lewat kata-kata. Akses koleksi buku terbaik kami secara digital di mana saja.
                </p>
                <a href="peminjaman.php" class="inline-flex items-center bg-blue-600 hover:bg-blue-500 text-white px-10 py-4 rounded-2xl font-bold transition-custom hover:shadow-xl hover:shadow-blue-500/40 active:scale-95 group">
                    Jelajahi Katalog
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-3 group-hover:translate-x-2 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
            
            <div class="absolute top-0 right-0 -mr-24 -mt-24 w-96 h-96 bg-blue-600/20 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-0 right-10 opacity-10 hidden lg:block">
                 <svg width="350" height="350" viewBox="0 0 24 24" fill="white"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </div>
        </section>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" /></svg>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-1">Koleksi Buku</p>
                <p class="text-4xl font-extrabold text-slate-900"><?= number_format($count_buku); ?></p>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col items-center text-center">
                <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-1">Sedang Pinjam</p>
                <p class="text-4xl font-extrabold text-slate-900"><?= number_format($count_pinjam); ?></p>
            </div>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4 text-center md:text-left">
            <div>
                <h3 class="text-3xl font-extrabold text-slate-900">Buku Terbaru</h3>
                <p class="text-slate-500 font-medium">Rekomendasi bacaan spesial untuk hari ini.</p>
            </div>
            <a href="peminjaman.php" class="text-sm font-bold text-blue-600 bg-blue-50 px-6 py-3 rounded-xl hover:bg-blue-100 transition-colors flex items-center">
                Lihat Katalog Lengkap <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php while($row = mysqli_fetch_assoc($buku_terbaru)): ?>
            <div class="group bg-white p-5 rounded-[3rem] border border-slate-100 shadow-sm hover:shadow-2xl transition-custom book-card flex flex-col h-full items-center text-center">
                
                <div class="w-full aspect-[3/4] rounded-[2.2rem] mb-6 overflow-hidden bg-slate-50 shadow-inner">
                    <?php if(!empty($row['gambar']) && file_exists("../assets/buku/".$row['gambar'])): ?>
                        <img src="../assets/buku/<?= $row['gambar']; ?>" class="book-image w-full h-full object-cover transition-custom">
                    <?php else: ?>
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <span class="text-[9px] uppercase font-bold tracking-widest">No Cover</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="flex-1 flex flex-col items-center w-full">
                    <div class="mb-4">
                        <?php if($row['stok'] > 0): ?>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-4 py-1.5 rounded-full uppercase tracking-tight border border-emerald-100">
                                Tersedia: <?= $row['stok'] ?>
                            </span>
                        <?php else: ?>
                            <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-4 py-1.5 rounded-full uppercase tracking-tight border border-rose-100">
                                Stok Kosong
                            </span>
                        <?php endif; ?>
                    </div>

                    <h4 class="font-extrabold text-slate-900 text-xl leading-tight truncate w-full mb-1 group-hover:text-blue-600 transition-colors">
                        <?= htmlspecialchars($row['judul']); ?>
                    </h4>
                    <p class="text-[11px] font-bold text-slate-400 mb-4 uppercase tracking-widest italic">
                        <?= htmlspecialchars($row['penulis']); ?>
                    </p>
                    
                    <p class="text-sm text-slate-500 leading-relaxed line-clamp-2 mb-8">
                        <?= htmlspecialchars($row['deskripsi']) ?: 'Temukan wawasan dan inspirasi baru dalam koleksi buku terbaru kami ini.' ?>
                    </p>
                    
                    <a href="peminjaman.php?id=<?= $row['id_buku']; ?>" class="mt-auto w-full bg-slate-900 hover:bg-blue-600 text-white py-4 rounded-[1.5rem] text-[11px] font-bold uppercase tracking-[0.2em] transition-custom shadow-lg active:scale-95">
                        Lihat & Pinjam
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

    </main>

</body>
</html>