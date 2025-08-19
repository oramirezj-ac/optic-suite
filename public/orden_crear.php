<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar los datos del formulario
    $id_venta = $_POST['id_venta'];
    $nombre_laboratorio = $_POST['nombre_laboratorio'] ?? '';
    $numero_orden_externa = $_POST['numero_orden_externa'] ?? '';
    $fecha_envio = $_POST['fecha_envio'];
    $costo_laboratorio = $_POST['costo_laboratorio'] ?? null;
    $estado_orden = $_POST['estado_orden'] ?? 'Enviada';

    // Preparar y ejecutar la consulta SQL
    $sql = "INSERT INTO ordenes_laboratorio (id_venta, nombre_laboratorio, numero_orden_externa, fecha_envio, costo_laboratorio, estado_orden) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        $id_venta,
        $nombre_laboratorio,
        $numero_orden_externa,
        $fecha_envio,
        $costo_laboratorio,
        $estado_orden
    ]);

    // Redirigir de vuelta al detalle de la venta
    header("Location: /venta_detalle.php?id=" . $id_venta . "&status=orden_creada");
    exit();
}