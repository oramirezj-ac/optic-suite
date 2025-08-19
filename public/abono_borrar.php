<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_abono = $_POST['id_abono'];
    $id_venta = $_POST['id_venta'];

    // 1. Borrar el abono
    $stmt_delete = $pdo->prepare("DELETE FROM abonos WHERE id_abono = ?");
    $stmt_delete->execute([$id_abono]);

    // 2. Recalcular el total abonado y el saldo
    $stmt_total = $pdo->prepare("SELECT costo_total, (SELECT SUM(monto_abono) FROM abonos WHERE id_venta = ?) as total_abonado FROM ventas WHERE id_venta = ?");
    $stmt_total->execute([$id_venta, $id_venta]);
    $calculos = $stmt_total->fetch();
    
    $total_abonado = $calculos['total_abonado'] ?? 0;
    $nuevo_saldo = $calculos['costo_total'] - $total_abonado;
    $nuevo_estado = ($nuevo_saldo <= 0) ? 'Pagado' : (($total_abonado > 0) ? 'Abonos' : 'Pendiente');

    // 3. Actualizar la venta
    $sql_update = "UPDATE ventas SET saldo = ?, estado_pago = ?, anticipo = (SELECT monto_abono FROM abonos WHERE id_venta = ? ORDER BY fecha_abono ASC LIMIT 1) WHERE id_venta = ?";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([$nuevo_saldo, $nuevo_estado, $id_venta, $id_venta]);

    header("Location: /venta_pagos_detalle.php?id_venta=" . $id_venta);
    exit();
}