<?php
require __DIR__ . '/conn.php';
require __DIR__ . '/session_db.php';

if (isset($_SESSION['login'])) {
    header("Location: dashboard.php");
    exit;
}



$error = "";
$success = "";

if (isset($_POST['register'])) {

    // Sanitasi input
    $nama     = htmlspecialchars(trim($_POST['nama']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);
    $konfirmasi = trim($_POST['konfirmasi']);

    // Validasi field kosong
    if (empty($nama) || empty($username) || empty($password) || empty($konfirmasi)) {
        $error = "Semua field wajib diisi.";
    }
    // Validasi panjang username
    elseif (strlen($username) < 4 || strlen($username) > 20) {
        $error = "Username harus antara 4–20 karakter.";
    }
    // Validasi karakter username (hanya huruf, angka, underscore)
    elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Username hanya boleh mengandung huruf, angka, dan underscore.";
    }
    // Validasi panjang password
    elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    }
    // Validasi konfirmasi password
    elseif ($password !== $konfirmasi) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        // Cek username sudah dipakai
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Username sudah digunakan, pilih username lain.";
        } else {
            // Hash password & simpan user baru
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmtInsert = mysqli_prepare($conn, "INSERT INTO users (nama, username, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmtInsert, "sss", $nama, $username, $hashedPassword);

            if (mysqli_stmt_execute($stmtInsert)) {
                header("Location: index.php?registrasi=sukses");
                exit;
            } else {
                $error = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Aplikasi Keuangan</title>

    <link rel="icon" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2334d399' stroke-width='1.5'><path stroke-linecap='round' stroke-linejoin='round' d='M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z' /></svg>" type="image/svg+xml">


    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center relative overflow-hidden bg-slate-900">

    <!-- Background dekoratif -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-500 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-400 rounded-full mix-blend-screen filter blur-3xl opacity-10"></div>
    <div class="absolute top-1/3 right-1/4 w-72 h-72 bg-teal-400 rounded-full mix-blend-screen filter blur-3xl opacity-10"></div>

    <!-- Kartu Registrasi -->
    <div class="relative z-10 w-full max-w-md mx-4 my-8">

        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl p-8 sm:p-10">

            <!-- Logo / Judul -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/20 border border-emerald-400/30 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Buat Akun Baru</h2>
                <p class="text-slate-300 text-sm mt-1">Daftar dan mulai kelola keuanganmu</p>
            </div>

            <!-- Pesan Error -->
            <?php if ($error !== "") { ?>
                <div class="mb-5 px-4 py-3 rounded-lg bg-red-500/10 border border-red-400/30 text-red-300 text-sm text-center">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php } ?>

            <!-- Form Registrasi -->
            <form method="POST" class="space-y-4">

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1.5">Nama Lengkap</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="nama"
                            placeholder="Masukkan nama lengkap"
                            value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"
                            required
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition">
                    </div>
                </div>

                <!-- Username -->
                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1.5">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="username"
                            placeholder="Buat username (4–20 karakter)"
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                            required
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition">
                    </div>
                    <p class="text-xs text-slate-400 mt-1 ml-1">Huruf, angka, dan underscore saja.</p>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </span>
                        <input
                            type="password"
                            name="password"
                            placeholder="Buat password (min. 6 karakter)"
                            required
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition">
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1.5">Konfirmasi Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        </span>
                        <input
                            type="password"
                            name="konfirmasi"
                            placeholder="Ulangi password"
                            required
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition">
                    </div>
                </div>

                <button
                    type="submit"
                    name="register"
                    class="w-full py-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-semibold transition shadow-lg shadow-emerald-500/30 mt-2">
                    Daftar Sekarang
                </button>

            </form>

            <p class="text-center text-slate-300 text-sm mt-6">
                Sudah punya akun?
                <a href="index.php" class="text-emerald-400 hover:text-emerald-300 font-medium transition">Masuk di sini</a>
            </p>

            <p class="text-center text-slate-400 text-xs mt-4">
                &copy; <?php echo date("Y"); ?> Aplikasi Keuangan Admin
            </p>

        </div>
    </div>

</body>

</html> 