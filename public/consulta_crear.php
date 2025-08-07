<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar los datos del formulario
    $id_paciente = $_POST['id_paciente'];
    $fecha_consulta = $_POST['fecha_consulta'];
    $av_ao = $_POST['av_ao'] ?? '';
    $av_od = $_POST['av_od'] ?? '';
    $av_oi = $_POST['av_oi'] ?? '';
    $motivo_consulta = $_POST['motivo_consulta'] ?? '';
    $notas_adicionales = $_POST['notas_adicionales'] ?? '';

    // Preparar y ejecutar la consulta SQL
    $sql = "INSERT INTO consultas (id_paciente, fecha_consulta, av_ao, av_od, av_oi, motivo_consulta, notas_adicionales) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        $id_paciente, 
        $fecha_consulta, 
        $av_ao,
        $av_od,
        $av_oi, 
        $motivo_consulta, 
        $notas_adicionales
    ]);

    // Obtenemos el ID de la consulta recién creada
    $id_nueva_consulta = $pdo->lastInsertId();

    // Redirigir a la página de DETALLE de la nueva consulta
    header("Location: /consulta_detalle.php?id_consulta=" . $id_nueva_consulta . "&id_paciente=" . $id_paciente . "&status=success");
    exit();
}