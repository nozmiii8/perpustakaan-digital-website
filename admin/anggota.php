<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
require_once '../config/database.php';

$query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC, nama_lengkap ASC");
$total_data = mysqli_num_rows($query_users);

if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    if ($id == $_SESSION['id_user']) {
        header("Location: anggota.php?pesan=gagal_hapus_diri");
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id_user = '$id'");
        header("Location: anggota.php?pesan=dihapus");
    }
    exit;
}

if (isset($_POST['simpan_anggota'])) {
    $id_user  = $_POST['id_user'];
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);
    $password = $_POST['password'];
    
    if (empty($id_user)) {
        $pass_fix = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : password_hash('123', PASSWORD_DEFAULT);
        mysqli_query($conn, "INSERT INTO users (nama_lengkap, username, password, role) VALUES ('$nama', '$username', '$pass_fix', '$role')");
        $msg = "ditambahkan";
    } else {
        if (!empty($password)) {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET nama_lengkap='$nama', username='$username', password='$pass_hash', role='$role' WHERE id_user='$id_user'");
        } else {
            mysqli_query($conn, "UPDATE users SET nama_lengkap='$nama', username='$username', role='$role' WHERE id_user='$id_user'");
        }
        $msg = "diperbarui";
    }
    header("Location: anggota.php?pesan=$msg");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Zanith Libs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="flex flex-col md:flex-row min-h-screen relative overflow-x-hidden">

    <?php include 'include/header.php'; ?>

    <main class="flex-1 p-5 md:p-10 w-full flex flex-col items-center">
        
        <div class="mb-10 mt-4 md:mt-0 flex flex-col items-center text-center w-full max-w-6xl">
            <p class="text-[9px] font-black text-blue-600 uppercase tracking-[0.3em] italic mb-2">User Management</p>
            <h2 class="text-3xl md:text-5xl font-black text-slate-800 tracking-tighter italic uppercase leading-none mb-8">Daftar Anggota</h2>
            
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 w-full bg-white p-3 rounded-[2.5rem] shadow-sm border border-slate-100">
                <div class="relative w-full md:w-1/2">
                    <input type="text" id="searchInput" onkeyup="liveSearch()" placeholder="Cari nama atau username..." class="w-full bg-slate-50 border-none rounded-2xl px-12 py-3.5 text-[11px] font-bold text-slate-700 italic focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 absolute left-5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>

                <button onclick="openUserModal()" class="w-full md:w-auto bg-slate-900 text-white px-8 py-3.5 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic hover:bg-blue-600 transition-all active:scale-95 flex items-center justify-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                    Tambah Anggota
                </button>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-sm overflow-hidden w-full max-w-6xl">
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left min-w-[700px]">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 uppercase italic">
                            <th class="px-10 py-5 text-[9px] font-black text-slate-400 tracking-widest">User Details</th>
                            <th class="px-10 py-5 text-[9px] font-black text-slate-400 tracking-widest text-center">Access Role</th>
                            <th class="px-10 py-5 text-[9px] font-black text-slate-400 tracking-widest text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="userTableBody">
                        <?php while($user = mysqli_fetch_assoc($query_users)): ?>
                        <tr class="hover:bg-slate-50/50 transition-all group user-row-item" 
                            data-nama="<?= strtoupper($user['nama_lengkap']); ?>" 
                            data-user="<?= strtoupper($user['username']); ?>">
                            <td class="px-10 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 <?= $user['role'] == 'admin' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600' ?> rounded-full flex items-center justify-center font-black italic text-xs border-4 border-white shadow-sm uppercase">
                                        <?= substr($user['nama_lengkap'], 0, 1); ?>
                                    </div>
                                    <div>
                                        <p class="font-black text-slate-800 text-sm uppercase italic tracking-tighter leading-tight"><?= $user['nama_lengkap']; ?></p>
                                        <p class="text-[9px] text-slate-400 font-bold italic uppercase">@<?= $user['username']; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-5 text-center">
                                <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic border <?= $user['role'] == 'admin' ? 'bg-amber-50 text-amber-500 border-amber-100' : 'bg-slate-50 text-slate-500 border-slate-100' ?>">
                                    <?= $user['role']; ?>
                                </span>
                            </td>
                            <td class="px-10 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <button onclick='openUserModal(<?= json_encode($user); ?>)' class="p-2.5 bg-slate-50 text-slate-400 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-all border border-slate-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                    <?php if($user['id_user'] != $_SESSION['id_user']): ?>
                                    <a href="?hapus=<?= $user['id_user']; ?>" onclick="return confirm('Hapus user ini?')" class="p-2.5 bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600 rounded-xl transition-all border border-slate-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div id="emptyState" class="hidden py-16 text-center">
                <p class="text-slate-400 font-bold italic uppercase text-[10px] tracking-widest">Anggota tidak ditemukan...</p>
            </div>

            <div class="p-6 border-t border-slate-50 flex flex-col md:flex-row items-center justify-between gap-4 bg-white">
                <p class="text-[9px] font-bold text-slate-400 uppercase italic">Showing <span id="showingCount">0</span> of <span id="totalCount">0</span></p>
                <div class="flex items-center gap-2">
                    <button id="prevBtn" class="px-4 py-2 rounded-xl border border-slate-100 text-[9px] font-bold text-slate-600 uppercase tracking-widest hover:bg-slate-50 transition-all">Back</button>
                    <div class="flex gap-1.5" id="paginationNumbers"></div>
                    <button id="nextBtn" class="px-4 py-2 rounded-xl border border-slate-100 text-[9px] font-bold text-slate-600 uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">Next</button>
                </div>
            </div>
        </div>
    </main>

    <div id="userModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-md" onclick="closeUserModal()"></div>
        <div class="bg-white w-full max-w-md rounded-[3rem] shadow-2xl relative z-10 p-8 border border-slate-100 text-center">
            <h3 id="modalTitle" class="text-2xl font-black text-slate-800 italic uppercase tracking-tighter mb-6">Data Anggota</h3>
            <form action="" method="POST" class="space-y-4 text-left">
                <input type="hidden" name="id_user" id="id_user">
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase italic tracking-widest ml-4 mb-1 block">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" required class="w-full bg-slate-50 border-none rounded-xl px-5 py-3 text-xs font-bold text-slate-800 italic outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase italic tracking-widest ml-4 mb-1 block">Username</label>
                    <input type="text" name="username" id="username" required class="w-full bg-slate-50 border-none rounded-xl px-5 py-3 text-xs font-bold text-slate-800 italic outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase italic tracking-widest ml-4 mb-1 block">Password</label>
                    <input type="password" name="password" id="password" class="w-full bg-slate-50 border-none rounded-xl px-5 py-3 text-xs font-bold text-slate-800 italic outline-none focus:ring-2 focus:ring-blue-100" placeholder="••••••••">
                    <p id="passwordNoteLabel" class="text-[8px] text-slate-400 font-bold italic mt-1 ml-4 uppercase"></p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-400 uppercase italic tracking-widest ml-4 mb-1 block">Role Akses</label>
                    <select name="role" id="role" class="w-full bg-slate-50 border-none rounded-xl px-5 py-3 text-xs font-bold text-slate-800 italic outline-none appearance-none">
                        <option value="siswa">Siswa</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeUserModal()" class="flex-1 py-4 rounded-xl text-[10px] font-black uppercase italic text-slate-400 bg-slate-50">Batal</button>
                    <button type="submit" name="simpan_anggota" class="flex-[2] py-4 rounded-xl text-[10px] font-black uppercase italic text-white bg-blue-600 shadow-lg shadow-blue-100">Simpan Data</button>
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
            const allRows = Array.from(document.querySelectorAll('.user-row-item'));
            const filteredRows = allRows.filter(row => {
                const nama = row.getAttribute('data-nama');
                const user = row.getAttribute('data-user');
                return (nama.includes(input) || user.includes(input));
            });

            const totalRows = filteredRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            allRows.forEach(row => row.style.display = 'none');
            const visibleRows = filteredRows.slice(start, end);
            visibleRows.forEach(row => row.style.display = '');

            document.getElementById('totalCount').innerText = totalRows;
            document.getElementById('showingCount').innerText = visibleRows.length;
            document.getElementById('emptyState').classList.toggle('hidden', totalRows > 0);

            renderPaginationControls(totalPages);
        }

        function renderPaginationControls(totalPages) {
            const container = document.getElementById('paginationNumbers');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            container.innerHTML = '';

            if (currentPage > 1) {
                prevBtn.classList.remove('opacity-30', 'cursor-not-allowed');
                prevBtn.onclick = () => { currentPage--; renderTable(); };
            } else {
                prevBtn.classList.add('opacity-30', 'cursor-not-allowed');
                prevBtn.onclick = null;
            }

            if (currentPage < totalPages) {
                nextBtn.classList.remove('opacity-30', 'cursor-not-allowed');
                nextBtn.onclick = () => { currentPage++; renderTable(); };
            } else {
                nextBtn.classList.add('opacity-30', 'cursor-not-allowed');
                nextBtn.onclick = null;
            }

            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.innerText = i;
                btn.className = `w-8 h-8 flex items-center justify-center rounded-lg text-[9px] font-bold transition-all border ${i === currentPage ? 'bg-white text-blue-600 border-blue-100 shadow-sm' : 'bg-white text-slate-400 border-slate-100 hover:border-slate-300'}`;
                btn.onclick = () => { currentPage = i; renderTable(); };
                container.appendChild(btn);
            }
        }

        function openUserModal(data = null) {
            const modal = document.getElementById('userModal');
            document.getElementById('id_user').value = '';
            document.getElementById('nama_lengkap').value = '';
            document.getElementById('username').value = '';
            document.getElementById('password').value = '';
            document.getElementById('role').value = 'siswa';

            if (data) {
                document.getElementById('modalTitle').innerText = 'Edit Anggota';
                document.getElementById('passwordNoteLabel').innerText = '* Kosongkan jika tidak ganti';
                document.getElementById('id_user').value = data.id_user;
                document.getElementById('nama_lengkap').value = data.nama_lengkap;
                document.getElementById('username').value = data.username;
                document.getElementById('role').value = data.role;
            } else {
                document.getElementById('modalTitle').innerText = 'Tambah Anggota';
                document.getElementById('passwordNoteLabel').innerText = '* Default jika kosong: 123';
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
            document.getElementById('userModal').classList.remove('flex');
        }

        document.addEventListener('DOMContentLoaded', renderTable);
    </script>
</body>
</html>