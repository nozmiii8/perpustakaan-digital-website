<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
require_once '../config/database.php';

// --- LOGIKA PHP (Hapus & Simpan tetap sama) ---
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    $res = mysqli_query($conn, "SELECT gambar FROM buku WHERE id_buku = '$id'");
    $data = mysqli_fetch_assoc($res);
    if (!empty($data['gambar']) && file_exists("../assets/buku/" . $data['gambar'])) {
        unlink("../assets/buku/" . $data['gambar']);
    }
    mysqli_query($conn, "DELETE FROM buku WHERE id_buku = '$id'");
    header("Location: buku.php?pesan=dihapus");
    exit;
}

if (isset($_POST['simpan_buku'])) {
    $id_buku      = $_POST['id_buku'];
    $judul        = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis      = mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit     = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun_terbit = mysqli_real_escape_string($conn, $_POST['tahun_terbit']);
    $kategori     = mysqli_real_escape_string($conn, $_POST['kategori']);
    $stok         = mysqli_real_escape_string($conn, $_POST['stok']);
    $deskripsi    = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $gambar_nama = $_FILES['gambar']['name'];
    $gambar_tmp  = $_FILES['gambar']['tmp_name'];
    $nama_baru   = "";

    if (!empty($gambar_nama)) {
        $dimensi = getimagesize($gambar_tmp);
        if ($dimensi[0] != 1410 || $dimensi[1] != 2250) {
            echo "<script>alert('Gagal! Ukuran harus tepat 1410x2250 px.'); window.history.back();</script>";
            exit;
        }
        $ekstensi = pathinfo($gambar_nama, PATHINFO_EXTENSION);
        $nama_baru = "book_" . time() . "_" . rand(100, 999) . "." . $ekstensi;
        move_uploaded_file($gambar_tmp, "../assets/buku/" . $nama_baru);

        if (!empty($id_buku)) {
            $res_old = mysqli_query($conn, "SELECT gambar FROM buku WHERE id_buku = '$id_buku'");
            $old_data = mysqli_fetch_assoc($res_old);
            if (!empty($old_data['gambar']) && file_exists("../assets/buku/" . $old_data['gambar'])) {
                unlink("../assets/buku/" . $old_data['gambar']);
            }
        }
    }

    if (empty($id_buku)) {
        $sql = "INSERT INTO buku (judul, penulis, kategori, deskripsi, penerbit, tahun_terbit, stok, gambar) 
                VALUES ('$judul', '$penulis', '$kategori', '$deskripsi', '$penerbit', '$tahun_terbit', '$stok', '$nama_baru')";
    } else {
        $up_img = !empty($nama_baru) ? ", gambar='$nama_baru'" : "";
        $sql = "UPDATE buku SET judul='$judul', penulis='$penulis', kategori='$kategori', deskripsi='$deskripsi',
                penerbit='$penerbit', tahun_terbit='$tahun_terbit', stok='$stok' $up_img WHERE id_buku='$id_buku'";
    }
    mysqli_query($conn, $sql);
    header("Location: buku.php?pesan=sukses");
    exit;
}

