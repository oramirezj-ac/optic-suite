<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_venta = $_POST['id_venta'];
    $monto_abono = $_POST['monto_abono'];
    $fecha_abono = $_POST['fecha_abono'];
    $metodo_pago = $_POST['metodo_pago'] ?? 'Abono';

    // 1. Insertar el nuevo abono con su fecha y concepto
    $sql_abono = "INSERT INTO abonos (id_venta, monto_abono, fecha_abono, metodo_pago) VALUES (?, ?, ?, ?)";
    $stmt_abono = $pdo->prepare($sql_abono);
    $stmt_abono->execute([$id_venta, $monto_abono, $fecha_abono, $metodo_pago]);

    // 2. Recalcular y actualizar la venta
    $stmt_total = $pdo->prepare("SELECT costo_total, (SELECT SUM(monto_abono) FROM abonos WHERE id_venta = ?) as total_abonado FROM ventas WHERE id_venta = ?");
    $stmt_total->execute([$id_venta, $id_venta]);
    $calculos = $stmt_total->fetch();
    
    $nuevo_saldo = $calculos['costo_total'] - $calculos['total_abonado'];
    $nuevo_estado = ($nuevo_saldo <= 0) ? 'Pagado' : 'Abonos';

    $sql_update = "UPDATE ventas SET saldo = ?, estado_pago = ? WHERE id_venta = ?";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([$nuevo_saldo, $nuevo_estado, $id_venta]);

    // Regresar al detalle de la venta
    header("Location: /venta_detalle.php?id=" . $id_venta);
    exit();
}