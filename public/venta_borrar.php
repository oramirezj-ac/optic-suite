<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_venta = $_POST['id_venta'];

    $stmt = $pdo->prepare("DELETE FROM ventas WHERE id_venta = ?");
    $stmt->execute([$id_venta]);

    header("Location: /ventas.php?status=deleted");
    exit();
}