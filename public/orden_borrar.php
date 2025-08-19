<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_orden = $_POST['id_orden'];
    $id_venta = $_POST['id_venta'];

    $stmt = $pdo->prepare("DELETE FROM ordenes_laboratorio WHERE id_orden_lab = ?");
    $stmt->execute([$id_orden]);

    header("Location: /venta_detalle.php?id=" . $id_venta);
    exit();
}