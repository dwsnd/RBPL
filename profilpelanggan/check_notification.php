<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['notification'])) {
    echo json_encode(['notification' => $_SESSION['notification']]);
    unset($_SESSION['notification']); // Hapus setelah dibaca
} else {
    echo json_encode(['notification' => null]);
}
?>