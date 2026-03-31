<?php
session_start();
require_once 'config/database.php';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    // Mengambil angka saja dan menambahkan prefix +62
    $telepon  = "+62" . mysqli_real_escape_string($conn, $_POST['telepon']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Validasi Password Sama
    if ($password !== $confirm) {
        $_SESSION['error'] = "Konfirmasi password tidak sesuai!";
        header("Location: register.php");
        exit;
    }

    // Cek duplikasi username
    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek_user) > 0) {
        $_SESSION['error'] = "Username sudah terdaftar!";
        header("Location: register.php");
        exit;
    }

    // Hash Password & Simpan sebagai Siswa
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, password, nama_lengkap, telepon, role) 
              VALUES ('$username', '$hashed_password', '$nama', '$telepon', 'siswa')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Registrasi Berhasil!";
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['error'] = "Gagal mendaftar, coba lagi!";
        header("Location: register.php");
        exit;
    }
}

$error_message = "";
if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - Zanith Libs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Hilangkan tombol panah pada input nomor */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>
<body class="bg-white md:bg-slate-100 min-h-screen flex items-center justify-center md:p-4">

    <div class="flex flex-col md:flex-row bg-white w-full max-w-5xl min-h-[650px] md:rounded-3xl overflow-hidden md:shadow-2xl border border-slate-100">
        
        <div class="w-full md:w-5/12 bg-blue-600 p-12 flex flex-col justify-center items-center text-white text-center">
            <div class="bg-white/20 p-6 rounded-3xl backdrop-blur-md mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold mb-2 uppercase tracking-tighter">Zanith Libs</h2>
            <p class="text-blue-100 text-sm max-w-xs leading-relaxed">Bergabunglah sebagai anggota untuk menikmati layanan peminjaman buku digital kami.</p>
        </div>

        <div class="w-full md:w-7/12 p-8 md:p-12 flex flex-col justify-center bg-white overflow-y-auto">
            <div class="mb-8">
                <h3 class="text-2xl font-bold text-slate-800">Daftar Akun Baru</h3>
                <p class="text-slate-500 text-sm mt-1">Lengkapi formulir di bawah untuk mendaftar</p>
            </div>

            <?php if ($error_message): ?>
                <div id="alert-error" class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-2xl mb-6 text-sm flex items-center gap-3 transition-all duration-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 ml-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" required
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                        placeholder="Nama Lengkap Siswa">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 ml-1">Username</label>
                    <input type="text" name="username" required
                        class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                        placeholder="Untuk login">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 ml-1">No. Telepon</label>
                    <div class="relative flex items-center">
                        <div class="absolute left-4 flex items-center gap-2 border-r border-slate-300 pr-3 pointer-events-none">
                            <span class="text-base">🇮🇩</span>
                            <span class="text-slate-600 font-bold text-sm">+62</span>
                        </div>
                        <input type="number" name="telepon" required 
                            class="w-full pl-24 pr-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all" 
                            placeholder="812345678">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 ml-1">Password</label>
                    <div class="relative">
                        <input type="password" id="regPassword" name="password" required
                            class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePass('regPassword', 'eye1', 'eye1_closed')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400">
                            <svg id="eye1" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye1_closed" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2 ml-1">Konfirmasi</label>
                    <div class="relative">
                        <input type="password" id="confirmPassword" name="confirm_password" required
                            class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePass('confirmPassword', 'eye2', 'eye2_closed')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400">
                            <svg id="eye2" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye2_closed" xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="md:col-span-2 mt-4">
                    <button type="submit" name="register"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-blue-100 transition-all duration-300 active:scale-95">
                        Daftar Sekarang
                    </button>
                    <p class="text-center text-sm text-slate-500 mt-6">
                        Sudah punya akun? 
                        <a href="login.php" class="text-blue-600 font-bold hover:underline">Masuk di sini</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle Show/Hide Password
        function togglePass(inputId, eyeOpenId, eyeClosedId) {
            const input = document.getElementById(inputId);
            const eyeOpen = document.getElementById(eyeOpenId);
            const eyeClosed = document.getElementById(eyeClosedId);

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        // Auto-Hide Alert
        window.addEventListener('DOMContentLoaded', () => {
            const alertBox = document.getElementById('alert-error');
            if (alertBox) {
                setTimeout(() => {
                    alertBox.style.opacity = '0';
                    alertBox.style.transform = 'translateY(-10px)';
                    setTimeout(() => alertBox.remove(), 500);
                }, 5000);
            }
        });
    </script>

</body>
</html>