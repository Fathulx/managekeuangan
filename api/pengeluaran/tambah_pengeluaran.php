<?php
require __DIR__ . '../conn.php';
require __DIR__ . '../session_db.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}



if (isset($_POST['simpan'])) {

    $user_id    = (int) $_SESSION['user_id'];
    $tanggal    = $_POST['tanggal'];
    $keterangan = htmlspecialchars(trim($_POST['keterangan']));
    $jumlah     = (float) $_POST['jumlah'];

    // Prepared statement + simpan dengan user_id
    $stmt = mysqli_prepare($conn, "INSERT INTO pengeluaran (user_id, tanggal, keterangan, jumlah) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issd", $user_id, $tanggal, $keterangan, $jumlah);
    mysqli_stmt_execute($stmt);

    header("Location: pengeluaran.php?success=tambah");
    exit;
}