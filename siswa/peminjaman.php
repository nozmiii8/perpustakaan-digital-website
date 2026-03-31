<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['siswa', 'admin'])) {
    header("Location: ../login.php");
    exit;
}

$nama_user = $_SESSION['nama_lengkap'] ?? $_SESSION['nama'] ?? 'User';

// --- LOGIKA AJAX (SEARCH & FILTER) ---
if (isset($_GET['ajax_search'])) {
    $search   = mysqli_real_escape_string($conn, $_GET['search']);
    $category = mysqli_real_escape_string($conn, $_GET['category'] ?? 'all');
    
    $sql = "SELECT * FROM buku WHERE 1=1";
    if ($search !== "") { $sql .= " AND (judul LIKE '%$search%' OR penulis LIKE '%$search%')"; }
    if ($category !== 'all') { $sql .= " AND kategori = '$category'"; }
    $sql .= " ORDER BY id_buku DESC";
    $query_buku = mysqli_query($conn, $sql);

    if (mysqli_num_rows($query_buku) > 0) {
        while($row = mysqli_fetch_assoc($query_buku)) {
            $stok_status = $row['stok'] > 0 ? 'text-blue-500' : 'text-rose-500';
            $stok_text   = $row['stok'] > 0 ? 'Available Unit' : 'Out of Stock';
            $gambar      = (!empty($row['gambar']) && file_exists("../assets/buku/".$row['gambar'])) 
                           ? "../assets/buku/".$row['gambar'] 
                           : "../assets/buku/default.jpg";

            echo '
            <div class="group bg-white p-7 rounded-[3rem] border border-slate-100 shadow-sm hover:shadow-2xl hover:-translate-y-3 transition-all duration-500 flex flex-col items-center text-center h-full">
                <div class="w-full aspect-[3/4] rounded-[2.5rem] mb-7 overflow-hidden border border-slate-50 shadow-inner shrink-0">
                    <img src="'.$gambar.'" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>

                <div class="flex flex-col flex-grow w-full">
                    <span class="text-[8px] font-black text-blue-600 bg-blue-50 px-4 py-1.5 rounded-full uppercase tracking-[0.2em] italic border border-blue-100 self-center mb-4">
                        '.$row['kategori'].'
                    </span>

                    <h4 class="font-black text-slate-800 text-base md:text-lg uppercase italic tracking-tighter line-clamp-2 leading-tight mb-2 px-2 group-hover:text-blue-600 transition-colors">
                        '.$row['judul'].'
                    </h4>
                    
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest italic mb-6 opacity-60">
                        Work By '.$row['penulis'].'
                    </p>

                    <div class="mt-auto pt-6 border-t border-slate-50 w-full">
                        <div class="mb-5">
                            <p class="text-[8px] font-black text-slate-300 uppercase tracking-[0.3em] italic mb-1">'.$stok_text.'</p>
                            <p class="text-sm font-black text-slate-900 italic">'.$row['stok'].' <span class="text-[9px] text-slate-300">PCS</span></p>
                        </div>

                        <a href="view_buku.php?id='.$row['id_buku'].'" class="inline-flex items-center justify-center w-full bg-slate-900 text-white py-5 rounded-[1.8rem] text-[10px] font-black uppercase tracking-[0.25em] italic shadow-xl shadow-slate-200 hover:bg-blue-600 hover:shadow-blue-200 transition-all active:scale-95">
                            Explore Details
                        </a>
                    </div>
                </div>
            </div>';
        }
    } else {
        echo '<div class="col-span-full py-32 text-center">
                <h3 class="font-black text-slate-200 text-5xl md:text-7xl uppercase italic tracking-tighter opacity-40">No Results.</h3>
              </div>';
    }
    exit;
}

$categories_query = mysqli_query($conn, "SELECT DISTINCT kategori FROM buku ORDER BY kategori ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog - Zanith Libs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="../assets/logo/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfdfe; color: #1e293b; -webkit-font-smoothing: antialiased; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .active-nav { background: #2563eb !important; color: white !important; border-color: #2563eb !important; box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.3); }
        .title-gradient { background: linear-gradient(to bottom, #1e293b, #64748b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen">

    <?php include 'include/header.php'; ?>

    <main class="flex-1 pt-24 md:pt-16 pb-24">
        
        <header class="px-6 md:px-14 mb-16 max-w-5xl mx-auto text-center">
            <div class="mb-8">
                <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-6 py-2.5 rounded-full uppercase tracking-[0.4em] italic border border-blue-100 inline-block">
                    Library Discovery
                </span>
            </div>
            
            <h2 class="text-6xl md:text-8xl font-black tracking-tighter italic uppercase leading-[0.85] title-gradient mb-12">
                Zanith <br> <span class="text-blue-600">Katalog.</span>
            </h2>

            <div class="relative max-w-2xl mx-auto mb-10 group">
                <div class="absolute inset-y-0 left-8 flex items-center pointer-events-none text-slate-300 group-focus-within:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" id="live-search" placeholder="Search by title or author..." 
                       class="w-full bg-white border-2 border-slate-100 py-6 md:py-7 pl-20 pr-10 rounded-[2.5rem] shadow-2xl shadow-slate-200/30 focus:border-blue-600 focus:ring-0 transition-all font-bold text-sm italic outline-none placeholder:text-slate-300">
            </div>

            <nav class="flex items-center justify-center gap-3 overflow-x-auto no-scrollbar py-2">
                <button onclick="filterCategory('all', this)" class="category-btn active-nav whitespace-nowrap px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest italic border-2 border-slate-50 transition-all">
                    All Collection
                </button>
                <?php while($cat = mysqli_fetch_assoc($categories_query)): ?>
                <button onclick="filterCategory('<?= $cat['kategori']; ?>', this)" class="category-btn whitespace-nowrap bg-white text-slate-400 px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest italic border-2 border-slate-50 transition-all hover:text-blue-600 hover:border-blue-100">
                    <?= $cat['kategori']; ?>
                </button>
                <?php endwhile; ?>
            </nav>
        </header>

        <div class="px-6 md:px-14 max-w-[1440px] mx-auto">
            <div id="book-result" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 md:gap-10 transition-opacity duration-300">
                </div>
        </div>

    </main>

    <script>
        const resultContainer = document.getElementById('book-result');
        const searchInput = document.getElementById('live-search');
        let currentCategory = 'all';

        const fetchBooks = async (keyword = '', category = 'all') => {
            resultContainer.style.opacity = '0.4';
            try {
                const response = await fetch(`peminjaman.php?ajax_search=1&search=${encodeURIComponent(keyword)}&category=${encodeURIComponent(category)}`);
                const data = await response.text();
                resultContainer.innerHTML = data;
                resultContainer.style.opacity = '1';
            } catch (e) {
                resultContainer.innerHTML = '<p class="col-span-full text-center font-black italic text-rose-500">Connection Failed.</p>';
            }
        };

        function filterCategory(category, btn) {
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active-nav'));
            btn.classList.add('active-nav');
            currentCategory = category;
            fetchBooks(searchInput.value, currentCategory);
        }

        searchInput.addEventListener('input', (e) => {
            fetchBooks(e.target.value, currentCategory);
        });

        // First Load
        fetchBooks();
    </script>
</body>
</html>