<div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[60] hidden transition-opacity duration-300"></div>

<aside id="sidebar" class="fixed md:sticky top-0 left-0 h-screen w-72 bg-white border-r border-slate-100 p-6 flex flex-col z-[70] -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
    
    <div class="flex items-center justify-between mb-10 px-2 shrink-0">
        <div class="flex items-center gap-3">
            <img src="../assets/logo/logo.png" alt="Logo" class="w-9 h-9 object-contain">
            <span class="font-extrabold text-xl tracking-tighter text-slate-800 italic uppercase">Zanith Libary.</span>
        </div>
        <button onclick="toggleSidebar()" class="md:hidden text-slate-400 p-2 hover:bg-slate-50 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <nav class="flex-1 space-y-1.5 overflow-y-auto no-scrollbar pr-1">
        <?php 
            $current_page = basename($_SERVER['PHP_SELF']);
            // Deteksi folder saat ini untuk menentukan path
            $is_admin_folder = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
        ?>
        
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-4 px-4 italic">Navigation</p>

        <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-slate-500 hover:bg-slate-50' ?> flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold transition-all italic text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            Beranda
        </a>

        <?php if ($is_admin_folder): ?>
            <a href="peminjaman.php" class="<?= $current_page == 'peminjaman.php' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-slate-500 hover:bg-slate-50' ?> flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold transition-all italic text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                Sirkulasi
            </a>
            <a href="anggota.php" class="<?= $current_page == 'anggota.php' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-slate-500 hover:bg-slate-50' ?> flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold transition-all italic text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" /></svg>
                Data Anggota
            </a>
        <?php else: ?>
            <a href="peminjaman.php" class="<?= $current_page == 'peminjaman.php' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-slate-500 hover:bg-slate-50' ?> flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold transition-all italic text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                Katalog Buku
            </a>
            <a href="riwayat_pinjam.php" class="<?= $current_page == 'riwayat_pinjam.php' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-slate-500 hover:bg-slate-50' ?> flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold transition-all italic text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Riwayat Pinjam
            </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="pt-6">
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] mb-4 px-4 italic border-t border-slate-50 pt-4">Control Panel</p>
                
                <?php if ($is_admin_folder): ?>
                    <a href="../siswa/dashboard.php" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold text-slate-600 bg-slate-50 hover:bg-blue-600 hover:text-white transition-all group italic text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Dashboard Siswa
                    </a>
                <?php else: ?>
                    <a href="../admin/dashboard.php" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl font-bold text-slate-600 bg-amber-50 hover:bg-slate-900 hover:text-white transition-all group italic text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
                        Dashboard Admin
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </nav>

    <div class="mt-auto border-t border-slate-50 pt-6 shrink-0">
        <div class="bg-slate-50/80 p-4 rounded-[2rem] flex items-center gap-3 mb-3 border border-slate-100">
            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center font-black text-white border-2 border-white shadow-sm italic shrink-0">
                <?= strtoupper(substr($nama_user ?? 'U', 0, 1)); ?>
            </div>
            <div class="overflow-hidden">
                <p class="text-[11px] font-black text-slate-800 truncate uppercase italic"><?= $nama_user; ?></p>
                <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest italic"><?= $_SESSION['role'] ?? 'User'; ?></p>
            </div>
        </div>
        <a href="../logout.php" class="flex items-center gap-3 px-5 py-3 text-rose-500 hover:bg-rose-50 rounded-2xl font-black transition-all text-[10px] uppercase tracking-[0.15em] italic">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            Keluar
        </a>
    </div>
</aside>

<header class="md:hidden flex justify-between items-center p-4 bg-white/90 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
    <button onclick="toggleSidebar()" class="p-2.5 bg-slate-50 rounded-xl text-slate-600 border border-slate-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" /></svg>
    </button>
    
    <div class="flex items-center gap-2">
        <img src="../assets/logo/logo.png" alt="Logo" class="h-7 w-7 object-contain">
        <span class="font-black text-xl tracking-tighter text-blue-600 italic uppercase">Zanith Libary.</span>
    </div>
    
    <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center text-white font-black italic text-xs border border-white">
        <?= strtoupper(substr($nama_user, 0, 1)); ?>
    </div>
</header>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        // Cek apakah sidebar sedang tersembunyi
        const isHidden = sidebar.classList.contains('-translate-x-full');
        
        if (isHidden) {
            // Tampilkan Sidebar
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            // Tampilkan Overlay
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.add('opacity-100'), 10);
        } else {
            // Sembunyikan Sidebar
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            // Sembunyikan Overlay
            overlay.classList.remove('opacity-100');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }
</script>