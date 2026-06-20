<?php
require __DIR__ . '/conn.php';
require __DIR__ . '/session_db.php';

// Proteksi: hanya admin yang boleh akses
if (!isset($_SESSION['login']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit;
}


$error   = "";
$success = "";

// ─── DELETE USER ─────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];

    // Tidak boleh hapus diri sendiri (admin)
    if ($deleteId === (int) $_SESSION['user_id']) {
        $error = "Kamu tidak bisa menghapus akun sendiri.";
    } else {
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $deleteId);
        if (mysqli_stmt_execute($stmt)) {
            $success = "User berhasil dihapus.";
        } else {
            $error = "Gagal menghapus user.";
        }
    }
}

// ─── UPDATE USER ─────────────────────────────────────────────────────────────
if (isset($_POST['update'])) {
    $updateId = (int) $_POST['user_id'];
    $nama     = htmlspecialchars(trim($_POST['nama']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);

    if (empty($nama) || empty($username)) {
        $error = "Nama dan username wajib diisi.";
    } elseif (strlen($username) < 4 || strlen($username) > 20) {
        $error = "Username harus antara 4–20 karakter.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Username hanya boleh huruf, angka, dan underscore.";
    } else {
        // Cek username sudah dipakai user lain
        $stmtCek = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($stmtCek, "si", $username, $updateId);
        mysqli_stmt_execute($stmtCek);
        mysqli_stmt_store_result($stmtCek);

        if (mysqli_stmt_num_rows($stmtCek) > 0) {
            $error = "Username sudah digunakan user lain.";
        } else {
            if (!empty($password)) {
                // Update dengan password baru
                if (strlen($password) < 6) {
                    $error = "Password minimal 6 karakter.";
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmtUp = mysqli_prepare($conn, "UPDATE users SET nama = ?, username = ?, password = ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmtUp, "sssi", $nama, $username, $hashed, $updateId);
                    if (mysqli_stmt_execute($stmtUp)) {
                        $success = "Data user berhasil diperbarui.";
                        // Update session jika admin mengubah datanya sendiri
                        if ($updateId === (int) $_SESSION['user_id']) {
                            $_SESSION['username'] = $username;
                            $_SESSION['nama']     = $nama;
                        }
                    } else {
                        $error = "Gagal memperbarui data user.";
                    }
                }
            } else {
                // Update tanpa ganti password
                $stmtUp = mysqli_prepare($conn, "UPDATE users SET nama = ?, username = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmtUp, "ssi", $nama, $username, $updateId);
                if (mysqli_stmt_execute($stmtUp)) {
                    $success = "Data user berhasil diperbarui.";
                    if ($updateId === (int) $_SESSION['user_id']) {
                        $_SESSION['username'] = $username;
                        $_SESSION['nama']     = $nama;
                    }
                } else {
                    $error = "Gagal memperbarui data user.";
                }
            }
        }
    }
}

// ─── AMBIL SEMUA USER ────────────────────────────────────────────────────────
$users = [];
$result = mysqli_query($conn, "SELECT id, username, nama, created_at FROM users ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}

// User yang sedang diedit
$editUser = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmtEdit = mysqli_prepare($conn, "SELECT id, username, nama FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmtEdit, "i", $editId);
    mysqli_stmt_execute($stmtEdit);
    $editResult = mysqli_stmt_get_result($stmtEdit);
    $editUser = mysqli_fetch_assoc($editResult);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Aplikasi Keuangan</title>

    <link rel="icon" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2334d399' stroke-width='1.5'><path stroke-linecap='round' stroke-linejoin='round' d='M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z' /></svg>" type="image/svg+xml">


    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen relative overflow-x-hidden bg-slate-900">

    <!-- Background dekoratif -->
    <div class="fixed inset-0 bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 -z-10"></div>
    <div class="fixed -top-32 -left-32 w-96 h-96 bg-emerald-500 rounded-full mix-blend-screen filter blur-3xl opacity-20 -z-10"></div>
    <div class="fixed -bottom-32 -right-32 w-96 h-96 bg-amber-400 rounded-full mix-blend-screen filter blur-3xl opacity-10 -z-10"></div>
    <div class="fixed top-1/3 right-1/4 w-72 h-72 bg-teal-400 rounded-full mix-blend-screen filter blur-3xl opacity-10 -z-10"></div>

    <!-- Navbar -->
    <nav class="bg-white/10 backdrop-blur-xl border-b border-white/20 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-white font-semibold text-sm">Aplikasi Keuangan</span>
                <span class="text-slate-400 text-sm hidden sm:block">/ Manajemen User</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-slate-300 text-sm hidden sm:block">
                    Halo, <span class="text-emerald-400 font-medium"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                </span>
                <a href="dashboard.php"
                    class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-slate-200 text-sm transition border border-white/10">
                    Dashboard
                </a>
                <a href="logout.php"
                    class="px-3 py-1.5 rounded-lg bg-red-500/20 hover:bg-red-500/30 text-red-300 text-sm transition border border-red-400/20">
                    Keluar
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-8">

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">Manajemen User</h1>
            <p class="text-slate-400 text-sm mt-1">Kelola data pengguna aplikasi keuangan</p>
        </div>

        <!-- Notifikasi -->
        <?php if ($success !== "") { ?>
            <div class="mb-5 px-4 py-3 rounded-lg bg-emerald-500/10 border border-emerald-400/30 text-emerald-300 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php } ?>
        <?php if ($error !== "") { ?>
            <div class="mb-5 px-4 py-3 rounded-lg bg-red-500/10 border border-red-400/30 text-red-300 text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <div class="grid grid-cols-1 <?php echo $editUser ? 'lg:grid-cols-2' : ''; ?> gap-6">

            <!-- ── TABEL USER ── -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                    <div>
                        <h2 class="text-white font-semibold">Daftar User</h2>
                        <p class="text-slate-400 text-xs mt-0.5"><?php echo count($users); ?> user terdaftar</p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Mobile: Card view -->
                <div class="block sm:hidden divide-y divide-white/10">
                    <?php foreach ($users as $user): ?>
                        <div class="px-5 py-4">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-white font-medium text-sm"><?php echo htmlspecialchars($user['nama']); ?></p>
                                    <p class="text-slate-400 text-xs mt-0.5">@<?php echo htmlspecialchars($user['username']); ?></p>
                                    <p class="text-slate-500 text-xs mt-1"><?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
                                </div>
                                <div class="flex flex-col gap-2 shrink-0">
                                    <a href="?edit=<?php echo $user['id']; ?>"
                                        class="px-3 py-1.5 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-xs font-medium transition border border-amber-400/20 text-center">
                                        Edit
                                    </a>
                                    <?php if ($user['id'] !== (int) $_SESSION['user_id']): ?>
                                        <a href="?delete=<?php echo $user['id']; ?>"
                                            onclick="return confirm('Yakin hapus user <?php echo htmlspecialchars($user['nama']); ?>?')"
                                            class="px-3 py-1.5 rounded-lg bg-red-500/20 hover:bg-red-500/30 text-red-300 text-xs font-medium transition border border-red-400/20 text-center">
                                            Hapus
                                        </a>
                                    <?php else: ?>
                                        <span class="px-3 py-1.5 rounded-lg bg-white/5 text-slate-500 text-xs text-center border border-white/10">Kamu</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Desktop: Table view -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-white/10">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">#</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Username</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Bergabung</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            <?php foreach ($users as $index => $user): ?>
                                <tr class="hover:bg-white/5 transition <?php echo (isset($_GET['edit']) && (int)$_GET['edit'] === (int)$user['id']) ? 'bg-amber-500/5' : ''; ?>">
                                    <td class="px-5 py-3.5 text-slate-400 text-sm"><?php echo $index + 1; ?></td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-emerald-500/20 border border-emerald-400/20 flex items-center justify-center shrink-0">
                                                <span class="text-emerald-400 text-xs font-bold"><?php echo strtoupper(substr($user['nama'], 0, 1)); ?></span>
                                            </div>
                                            <span class="text-white text-sm font-medium"><?php echo htmlspecialchars($user['nama']); ?></span>
                                            <?php if ($user['id'] === (int) $_SESSION['user_id']): ?>
                                                <span class="text-xs px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-400/20">Kamu</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-300 text-sm">@<?php echo htmlspecialchars($user['username']); ?></td>
                                    <td class="px-5 py-3.5 text-slate-400 text-sm"><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <a href="?edit=<?php echo $user['id']; ?>"
                                                class="px-3 py-1.5 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 text-xs font-medium transition border border-amber-400/20">
                                                Edit
                                            </a>
                                            <?php if ($user['id'] !== (int) $_SESSION['user_id']): ?>
                                                <a href="?delete=<?php echo $user['id']; ?>"
                                                    onclick="return confirm('Yakin ingin menghapus user <?php echo htmlspecialchars($user['nama']); ?>?')"
                                                    class="px-3 py-1.5 rounded-lg bg-red-500/20 hover:bg-red-500/30 text-red-300 text-xs font-medium transition border border-red-400/20">
                                                    Hapus
                                                </a>
                                            <?php else: ?>
                                                <span class="px-3 py-1.5 rounded-lg bg-white/5 text-slate-500 text-xs border border-white/10 cursor-not-allowed">Hapus</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── FORM EDIT USER ── -->
            <?php if ($editUser): ?>
                <div class="bg-white/10 backdrop-blur-xl border border-amber-400/30 rounded-2xl shadow-2xl p-6 h-fit">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-white font-semibold">Edit User</h2>
                            <p class="text-slate-400 text-xs mt-0.5">Perbarui data pengguna</p>
                        </div>
                        <a href="manage_users.php"
                            class="w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition text-slate-400 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    </div>

                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="user_id" value="<?php echo $editUser['id']; ?>">

                        <!-- Nama -->
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
                                    value="<?php echo htmlspecialchars($editUser['nama']); ?>"
                                    required
                                    class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition">
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
                                    value="<?php echo htmlspecialchars($editUser['username']); ?>"
                                    required
                                    class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition">
                            </div>
                        </div>

                        <!-- Password baru (opsional) -->
                        <div>
                            <label class="block text-sm font-medium text-slate-200 mb-1.5">
                                Password Baru
                                <span class="text-slate-500 font-normal">(kosongkan jika tidak diubah)</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                </span>
                                <input
                                    type="password"
                                    name="password"
                                    placeholder="Biarkan kosong jika tidak diubah"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-white/5 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition">
                            </div>
                        </div>

                        <div class="flex gap-3 pt-1">
                            <button
                                type="submit"
                                name="update"
                                class="flex-1 py-2.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-900 font-semibold transition shadow-lg shadow-amber-500/20">
                                Simpan Perubahan
                            </button>
                            <a href="manage_users.php"
                                class="px-4 py-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-slate-200 font-medium transition border border-white/10 text-sm flex items-center">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

        </div>

        <p class="text-center text-slate-500 text-xs mt-8">
            &copy; <?php echo date("Y"); ?> Aplikasi Keuangan Admin
        </p>
    </div>

</body>

</html>