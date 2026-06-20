<?php
require __DIR__ . '/conn.php';
require __DIR__ . '/session_db.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}



$user_id = $_SESSION['user_id'];

$masuk = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT SUM(jumlah) total FROM pemasukan WHERE user_id='$user_id'"
    )
);

$keluar = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT SUM(jumlah) total FROM pengeluaran WHERE user_id='$user_id'"
    )
);

$totalMasuk = $masuk['total'] ?? 0;
$totalKeluar = $keluar['total'] ?? 0;

$saldo = $totalMasuk - $totalKeluar;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Aplikasi Keuangan</title>
    
    <link rel="icon" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2334d399' stroke-width='1.5'><path stroke-linecap='round' stroke-linejoin='round' d='M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z' /></svg>" type="image/svg+xml">

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen relative bg-slate-900">

    <!-- Background dekoratif (samakan dengan halaman login) -->
    <div class="fixed inset-0 bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 -z-10"></div>
    <div class="fixed -top-32 -left-32 w-96 h-96 bg-emerald-500 rounded-full mix-blend-screen filter blur-3xl opacity-20 -z-10"></div>
    <div class="fixed -bottom-32 -right-32 w-96 h-96 bg-amber-400 rounded-full mix-blend-screen filter blur-3xl opacity-10 -z-10"></div>
    <div class="fixed top-1/3 right-1/4 w-72 h-72 bg-teal-400 rounded-full mix-blend-screen filter blur-3xl opacity-10 -z-10"></div>

    <!-- Navbar -->
    <nav class="relative z-10 border-b border-white/10 bg-white/5 backdrop-blur-xl">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-full bg-emerald-500/20 border border-emerald-400/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-white font-semibold">Aplikasi Keuangan</span>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-slate-300 text-sm hidden sm:inline">
                    Selamat datang, <span class="text-white font-medium"><?= $_SESSION['username']; ?></span>
                </span>
                <a href="logout.php" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-400/30 text-red-300 text-sm hover:bg-red-500/20 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Konten -->
    <main class="relative z-10 max-w-6xl mx-auto px-6 py-10">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white">Dashboard</h1>
            <p class="text-slate-400 text-sm mt-1">Ringkasan keuangan kamu saat ini</p>
        </div>

        <!-- Card Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-10">

            <!-- Pemasukan -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-slate-300 text-sm">Total Pemasukan</span>
                    <div class="flex items-center justify-center w-9 h-9 rounded-full bg-emerald-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l-6.75-6.75M12 19.5l6.75-6.75" />
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-emerald-400">
                    Rp <?= number_format($totalMasuk, 0, ",", "."); ?>
                </p>
            </div>

            <!-- Pengeluaran -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-slate-300 text-sm">Total Pengeluaran</span>
                    <div class="flex items-center justify-center w-9 h-9 rounded-full bg-red-500/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l6.75 6.75M12 4.5l-6.75 6.75" />
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-red-400">
                    Rp <?= number_format($totalKeluar, 0, ",", "."); ?>
                </p>
            </div>

            <!-- Saldo -->
            <div class="bg-white/10 backdrop-blur-xl border border-amber-400/30 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-slate-300 text-sm">Saldo</span>
                    <div class="flex items-center justify-center w-9 h-9 rounded-full bg-amber-400/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M3.75 6.75A.75.75 0 003 6v.75m0 0v6.75A.75.75 0 003.75 14.25h16.5A.75.75 0 0021 13.5V6.75m0 0A.75.75 0 0020.25 6h-.75m0 0V5.25c0-.621-.504-1.125-1.125-1.125h-.75M9 11.25h6m-6 3h6" />
                        </svg>
                    </div>
                </div>
                <p class="text-2xl font-bold text-amber-300">
                    Rp <?= number_format($saldo, 0, ",", "."); ?>
                </p>
            </div>

        </div>

        <!-- Menu Navigasi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

            <a href="pemasukan/pemasukan.php" class="group bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-xl hover:bg-white/15 hover:border-emerald-400/40 transition flex items-center justify-between">
                <div>
                    <h4 class="text-white font-semibold text-lg">Data Pemasukan</h4>
                    <p class="text-slate-400 text-sm mt-1">Lihat & kelola riwayat pemasukan</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400 group-hover:text-emerald-400 group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>

            <a href="pengeluaran/pengeluaran.php" class="group bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-xl hover:bg-white/15 hover:border-red-400/40 transition flex items-center justify-between">
                <div>
                    <h4 class="text-white font-semibold text-lg">Data Pengeluaran</h4>
                    <p class="text-slate-400 text-sm mt-1">Lihat & kelola riwayat pengeluaran</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400 group-hover:text-red-400 group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>

            <?php if ($_SESSION['is_admin']) { ?>
            <a href="users.php" class="group bg-white/10 backdrop-blur-xl border border-amber-400/30 rounded-2xl p-6 shadow-xl hover:bg-white/15 hover:border-amber-400/60 transition flex items-center justify-between sm:col-span-2">
                <div>
                    <h4 class="text-white font-semibold text-lg flex items-center gap-2">
                        Kelola User
                        <span class="text-[10px] uppercase tracking-wide bg-amber-400/20 text-amber-300 px-2 py-0.5 rounded-full">Admin</span>
                    </h4>
                    <p class="text-slate-400 text-sm mt-1">Lihat seluruh user yang telah registrasi</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400 group-hover:text-amber-300 group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>
            <?php } ?>

        </div>

    </main>

</body>

</html>