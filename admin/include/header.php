<?php
// Fallback nama untuk icon dan profile
$display_name = $nama_admin ?? $_SESSION['nama_lengkap'] ?? 'Admin';
$initial = strtoupper(substr($display_name, 0, 1));
$current = basename($_SERVER['PHP_SELF']);
?>

<div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] hidden md:hidden transition-all duration-300 opacity-0"></div>

<aside id="sidebar" class="fixed md:sticky top-0 left-0 h-screen w-72 bg-white border-r border-slate-100 flex flex-col z-[70] -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
    
    <div class="p-8 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3">
            <img src="../assets/logo/logo.png" alt="Zanith Logo" class="w-10 h-10 object-contain">
            <div class="flex flex-col">
                <span class="font-black text-xl tracking-tighter text-slate-800 italic uppercase leading-none">Zanith Libary.</span>
                <span class="text-[9px] font-black text-blue-600 tracking-[0.2em] uppercase italic">Admin Panel</span>
            </div>
        </div>
        <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-slate-600 p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <nav class="flex-1 px-6 space-y-1.5 overflow-y-auto no-scrollbar">
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-4 ml-4 italic">Main Panel</p>

        <a href="dashboard.php" class="<?= $current == 'dashboard.php' ? 'bg-blue-600 text-white shadow-xl shadow-blue-100' : 'text-slate-500 hover:bg-slate-50' ?> flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all group italic text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
            Dashboard
        </a>

        <a href="buku.php" class="<?= $current == 'buku.php' ? 'bg-blue-600 text-white shadow-xl shadow-blue-100' : 'text-slate-500 hover:bg-slate-50' ?> flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all group italic text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" /></svg>
            Data Buku
        </a>

        <a href="peminjaman.php" class="<?= $current == 'peminjaman.php' ? 'bg-blue-600 text-white shadow-xl shadow-blue-100' : 'text-slate-500 hover:bg-slate-50' ?> flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all group italic text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            Peminjaman
        </a>

        <a href="anggota.php" class="<?= $current == 'anggota.php' ? 'bg-blue-600 text-white shadow-xl shadow-blue-100' : 'text-slate-500 hover:bg-slate-50' ?> flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all group italic text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            Data Anggota
        </a>

        <div class="py-4"></div>

        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-4 ml-4 italic">Switch View</p>
        <a href="../siswa/dashboard.php" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold text-slate-600 bg-slate-50 hover:bg-slate-900 hover:text-white transition-all group italic text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            Halaman Siswa
        </a>
    </nav>

    <div class="p-6 mt-auto border-t border-slate-50 shrink-0">
        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center font-black text-blue-600 shadow-sm border border-slate-100 italic">
                <?= $initial; ?>
            </div>
            <div class="truncate">
                <p class="text-[11px] font-black text-slate-800 truncate uppercase italic leading-none"><?= $display_name; ?></p>
                <p class="text-[9px] text-blue-500 font-bold uppercase tracking-widest mt-1 italic">Administrator</p>
            </div>
        </div>
        <a href="../logout.php" class="mt-4 flex items-center gap-3 px-5 py-3 text-rose-500 hover:bg-rose-50 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] transition-all italic">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7" /></svg>
            Keluar Aplikasi
        </a>
    </div>
</aside>

<header class="md:hidden fixed top-0 left-0 right-0 h-20 bg-white/90 backdrop-blur-md border-b border-slate-100 flex justify-between items-center px-6 z-50">
    <button onclick="toggleSidebar()" class="p-3 bg-slate-50 text-slate-600 rounded-2xl active:scale-90 transition-all border border-slate-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h10M4 18h16" />
        </svg>
    </button>

    <div class="flex items-center gap-2">
        <img src="../assets/logo/logo.png" alt="Logo" class="h-8 w-8 object-contain">
        <span class="font-black text-xl tracking-tighter text-blue-600 italic uppercase">Zanith Libary.</span>
    </div>

    <div class="w-10 h-10 bg-blue-600 rounded-2xl flex items-center justify-center text-white font-black italic shadow-lg shadow-blue-100 border border-white/20">
        <?= $initial; ?>
    </div>
</header>

<div class="h-20 md:hidden"></div>
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    // Toggle class untuk Sidebar
    sidebar.classList.toggle('-translate-x-full');
    
    // Toggle class untuk Overlay
    if (overlay.classList.contains('hidden')) {
        overlay.classList.remove('hidden');
        // Beri jeda sedikit agar efek transition opacity terlihat
        setTimeout(() => {
            overlay.classList.add('opacity-100');
        }, 10);
    } else {
        overlay.classList.remove('opacity-100');
        setTimeout(() => {
            overlay.classList.add('hidden');
        }, 300); // Sesuai durasi duration-300 di HTML
    }
}
</script>