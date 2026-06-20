<?php
require __DIR__ . '../conn.php';
require __DIR__ . '../session_db.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}



$user_id = (int) $_SESSION['user_id'];

// Ambil hanya data milik user yang login
$stmt = mysqli_prepare($conn, "SELECT * FROM pengeluaran WHERE user_id = ? ORDER BY tanggal DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengeluaran - Aplikasi Keuangan</title>

    <link rel="icon" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2334d399' stroke-width='1.5'><path stroke-linecap='round' stroke-linejoin='round' d='M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z' /></svg>" type="image/svg+xml">


    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen relative bg-slate-900">

    <!-- Background dekoratif -->
    <div class="fixed inset-0 bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 -z-10"></div>
    <div class="fixed -top-32 -left-32 w-96 h-96 bg-emerald-500 rounded-full mix-blend-screen filter blur-3xl opacity-20 -z-10"></div>
    <div class="fixed -bottom-32 -right-32 w-96 h-96 bg-amber-400 rounded-full mix-blend-screen filter blur-3xl opacity-10 -z-10"></div>
    <div class="fixed top-1/3 right-1/4 w-72 h-72 bg-teal-400 rounded-full mix-blend-screen filter blur-3xl opacity-10 -z-10"></div>

    <!-- Navbar -->
    <nav class="relative z-10 border-b border-white/10 bg-white/5 backdrop-blur-xl">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-full bg-red-500/20 border border-red-400/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-white font-semibold">Aplikasi Keuangan</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-slate-400 text-sm hidden sm:block">
                    <span class="text-red-400 font-medium"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                </span>
                <a href="../dashboard.php" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/10 border border-white/20 text-slate-200 text-sm hover:bg-white/20 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>
    </nav>

    <!-- Konten -->
    <main class="relative z-10 max-w-6xl mx-auto px-6 py-10">

        <!-- Notifikasi -->
        <?php if (isset($_GET['success'])): ?>
            <div class="mb-5 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-400/30 text-emerald-300 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <?php
                $msg = [
                    'tambah' => 'Data pengeluaran berhasil ditambahkan.',
                    'edit'   => 'Data pengeluaran berhasil diperbarui.',
                    'hapus'  => 'Data pengeluaran berhasil dihapus.',
                ];
                echo htmlspecialchars($msg[$_GET['success']] ?? 'Berhasil.');
                ?>
            </div>
        <?php endif; ?>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white">Data Pengeluaran</h1>
                <p class="text-slate-400 text-sm mt-1">Riwayat pengeluaran milik kamu</p>
            </div>
            <button type="button" onclick="document.getElementById('modalTambah').showModal()"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-red-500 hover:bg-red-400 text-white font-semibold transition shadow-lg shadow-red-500/30 w-fit">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Data
            </button>
        </div>

        <!-- Tabel -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10">
                            <th class="px-5 py-3 text-slate-300 text-sm font-medium">No</th>
                            <th class="px-5 py-3 text-slate-300 text-sm font-medium">Tanggal</th>
                            <th class="px-5 py-3 text-slate-300 text-sm font-medium">Keterangan</th>
                            <th class="px-5 py-3 text-slate-300 text-sm font-medium">Jumlah</th>
                            <th class="px-5 py-3 text-slate-300 text-sm font-medium text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $rows = [];
                        while ($row = mysqli_fetch_assoc($data)) {
                            $rows[] = $row;
                        }

                        if (count($rows) === 0): ?>
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-400 text-sm">
                                    Belum ada data pengeluaran. Klik <span class="text-red-400 font-medium">Tambah Data</span> untuk mulai mencatat.
                                </td>
                            </tr>
                        <?php else:
                            foreach ($rows as $row): ?>
                            <tr class="border-b border-white/5 hover:bg-white/5 transition">
                                <td class="px-5 py-3 text-slate-300"><?php echo $no++; ?></td>
                                <td class="px-5 py-3 text-slate-300"><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                <td class="px-5 py-3 text-slate-200"><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-red-500/15 text-red-400 text-sm font-medium">
                                        Rp <?php echo number_format($row['jumlah'], 0, ",", "."); ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="document.getElementById('modalEdit<?php echo $row['id']; ?>').showModal()"
                                            class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-amber-400/15 text-amber-300 text-sm hover:bg-amber-400/25 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <a href="hapus_pengeluaran.php?id=<?php echo $row['id']; ?>"
                                            onclick="return confirm('Yakin ingin menghapus data ini?');"
                                            class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-500/15 text-red-300 text-sm hover:bg-red-500/25 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <dialog id="modalEdit<?php echo $row['id']; ?>" class="backdrop:bg-slate-900/70 backdrop:backdrop-blur-sm rounded-2xl p-0 m-auto bg-transparent">
                                <div class="w-[90vw] max-w-md bg-slate-900/95 border border-white/20 rounded-2xl shadow-2xl p-6 sm:p-7">
                                    <div class="flex items-center justify-between mb-5">
                                        <h3 class="text-lg font-bold text-white">Edit Data Pengeluaran</h3>
                                        <button type="button" onclick="document.getElementById('modalEdit<?php echo $row['id']; ?>').close()" class="text-slate-400 hover:text-white transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <form method="POST" action="edit_pengeluaran.php?id=<?php echo $row['id']; ?>" class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-200 mb-1.5">Tanggal</label>
                                            <input type="date" name="tanggal" value="<?php echo htmlspecialchars($row['tanggal']); ?>" required
                                                class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition [color-scheme:dark]">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-200 mb-1.5">Keterangan</label>
                                            <input type="text" name="keterangan" value="<?php echo htmlspecialchars($row['keterangan']); ?>" required
                                                class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-200 mb-1.5">Jumlah</label>
                                            <input type="number" name="jumlah" value="<?php echo htmlspecialchars($row['jumlah']); ?>" required min="0"
                                                class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition">
                                        </div>
                                        <div class="flex gap-3 pt-2">
                                            <button type="button" onclick="document.getElementById('modalEdit<?php echo $row['id']; ?>').close()"
                                                class="flex-1 py-2.5 rounded-lg bg-white/10 border border-white/20 text-slate-200 font-medium hover:bg-white/15 transition">
                                                Batal
                                            </button>
                                            <button type="submit" name="update"
                                                class="flex-1 py-2.5 rounded-lg bg-amber-400 hover:bg-amber-300 text-slate-900 font-semibold transition shadow-lg shadow-amber-400/30">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </dialog>

                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Modal Tambah -->
    <dialog id="modalTambah" class="backdrop:bg-slate-900/70 backdrop:backdrop-blur-sm rounded-2xl p-0 m-auto bg-transparent">
        <div class="w-[90vw] max-w-md bg-slate-900/95 border border-white/20 rounded-2xl shadow-2xl p-6 sm:p-7">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-white">Tambah Data Pengeluaran</h3>
                <button type="button" onclick="document.getElementById('modalTambah').close()" class="text-slate-400 hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="tambah_pengeluaran.php" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" required
                        class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition [color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1.5">Keterangan</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Bayar listrik" required
                        class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1.5">Jumlah</label>
                    <input type="number" name="jumlah" placeholder="Contoh: 150000" required min="0"
                        class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition">
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modalTambah').close()"
                        class="flex-1 py-2.5 rounded-lg bg-white/10 border border-white/20 text-slate-200 font-medium hover:bg-white/15 transition">
                        Batal
                    </button>
                    <button type="submit" name="simpan"
                        class="flex-1 py-2.5 rounded-lg bg-red-500 hover:bg-red-400 text-white font-semibold transition shadow-lg shadow-red-500/30">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </dialog>

</body>
</html>