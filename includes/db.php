<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "petshop";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
