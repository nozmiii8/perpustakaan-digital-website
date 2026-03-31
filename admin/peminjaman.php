<?php
// ... (Logika PHP di bagian atas tetap sama seperti sebelumnya)
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
require_once '../config/database.php';

if (isset($_POST['update_status'])) {
    $id_peminjaman = mysqli_real_escape_string($conn, $_POST['id_peminjaman']);
    $id_buku = mysqli_real_escape_string($conn, $_POST['id_buku']);
    $status_baru = $_POST['status'];

    if ($status_baru === 'kembali') {
        mysqli_query($conn, "UPDATE peminjaman SET status = 'kembali' WHERE id_peminjaman = '$id_peminjaman'");
        mysqli_query($conn, "UPDATE buku SET stok = stok + 1 WHERE id_buku = '$id_buku'");
        $msg = "dikembalikan";
    }
    header("Location: peminjaman.php?pesan=$msg");
    exit;
}

$query_pinjam = mysqli_query($conn, "
    SELECT p.*, u.nama_lengkap, u.username, b.judul 
    FROM peminjaman p 
    JOIN users u ON p.id_user = u.id_user 
    JOIN buku b ON p.id_buku = b.id_buku 
    ORDER BY p.status ASC, p.tanggal_pinjam DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sirkulasi - Zanith Libs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .status-badge-dipinjam { background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .status-badge-kembali { background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .tab-active { background-color: #1e293b; color: white; transform: translateY(-2px); }
        .page-link.active { background-color: #2563eb; color: white; border-color: #2563eb; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen relative overflow-x-hidden">

    <?php include 'include/header.php'; ?>

    <main class="flex-1 p-5 md:p-10 w-full transition-all duration-300">
        
        <div class="mb-10 mt-4 md:mt-0 text-center">
            <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] italic mb-1">Circulation Desk</p>
            <h2 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tighter italic uppercase leading-none">Data Peminjaman</h2>
            
            <div class="flex flex-col items-center gap-6 mt-8">
                <div class="relative w-full max-w-xl">
                    <input type="text" id="searchInput" onkeyup="liveSearch()" placeholder="Cari peminjam atau judul buku..." 
                    class="w-full bg-white border border-slate-200 rounded-full px-8 py-4 text-xs font-bold text-slate-800 italic focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm text-center">
                </div>

                <div class="flex gap-2 overflow-x-auto no-scrollbar pb-2 justify-center w-full">
                    <button onclick="filterStatus('all')" id="tab-all" class="tab-btn tab-active px-6 py-3 rounded-2xl text-[10px] font-black uppercase italic tracking-widest transition-all">Semua</button>
                    <button onclick="filterStatus('dipinjam')" id="tab-dipinjam" class="tab-btn bg-white text-slate-400 px-6 py-3 rounded-2xl text-[10px] font-black uppercase italic tracking-widest transition-all border border-slate-100 shadow-sm">Dipinjam</button>
                    <button onclick="filterStatus('kembali')" id="tab-kembali" class="tab-btn bg-white text-slate-400 px-6 py-3 rounded-2xl text-[10px] font-black uppercase italic tracking-widest transition-all border border-slate-100 shadow-sm">Kembali</button>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-[3rem] shadow-sm overflow-hidden max-w-6xl mx-auto">
            <div class="overflow-x-auto">
                <table class="w-full text-center" id="peminjamanTable">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 uppercase italic">
                            <th class="px-6 py-7 text-[10px] font-black text-slate-400 tracking-widest">Peminjam</th>
                            <th class="px-6 py-7 text-[10px] font-black text-slate-400 tracking-widest">Buku</th>
                            <th class="px-6 py-7 text-[10px] font-black text-slate-400 tracking-widest">Tenggat</th>
                            <th class="px-6 py-7 text-[10px] font-black text-slate-400 tracking-widest">Status</th>
                            <th class="px-6 py-7 text-[10px] font-black text-slate-400 tracking-widest">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="tableBody">
                        <?php while($row = mysqli_fetch_assoc($query_pinjam)): ?>
                        <tr class="hover:bg-slate-50/50 transition-all group table-row-item" data-status="<?= $row['status']; ?>">
                            <td class="px-6 py-6">
                                <p class="font-black text-slate-800 text-sm uppercase italic tracking-tighter search-name"><?= $row['nama_lengkap']; ?></p>
                                <p class="text-[9px] text-blue-500 font-bold italic uppercase">@<?= $row['username']; ?></p>
                            </td>
                            <td class="px-6 py-6">
                                <p class="text-xs font-bold text-slate-600 uppercase italic leading-tight search-book"><?= $row['judul']; ?></p>
                            </td>
                            <td class="px-6 py-6">
                                <p class="text-[11px] font-black text-slate-500 italic"><?= date('d/m/y', strtotime($row['tanggal_kembali'] ?? 'now')); ?></p>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex justify-center">
                                    <?php if ($row['status'] == 'dipinjam'): ?>
                                        <span class="status-badge-dipinjam px-4 py-1.5 rounded-xl text-[9px] font-black uppercase italic tracking-widest">Dipinjam</span>
                                    <?php else: ?>
                                        <span class="status-badge-kembali px-4 py-1.5 rounded-xl text-[9px] font-black uppercase italic tracking-widest">Kembali</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex justify-center">
                                    <?php if ($row['status'] == 'dipinjam'): ?>
                                        <button onclick='openActionModal(<?= json_encode($row); ?>)' class="bg-slate-900 text-white px-6 py-2.5 rounded-xl text-[9px] font-black uppercase italic tracking-widest hover:bg-blue-600 transition-all active:scale-95">Proses</button>
                                    <?php else: ?>
                                        <button class="bg-slate-50 text-slate-300 px-6 py-2.5 rounded-xl text-[9px] font-black uppercase italic tracking-widest cursor-not-allowed">Selesai</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div id="emptyState" class="hidden py-20 text-center">
                <p class="text-slate-400 font-bold italic uppercase text-xs tracking-widest">Tidak ada data yang cocok...</p>
            </div>

            <div class="bg-slate-50/50 border-t border-slate-100 px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase italic">Tampilan <span id="showingCount">0</span> dari <span id="totalCount">0</span> transaksi</p>
                <div class="flex items-center gap-2" id="paginationWrapper">
                    </div>
            </div>
        </div>
    </main>

    <div id="actionModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeActionModal()"></div>
        <div class="bg-white w-full max-w-md rounded-[3rem] shadow-2xl relative z-10 p-10 border border-slate-100 text-center">
            <h3 class="text-2xl font-black text-slate-800 italic uppercase tracking-tighter mb-6">Konfirmasi Buku</h3>
            <div class="bg-slate-50 rounded-[2rem] p-6 mb-8 border border-slate-100 text-left">
                <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest mb-1">Judul Buku</p>
                <p id="modal_judul" class="text-sm font-black text-slate-700 uppercase italic mb-4"></p>
                <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest mb-1">Peminjam</p>
                <p id="modal_siswa" class="text-sm font-black text-slate-700 uppercase italic"></p>
            </div>
            <form action="" method="POST" class="space-y-3">
                <input type="hidden" name="id_peminjaman" id="modal_id_peminjaman">
                <input type="hidden" name="id_buku" id="modal_id_buku">
                <input type="hidden" name="status" value="kembali">
                <button type="submit" name="update_status" class="w-full py-4 rounded-2xl text-[11px] font-black uppercase italic text-white bg-blue-600 shadow-lg shadow-blue-200 hover:bg-slate-900 transition-all">Konfirmasi Pengembalian</button>
                <button type="button" onclick="closeActionModal()" class="w-full py-4 text-[11px] font-black uppercase italic text-slate-400">Batalkan</button>
            </form>
        </div>
    </div>

    <script>
        let currentFilter = 'all';
        let currentPage = 1;
        const rowsPerPage = 5; // Jumlah baris per halaman

        function liveSearch() {
            currentPage = 1; // Reset ke halaman 1 saat mencari
            renderTable();
        }

        function filterStatus(status) {
            currentFilter = status;
            currentPage = 1;
            
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('tab-active', 'bg-slate-900', 'text-white');
                btn.classList.add('bg-white', 'text-slate-400');
            });
            const activeBtn = document.getElementById('tab-' + status);
            activeBtn.classList.add('tab-active');
            activeBtn.classList.remove('bg-white', 'text-slate-400');

            renderTable();
        }

        function renderTable() {
            const input = document.getElementById('searchInput').value.toUpperCase();
            const allRows = Array.from(document.querySelectorAll('.table-row-item'));
            
            // 1. Filter Data
            const filteredRows = allRows.filter(row => {
                const name = row.querySelector('.search-name').innerText.toUpperCase();
                const book = row.querySelector('.search-book').innerText.toUpperCase();
                const status = row.getAttribute('data-status');
                
                const matchesSearch = (name.includes(input) || book.includes(input));
                const matchesStatus = (currentFilter === 'all' || status === currentFilter);
                
                return matchesSearch && matchesStatus;
            });

            // 2. Logika Pagination
            const totalRows = filteredRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            // Sembunyikan semua dulu
            allRows.forEach(row => row.style.display = 'none');

            // Tampilkan yang masuk range halaman saat ini
            const visibleRows = filteredRows.slice(start, end);
            visibleRows.forEach(row => row.style.display = '');

            // 3. Update UI Info & Empty State
            document.getElementById('totalCount').innerText = totalRows;
            document.getElementById('showingCount').innerText = visibleRows.length;
            document.getElementById('emptyState').classList.toggle('hidden', totalRows > 0);

            renderPaginationControls(totalPages);
        }

        function renderPaginationControls(totalPages) {
            const wrapper = document.getElementById('paginationWrapper');
            wrapper.innerHTML = '';

            if (totalPages <= 1) return;

            // Back Button
            const prevBtn = createPageBtn('Back', currentPage > 1 ? currentPage - 1 : null);
            wrapper.appendChild(prevBtn);

            // Numbered Buttons
            for (let i = 1; i <= totalPages; i++) {
                const btn = createPageBtn(i, i);
                if (i === currentPage) btn.classList.add('active');
                wrapper.appendChild(btn);
            }

            // Next Button
            const nextBtn = createPageBtn('Next', currentPage < totalPages ? currentPage + 1 : null);
            wrapper.appendChild(nextBtn);
        }

        function createPageBtn(label, targetPage) {
            const btn = document.createElement('button');
            btn.innerText = label;
            btn.className = `px-4 py-2 rounded-xl text-[10px] font-bold uppercase border border-slate-200 transition-all ${targetPage ? 'hover:bg-slate-100 text-slate-600' : 'opacity-30 cursor-not-allowed text-slate-300'}`;
            
            if (targetPage) {
                btn.onclick = () => {
                    currentPage = targetPage;
                    renderTable();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                };
            }
            return btn;
        }

        function openActionModal(data) {
            document.getElementById('modal_id_peminjaman').value = data.id_peminjaman;
            document.getElementById('modal_id_buku').value = data.id_buku;
            document.getElementById('modal_judul').innerText = data.judul;
            document.getElementById('modal_siswa').innerText = data.nama_lengkap;
            document.getElementById('actionModal').classList.remove('hidden');
            document.getElementById('actionModal').classList.add('flex');
        }

        function closeActionModal() {
            document.getElementById('actionModal').classList.add('hidden');
            document.getElementById('actionModal').classList.remove('flex');
        }

        // Inisialisasi Pertama
        document.addEventListener('DOMContentLoaded', renderTable);
    </script>
</body>
</html>