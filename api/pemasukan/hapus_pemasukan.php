<?php
require __DIR__ . '/../conn.php';
require __DIR__ . '/../session_db.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {

    $user_id = (int) $_SESSION['user_id'];
    $id      = (int) $_GET['id'];

    // Pastikan data milik user yang login sebelum hapus
    $stmt = mysqli_prepare($conn, "DELETE FROM pemasukan WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
    mysqli_stmt_execute($stmt);

    header("Location: pemasukan.php?success=hapus");
    exit;
}