<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar los datos del formulario
    $id_paciente = $_POST['id_paciente'];
    $numero_nota = $_POST['numero_nota'] ?? '';
    $fecha_venta = $_POST['fecha_venta'];
    $costo_total = $_POST['costo_total'] ?? 0;
    $anticipo = $_POST['anticipo'] ?? 0;
    $estado_pago = $_POST['estado_pago'];
    $observaciones_venta = $_POST['observaciones_venta'] ?? '';

    // Calcular el saldo
    $saldo = $costo_total - $anticipo;

    // Preparar y ejecutar la consulta para la tabla 'ventas'
    $sql = "INSERT INTO ventas (id_paciente, numero_nota, fecha_venta, costo_total, anticipo, saldo, estado_pago, observaciones_venta) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        $id_paciente,
        $numero_nota,
        $fecha_venta,
        $costo_total,
        $anticipo,
        $saldo,
        $estado_pago,
        $observaciones_venta
    ]);

    // Obtenemos el ID de la venta recién creada
    $id_nueva_venta = $pdo->lastInsertId();

    // --- NUEVA LÓGICA ---
    // Si hay un anticipo, lo registramos como el primer abono
    if ($anticipo > 0) {
        $sql_abono = "INSERT INTO abonos (id_venta, monto_abono, fecha_abono, metodo_pago) VALUES (?, ?, ?, ?)";
        $stmt_abono = $pdo->prepare($sql_abono);
        // Usamos la fecha de la venta y un método de pago genérico para el anticipo
        $stmt_abono->execute([$id_nueva_venta, $anticipo, $fecha_venta, 'Anticipo']);
    }

    // Redirigir a la página de DETALLE de la nueva venta
    header("Location: /venta_detalle.php?id=" . $id_nueva_venta);
    exit();
}