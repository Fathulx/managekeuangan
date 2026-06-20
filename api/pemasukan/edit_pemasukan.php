<?php
require __DIR__ . '/../conn.php';
require __DIR__ . '/../session_db.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}


if (isset($_POST['update'])) {

    $user_id    = (int) $_SESSION['user_id'];
    $id         = (int) $_GET['id'];
    $tanggal    = $_POST['tanggal'];
    $keterangan = htmlspecialchars(trim($_POST['keterangan']));
    $jumlah     = (float) $_POST['jumlah'];

    
    $stmt = mysqli_prepare($conn, "UPDATE pemasukan SET tanggal = ?, keterangan = ?, jumlah = ? WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ssdii", $tanggal, $keterangan, $jumlah, $id, $user_id);
    mysqli_stmt_execute($stmt);

    header("Location: pemasukan.php?success=edit");
    exit;
}