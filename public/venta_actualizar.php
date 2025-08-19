<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_venta = $_POST['id_venta'];
    $numero_nota = $_POST['numero_nota'] ?? '';
    $fecha_venta = $_POST['fecha_venta'];
    $costo_total = $_POST['costo_total'] ?? 0;
    $anticipo = $_POST['anticipo'] ?? 0;
    $estado_pago = $_POST['estado_pago'];
    $observaciones_venta = $_POST['observaciones_venta'] ?? '';
    $saldo = $costo_total - $anticipo;

    $sql = "UPDATE ventas SET 
                numero_nota = ?,
                fecha_venta = ?,
                costo_total = ?,
                anticipo = ?,
                saldo = ?,
                estado_pago = ?,
                observaciones_venta = ?
            WHERE id_venta = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $numero_nota, $fecha_venta, $costo_total, $anticipo, $saldo, $estado_pago, $observaciones_venta, $id_venta
    ]);

    header("Location: /venta_detalle.php?id=" . $id_venta . "&status=updated");
    exit();
}