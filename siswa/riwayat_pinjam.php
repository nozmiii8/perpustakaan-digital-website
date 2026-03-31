<?php
session_start();
require_once '../config/database.php';

// 1. Proteksi Halaman
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['siswa', 'admin'])) {
    header("Location: ../login.php");
    exit;
}

// 2. Identitas User & Inisialisasi Variabel
$nama_user = $_SESSION['nama_lengkap'] ?? $_SESSION['nama'] ?? 'User';
$id_user   = $_SESSION['id_user'] ?? 0;
$tgl_sekarang = date('Y-m-d');

// 3. Eksekusi Query (Pastikan variabel ini selalu ada)
$sql = "SELECT p.*, b.judul, b.penulis, b.gambar 
        FROM peminjaman p 
        JOIN buku b ON p.id_buku = b.id_buku 
        WHERE p.id_user = ? 
        ORDER BY p.id_peminjaman DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_user);
$stmt->execute();
$query_riwayat = $stmt->get_result(); // Variabel ini sekarang dijamin terdefinisi
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Archive - Zanith Libs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="../assets/logo/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfdfe; color: #1e293b; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen">

    <?php include 'include/header.php'; ?>

    <main class="flex-1 transition-all pt-24 md:pt-0">
        
        <section class="py-10 md:py-16 px-5 md:px-14 border-b border-slate-50 bg-white">
            <div class="max-w-4xl mx-auto text-center">
                <div class="mb-8">
                    <span class="text-[9px] md:text-[10px] font-black text-blue-600 bg-blue-50 px-5 py-2 rounded-full uppercase tracking-[0.4em] italic border border-blue-100">
                        Member Archive
                    </span>
                    <h2 class="text-5xl md:text-7xl font-black text-slate-900 tracking-tighter italic uppercase leading-none mt-6">
                        Aktivitas <br> <span class="text-blue-600">Literasi.</span>
                    </h2>
                    <p class="text-slate-400 text-[10px] md:text-[11px] mt-4 font-bold italic uppercase tracking-widest">
                        Welcome back, <span class="text-slate-800"><?= explode(' ', $nama_user)[0]; ?></span>.
                    </p>
                </div>

                <div class="relative max-w-2xl mx-auto mb-10 group">
                    <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none text-slate-300 group-focus-within:text-blue-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" id="live-search" placeholder="Cari riwayat pinjaman..." 
                           class="w-full bg-slate-50 border-2 border-slate-100 py-4 md:py-5 pl-14 pr-10 rounded-[1.8rem] md:rounded-[2.2rem] shadow-sm focus:bg-white focus:border-blue-600 focus:ring-0 transition-all font-bold text-xs italic outline-none">
                </div>

                <div class="flex items-center justify-center gap-2 overflow-x-auto no-scrollbar pb-2">
                    <button onclick="filterStatus('all')" class="filter-btn active bg-blue-600 text-white px-6 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest italic transition-all shrink-0">Semua</button>
                    <button onclick="filterStatus('dipinjam')" class="filter-btn bg-slate-100 text-slate-400 px-6 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest italic transition-all shrink-0">Dipinjam</button>
                    <button onclick="filterStatus('kembali')" class="filter-btn bg-slate-100 text-slate-400 px-6 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest italic transition-all shrink-0">Kembali</button>
                </div>
            </div>
        </section>

        <div class="p-5 md:p-14 max-w-7xl mx-auto">
            <div id="riwayat-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
                <?php if ($query_riwayat && $query_riwayat->num_rows > 0): ?>
                    <?php while($row = $query_riwayat->fetch_assoc()): 
                        $is_late = ($row['status'] == 'dipinjam' && $tgl_sekarang > $row['tanggal_kembali']);
                    ?>
                    <div class="riwayat-card bg-white p-7 rounded-[2.8rem] border border-slate-100 shadow-xl shadow-slate-200/40 relative group" 
                         data-status="<?= $row['status']; ?>" 
                         data-judul="<?= strtolower($row['judul']); ?>">
                        
                        <?php if($is_late): ?>
                            <div class="absolute top-0 right-0 bg-rose-500 text-white text-[7px] font-black px-4 py-2 uppercase italic rounded-bl-2xl animate-pulse">Overdue</div>
                        <?php endif; ?>

                        <div class="flex gap-5 mb-6">
                            <img src="../assets/buku/<?= $row['gambar'] ?: 'default.jpg'; ?>" class="w-20 h-28 object-cover rounded-2xl shadow-md border border-slate-50">
                            <div class="flex-1 pt-2">
                                <h4 class="font-black text-slate-800 text-base uppercase tracking-tight leading-tight italic line-clamp-2"><?= $row['judul']; ?></h4>
                                <p class="text-[9px] text-slate-400 font-bold italic mt-1 uppercase tracking-widest"><?= $row['penulis']; ?></p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 bg-slate-50/50 p-4 rounded-2xl mb-6 border border-slate-50">
                            <div class="text-center">
                                <p class="text-[7px] font-black text-slate-300 uppercase italic">Pinjam</p>
                                <p class="text-[10px] font-black text-slate-600 italic"><?= date('d/m/y', strtotime($row['tanggal_pinjam'])); ?></p>
                            </div>
                            <div class="text-center border-l border-slate-200">
                                <p class="text-[7px] font-black text-slate-300 uppercase italic">Batas</p>
                                <p class="text-[10px] font-black <?= $is_late ? 'text-rose-500' : 'text-slate-600' ?> italic"><?= date('d/m/y', strtotime($row['tanggal_kembali'])); ?></p>
                            </div>
                        </div>

                        <?php if ($row['status'] == 'dipinjam'): ?>
                            <button onclick="konfirmasiKembali(<?= $row['id_peminjaman']; ?>, '<?= addslashes($row['judul']); ?>')" 
                                    class="w-full bg-slate-900 text-white py-4 rounded-2xl text-[9px] font-black uppercase italic tracking-widest shadow-lg hover:bg-blue-600 transition-all active:scale-95">
                                Kembalikan
                            </button>
                        <?php else: ?>
                            <div class="w-full bg-emerald-50 text-emerald-600 py-3 rounded-2xl text-[9px] font-black uppercase italic tracking-widest text-center border border-emerald-100">
                                Sudah Kembali
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-full py-20 text-center font-black text-slate-200 italic uppercase tracking-tighter text-4xl opacity-30">Archive Empty.</div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script>
        // LIVE SEARCH & FILTER LOGIC
        const searchInput = document.getElementById('live-search');
        const cards = document.querySelectorAll('.riwayat-card');
        const filterBtns = document.querySelectorAll('.filter-btn');

        function updateDisplay() {
            const searchTerm = searchInput.value.toLowerCase();
            const activeFilter = document.querySelector('.filter-btn.active').getAttribute('onclick').match(/'([^']+)'/)[1];

            cards.forEach(card => {
                const judul = card.getAttribute('data-judul');
                const status = card.getAttribute('data-status');
                
                const matchesSearch = judul.includes(searchTerm);
                const matchesFilter = (activeFilter === 'all' || status === activeFilter);

                card.style.display = (matchesSearch && matchesFilter) ? "block" : "none";
            });
        }

        searchInput.addEventListener('input', updateDisplay);

        function filterStatus(status) {
            filterBtns.forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white', 'active');
                btn.classList.add('bg-slate-100', 'text-slate-400');
            });
            event.target.classList.add('bg-blue-600', 'text-white', 'active');
            event.target.classList.remove('bg-slate-100', 'text-slate-400');
            updateDisplay();
        }

        function konfirmasiKembali(id, judul) {
            Swal.fire({
                title: 'SELESAI PINJAM?',
                html: `Ingin mengembalikan buku<br><b class="text-blue-600 italic">"${judul}"</b>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'YA, SELESAI',
                confirmButtonColor: '#0f172a',
                customClass: { popup: 'rounded-[2.5rem]', confirmButton: 'rounded-xl text-[10px] font-black uppercase italic' }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `proses_kembali.php?id=${id}`;
                }
            })
        }
    </script>
</body>
</html>