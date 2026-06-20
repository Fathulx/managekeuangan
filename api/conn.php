<?php
// api/koneksi.php

$host = "thomas.proxy.rlwy.net";
$port = 25960;
$user = "root";
$password = "DUJAfjSgvyBYDEEtJimJNiwEyfSYpXvy";
$database = "railway";

$conn = mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}