$query_buku = mysqli_query($conn, "SELECT * FROM buku ORDER BY id_buku DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Zanith Libs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .page-link.active { background-color: #1e293b; color: white; border-color: #1e293b; }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen relative overflow-x-hidden">

    <?php include 'include/header.php'; ?>

    <main class="flex-1 p-5 md:p-10 w-full transition-all duration-300">
        
        <div class="mb-10 text-center">
            <p class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] italic mb-1">Inventory Management</p>
            <h2 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tighter italic uppercase leading-none mb-8">Kelola Koleksi</h2>
            
            <div class="flex flex-col items-center gap-6">
                <div class="relative w-full max-w-xl">
                    <input type="text" id="searchInput" onkeyup="liveSearch()" placeholder="Cari judul, penulis, atau kategori..." 
                    class="w-full bg-white border border-slate-200 rounded-full px-8 py-4 text-xs font-bold text-slate-800 italic focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all shadow-sm text-center">
                </div>

                <button onclick="openModal()" class="bg-slate-900 text-white px-8 py-4 rounded-full text-[11px] font-black uppercase tracking-[0.2em] italic shadow-xl shadow-slate-200 hover:bg-blue-600 transition-all active:scale-95 flex items-center justify-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                    Tambah Buku Baru
                </button>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-[3rem] shadow-sm overflow-hidden max-w-6xl mx-auto">
            <div class="overflow-x-auto">
                <table class="w-full text-center" id="bukuTable">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 uppercase italic">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 tracking-widest text-left">Detail Koleksi</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 tracking-widest">Stok</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 tracking-widest">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="tableBody">
                        <?php while($row = mysqli_fetch_assoc($query_buku)): ?>
                        <tr class="hover:bg-slate-50/50 transition-all group table-row-item">
                            <td class="px-8 py-6 text-left max-w-md">
                                <div class="flex gap-5">
                                    <div class="w-16 h-24 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 shadow-sm">
                                        <?php if(!empty($row['gambar']) && file_exists("../assets/buku/".$row['gambar'])): ?>
                                            <img src="../assets/buku/<?= $row['gambar']; ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-[8px] font-black text-slate-300 italic text-center">NO COVER</div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="font-black text-slate-800 text-sm uppercase italic tracking-tighter search-data"><?= $row['judul']; ?></p>
                                        <p class="text-[9px] text-blue-600 font-bold uppercase italic mb-1 search-data"><?= $row['penulis']; ?> • <?= $row['kategori']; ?></p>
                                        <p class="text-[10px] text-slate-500 font-medium italic line-clamp-2 leading-relaxed"><?= $row['deskripsi'] ?: 'Tanpa sinopsis.' ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-[11px] font-black italic text-slate-600 bg-slate-100 px-4 py-2 rounded-full border border-slate-200">
                                    <?= $row['stok']; ?> <span class="text-slate-400 uppercase">Pcs</span>
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex justify-center gap-2">
                                    <button onclick='openModal(<?= json_encode($row); ?>)' class="p-3 bg-white text-slate-400 hover:text-blue-600 rounded-xl transition-all border border-slate-100 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                    <a href="?hapus=<?= $row['id_buku']; ?>" onclick="return confirm('Hapus permanen?')" class="p-3 bg-white text-slate-400 hover:text-rose-600 rounded-xl transition-all border border-slate-100 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div id="emptyState" class="hidden py-20 text-center">
                <p class="text-slate-400 font-bold italic uppercase text-xs tracking-widest">Buku tidak ditemukan...</p>
            </div>

            <div class="bg-slate-50/50 border-t border-slate-100 px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase italic">Menampilkan <span id="showingCount">0</span> Koleksi</p>
                <div class="flex items-center gap-2" id="paginationWrapper">
                    </div>
            </div>
        </div>
    </main>

    <div id="bookModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="bg-white w-full max-w-3xl rounded-[3rem] shadow-2xl relative z-10 p-8 md:p-10 border border-slate-100 max-h-[95vh] overflow-y-auto no-scrollbar">
            <h3 id="modalTitle" class="text-2xl font-black text-slate-800 italic uppercase tracking-tighter text-center mb-8">Tambah Koleksi</h3>
            <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <input type="hidden" name="id_buku" id="id_buku">
                <div class="md:col-span-2">
                    <label class="text-[9px] font-black text-slate-400 uppercase italic ml-4 mb-1 block">Judul Buku</label>
                    <input type="text" name="judul" id="judul" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-800 italic focus:ring-2 focus:ring-blue-500/20 transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="text-[9px] font-black text-slate-400 uppercase italic ml-4 mb-1 block">Sinopsis</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-800 italic focus:ring-2 focus:ring-blue-500/20 transition-all"></textarea>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase italic ml-4 mb-1 block">Penulis</label>
                    <input type="text" name="penulis" id="penulis" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-800 italic focus:ring-2 focus:ring-blue-500/20 transition-all">
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase italic ml-4 mb-1 block">Kategori</label>
                    <input type="text" name="kategori" id="kategori" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-800 italic focus:ring-2 focus:ring-blue-500/20 transition-all">
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase italic ml-4 mb-1 block">Stok</label>
                    <input type="number" name="stok" id="stok" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-800 italic focus:ring-2 focus:ring-blue-500/20 transition-all">
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase italic ml-4 mb-1 block">Cover (1410x2250)</label>
                    <input type="file" name="gambar" id="gambar" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-3.5 text-[10px] font-bold text-slate-400 italic">
                </div>
                <div class="md:col-span-2 pt-6 flex gap-4">
                    <button type="button" onclick="closeModal()" class="flex-1 px-6 py-5 rounded-2xl text-[11px] font-black uppercase italic text-slate-400 bg-slate-100 hover:bg-slate-200 transition-all">Batal</button>
                    <button type="submit" name="simpan_buku" class="flex-[2] px-6 py-5 rounded-2xl text-[11px] font-black uppercase italic text-white bg-blue-600 shadow-xl shadow-blue-100 hover:bg-slate-900 transition-all">Simpan Koleksi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentPage = 1;
        const rowsPerPage = 5;

        function liveSearch() {
            currentPage = 1;
            renderTable();
        }

        function renderTable() {
            const input = document.getElementById('searchInput').value.toUpperCase();
            const allRows = Array.from(document.querySelectorAll('.table-row-item'));
            
            // Filter
            const filteredRows = allRows.filter(row => {
                const text = row.innerText.toUpperCase();
                return text.includes(input);
            });

            // Pagination Logic
            const totalRows = filteredRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            allRows.forEach(row => row.style.display = 'none');
            const visibleRows = filteredRows.slice(start, end);
            visibleRows.forEach(row => row.style.display = '');

            // UI Updates
            document.getElementById('showingCount').innerText = visibleRows.length;
            document.getElementById('emptyState').classList.toggle('hidden', totalRows > 0);
            
            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            const wrapper = document.getElementById('paginationWrapper');
            wrapper.innerHTML = '';
            if (totalPages <= 1) return;

            // Tombol Back
            wrapper.appendChild(createBtn('Back', currentPage > 1 ? currentPage - 1 : null));

            // Angka 1 2 3 4
            for (let i = 1; i <= totalPages; i++) {
                const btn = createBtn(i, i);
                if (i === currentPage) btn.classList.add('active', 'bg-slate-900', 'text-white');
                wrapper.appendChild(btn);
            }

            // Tombol Next
            wrapper.appendChild(createBtn('Next', currentPage < totalPages ? currentPage + 1 : null));
        }

        function createBtn(label, target) {
            const btn = document.createElement('button');
            btn.innerText = label;
            btn.className = `px-4 py-2 rounded-xl text-[10px] font-bold uppercase border border-slate-200 transition-all page-link ${target ? 'hover:bg-slate-100 text-slate-600' : 'opacity-30 cursor-not-allowed text-slate-300'}`;
            if (target) btn.onclick = () => { currentPage = target; renderTable(); };
            return btn;
        }

        function openModal(data = null) {
            const modal = document.getElementById('bookModal');
            document.getElementById('id_buku').value = data?.id_buku || '';
            document.getElementById('judul').value = data?.judul || '';
            document.getElementById('deskripsi').value = data?.deskripsi || '';
            document.getElementById('penulis').value = data?.penulis || '';
            document.getElementById('kategori').value = data?.kategori || '';
            document.getElementById('stok').value = data?.stok || '';
            document.getElementById('modalTitle').innerText = data ? 'Edit Koleksi' : 'Tambah Koleksi';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            document.getElementById('bookModal').classList.add('hidden');
            document.getElementById('bookModal').classList.remove('flex');
        }

        document.addEventListener('DOMContentLoaded', renderTable);
    </script>
</body>
</html>