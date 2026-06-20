<?php
require __DIR__ . '/conn.php';
require __DIR__ . '/session_db.php';

if (isset($_SESSION['login'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {

    // Sanitasi input
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);

    // Prepared statement (aman dari SQL Injection)
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {

        // Regenerate session ID untuk keamanan
        session_regenerate_id(true);

        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['is_admin'] = ($user['username'] === 'admin');

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau Password salah";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Keuangan</title>

    <link rel="icon" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2334d399' stroke-width='1.5'><path stroke-linecap='round' stroke-linejoin='round' d='M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z' /></svg>" type="image/svg+xml">

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center relative overflow-hidden bg-slate-900">

    <!-- Background dekoratif -->
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900"></div>
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-500 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-400 rounded-full mix-blend-screen filter blur-3xl opacity-10"></div>
    <div class="absolute top-1/3 right-1/4 w-72 h-72 bg-teal-400 rounded-full mix-blend-screen filter blur-3xl opacity-10"></div>

    <!-- Kartu Login -->
    <div class="relative z-10 w-full max-w-md mx-4">

        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl p-8 sm:p-10">

            <!-- Logo / Judul -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-500/20 border border-emerald-400/30 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white">Aplikasi Keuangan</h2>
                <p class="text-slate-300 text-sm mt-1">Kelola pemasukan & pengeluaran dengan mudah</p>
            </div>

            <!-- Pesan Error -->
            <?php if ($error !== "") { ?>
                <div class="mb-5 px-4 py-3 rounded-lg bg-red-500/10 border border-red-400/30 text-red-300 text-sm text-center">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php } ?>

            <!-- Pesan Registrasi Sukses -->
            <?php if (isset($_GET['registrasi']) && $_GET['registrasi'] === 'sukses') { ?>
                <div class="mb-5 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-400/30 text-emerald-300 text-sm text-center">
                    Registrasi berhasil! Silakan login.
                </div>
            <?php } ?>

            <!-- Form Login -->
            <form method="POST" class="space-y-5">

                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1.5">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="username"
                            placeholder="Masukkan username"
                            required
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition">
                    </div>
                </div>

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
                            placeholder="Masukkan password"
                            required
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition">
                    </div>
                </div>

                <button
                    type="submit"
                    name="login"
                    class="w-full py-2.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-900 font-semibold transition shadow-lg shadow-emerald-500/30">
                    Masuk
                </button>

            </form>

            <p class="text-center text-slate-300 text-sm mt-6">
                Belum punya akun?
                <a href="register.php" class="text-emerald-400 hover:text-emerald-300 font-medium transition">Daftar di sini</a>
            </p>

            <p class="text-center text-slate-400 text-xs mt-4">
                &copy; <?php echo date("Y"); ?> Aplikasi Keuangan Admin
            </p>

        </div>
    </div>

</body>

</html>